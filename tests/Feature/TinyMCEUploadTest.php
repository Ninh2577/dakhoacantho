<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TinyMCEUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_upload_images()
    {
        $response = $this->postJson(route('admin.tinymce.upload-image'), [
            'image' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function non_admin_users_cannot_upload_images()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('admin.tinymce.upload-image'), [
                'image' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        $response->assertStatus(403);
        $response->assertJsonFragment(['error' => 'Unauthorized access.']);
    }

    /** @test */
    public function admins_can_upload_valid_images()
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.tinymce.upload-image'), [
                'image' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['location']);

        $location = $response->json('location');
        $this->assertNotEmpty($location);

        // Verify the path is stored on the public disk
        $parsedUrl = parse_url($location);
        $path = $parsedUrl['path'] ?? '';
        // Extract path after /storage/
        if (preg_match('/\/storage\/(.+)$/', $path, $matches)) {
            $storagePath = $matches[1];
            Storage::disk('public')->assertExists($storagePath);
        } else {
            $this->fail("Could not extract relative storage path from location: " . $location);
        }
    }

    /** @test */
    public function admins_cannot_upload_invalid_files()
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Uploading a text file instead of image
        $response = $this->actingAs($admin)
            ->postJson(route('admin.tinymce.upload-image'), [
                'image' => UploadedFile::fake()->create('document.txt', 100),
            ]);

        $response->assertStatus(400);
        $response->assertJsonStructure(['error']);
    }
}
