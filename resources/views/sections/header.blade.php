@if (is_admin_bar_showing())
  @php($topFixed = '32px')
@else
  @php($topFixed = '0')
@endif

<header class="banner py-3 bg-violet-300 min-[601px]:fixed w-full z-50 top-[{{$topFixed}}]">
  <div class="container mx-auto">
    <a class="brand text-violet-600 text-2xl" href="{{ home_url('/') }}">
      {!! $siteName !!}
    </a>
  
    @if (has_nav_menu('primary_navigation'))
      <nav class="nav-primary" aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'echo' => false]) !!}
      </nav>
    @endif
  </div>
</header>
