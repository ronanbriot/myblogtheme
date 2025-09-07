<?php

namespace Tests\Unit\View\Composers;

use App\View\Composers\TimelineComposer;
use WP_Post;
use WP_UnitTestCase;

/**
 * Test de la classe TimelineComposer
 *
 * @phpstan-ignore-next-line
 *
 * @psalm-suppress UndefinedMethod
 */
class TimelineComposerTest extends WP_UnitTestCase
{
    /**
     * Instance de TimelineComposer
     */
    private $timelineComposer;

    /**
     * Posts de test créés
     */
    private $testPosts = [];

    /**
     * Configuration initiale pour chaque test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Créer une instance de TimelineComposer
        $this->timelineComposer = new TimelineComposer;

        // Créer des posts de test avec différentes catégories
        $this->createTestPosts();
    }

    /**
     * Nettoyage après chaque test
     */
    protected function tearDown(): void
    {
        // Supprimer les posts de test
        foreach ($this->testPosts as $postId) {
            wp_delete_post($postId, true);
        }

        // Réinitialiser les superglobales
        $_GET = [];
        $_POST = [];

        parent::tearDown();
    }

    /**
     * Créer des posts de test avec différentes catégories
     */
    private function createTestPosts()
    {
        // Créer des catégories de test si elles n'existent pas
        $category1 = wp_create_category('Événement général');
        $category2 = wp_create_category('Anniversaire');
        $category3 = wp_create_category('Noël');

        // Créer plusieurs posts de test
        $post1 = $this->factory->post->create([
            'post_title' => 'Test Post 1',
            'post_content' => 'Contenu du test 1',
            'post_status' => 'publish',
            'post_date' => '2024-01-15 10:30:00',
            'post_category' => [$category1],
        ]);

        $post2 = $this->factory->post->create([
            'post_title' => 'Test Post 2 - Anniversaire',
            'post_content' => 'Contenu du test 2',
            'post_status' => 'publish',
            'post_date' => '2024-01-14 14:20:00',
            'post_category' => [$category2],
        ]);

        $post3 = $this->factory->post->create([
            'post_title' => 'Test Post 3 - Noël',
            'post_content' => 'Contenu du test 3',
            'post_status' => 'publish',
            'post_date' => '2024-01-13 09:15:00',
            'post_category' => [$category3],
        ]);

        $this->testPosts = [$post1, $post2, $post3];
    }

    /**
     * Test de la méthode with()
     */
    public function test_with_method()
    {
        $result = $this->timelineComposer->with();

        // Vérifier que le résultat contient les clés attendues
        $this->assertIsArray($result);
        $this->assertArrayHasKey('events', $result);
        $this->assertArrayHasKey('current_page', $result);
        $this->assertArrayHasKey('max_pages', $result);

        // Vérifier les types de données
        $this->assertIsArray($result['events']);
        $this->assertIsInt($result['current_page']);
        $this->assertIsInt($result['max_pages']);

        // Vérifier que current_page est 1 par défaut
        $this->assertEquals(1, $result['current_page']);
    }

    /**
     * Test de la méthode events() avec pagination
     */
    public function test_events_method()
    {
        // Test avec page par défaut
        $result = $this->timelineComposer->events();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('events', $result);
        $this->assertArrayHasKey('max_pages', $result);
        $this->assertArrayHasKey('current_page', $result);

        // Vérifier qu'on a des événements
        $this->assertGreaterThan(0, count($result['events']));

        // Vérifier la structure des événements
        if (! empty($result['events'])) {
            $event = $result['events'][0];
            $this->assertInstanceOf(WP_Post::class, $event);
            $this->assertObjectHasProperty('event_date', $event);
            $this->assertObjectHasProperty('post_thumbnail', $event);
            $this->assertObjectHasProperty('event_icon', $event);
        }
    }

    /**
     * Test de la méthode events() avec paramètre de page
     */
    public function test_events_method_with_page_parameter()
    {
        // Simuler un paramètre de page
        $_GET['page'] = 2;

        $result = $this->timelineComposer->events();

        $this->assertEquals(2, $result['current_page']);
    }

