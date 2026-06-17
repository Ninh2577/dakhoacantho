<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class InternalLinkSearchTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear search caches before each test run
        Cache::flush();

        // Create a default category to satisfy the NOT NULL constraint on articles.category_id
        $this->category = Category::create([
            'name' => 'Default Category',
            'slug' => 'default-category',
            'parent_id' => -1,
        ]);
    }

    /** @test */
    public function guests_are_unauthorized()
    {
        $response = $this->getJson(route('admin.internal-links.search'));

        $response->assertStatus(401);
    }

    /** @test */
    public function non_admin_users_are_forbidden()
    {
        $user = User::factory()->create([
            'role' => 'editor',
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('admin.internal-links.search'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admins_can_search_internal_links()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create published article
        $article = Article::create([
            'title' => 'Những điều cần lưu ý sau phá thai',
            'slug' => 'nhung-dieu-can-luu-y-sau-pha-thai',
            'content' => 'Lorem ipsum',
            'category_id' => $this->category->id,
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search').'?q=pha%20thai');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'type', 'title', 'slug', 'url', 'absolute_url'],
        ]);

        $this->assertCount(1, $response->json());
        $this->assertEquals('article', $response->json('0.type'));
        $this->assertEquals($article->title, $response->json('0.title'));
        $this->assertStringContainsString('/nhung-dieu-can-luu-y-sau-pha-thai', $response->json('0.url'));
    }

    /** @test */
    public function searches_with_accents_and_without_yields_same_results()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Article::create([
            'title' => 'Phương pháp điều trị bệnh giang mai',
            'slug' => 'phuong-phap-dieu-tri-benh-giang-mai',
            'content' => 'Lorem ipsum',
            'category_id' => $this->category->id,
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        // 1. Search with accents
        $response1 = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search').'?q=gi%E1%BA%A3ng%20m%C3%A0i'); // "giảng mài" which slugifies to "giang-mai"

        // 2. Search without accents / slug format
        $response2 = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search').'?q=giang-mai');

        $this->assertCount(1, $response1->json());
        $this->assertCount(1, $response2->json());
        $this->assertEquals($response1->json('0.id'), $response2->json('0.id'));
    }

    /** @test */
    public function draft_and_scheduled_articles_are_excluded()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Draft
        Article::create([
            'title' => 'Bài viết nháp',
            'slug' => 'bai-viet-nhap',
            'content' => 'Lorem ipsum',
            'category_id' => $this->category->id,
            'is_published' => false,
            'published_at' => now()->subHour(),
        ]);

        // Scheduled in future
        Article::create([
            'title' => 'Bài viết lên lịch',
            'slug' => 'bai-viet-len-lich',
            'content' => 'Lorem ipsum',
            'category_id' => $this->category->id,
            'is_published' => true,
            'published_at' => now()->addHour(),
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search').'?q=bai-viet');

        $this->assertCount(0, $response->json());
    }

    /** @test */
    public function exclude_id_parameter_removes_current_article()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $article1 = Article::create([
            'title' => 'Bài viết số một',
            'slug' => 'bai-viet-so-mot',
            'content' => 'Lorem ipsum',
            'category_id' => $this->category->id,
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        $article2 = Article::create([
            'title' => 'Bài viết số hai',
            'slug' => 'bai-viet-so-hai',
            'content' => 'Lorem ipsum',
            'category_id' => $this->category->id,
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search')."?q=bai-viet&exclude_id={$article1->id}");

        $this->assertCount(1, $response->json());
        $this->assertEquals($article2->id, $response->json('0.id'));
    }

    /** @test */
    public function search_results_are_cached()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $article = Article::create([
            'title' => 'Khám nam khoa uy tín',
            'slug' => 'kham-nam-khoa-uy-tin',
            'content' => 'Lorem ipsum',
            'category_id' => $this->category->id,
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        // First query (populates cache)
        $response1 = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search').'?q=nam-khoa');
        $this->assertCount(1, $response1->json());

        // Delete article from database directly (without triggering model events, so cache isn't cleared)
        Article::where('id', $article->id)->delete();

        // Second query (should hit cache and return the deleted article)
        $response2 = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search').'?q=nam-khoa');
        $this->assertCount(1, $response2->json());
        $this->assertEquals($article->title, $response2->json('0.title'));

        // Clear cache
        Cache::flush();

        // Third query (cache cleared, should return empty)
        $response3 = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search').'?q=nam-khoa');
        $this->assertCount(0, $response3->json());
    }

    /** @test */
    public function empty_query_returns_10_latest_articles()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        for ($i = 1; $i <= 15; $i++) {
            Article::create([
                'title' => "Bài viết thứ {$i}",
                'slug' => "bai-viet-thu-{$i}",
                'content' => 'Lorem ipsum',
                'category_id' => $this->category->id,
                'is_published' => true,
                'published_at' => now()->subHours($i),
            ]);
        }

        $response = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search'));

        $response->assertStatus(200);
        $this->assertCount(10, $response->json());
        // Verify they are ordered by published_at desc (Bài viết thứ 1 is the newest since it has subHours(1))
        $this->assertEquals('Bài viết thứ 1', $response->json('0.title'));
    }

    /** @test */
    public function articles_with_null_published_at_are_included_in_search()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $article = Article::create([
            'title' => 'Bài viết không có ngày xuất bản',
            'slug' => 'bai-viet-khong-co-ngay-xuat-ban',
            'content' => 'Lorem ipsum',
            'category_id' => $this->category->id,
            'is_published' => true,
            'published_at' => null,
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search').'?q=khong-co-ngay');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
        $this->assertEquals($article->title, $response->json('0.title'));
    }

    /** @test */
    public function empty_query_returns_recent_articles_even_with_null_published_at()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            Article::create([
                'title' => "Bài viết thứ {$i}",
                'slug' => "bai-viet-thu-{$i}",
                'content' => 'Lorem ipsum',
                'category_id' => $this->category->id,
                'is_published' => true,
                'published_at' => null,
            ]);
        }

        $response = $this->actingAs($admin)
            ->getJson(route('admin.internal-links.search'));

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }
}
