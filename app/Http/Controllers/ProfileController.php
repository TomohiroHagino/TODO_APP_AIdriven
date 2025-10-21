<?php

namespace App\Http\Controllers;

use App\Application\UserAggregate\Service\DeleteUserAccountService;
use App\Application\UserAggregate\Service\UpdateUserPasswordService;
use App\Application\UserAggregate\Service\UpdateUserProfileService;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use RuntimeException;

/**
 * プロフィール管理コントローラー（DDD化済み）
 * 
 * Application Serviceを通じてUser Aggregateを操作
 */
class ProfileController extends Controller
{
    private UpdateUserProfileService $updateProfileService;
    private UpdateUserPasswordService $updatePasswordService;
    private DeleteUserAccountService $deleteAccountService;

    public function __construct(
        UpdateUserProfileService $updateProfileService,
        UpdateUserPasswordService $updatePasswordService,
        DeleteUserAccountService $deleteAccountService
    ) {
        $this->updateProfileService = $updateProfileService;
        $this->updatePasswordService = $updatePasswordService;
        $this->deleteAccountService = $deleteAccountService;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $this->updateProfileService->handle(
                $request->user()->id,
                $request->validated('name'),
                $request->validated('email')
            );

            // メールアドレスが変更された場合は検証をリセット
            if ($request->user()->email !== $request->validated('email')) {
                $request->user()->email_verified_at = null;
                $request->user()->save();
            }

            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        } catch (RuntimeException $e) {
            return Redirect::route('profile.edit')
                ->withErrors(['email' => $e->getMessage()]);
        }
    }

    /**
     * Delete the user's account.
     * User削除時、所有するTodoも自動削除される（Cascade）
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $userId = $request->user()->id;

        try {
            // ログアウト処理
            Auth::logout();

            // User Aggregate削除（Todoも一緒に削除される）
            $this->deleteAccountService->handle($userId);

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/');
        } catch (RuntimeException $e) {
            return Redirect::route('profile.edit')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
