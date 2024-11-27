<ul class="timeline timeline-snap-icon max-md:timeline-compact timeline-vertical timeline-centered py-5">
    @foreach ($events as $event)
        @if ($loop->first)
        <!-- timeline item 1-->
        <li>
            <div class="timeline-middle h-16">
                <span class="bg-primary/20 flex size-8 items-center justify-center rounded-full">
                    <span class="{{ $event->event_icon }} text-primary size-5"></span>
                </span>
            </div>
            <div class="timeline-start me-4 mt-6 max-md:pt-2">
                <div class="text-base-content font-normal">{{ $event->event_date }}</div>
            </div>
            <div class="timeline-end ms-4 mb-8">
                <div class="card sm:max-w-sm bg-gray-200">
                    <figure>
                        {!! get_the_post_thumbnail($event->ID) !!}
                    </figure>
                    <div class="card-body">
                        <h5 class="card-title mb-2.5 text-primary">{{ $event->post_title }}</h5>
                        @if ($event->post_excerpt !== "")
                            <p class="mb-4">{{ $event->post_excerpt }}</p>
                        @endif
                        <div class="card-actions">
                            <a href="{{ $event->guid }}" class="timeline-end text-primary">
                                <div class="timeline-box bg-gray-100 border-primary">{!! __('See the photos', 'sage') !!}</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="bg-primary" />
        </li>
        <!-- /timeline item 1-->
        @elseif ($loop->last)
        <!-- timeline last item-->
        <li>
            <div class="timeline-middle h-16">
                <span class="bg-primary/20 flex size-8 items-center justify-center rounded-full">
                <span class="{{ $event->event_icon }} text-primary size-5"></span>
                </span>
            </div>
            <div class="timeline-start me-4 mt-6 max-md:pt-2">
                <div class="text-base-content font-normal">{{ $event->event_date }}</div>
            </div>
            <div class="timeline-end ms-4">
                <div class="card sm:max-w-sm bg-gray-200">
                    <figure>
                        {!! get_the_post_thumbnail($event->ID) !!}
                    </figure>
                    <div class="card-body">
                        <h5 class="card-title mb-2.5 text-primary">{{ $event->post_title }}</h5>
                        @if ($event->post_excerpt !== "")
                            <p class="mb-4">{{ $event->post_excerpt }}</p>
                        @endif
                        <div class="card-actions">
                            <a href="{{ $event->guid }}" class="timeline-end text-primary">
                                <div class="timeline-box bg-gray-100 border-primary">{!! __('See the photos', 'sage') !!}</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="bg-primary" />
        </li>
        <!-- /timeline last item-->
        @else
        <!-- timeline item 2-->
        <li class="timeline-shift">
            <div class="timeline-middle h-16">
                <span class="bg-primary/20 flex size-8 items-center justify-center rounded-full">
                    <span class="{{ $event->event_icon }} text-primary size-5"></span>
                </span>
            </div>
            <div class="timeline-end mt-6 px-1">
                <div class="text-base-content font-normal">{{ $event->event_date }}</div>
            </div>
            <div class="timeline-start me-4 mb-8">
                <div class="card sm:max-w-sm bg-gray-200">
                    <figure>
                        {!! get_the_post_thumbnail($event->ID) !!}
                    </figure>
                    <div class="card-body">
                        <h5 class="card-title mb-2.5 text-primary">{{ $event->post_title }}</h5>
                        @if ($event->post_excerpt !== "")
                            <p class="mb-4">{{ $event->post_excerpt }}</p>
                        @endif
                        <div class="card-actions">
                            <a href="{{ $event->guid }}" class="timeline-end text-primary">
                                <div class="timeline-box bg-gray-100 border-primary">{!! __('See the photos', 'sage') !!}</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="bg-primary" />
        </li>
        <!-- /timeline item 2-->
        @endif
    @endforeach
</ul>

<div class="w-1/2 lg:w-1/4 mx-auto py-4">
    <img src="@asset('images/cigogne.webp')" alt="" class="rounded-full">
</div>