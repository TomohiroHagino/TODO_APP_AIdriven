@extends('layouts.main')

@section('content')
<div class="page-header">
    <h2 class="page-header__title">プロフィール</h2>
</div>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        @include('profile.partials.update-password-form')
    </div>

    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
