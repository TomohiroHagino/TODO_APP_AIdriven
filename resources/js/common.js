/**
 * 共通JavaScript
 * 
 * カスタムレイアウト（BEM）で使用する共通のJavaScript
 */

// ハンバーガーメニューのトグル
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const headerNav = document.getElementById('headerNav');

    if (menuToggle && headerNav) {
        menuToggle.addEventListener('click', function(event) {
            event.stopPropagation(); // イベントの伝播を止める
            
            headerNav.classList.toggle('header__nav--open');
            menuToggle.classList.toggle('header__menu-toggle--open');
            
            // アクセシビリティ
            const isOpen = headerNav.classList.contains('header__nav--open');
            menuToggle.setAttribute('aria-label', isOpen ? 'メニューを閉じる' : 'メニューを開く');
        });

        // メニュー外をクリックしたら閉じる
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.header__container')) {
                headerNav.classList.remove('header__nav--open');
                menuToggle.classList.remove('header__menu-toggle--open');
                menuToggle.setAttribute('aria-label', 'メニューを開く');
            }
        });
    }
});

