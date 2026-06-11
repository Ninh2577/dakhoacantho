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
}
