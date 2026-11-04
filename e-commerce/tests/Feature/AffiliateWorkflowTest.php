<?php

namespace Tests\Feature;

use App\Models\AffiliateCommission;
use App\Models\AffiliateReferral;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\OrderCompletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'payway.enabled' => true,
            'payway.merchant_id' => 'sandbox-merchant',
            'payway.api_key' => 'sandbox-api-key',
            'payway.purchase_url' => 'https://checkout-sandbox.payway.test/purchase',
            'payway.currency' => 'USD',
            'marketplace.affiliate.minimum_payout' => 10,
        ]);
    }

    public function test_click_cart_conversion_commission_approval_and_payout_workflow(): void
    {
        $creator = $this->userWithRole('creator');
        $affiliate = $this->userWithRole('affiliate');
        $buyer = $this->userWithRole('buyer');
        $admin = $this->userWithRole('super-admin');
        $product = Product::factory()->create([
            'user_id' => $creator->id,
            'price' => 100,
            'affiliate_commission_rate' => 20,
        ]);

        $this->actingAs($affiliate)->post(route('affiliate.products.referral.store', $product))->assertRedirect();
        $referral = AffiliateReferral::firstOrFail();

        $this->actingAs($buyer)->get(route('affiliate.capture', $referral->code))->assertRedirect(route('products.show', $product));
        $this->post(route('cart.add', $product))->assertRedirect();
        $this->post(route('checkout.cart.start'), ['payment_method' => 'card'])->assertOk();

        $payment = Payment::with('order')->firstOrFail();
        $this->assertSame($referral->id, $payment->order->affiliate_referral_id);
        $this->assertNotNull($payment->order->affiliate_click_id);

        app(OrderCompletionService::class)->completeVerifiedPayment($payment, 'AFFILIATE-PAID-1', ['status' => 0]);
        $commission = AffiliateCommission::firstOrFail();
        $this->assertSame('pending', $commission->status);
        $this->assertSame('20.00', $commission->amount);
        $this->assertNotNull($commission->click->converted_at);

        $this->actingAs($admin)->get(route('admin.affiliates.index'))
            ->assertOk()->assertSeeText($affiliate->email);

        $this->actingAs($admin)->put(route('admin.affiliate-commissions.update', $commission), [
            'status' => 'approved',
        ])->assertRedirect();

        $this->actingAs($affiliate)->get(route('affiliate.payouts.index'))
            ->assertOk()->assertSeeText('$20.00');

        $this->actingAs($affiliate)->post(route('affiliate.payouts.store'))->assertRedirect(route('affiliate.payouts.index'));
        $payout = $affiliate->payouts()->firstOrFail();
        $this->assertSame('20.00', $payout->amount);

        $this->actingAs($admin)->put(route('admin.affiliate-payouts.update', $payout), [
            'status' => 'paid',
            'reference' => 'ABA-PAYOUT-1001',
        ])->assertRedirect();
        $this->assertSame('paid', $payout->fresh()->status);
        $this->assertSame('paid', $commission->fresh()->status);
    }

    public function test_self_referral_is_flagged_and_not_attributed(): void
    {
        $affiliate = $this->userWithRole('affiliate');
        $product = Product::factory()->create();
        $this->actingAs($affiliate)->post(route('affiliate.products.referral.store', $product));
        $referral = AffiliateReferral::firstOrFail();

        $this->get(route('affiliate.capture', $referral->code))->assertRedirect();

        $this->assertDatabaseHas('affiliate_clicks', [
            'affiliate_referral_id' => $referral->id,
            'is_suspicious' => true,
            'suspicion_reason' => 'self_referral',
        ]);
        $this->assertSame(0, $referral->fresh()->clicks);
        $this->assertNull(session('affiliate_referral_id'));
    }

    private function userWithRole(string $name): User
    {
        $role = Role::firstOrCreate(['name' => $name], ['display_name' => str($name)->replace('-', ' ')->title()]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }
}
