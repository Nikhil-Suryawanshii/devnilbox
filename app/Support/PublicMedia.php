<?php

namespace App\Support;

class PublicMedia
{
    /** Minimal valid JPEG used when GD is unavailable. */
    private const PLACEHOLDER_JPEG = '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCdABmX/9k=';

    public static function exists(?string $src): bool
    {
        $src = self::normalizeSrc($src);

        if (! $src) {
            return false;
        }

        return is_file(storage_path('app/public/'.$src));
    }

    public static function url(?string $src, ?string $default = 'default/default.jpg'): string
    {
        self::ensureDefaultImages();

        $src = self::normalizeSrc($src);

        if ($src && self::exists($src)) {
            return self::cleanUrl(self::assetUrl('storage/'.$src));
        }

        return self::cleanUrl(self::assetUrl($default ?? 'default/default.jpg'));
    }

    /** Remove spaces/newlines so URLs are never split (logs or bad DB data). */
    public static function cleanUrl(string $url): string
    {
        return preg_replace('/\s+/', '', $url) ?? $url;
    }

    /**
     * Strip full URLs down to the storage-relative path (e.g. products/foo.png).
     */
    public static function normalizeSrc(?string $src): ?string
    {
        if (! $src) {
            return null;
        }

        $src = preg_replace('/\s+/', '', $src) ?? $src;

        if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
            $path = parse_url($src, PHP_URL_PATH) ?: '';

            if (preg_match('#/storage/(.+)$#', $path, $matches)) {
                return ltrim($matches[1], '/');
            }

            return null;
        }

        return ltrim($src, '/');
    }

    public static function assetUrl(string $path): string
    {
        return rtrim((string) config('app.url'), '/').'/'.ltrim($path, '/');
    }

    public static function ensureDefaultImages(): void
    {
        $dir = public_path('default');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        foreach (['default.jpg', 'profile.jpg'] as $file) {
            $path = $dir.DIRECTORY_SEPARATOR.$file;

            if (is_file($path)) {
                continue;
            }

            if (function_exists('imagecreatetruecolor')) {
                $img = imagecreatetruecolor(400, 400);
                $bg = imagecolorallocate($img, 241, 245, 249);
                imagefill($img, 0, 0, $bg);
                imagejpeg($img, $path, 90);
                imagedestroy($img);

                continue;
            }

            file_put_contents($path, base64_decode(self::PLACEHOLDER_JPEG));
        }
    }
}
