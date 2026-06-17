<?php

namespace Tests\Feature;

use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\ArticleResource\Pages\CreateArticle;
use App\Filament\Resources\ArticleResource\Pages\EditArticle;
use App\Filament\Resources\ArticleResource\Pages\ListArticles;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admins_can_access_create_article_page()
    {
        $admin = User::factory()->create([
            'name' => 'Admin Writer',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->get(ArticleResource::getUrl('create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admins_can_create_article_via_filament_form()
    {
        $admin = User::factory()->create([
            'name' => 'Admin Writer',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
            'parent_id' => null,
            'order' => 0,
        ]);

        Livewire::actingAs($admin)
            ->test(CreateArticle::class)
            ->fillForm([
                'title' => 'Test Article',
                'slug' => 'test-article',
                'content' => '<h1>Hello World</h1><p>This is a test article.</p>',
                'category_id' => $category->id,
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'content' => '<h1>Hello World</h1><p>This is a test article.</p>',
            'category_id' => $category->id,
            'is_published' => 1,
            'author_id' => $admin->id,
        ]);
    }

    /** @test */
    public function admins_can_select_article_author_from_users()
    {
        $admin = User::factory()->create([
            'name' => 'Admin Writer',
            'role' => 'admin',
        ]);

        $doctor = User::factory()->create([
            'name' => 'Doctor Author',
            'email' => 'doctor@example.com',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
            'parent_id' => null,
            'order' => 0,
        ]);

        Livewire::actingAs($admin)
            ->test(CreateArticle::class)
            ->fillForm([
                'title' => 'Article With Selected Author',
                'slug' => 'article-with-selected-author',
                'content' => '<p>This is a test article.</p>',
                'category_id' => $category->id,
                'author_id' => $doctor->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('articles', [
            'title' => 'Article With Selected Author',
            'author_id' => $doctor->id,
        ]);
    }

    /** @test */
    public function admins_can_trigger_preview_create()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        Livewire::actingAs($admin)
            ->test(CreateArticle::class)
            ->fillForm([
                'title' => 'Test Article for Preview',
                'slug' => 'test-article-for-preview',
                'content' => '<p>Preview content</p>',
                'category_id' => $category->id,
            ])
            ->call('previewArticle')
            ->assertDispatched('open-preview');

        $this->assertEquals('Test Article for Preview', session('article_preview_create.title'));
        $this->assertEquals('<p>Preview content</p>', session('article_preview_create.content'));
    }

    /** @test */
    public function admins_can_trigger_preview_edit()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $article = Article::create([
            'title' => 'Existing Article',
            'slug' => 'existing-article',
            'content' => '<p>Existing content</p>',
            'category_id' => $category->id,
            'is_published' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(EditArticle::class, [
                'record' => $article->getKey(),
            ])
            ->fillForm([
                'title' => 'Updated Article for Preview',
                'content' => '<p>Updated preview content</p>',
            ])
            ->call('previewArticle')
            ->assertDispatched('open-preview');

        $this->assertEquals('Updated Article for Preview', session('article_preview_create.title'));
        $this->assertEquals('<p>Updated preview content</p>', session('article_preview_create.content'));
    }

    /** @test */
    public function admins_can_view_preview_page_with_array_session_data()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        // Mock session with array structures returned by Filament components
        session()->put('article_preview_create', [
            'title' => 'Test Article for Preview Page',
            'slug' => 'test-article-for-preview-page',
            'content' => '<p>Preview content page</p>',
            'excerpt' => 'Excerpt test',
            'featured_image' => ['articles/2026/06/file.jpg'], // Array format from FileUpload
            'category_id' => [$category->id], // Array format from select relationship
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'og_image' => ['articles/seo/og.jpg'], // Array format from FileUpload
            'twitter_image' => [], // Empty array from FileUpload
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/articles/preview-create');

        $response->assertStatus(200);
        $response->assertSee('Test Article for Preview Page');
    }

    /** @test */
    public function admins_can_post_data_to_preview_page()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $response = $this->actingAs($admin)
            ->post('/admin/articles/preview-create', [
                'data' => [
                    'title' => 'Test POST Article Preview',
                    'slug' => 'test-post-article-preview',
                    'content' => '<p>POST preview content page</p>',
                    'category_id' => $category->id,
                ],
            ]);

        $response->assertStatus(200);
        $response->assertSee('Test POST Article Preview');

        $this->assertEquals('Test POST Article Preview', session('article_preview_create.title'));
    }

    /** @test */
    public function migration_maps_existing_author_string_to_author_id()
    {
        $user = User::factory()->create([
            'name' => 'Doctor John',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Medicine',
            'slug' => 'medicine',
        ]);

        // Rollback the last migration
        $this->artisan('migrate:rollback', ['--step' => 1]);

        // Insert legacy article with author name string
        DB::table('articles')->insert([
            'title' => 'Legacy Article',
            'slug' => 'legacy-article',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author' => 'Doctor John',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Run migration up again
        $this->artisan('migrate');

        // Check if author_id is correctly mapped
        $this->assertDatabaseHas('articles', [
            'title' => 'Legacy Article',
            'author_id' => $user->id,
        ]);
    }

    /** @test */
    public function migration_sets_author_id_to_null_if_author_string_not_found()
    {
        $category = Category::create([
            'name' => 'Medicine',
            'slug' => 'medicine',
        ]);

        // Rollback the last migration
        $this->artisan('migrate:rollback', ['--step' => 1]);

        // Insert legacy article with unknown author name
        DB::table('articles')->insert([
            'title' => 'Legacy Article Unknown',
            'slug' => 'legacy-article-unknown',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author' => 'Unknown Person',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Run migration up again
        $this->artisan('migrate');

        // Check if author_id is set to null
        $this->assertDatabaseHas('articles', [
            'title' => 'Legacy Article Unknown',
            'author_id' => null,
        ]);
    }

    /** @test */
    public function article_form_automatically_assigns_auth_id_if_author_id_is_not_passed()
    {
        $admin = User::factory()->create([
            'name' => 'Admin Writer',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        Livewire::actingAs($admin)
            ->test(CreateArticle::class)
            ->fillForm([
                'title' => 'Test Article Auto Assign',
                'slug' => 'test-article-auto-assign',
                'content' => '<h1>Hello World</h1><p>This is a test article.</p>',
                'category_id' => $category->id,
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article Auto Assign',
            'author_id' => $admin->id,
        ]);
    }

    /** @test */
    public function article_preview_displays_author_name_not_id()
    {
        $admin = User::factory()->create([
            'name' => 'Admin Writer',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        Livewire::actingAs($admin)
            ->test(CreateArticle::class)
            ->fillForm([
                'title' => 'Test Preview Name',
                'slug' => 'test-preview-name',
                'content' => '<p>Content</p>',
                'category_id' => $category->id,
                'author_id' => $admin->id,
            ])
            ->call('previewArticle')
            ->assertDispatched('open-preview');

        // Retrieve the session preview data and query page
        $response = $this->actingAs($admin)
            ->get('/admin/articles/preview-create');

        $response->assertStatus(200);
        $response->assertSee('Tác giả: Admin Writer');
        $response->assertDontSee('Tác giả: '.$admin->id);
    }

    /** @test */
    public function frontend_displays_author_name_after_publishing()
    {
        $author = User::factory()->create([
            'name' => 'Dr. Jane Watson',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $article = Article::create([
            'title' => 'Published Article Watson',
            'slug' => 'published-article-watson',
            'content' => '<p>Article content by Jane.</p>',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'is_published' => true,
        ]);

        $response = $this->get('/'.$article->slug);

        $response->assertStatus(200);
        $response->assertSee('Tác giả: Dr. Jane Watson');
    }

    /** @test */
    public function admins_can_view_preview_page_via_cache_token()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $uuid = (string) Str::uuid();
        $payload = [
            'preview_uuid' => $uuid,
            'cached_auth_id' => $admin->id,
            'title' => 'Test Cache Preview Article',
            'slug' => 'test-cache-preview-article',
            'content' => '<p>Cache content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
        ];

        Cache::put("preview:{$uuid}", $payload, 60);

        $response = $this->actingAs($admin)
            ->get("/admin/articles/preview/{$uuid}");

        $response->assertStatus(200);
        $response->assertSee('Test Cache Preview Article');
        $this->assertNull(Cache::get("preview:{$uuid}")); // Pulled on read
    }

    /** @test */
    public function admins_cannot_view_expired_preview_page_via_cache_token()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/articles/preview/non-existent-uuid');

        $response->assertStatus(404);
        $response->assertSee('Bản xem trước hết hạn');
    }

    /** @test */
    public function admins_cannot_view_unauthorized_preview_page_via_cache_token()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $uuid = (string) Str::uuid();
        $payload = [
            'preview_uuid' => $uuid,
            'cached_auth_id' => 9999, // Mismatched owner
            'title' => 'Unauthorized Cache Preview Article',
            'slug' => 'unauthorized-cache-preview-article',
            'content' => '<p>Unauthorized cache content</p>',
            'category_id' => $category->id,
        ];

        Cache::put("preview:{$uuid}", $payload, 60);

        $response = $this->actingAs($admin)
            ->get("/admin/articles/preview/{$uuid}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admins_can_list_articles_and_use_new_columns()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $article = Article::create([
            'title' => 'Article One',
            'slug' => 'article-one',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'seo_score' => 0,
            'is_published' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->assertCanRenderTableColumn('id')
            ->assertTableColumnExists('slug')
            ->assertCanRenderTableColumn('created_at')
            ->assertTableColumnExists('updated_at')
            ->assertCanRenderTableColumn('seo_score')
            ->assertCanSeeTableRecords([$article]);
    }

    /** @test */
    public function admins_can_filter_articles_by_author()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $author = User::factory()->create([
            'name' => 'Specific Author',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $article1 = Article::create([
            'title' => 'Article One',
            'slug' => 'article-one',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'seo_score' => 0,
            'is_published' => true,
        ]);

        $article2 = Article::create([
            'title' => 'Article Two',
            'slug' => 'article-two',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'seo_score' => 85,
            'is_published' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->filterTable('author_id', $author->id)
            ->assertCanSeeTableRecords([$article2])
            ->assertCanNotSeeTableRecords([$article1]);
    }

    /** @test */
    public function admins_can_filter_articles_by_seo_score()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $article1 = Article::create([
            'title' => 'Article One',
            'slug' => 'article-one',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'seo_score' => 0,
            'is_published' => true,
        ]);

        $article2 = Article::create([
            'title' => 'Article Two',
            'slug' => 'article-two',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'seo_score' => 85,
            'is_published' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->filterTable('seo_filter', 'not_configured')
            ->assertCanSeeTableRecords([$article1])
            ->assertCanNotSeeTableRecords([$article2]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->filterTable('seo_filter', 'good')
            ->assertCanSeeTableRecords([$article2])
            ->assertCanNotSeeTableRecords([$article1]);
    }

    /** @test */
    public function admins_can_filter_articles_by_media()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $article1 = Article::create([
            'title' => 'Article One',
            'slug' => 'article-one',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'seo_score' => 0,
            'is_published' => true,
        ]);

        $article2 = Article::create([
            'title' => 'Article Two',
            'slug' => 'article-two',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'seo_score' => 85,
            'is_published' => false,
            'featured_image' => 'articles/featured/image.jpg',
        ]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->filterTable('has_featured_image', true)
            ->assertCanSeeTableRecords([$article2])
            ->assertCanNotSeeTableRecords([$article1]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->filterTable('has_featured_image', false)
            ->assertCanSeeTableRecords([$article1])
            ->assertCanNotSeeTableRecords([$article2]);
    }

    /** @test */
    public function admins_can_bulk_publish_articles()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $article1 = Article::create([
            'title' => 'Article One',
            'slug' => 'article-one',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'is_published' => false,
            'published_at' => null,
        ]);

        $article2 = Article::create([
            'title' => 'Article Two',
            'slug' => 'article-two',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'is_published' => false,
            'published_at' => null,
        ]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->callTableBulkAction('publish', [$article1, $article2])
            ->assertHasNoTableActionErrors();

        $this->assertTrue($article1->fresh()->is_published);
        $this->assertNotNull($article1->fresh()->published_at);
        $this->assertTrue($article2->fresh()->is_published);
        $this->assertNotNull($article2->fresh()->published_at);
    }

    /** @test */
    public function admins_can_bulk_draft_articles()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $article1 = Article::create([
            'title' => 'Article One',
            'slug' => 'article-one',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'is_published' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->callTableBulkAction('draft', [$article1])
            ->assertHasNoTableActionErrors();

        $this->assertFalse($article1->fresh()->is_published);
    }

    /** @test */
    public function admins_can_bulk_change_category()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category1 = Category::create([
            'name' => 'Medicine 1',
            'slug' => 'medicine-1',
        ]);

        $category2 = Category::create([
            'name' => 'Medicine 2',
            'slug' => 'medicine-2',
        ]);

        $article = Article::create([
            'title' => 'Article One',
            'slug' => 'article-one',
            'content' => '<p>Content</p>',
            'category_id' => $category1->id,
            'author_id' => $admin->id,
            'is_published' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->callTableBulkAction('changeCategory', [$article], [
                'category_id' => $category2->id,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertEquals($category2->id, $article->fresh()->category_id);
    }

    /** @test */
    public function admins_can_bulk_delete_articles()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $article = Article::create([
            'title' => 'Article One',
            'slug' => 'article-one',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'is_published' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ListArticles::class)
            ->callTableBulkAction('delete', [$article])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
    }

    /** @test */
    public function admins_can_unpublish_article_on_edit_page()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $article = Article::create([
            'title' => 'Article One',
            'slug' => 'article-one',
            'content' => '<p>Content</p>',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'is_published' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(EditArticle::class, [
                'record' => $article->getKey(),
            ])
            ->fillForm([
                'is_published' => false,
            ])
            ->callAction('publish')
            ->assertHasNoFormErrors();

        $this->assertFalse($article->fresh()->is_published);
    }

    /** @test */
    public function article_keeps_figure_and_figcaption_upon_creation_and_displays_them()
    {
        $admin = User::factory()->create([
            'name' => 'Admin Writer',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $captionedHtml = '<figure class="image"><img src="http://localhost/image.jpg" alt="test alt" /><figcaption>This is a test caption</figcaption></figure>';

        Livewire::actingAs($admin)
            ->test(CreateArticle::class)
            ->fillForm([
                'title' => 'Article with Caption',
                'slug' => 'article-with-caption',
                'content' => $captionedHtml,
                'category_id' => $category->id,
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('articles', [
            'title' => 'Article with Caption',
            'content' => $captionedHtml,
        ]);

        $response = $this->get('/article-with-caption');
        $response->assertStatus(200);
        $response->assertSee('<figure class="image">', false);
        $response->assertSee('<img src="http://localhost/image.jpg" alt="test alt" loading="lazy" decoding="async" />', false);
        $response->assertSee('This is a test caption');
    }

    /** @test */
    public function article_keeps_legacy_wordpress_caption_classes_and_renders_them()
    {
        $admin = User::factory()->create([
            'name' => 'Admin Writer',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $legacyHtml = '<div class="wp-caption"><img src="http://localhost/legacy.jpg" /><p class="wp-caption-text">Legacy caption text</p></div>';

        Livewire::actingAs($admin)
            ->test(CreateArticle::class)
            ->fillForm([
                'title' => 'Article with Legacy Caption',
                'slug' => 'article-with-legacy-caption',
                'content' => $legacyHtml,
                'category_id' => $category->id,
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('articles', [
            'title' => 'Article with Legacy Caption',
            'content' => $legacyHtml,
        ]);

        $response = $this->get('/article-with-legacy-caption');
        $response->assertStatus(200);
        $response->assertSee('<div class="wp-caption">', false);
        $response->assertSee('Legacy caption text');
    }

    /** @test */
    public function article_without_caption_renders_normally_as_img()
    {
        $admin = User::factory()->create([
            'name' => 'Admin Writer',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
        ]);

        $simpleHtml = '<p>Normal paragraph</p><img src="http://localhost/simple.jpg" alt="Simple" />';

        Livewire::actingAs($admin)
            ->test(CreateArticle::class)
            ->fillForm([
                'title' => 'Article with Simple Image',
                'slug' => 'article-with-simple-image',
                'content' => $simpleHtml,
                'category_id' => $category->id,
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $response = $this->get('/article-with-simple-image');
        $response->assertStatus(200);
        $response->assertSee('<img src="http://localhost/simple.jpg" alt="Simple" loading="lazy" decoding="async" />', false);
        $response->assertDontSee('<figcaption>');
    }
}
