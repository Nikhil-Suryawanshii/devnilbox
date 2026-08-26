<?php

namespace Tests\Feature;

use Tests\TestCase;

class DefaultPlaceholderImageTest extends TestCase
{
    public function test_default_profile_jpg_returns_jpeg_200(): void
    {
        $response = $this->get('/default/profile.jpg');

        $response->assertOk();
        $ct = $response->headers->get('Content-Type');
        $this->assertNotNull($ct);
        $this->assertStringContainsString('image/jpeg', strtolower($ct));
        $body = $response->getContent();
        $this->assertNotEmpty($body);
        $this->assertSame("\xFF\xD8", substr($body, 0, 2), 'Body should start with JPEG SOI marker');
    }

    public function test_default_default_jpg_returns_jpeg_200(): void
    {
        $response = $this->get('/default/default.jpg');

        $response->assertOk();
        $ct = $response->headers->get('Content-Type');
        $this->assertNotNull($ct);
        $this->assertStringContainsString('image/jpeg', strtolower($ct));
    }

    public function test_default_unknown_file_returns_404(): void
    {
        $this->get('/default/other.jpg')->assertNotFound();
    }
}
