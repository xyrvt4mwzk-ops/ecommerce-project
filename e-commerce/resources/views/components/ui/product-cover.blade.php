@props([
    'product',
    'class' => 'h-52',
])

@php
    $fallbackImages = [
        'graphics' => 'images/products/mobile-app-ui-kit.svg',
        'templates' => 'images/products/premium-instagram-templates.svg',
        'music' => 'images/products/short-video-editing-pack.svg',
        'courses' => 'images/products/mini-creator-business-course.svg',
        'software' => 'images/products/premium-client-portal-kit.svg',
        'marketing' => 'images/products/launch-email-swipe-file.svg',
    ];
    $coverUrl = $product->coverImageUrl() ?? asset($fallbackImages[$product->category] ?? 'images/products/mobile-app-ui-kit.svg');
@endphp

<div class="relative overflow-hidden bg-slate-950 {{ $class }}">
    <img src="{{ $coverUrl }}" alt="{{ $product->title }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]" />
    <div class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-white/10"></div>
</div>
