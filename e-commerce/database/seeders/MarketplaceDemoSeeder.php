<?php

namespace Database\Seeders;

use App\Models\AffiliateCommission;
use App\Models\AffiliateReferral;
use App\Models\License;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('DEMO_ACCOUNT_PASSWORD', 'password');

        $creator = User::updateOrCreate(
            ['email' => 'creator@example.com'],
            [
                'name' => 'Demo Creator',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $buyer = User::updateOrCreate(
            ['email' => 'buyer@example.com'],
            [
                'name' => 'Demo Buyer',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $affiliate = User::updateOrCreate(
            ['email' => 'affiliate@example.com'],
            [
                'name' => 'Demo Affiliate',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $reviewer = User::updateOrCreate(
            ['email' => 'reviewer@example.com'],
            [
                'name' => 'Demo Content Reviewer',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $creator->assignPrimaryRole('creator');
        $buyer->assignPrimaryRole('buyer');
        $affiliate->assignPrimaryRole('affiliate');
        $reviewer->assignPrimaryRole('content-reviewer');

        // Keep catalog ownership with a real creator account when one is configured.
        // The demo account remains available only for development demonstrations.
        $catalogCreator = User::query()
            ->where('email', env('MARKETPLACE_CATALOG_CREATOR_EMAIL', $creator->email))
            ->first() ?? $creator;

        $buyer->orders()->delete();

        collect([
            'gamma-pro-presentation-tools' => ['title' => 'Gamma Pro — 1 Month', 'price' => 7.00],
            'microsoft-365-office' => ['title' => 'Microsoft 365 — 12 Months', 'price' => 7.00],
            'youtube-premium' => ['title' => 'YouTube Premium — 3 Months', 'price' => 6.00],
        ])->each(function (array $offer, string $oldSlug) {
            Product::where('slug', $oldSlug)->update([
                'title' => $offer['title'],
                'slug' => Str::slug($offer['title']),
                'price' => $offer['price'],
            ]);
        });

        $products = collect([
            [
                'title' => 'Premium Instagram Templates',
                'category' => 'templates',
                'price' => 19.00,
                'short_description' => 'A polished social launch pack with reels, stories, and campaign slides.',
                'full_description' => 'Designed for modern creators, this premium template set includes launch posts, sale banners, and swipe-ready story layouts for digital product promotions.',
                'cover_image_path' => 'images/products/premium-instagram-templates.svg',
            ],
            [
                'title' => 'Mobile App UI Kit',
                'category' => 'graphics',
                'price' => 29.00,
                'short_description' => 'High-end mobile screens for fintech, e-learning, and creator tools.',
                'full_description' => 'A complete mobile UI kit with onboarding flows, dashboard cards, checkout screens, and product pages for app presentations.',
                'cover_image_path' => 'images/products/mobile-app-ui-kit.svg',
            ],
            [
                'title' => 'Launch Email Swipe File',
                'category' => 'marketing',
                'price' => 15.00,
                'short_description' => 'Conversion-driven launch emails for new products, discounts, and waitlists.',
                'full_description' => 'Includes pre-launch, cart-open, urgency, and bonus reminder emails for digital product campaigns and premium memberships.',
                'cover_image_path' => 'images/products/launch-email-swipe-file.svg',
            ],
            [
                'title' => 'Short Video Editing Pack',
                'category' => 'software',
                'price' => 24.00,
                'short_description' => 'A ready-to-use toolkit of transitions, social captions, and promo scenes.',
                'full_description' => 'Built for short-form creators who need attention-grabbing animations, kinetic text scenes, and reusable video layouts.',
                'cover_image_path' => 'images/products/short-video-editing-pack.svg',
            ],
            [
                'title' => 'Modern Brand Fonts Bundle',
                'category' => 'graphics',
                'price' => 18.00,
                'short_description' => 'Elegant brand type pairings for product pages, media kits, and premium offers.',
                'full_description' => 'Includes modern serif and clean sans pairing suggestions tailored for marketplaces, portfolios, and brand launch decks.',
                'cover_image_path' => 'images/products/modern-brand-fonts-bundle.svg',
            ],
            [
                'title' => 'Mini Creator Business Course',
                'category' => 'courses',
                'price' => 39.00,
                'short_description' => 'A fast-track course on launching and packaging digital products.',
                'full_description' => 'Covers offer creation, pricing, launch planning, audience growth, and how to turn expertise into premium downloadable products.',
                'cover_image_path' => 'images/products/mini-creator-business-course.svg',
            ],
            [
                'title' => 'Commercial License Starter Bundle',
                'category' => 'templates',
                'price' => 22.00,
                'short_description' => 'Editable license templates for freelance, agency, and product sales.',
                'full_description' => 'A commercial-use agreement bundle with client-friendly language for digital downloads, memberships, and limited redistribution terms.',
                'cover_image_path' => 'images/products/commercial-license-starter-bundle.svg',
            ],
            [
                'title' => 'Premium Client Portal Kit',
                'category' => 'software',
                'price' => 34.00,
                'short_description' => 'A premium client account dashboard kit for onboarding and account access design.',
                'full_description' => 'Built for SaaS and service businesses, this kit includes account dashboards, subscription views, settings panels, and billing layouts.',
                'cover_image_path' => 'images/products/premium-client-portal-kit.svg',
            ],
            [
                'title' => 'VIP Membership Launch Kit',
                'category' => 'marketing',
                'price' => 27.00,
                'short_description' => 'Launch assets for premium communities, coaching clubs, and private member access offers.',
                'full_description' => 'Includes member-welcome email copy, landing section prompts, pricing card blocks, and a premium-access campaign plan.',
                'cover_image_path' => 'images/products/vip-membership-launch-kit.svg',
            ],
            [
                'title' => 'Digital Course License Pack',
                'category' => 'courses',
                'price' => 31.00,
                'short_description' => 'A legal-and-product pack for course creators selling licensed educational assets.',
                'full_description' => 'Created for educators and workshop sellers who need a polished license framework and premium course delivery assets.',
                'cover_image_path' => 'images/products/digital-course-license-pack.svg',
            ],
            [
                'title' => 'Adobe Creative Cloud Pro',
                'category' => 'software',
                'price' => 9.00,
                'short_description' => 'Creative-tool access listing with an Adobe promotional cover.',
                'full_description' => 'A digital access product for customers seeking creative software tools. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/adobe-creative-cloud-pro.jpg',
            ],
            [
                'title' => 'Claude Pro',
                'category' => 'software',
                'price' => 6.00,
                'short_description' => 'AI productivity access listing with a Claude Pro promotional cover.',
                'full_description' => 'A digital access product for AI-assisted writing and productivity workflows. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/claude-pro.jpg',
            ],
            [
                'title' => 'Gamma Pro — 1 Month',
                'category' => 'software',
                'price' => 7.00,
                'short_description' => 'AI presentation tools with one month of access.',
                'full_description' => 'A digital access product for presentation and content-creation workflows. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/gamma-english.png',
            ],
            [
                'title' => 'Microsoft 365 — 12 Months',
                'category' => 'software',
                'price' => 7.00,
                'short_description' => 'Microsoft productivity tools with twelve months of access.',
                'full_description' => 'A digital access product for office and cloud productivity tools. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/microsoft-365-office.jpg',
            ],
            [
                'title' => 'NordVPN 3 Months',
                'category' => 'software',
                'price' => 5.00,
                'short_description' => 'VPN access listing with a NordVPN three-month promotional cover.',
                'full_description' => 'A digital access product for online privacy tools. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/nordvpn-three-months.jpg',
            ],
            [
                'title' => 'ChatGPT Plus Productivity',
                'category' => 'software',
                'price' => 7.00,
                'short_description' => 'AI productivity access listing with a ChatGPT Plus promotional cover.',
                'full_description' => 'A digital access product for AI-assisted productivity workflows. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/chatgpt-plus-productivity.jpg',
            ],
            [
                'title' => 'ChatGPT Plus Standard',
                'category' => 'software',
                'price' => 7.00,
                'short_description' => 'AI access listing with a ChatGPT Plus promotional cover.',
                'full_description' => 'A digital access product for AI-assisted conversations and creation. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/chatgpt-plus-standard.jpg',
            ],
            [
                'title' => 'ChatGPT Plus Creative',
                'category' => 'software',
                'price' => 6.00,
                'short_description' => 'AI creative-tool access listing with a ChatGPT Plus promotional cover.',
                'full_description' => 'A digital access product for AI-assisted creative workflows. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/chatgpt-plus-creative.jpg',
            ],
            [
                'title' => 'Gemini Pro',
                'category' => 'software',
                'price' => 3.00,
                'short_description' => 'AI productivity access listing with a Gemini Pro promotional cover.',
                'full_description' => 'A digital access product for research and productivity workflows. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/gemini-pro.jpg',
            ],
            [
                'title' => 'YouTube Premium — 3 Months',
                'category' => 'software',
                'price' => 6.00,
                'short_description' => 'Ad-free video and music access for three months.',
                'full_description' => 'A digital access product for a premium video and music experience. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/youtube-english.png',
            ],
            [
                'title' => 'Claude Max',
                'category' => 'software',
                'price' => 10.00,
                'short_description' => 'Advanced AI access listing with a Claude Max promotional cover.',
                'full_description' => 'A digital access product for high-usage AI workflows. Fulfilment and account use must comply with the applicable provider terms and licensing requirements.',
                'cover_image_path' => 'images/products/subscriptions/claude-max.jpg',
            ],
            ['title' => 'Coursera Plus — 12 Months', 'category' => 'courses', 'price' => 10.00, 'short_description' => 'Online learning access for twelve months.', 'full_description' => 'A digital learning access product for professional courses and skills development. Fulfilment must comply with the provider terms.', 'cover_image_path' => 'images/products/subscriptions/coursera-english.png'],
            ['title' => 'CapCut Pro — 12 Months', 'category' => 'software', 'price' => 21.00, 'short_description' => 'Video editing tools with twelve months of access.', 'full_description' => 'A digital video-editing access product. Fulfilment must comply with the provider terms.', 'cover_image_path' => 'images/products/subscriptions/capcut-english.png'],
            ['title' => 'Udemy — 1 Month', 'category' => 'courses', 'price' => 7.00, 'short_description' => 'Course learning access for one month.', 'full_description' => 'A digital learning access product for online courses. Fulfilment must comply with the provider terms.', 'cover_image_path' => 'images/products/subscriptions/udemy-english.png'],
            ['title' => 'Prime Video — 12 Months', 'category' => 'software', 'price' => 9.00, 'short_description' => 'Streaming entertainment access for twelve months.', 'full_description' => 'A digital streaming access product. Fulfilment must comply with the provider terms.', 'cover_image_path' => 'images/products/subscriptions/primevideo-english.png'],
            ['title' => 'Grammarly Premium — 12 Months', 'category' => 'software', 'price' => 8.00, 'short_description' => 'Writing assistance tools with twelve months of access.', 'full_description' => 'A digital writing-assistance access product. Fulfilment must comply with the provider terms.', 'cover_image_path' => 'images/products/subscriptions/grammarly-english.png'],
            ['title' => 'Duolingo Super — 1 Month', 'category' => 'courses', 'price' => 2.00, 'short_description' => 'Language-learning access for one month.', 'full_description' => 'A digital language-learning access product. Fulfilment must comply with the provider terms.', 'cover_image_path' => 'images/products/logos/duolingo.svg'],
            ['title' => 'Figma Professional — 12 Months', 'category' => 'software', 'price' => 17.00, 'short_description' => 'Collaborative design tools with twelve months of access.', 'full_description' => 'A digital design-tool access product. Fulfilment must comply with the provider terms.', 'cover_image_path' => 'images/products/subscriptions/figma-english.png'],
            ['title' => 'Canva Pro — Lifetime', 'category' => 'software', 'price' => 4.00, 'short_description' => 'Creative design access listed as a lifetime offer.', 'full_description' => 'A digital creative-tool access product. Fulfilment must comply with the provider terms.', 'cover_image_path' => 'images/products/subscriptions/canva-english.png'],
        ])->map(function (array $item) use ($catalogCreator) {
            return Product::updateOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'user_id' => $catalogCreator->id,
                    'title' => $item['title'],
                    'short_description' => $item['short_description'],
                    'full_description' => $item['full_description'],
                    'category' => $item['category'],
                    'price' => $item['price'],
                    'cover_image_path' => $item['cover_image_path'],
                    'file_path' => null,
                    'status' => Product::STATUS_PUBLISHED,
                ]
            );
        });

        $referrals = $products->take(3)->values()->map(function (Product $product) use ($affiliate) {
            return AffiliateReferral::updateOrCreate(
                [
                    'affiliate_user_id' => $affiliate->id,
                    'product_id' => $product->id,
                ],
                [
                    'code' => Str::lower(Str::random(12)),
                    'clicks' => 12,
                ]
            );
        });

        $this->createSeedOrder($buyer, $products[0], $referrals[0] ?? null);
        $this->createSeedOrder($buyer, $products[1], null);
    }

    private function createSeedOrder(User $buyer, Product $product, ?AffiliateReferral $referral): void
    {
        DB::transaction(function () use ($buyer, $product, $referral) {
            $order = Order::create([
                'order_number' => 'ORD-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
                'user_id' => $buyer->id,
                'affiliate_referral_id' => $referral?->id,
                'subtotal' => $product->price,
                'total' => $product->price,
                'status' => 'paid',
            ]);

            $item = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => 1,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'method' => 'khqr',
                'status' => 'paid',
                'amount' => $product->price,
                'provider' => 'bakong',
                'provider_reference' => 'BAKONG-'.Str::upper(Str::random(10)),
                'metadata' => ['type' => 'seeded-order'],
            ]);

            License::create([
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'product_id' => $product->id,
                'user_id' => $buyer->id,
                'license_key' => implode('-', [
                    Str::upper(Str::random(4)),
                    Str::upper(Str::random(4)),
                    Str::upper(Str::random(4)),
                    Str::upper(Str::random(4)),
                ]),
            ]);

            if ($referral) {
                AffiliateCommission::create([
                    'affiliate_referral_id' => $referral->id,
                    'affiliate_user_id' => $referral->affiliate_user_id,
                    'order_id' => $order->id,
                    'amount' => round($product->price * 0.10, 2),
                    'rate' => 10,
                    'status' => 'paid',
                ]);
            }
        });
    }
}
