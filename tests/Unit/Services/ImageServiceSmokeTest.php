<?php

namespace Tests\Unit\Services;

use App\Services\Image\ImageService;
use PHPUnit\Framework\TestCase;

class ImageServiceSmokeTest extends TestCase
{
    public function test_image_service_class_exists(): void
    {
        $this->assertTrue(class_exists(ImageService::class));
    }
}
