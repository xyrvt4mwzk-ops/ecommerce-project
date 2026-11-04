<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $covers = [
            'premium-instagram-templates' => 'images/products/premium-instagram-templates.svg',
            'mobile-app-ui-kit' => 'images/products/mobile-app-ui-kit.svg',
            'launch-email-swipe-file' => 'images/products/launch-email-swipe-file.svg',
            'short-video-editing-pack' => 'images/products/short-video-editing-pack.svg',
            'modern-brand-fonts-bundle' => 'images/products/modern-brand-fonts-bundle.svg',
            'mini-creator-business-course' => 'images/products/mini-creator-business-course.svg',
            'commercial-license-starter-bundle' => 'images/products/commercial-license-starter-bundle.svg',
            'premium-client-portal-kit' => 'images/products/premium-client-portal-kit.svg',
            'vip-membership-launch-kit' => 'images/products/vip-membership-launch-kit.svg',
            'digital-course-license-pack' => 'images/products/digital-course-license-pack.svg',
        ];

        foreach ($covers as $slug => $coverImagePath) {
            DB::table('products')->where('slug', $slug)->update(['cover_image_path' => $coverImagePath]);
        }
    }

    public function down(): void
    {
        $covers = [
            'premium-instagram-templates' => 'images/products/premium-instagram-templates.svg',
            'mobile-app-ui-kit' => 'images/products/mobile-app-ui-kit.svg',
            'launch-email-swipe-file' => 'images/products/launch-email-swipe-file.svg',
            'short-video-editing-pack' => 'images/products/short-video-editing-pack.svg',
            'modern-brand-fonts-bundle' => 'images/products/modern-brand-fonts-bundle.svg',
            'mini-creator-business-course' => 'images/products/mini-creator-business-course.svg',
            'commercial-license-starter-bundle' => 'images/products/commercial-license-starter-bundle.svg',
            'premium-client-portal-kit' => 'images/products/premium-client-portal-kit.svg',
            'vip-membership-launch-kit' => 'images/products/vip-membership-launch-kit.svg',
            'digital-course-license-pack' => 'images/products/digital-course-license-pack.svg',
        ];

        foreach ($covers as $slug => $coverImagePath) {
            DB::table('products')->where('slug', $slug)->update(['cover_image_path' => $coverImagePath]);
        }
    }
};
