@if (is_admin_bar_showing())
  @php($topFixed = '32px')
@else
  @php($topFixed = '0')
@endif

<header class="banner py-3 bg-violet-300 min-[601px]:fixed w-full z-50 top-[{{$topFixed}}]">
  <div class="container mx-auto flex items-center justify-between">
    <a class="brand text-violet-600 text-2xl" href="{{ home_url('/') }}">
      {!! $siteName !!}
    </a>
  
    <div class="flex items-center gap-4">
      @if (has_nav_menu('primary_navigation'))
        <nav class="nav-primary" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
          {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'echo' => false]) !!}
        </nav>
      @endif

      @if (is_user_logged_in())
        <div class="user-menu flex items-center gap-2">
          <span class="text-sm text-violet-700">
            Bonjour, {{ wp_get_current_user()->display_name }} !
          </span>
          <a href="{{ wp_logout_url() }}" class="btn btn-sm btn-outline btn-error">
            Se déconnecter
          </a>
        </div>
      @endif
    </div>
  </div>
</header>
