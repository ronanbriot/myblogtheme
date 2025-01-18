<article @php(post_class('h-entry'))>
  <header>
    <h1 class="p-name text-4xl">
      {!! $title !!} {{ __('on', 'sage') }} {{ $eventDate }}
    </h1>

    @include('partials.entry-meta')
  </header>

  <div class="e-content">
    @if ($eventSlideshowUrl === 'placeholder')
      <div class="radial-progress bg-primary text-primary-content border-primary border-4" style="--value:70;" role="progressbar" aria-label="Primary Radial Progressbar">70%</div>
    @else
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
