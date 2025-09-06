<!doctype html>
<html @php(language_attributes())>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php(do_action('get_header'))
    @php(wp_head())
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </head>

  <body @php(body_class('bg-gray-100 text-gray-800 min-h-screen'))>
    @php(wp_body_open())

    <div id="app">
      <a class="sr-only focus:not-sr-only" href="#main">
        {{ __('Skip to content') }}
      </a>

      @include('sections.header')

      @if (is_admin_bar_showing())
        @php($topPaddingMain = 'pt-8 sm:pt-20')
      @else
        @php($topPaddingMain = 'sm:pt-[26px]')
      @endif
      <main id="main" class="main container mx-auto flex flex-col justify-center {{ $topPaddingMain }} pb-56 md:pb-32">
        @yield('content')
      </main>

      @hasSection('sidebar')
        <aside class="sidebar">
          @yield('sidebar')
        </aside>
      @endif

      @include('sections.footer')
    </div>

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
