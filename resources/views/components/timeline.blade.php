@props(['events', 'max_pages', 'current_page'])

<script>
window.timelineData = function() {
    return {
        events: @json($events),
        currentPage: {{ $current_page }},
        maxPages: {{ $max_pages }},
        loading: false,
        hasMorePages: {{ $current_page < $max_pages ? 'true' : 'false' }},
        
        init() {
            // Ajouter l'event listener pour le scroll
            window.addEventListener('scroll', this.handleScroll.bind(this));
        },
        
        handleScroll() {
            // Détecter le scroll au niveau de l'image cigogne
            const cigogneImg = document.querySelector('img[src*="cigogne"]');
            if (!cigogneImg) return;
            
            const cigogneRect = cigogneImg.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            
            // Se déclencher quand l'image cigogne devient visible (avec une marge de 100px)
            const threshold = 100;
            if (cigogneRect.top <= windowHeight + threshold) {
                this.loadMore();
            }
        },
        
        async loadMore() {
            if (this.loading || !this.hasMorePages) {
                return;
            }
            
            this.loading = true;
            const nextPage = this.currentPage + 1;

            try {
                const response = await fetch('{{ admin_url('admin-ajax.php') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'load_more_events',
                        page: nextPage,
                        nonce: '{{ wp_create_nonce("load_more_events") }}',
                        timestamp: Date.now(), // Éviter le cache
                        exclude_ids: this.events.map(e => e.ID).join(',') // Exclure les IDs déjà chargés
                    })
                });

                const data = await response.json();
                
                if (data.success && data.data.events && data.data.events.length > 0) {
                    // Créer un Set des IDs existants pour éviter les doublons
                    const existingIds = new Set(this.events.map(event => event.ID));
                    
                    // Filtrer les nouveaux événements pour éviter les doublons
                    const newUniqueEvents = data.data.events.filter(newEvent => {
                        return !existingIds.has(newEvent.ID);
                    });
                    
                    // Ajouter les nouveaux événements uniques
                    newUniqueEvents.forEach(newEvent => {
                        this.events.push(newEvent);
                    });
                    
                    this.currentPage = data.data.current_page;
                    
                    // Si on a reçu moins d'événements que prévu, c'est qu'il n'y en a plus
                    const expectedEvents = 10;
                    const actualEvents = newUniqueEvents.length;
                    const hasMoreBasedOnCount = actualEvents >= expectedEvents;
                    const hasMoreBasedOnServer = this.currentPage < data.data.max_pages;
                    
                    // On continue seulement si les deux conditions sont vraies
                    this.hasMorePages = hasMoreBasedOnCount && hasMoreBasedOnServer;
                } else {
                    // Aucun événement reçu - on a atteint la fin
                    this.hasMorePages = false;
                    if (data.success === false) {
                        console.error('Erreur côté serveur:', data.data);
                    }
                }
            } catch (error) {
                console.error('Erreur lors du chargement des événements:', error);
            } finally {
                this.loading = false;
            }
        },
        
        destroy() {
            // Nettoyer l'event listener quand le composant est détruit
            window.removeEventListener('scroll', this.handleScroll);
        }
    }
};
</script>

<div x-data="timelineData()">
    <ul class="timeline timeline-snap-icon max-md:timeline-compact timeline-vertical timeline-centered py-5">
        <template x-for="(event, index) in events" :key="`event-${event.ID}-${index}`">
            <li :class="{ 'timeline-shift': index % 2 === 1 }">
                <div class="timeline-middle h-16">
                    <span class="bg-primary/20 flex size-8 items-center justify-center rounded-full">
                        <span :class="event.event_icon" class="text-primary size-5"></span>
                    </span>
                </div>
                
                <!-- Date à gauche pour les éléments pairs, à droite pour les impairs -->
                <div x-show="index % 2 === 0" class="timeline-start me-4 mt-6 max-md:pt-2">
                    <div class="text-base-content font-normal" x-text="event.event_date"></div>
                </div>
                <div x-show="index % 2 === 1" class="timeline-end mt-6 px-1">
                    <div class="text-base-content font-normal" x-text="event.event_date"></div>
                </div>
                
                <!-- Contenu à droite pour les éléments pairs, à gauche pour les impairs -->
                <div x-show="index % 2 === 0" class="timeline-end ms-4 mb-8">
                    <div class="card sm:max-w-sm bg-gray-200">
                        <figure x-html="event.post_thumbnail"></figure>
                        <div class="card-body">
                            <h5 class="card-title mb-2.5 text-primary" x-text="event.post_title"></h5>
                            <p class="mb-4" x-show="event.post_excerpt" x-text="event.post_excerpt"></p>
                            <div class="card-actions">
                                <a :href="event.guid" class="timeline-end text-primary">
                                    <div class="timeline-box bg-gray-100 border-primary">{!! __('See the photos', 'sage') !!}</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div x-show="index % 2 === 1" class="timeline-start me-4 mb-8">
                    <div class="card sm:max-w-sm bg-gray-200">
                        <figure x-html="event.post_thumbnail"></figure>
                        <div class="card-body">
                            <h5 class="card-title mb-2.5 text-primary" x-text="event.post_title"></h5>
                            <p class="mb-4" x-show="event.post_excerpt" x-text="event.post_excerpt"></p>
                            <div class="card-actions">
                                <a :href="event.guid" class="timeline-end text-primary">
                                    <div class="timeline-box bg-gray-100 border-primary">{!! __('See the photos', 'sage') !!}</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="bg-primary" />
            </li>
        </template>
    </ul>

    <!-- Overlay de chargement -->
    <div x-show="loading" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 shadow-xl flex flex-col items-center space-y-4">
            <div class="loading loading-spinner loading-lg text-primary"></div>
            <p class="text-base-content font-medium">Chargement de nouveaux événements...</p>
        </div>
    </div>

    <!-- Indicateur de chargement discret en bas de page -->
    <div x-show="loading" class="text-center py-8">
        <div class="flex items-center justify-center space-x-3">
            <div class="loading loading-spinner loading-md text-primary"></div>
            <span class="text-base-content">Nouveaux événements en cours de chargement...</span>
        </div>
    </div>

    <!-- Message de fin si plus de pages -->
    <div x-show="!hasMorePages && !loading" class="text-center py-8">
        <div class="flex items-center justify-center space-x-2">
            <p class="text-base-content opacity-70">Tous les événements ont été chargés</p>
        </div>
    </div>


</div>

<div class="w-1/2 lg:w-1/4 mx-auto py-4">
    <img src="@asset('images/cigogne.webp')" alt="" class="rounded-full">
</div>