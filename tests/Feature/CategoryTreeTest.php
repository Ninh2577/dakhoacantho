<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategoryTreeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear category caches before each test
        Cache::forget('dakhoacantho:categories:tree_options');
    }

    public function test_get_tree_options_renders_correct_hierarchy_and_sorting(): void
    {
        // 1. Create top-level parent categories
        $parent1 = Category::create([
            'name' => 'Bệnh Xã Hội T',
            'slug' => 'benh-xa-hoi-t',
            'parent_id' => -1,
            'order' => 999, // use high order to avoid conflicting with seeds
        ]);

        $parent2 = Category::create([
            'name' => 'Nam Khoa T',
            'slug' => 'nam-khoa-t',
            'parent_id' => -1,
            'order' => 1000,
        ]);

        // 2. Create subcategories under Parent 1
        $child1_2 = Category::create([
            'name' => 'Chlamydia T',
            'slug' => 'chlamydia-t',
            'parent_id' => $parent1->id,
            'order' => 2,
        ]);

        $child1_1 = Category::create([
            'name' => 'Giang Mai T',
            'slug' => 'giang-mai-t',
            'parent_id' => $parent1->id,
            'order' => 1,
        ]);

        // 3. Create subcategories under Parent 2
        $child2_1 = Category::create([
            'name' => 'Bao Quy Đầu T',
            'slug' => 'bao-quy-dau-t',
            'parent_id' => $parent2->id,
            'order' => 1,
        ]);

        // Clear cache so it fetches fresh data
        Cache::forget('dakhoacantho:categories:tree_options');

        $options = Category::getTreeOptions();

        // Check if top parents exist
        $this->assertArrayHasKey($parent1->id, $options);
        $this->assertArrayHasKey($parent2->id, $options);

        // Check correct display labels
        $this->assertEquals('Bệnh Xã Hội T', $options[$parent1->id]);
        $this->assertEquals('— Giang Mai T', $options[$child1_1->id]);
        $this->assertEquals('— Chlamydia T', $options[$child1_2->id]);
        $this->assertEquals('Nam Khoa T', $options[$parent2->id]);
        $this->assertEquals('— Bao Quy Đầu T', $options[$child2_1->id]);

        // Clean up test data
        $child1_1->delete();
        $child1_2->delete();
        $child2_1->delete();
        $parent1->delete();
        $parent2->delete();
    }

    public function test_get_descendants_and_self_returns_full_hierarchy(): void
    {
        $parent = Category::create([
            'name' => 'Nam Khoa T',
            'slug' => 'nam-khoa-desc-t',
            'parent_id' => -1,
            'order' => 999,
        ]);

        $child = Category::create([
            'name' => 'Bao Quy Đầu T',
            'slug' => 'bao-quy-dau-desc-t',
            'parent_id' => $parent->id,
            'order' => 1,
        ]);

        $grandchild = Category::create([
            'name' => 'Cắt Bao Quy Đầu T',
            'slug' => 'cat-bao-quy-dau-desc-t',
            'parent_id' => $child->id,
            'order' => 1,
        ]);

        $descendants = Category::getDescendantIdsAndSelf($parent->id);

        $this->assertContains($parent->id, $descendants);
        $this->assertContains($child->id, $descendants);
        $this->assertContains($grandchild->id, $descendants);
        $this->assertCount(3, $descendants);

        // Clean up test data
        $grandchild->delete();
        $child->delete();
        $parent->delete();
    }
}