    /**
     * Test du formatage des dates
     */
    public function test_date_formatting()
    {
        $result = $this->timelineComposer->events();

        if (! empty($result['events'])) {
            $event = $result['events'][0];

            // Vérifier que la date est formatée en français
            $this->assertStringContainsString('à', $event->event_date);
            $this->assertStringContainsString('h', $event->event_date);

            // Vérifier que c'est une date valide
            $this->assertNotEmpty($event->event_date);
        }
    }

    /**
     * Test des icônes selon les catégories
     */
    public function test_event_icons_by_category()
    {
        $result = $this->timelineComposer->events();

        foreach ($result['events'] as $event) {
            $this->assertObjectHasProperty('event_icon', $event);
            $this->assertIsString($event->event_icon);

            // Vérifier que l'icône commence par 'icon-[tabler--'
            $this->assertStringStartsWith('icon-[tabler--', $event->event_icon);
        }
    }

    /**
     * Test de la méthode loadMore() avec données POST
     */
    public function test_load_more_method()
    {
        // Simuler des données POST
        $_POST['page'] = 2;
        $_POST['exclude_ids'] = implode(',', [$this->testPosts[0]]);

        // En mode test, la méthode retourne directement les données
        $result = $this->timelineComposer->loadMore();

        // Vérifier la structure des données retournées
        $this->assertIsArray($result);
        $this->assertArrayHasKey('events', $result);
        $this->assertArrayHasKey('max_pages', $result);
        $this->assertArrayHasKey('current_page', $result);

        // Vérifier les types de données
        $this->assertIsArray($result['events']);
        $this->assertIsInt($result['max_pages']);
        $this->assertIsInt($result['current_page']);

        // Vérifier que la page courante est correcte
        $this->assertEquals(2, $result['current_page']);

        // Vérifier qu'on a des événements (excluant le premier post)
        $this->assertGreaterThan(0, count($result['events']));
    }

    /**
     * Test de la méthode loadMore() sans paramètres
     */
    public function test_load_more_method_without_parameters()
    {
        // Réinitialiser $_POST
        $_POST = [];

        // En mode test, la méthode retourne directement les données
        $result = $this->timelineComposer->loadMore();

        // Vérifier la structure des données retournées
        $this->assertIsArray($result);
        $this->assertArrayHasKey('events', $result);
        $this->assertArrayHasKey('max_pages', $result);
        $this->assertArrayHasKey('current_page', $result);

        // Vérifier que la page par défaut est 1
        $this->assertEquals(1, $result['current_page']);

        // Vérifier qu'on a des événements
        $this->assertGreaterThan(0, count($result['events']));
    }

    /**
     * Test de la pagination avec posts_per_page
     */
    public function test_pagination_with_posts_per_page()
    {
        // Créer plus de posts pour tester la pagination
        for ($i = 0; $i < 15; $i++) {
            $postId = $this->factory->post->create([
                'post_title' => "Test Post Pagination {$i}",
                'post_content' => "Contenu du test pagination {$i}",
                'post_status' => 'publish',
                'post_date' => date('Y-m-d H:i:s', strtotime("-{$i} days")),
            ]);
            $this->testPosts[] = $postId;
        }

        $result = $this->timelineComposer->events();

        // Vérifier qu'on a bien 10 posts par page (posts_per_page = 10)
        $this->assertLessThanOrEqual(10, count($result['events']));

        // Vérifier que max_pages est calculé correctement
        $this->assertGreaterThan(1, $result['max_pages']);
    }

    /**
     * Test de l'ordre des posts (DESC par date)
     */
    public function test_posts_order_by_date()
    {
        $result = $this->timelineComposer->events();

        if (count($result['events']) > 1) {
            $firstPost = $result['events'][0];
            $secondPost = $result['events'][1];

            // Vérifier que les posts sont triés par date décroissante
            $this->assertGreaterThanOrEqual(
                strtotime($secondPost->post_date),
                strtotime($firstPost->post_date)
            );
        }
    }

    /**
     * Test de la propriété statique $views
     */
    public function test_views_property()
    {
        $reflection = new \ReflectionClass($this->timelineComposer);
        $viewsProperty = $reflection->getProperty('views');
        $viewsProperty->setAccessible(true);
        $views = $viewsProperty->getValue($this->timelineComposer);

        $this->assertIsArray($views);
        $this->assertContains('components.timeline', $views);
    }
}
