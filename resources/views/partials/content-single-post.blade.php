<article @php(post_class('h-entry'))>
  <header>
    <h1 class="p-name text-4xl">
      {!! $title !!} {{ __('on', 'sage') }} {{ $eventDate }}
    </h1>

    @include('partials.entry-meta')
  </header>

  <div class="e-content">
    @if ($eventSlideshowUrl)
      <video src="{{ $eventSlideshowUrl }}" class="py-4 mx-auto rounded" controls></video>
    @endif
    @php(the_content())
  </div>

  @if ($pagination)
    <footer>
      <nav class="page-nav" aria-label="Page">
        {!! $pagination !!}
      </nav>
    </footer>
  @endif

  @php(comments_template())
</article>
