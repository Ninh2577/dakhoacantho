<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\ArticleResource\Pages\CreateArticle;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admins_can_access_create_article_page()
    {
        $admin = User::factory()->create([
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
            ->assertDispatched('open-preview', url: url('/admin/articles/preview-create'));

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
            ->test(\App\Filament\Resources\ArticleResource\Pages\EditArticle::class, [
                'record' => $article->getKey(),
            ])
            ->fillForm([
                'title' => 'Updated Article for Preview',
                'content' => '<p>Updated preview content</p>',
            ])
            ->call('previewArticle')
            ->assertDispatched('open-preview', url: url('/admin/articles/preview-create'));

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
                ]
            ]);

        $response->assertStatus(200);
        $response->assertSee('Test POST Article Preview');
        
        $this->assertEquals('Test POST Article Preview', session('article_preview_create.title'));
    }
}


