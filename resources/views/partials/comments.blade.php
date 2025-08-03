@php
    // Get only the approved comments
    $args = array(
      'status' => 'approve',
      'post_id' => get_the_ID(),
    );

    // The comment Query
    $comments_query = new WP_Comment_Query();
    $comments       = $comments_query->query( $args );
@endphp

@if (! post_password_required())
  <section id="comments" class="comments py-4">
    @if ($closed)
      <x-alert type="warning">
        {!! __('Comments are closed.', 'sage') !!}
      </x-alert>
    @endif

    @if ($responses)
      <h2>
        {!! $title !!}
      </h2>

      <ol class="comment-list">
        {{-- {!! $responses !!} --}}
        @if ($comments)
            @foreach ($comments as $comment)
              @if ($comment->user_id == get_current_user_id())
                <div class="chat chat-sender">
                  <div class="chat-avatar avatar">
                    <div class="size-10 rounded-full">
                      <img src="{{get_avatar_url($comment->user_id)}}" alt="avatar" />
                    </div>
                  </div>
                  <div class="chat-header text-base-content/90">
                    {{ $comment->comment_author }}
                    <time class="text-base-content/50">{{ $comment->comment_date }}</time>
                  </div>
                  <div class="chat-bubble">{{ $comment->comment_content }}</div>
                  <div class="chat-footer text-base-content/50">
                    @if ($comment->comment_approved)
                      @php($icon = 'icon-[tabler--circle-check-filled]')
                      @php($msg = __('Approved', 'sage'))
                    @else
                      @php($icon = 'icon-[tabler--circle-dashed-check]')
                      @php($msg = __('Disapproved', 'sage'))
                    @endif
                    {{ $msg }}
                    <span class="{{ $icon }} text-success align-bottom"></span>
                  </div>
                </div>
              @else
                <div class="chat chat-receiver">
                  <div class="chat-avatar avatar">
                    <div class="size-10 rounded-full">
                      <img src="{{get_avatar_url($comment->user_id)}}" alt="avatar" />
                    </div>
                  </div>
                  <div class="chat-header text-base-content/90">
                    {{ $comment->comment_author }}
                    <time class="text-base-content/50">{{ $comment->comment_date }}</time>
                  </div>
                  <div class="chat-bubble">{{ $comment->comment_content }}</div>
                  {{-- <div class="chat-footer text-base-content/50">
                    <div>Delivered</div>
                  </div> --}}
                </div>
              @endif
              {{-- <p>{{$comment->comment_content}}</p> --}}
            @endforeach
        @else
            <p>{{ __('No comment found', 'sage') }}</p>
        @endif
      </ol>

      @if ($paginated)
        <nav aria-label="Comment">
          <ul class="pager">
            @if ($previous)
              <li class="previous">
                {!! $previous !!}
              </li>
            @endif

            @if ($next)
              <li class="next">
                {!! $next !!}
              </li>
            @endif
          </ul>
        </nav>
      @endif
    @endif

    @if ($closed)
      <x-alert type="warning">
        {!! __('Comments are closed.', 'sage') !!}
      </x-alert>
    @endif

    @php(comment_form($commentFormArgs))
  </section>
@endif
