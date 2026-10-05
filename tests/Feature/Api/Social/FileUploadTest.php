<?php

namespace Tests\Feature\Api\Social;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadTest extends SocialTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('s3');
    }

    public function test_upload_image_stores_file_and_returns_url(): void
    {
        $this->actingAsUser();

        $response = $this->post('/api/upload-image', [
            'image' => UploadedFile::fake()->image('photo.jpg'),
            'dir' => 'statuses',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['status', 'url'])
            ->assertJson(['status' => true]);

        $this->assertNotEmpty($response->json('url'));
    }

    public function test_upload_video_stores_file_and_returns_url(): void
    {
        $this->actingAsUser();

        $response = $this->post('/api/upload-video', [
            'video' => UploadedFile::fake()->create('clip.mp4', 500, 'video/mp4'),
            'dir' => 'videos',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['status', 'url'])
            ->assertJson(['status' => true]);

        $this->assertNotEmpty($response->json('url'));
    }

    public function test_upload_file_stores_archive_and_returns_url(): void
    {
        $this->actingAsUser();

        $response = $this->post('/api/upload-file', [
            'file' => UploadedFile::fake()->create('archive.zip', 200, 'application/zip'),
            'dir' => 'files',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['status', 'url'])
            ->assertJson(['status' => true]);

        $this->assertNotEmpty($response->json('url'));
    }

    public function test_upload_image_requires_image_field(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/upload-image', [])
            ->assertStatus(400)
            ->assertJson(['status' => 0]);
    }
}
