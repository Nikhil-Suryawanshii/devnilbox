<?php

namespace App\Console\Commands;

use App\Models\ShopPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOldShopPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:delete-old-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete shop posts older than 24 hours and their associated media';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for old shop posts...');

        $posts = ShopPost::where('created_at', '<', now()->subHours(24))->get();

        if ($posts->isEmpty()) {
            $this->info('No old posts found.');
            return;
        }

        $count = $posts->count();
        $this->info("Found {$count} posts to delete.");

        foreach ($posts as $post) {
            // Delete associated media files and records
            foreach ($post->media as $media) {
                if (Storage::disk('public')->exists($media->src)) {
                    Storage::disk('public')->delete($media->src);
                }
                $media->delete();
            }

            // Sync media to remove pivot records (optional since we deleted media, cascading delete handled by DB if set, but good practice)
            $post->media()->detach();

            $post->delete();
        }

        $this->info("Successfully deleted {$count} posts.");
    }
}
