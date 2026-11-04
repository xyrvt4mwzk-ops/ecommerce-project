<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $covers = [
            'premium-instagram-templates', 'mobile-app-ui-kit', 'launch-email-swipe-file',
            'short-video-editing-pack', 'modern-brand-fonts-bundle', 'mini-creator-business-course',
            'commercial-license-starter-bundle', 'premium-client-portal-kit', 'vip-membership-launch-kit',
            'digital-course-license-pack',
        ];

        foreach ($covers as $slug) {
            DB::table('products')->where('slug', $slug)->update([
                'cover_image_path' => "images/products/{$slug}.svg",
            ]);
        }
    }

    public function down(): void
    {
        // Logo covers remain the intended default.
    }
};
