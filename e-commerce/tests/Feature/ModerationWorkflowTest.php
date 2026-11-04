<?php

namespace Tests\Feature;

use App\Models\ContentReport;
use App\Models\License;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_response_dmca_takedown_counter_notice_and_restore_workflow(): void
    {
        $creator = $this->userWithRole('creator');
        $buyer = $this->userWithRole('buyer');
        $reviewer = $this->userWithRole('content-reviewer');
        $product = Product::factory()->create(['user_id' => $creator->id]);
        License::factory()->create(['user_id' => $buyer->id, 'product_id' => $product->id, 'status' => 'active']);

        $this->actingAs($buyer)->post(route('products.reviews.store', $product), [
            'rating' => 5,
            'body' => 'Excellent product and clear delivery.',
        ])->assertRedirect();
        $review = Review::firstOrFail();

        $this->actingAs($creator)->post(route('creator.reviews.respond', $review), [
            'creator_response' => 'Thank you for your detailed feedback.',
        ])->assertRedirect();
        $this->assertSame('Thank you for your detailed feedback.', $review->fresh()->creator_response);

        $this->actingAs($buyer)->post(route('content-reports.store', $product), [
            'review_id' => $review->id,
            'reason' => 'This listing contains copyrighted material that I own and distribute.',
            'evidence' => 'https://rights-holder.example/original-work',
        ])->assertRedirect();
        $report = ContentReport::firstOrFail();

        $this->actingAs($reviewer)->get(route('reviewer.moderation.index'))
            ->assertOk()->assertSeeText($product->title);
        $this->get(route('reviewer.moderation.show', $report))
            ->assertOk()->assertSeeText('Moderation case #'.$report->id);
        $this->actingAs($creator)->get(route('creator.content-reports.index'))
            ->assertOk()->assertSeeText($product->title);

        $this->actingAs($reviewer)->post(route('reviewer.moderation.takedown', $report), [
            'admin_notes' => 'Evidence is sufficient for a temporary takedown pending response.',
        ])->assertRedirect();
        $this->assertSame(Product::MODERATION_TAKEN_DOWN, $product->fresh()->moderation_status);
        $this->assertSame('hidden', $review->fresh()->status);

        $this->actingAs($creator)->post(route('creator.content-reports.counter', $report), [
            'counter_notice' => 'I have a valid commercial license and can provide the signed agreement.',
        ])->assertRedirect();
        $this->assertSame('countered', $report->fresh()->status);

        $this->actingAs($reviewer)->post(route('reviewer.moderation.restore', $report), [
            'admin_notes' => 'The counter-notice and license were verified. Restore the content.',
        ])->assertRedirect();
        $this->assertSame(Product::MODERATION_RESTORED, $product->fresh()->moderation_status);
        $this->assertSame('published', $review->fresh()->status);
        $this->assertSame('restored', $report->fresh()->resolution);

        $this->actingAs($buyer)->delete(route('reviews.destroy', $review))->assertRedirect();
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_users_cannot_moderate_or_respond_to_content_they_do_not_own(): void
    {
        $creator = $this->userWithRole('creator');
        $otherCreator = $this->userWithRole('creator');
        $buyer = $this->userWithRole('buyer');
        $product = Product::factory()->create(['user_id' => $creator->id]);
        $review = Review::create(['product_id' => $product->id, 'user_id' => $buyer->id, 'rating' => 4, 'body' => 'Good product.']);

        $this->actingAs($otherCreator)->post(route('creator.reviews.respond', $review), [
            'creator_response' => 'Unauthorized response.',
        ])->assertForbidden();

        $this->actingAs($buyer)->get(route('reviewer.moderation.index'))->assertForbidden();
    }

    private function userWithRole(string $name): User
    {
        $role = Role::firstOrCreate(['name' => $name], ['display_name' => str($name)->replace('-', ' ')->title()]);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }
}
