<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('affiliate_enabled')->default(true)->after('status');
            $table->decimal('affiliate_commission_rate', 5, 2)->default(10)->after('affiliate_enabled');
            $table->string('moderation_status')->default('clear')->index()->after('affiliate_commission_rate');
            $table->text('moderation_notes')->nullable()->after('moderation_status');
            $table->timestamp('taken_down_at')->nullable()->after('moderation_notes');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->string('status')->default('published')->index()->after('creator_response');
            $table->timestamp('creator_responded_at')->nullable()->after('status');
            $table->timestamp('flagged_at')->nullable()->after('creator_responded_at');
            $table->text('moderation_notes')->nullable()->after('flagged_at');
        });

        Schema::table('content_reports', function (Blueprint $table) {
            $table->foreignId('review_id')->nullable()->after('product_id')->constrained('reviews')->nullOnDelete();
            $table->foreignId('reviewer_id')->nullable()->after('reporter_id')->constrained('users')->nullOnDelete();
            $table->text('evidence')->nullable()->after('reason');
            $table->text('counter_notice')->nullable()->after('admin_notes');
            $table->string('resolution')->nullable()->after('counter_notice');
            $table->timestamp('taken_down_at')->nullable();
            $table->timestamp('countered_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('affiliate_status')->default('active')->index()->after('password');
            $table->timestamp('affiliate_fraud_flagged_at')->nullable()->after('affiliate_status');
            $table->text('affiliate_fraud_reason')->nullable()->after('affiliate_fraud_flagged_at');
        });

        Schema::table('affiliate_referrals', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->index()->after('clicks');
        });

        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_referral_id')->constrained('affiliate_referrals')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('ip_hash', 64)->index();
            $table->string('session_hash', 64)->index();
            $table->string('user_agent', 500)->nullable();
            $table->boolean('is_suspicious')->default(false)->index();
            $table->string('suspicion_reason')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            $table->index(['affiliate_referral_id', 'created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('affiliate_click_id')->nullable()->after('affiliate_referral_id')->constrained('affiliate_clicks')->nullOnDelete();
        });

        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->foreignId('affiliate_click_id')->nullable()->after('affiliate_referral_id')->constrained('affiliate_clicks')->nullOnDelete();
            $table->foreignId('payout_id')->nullable()->after('order_id')->constrained('payouts')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->timestamp('fraud_flagged_at')->nullable()->after('approved_at');
            $table->text('fraud_reason')->nullable()->after('fraud_flagged_at');
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->string('kind')->default('affiliate')->index()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('payouts', fn (Blueprint $table) => $table->dropColumn('kind'));
        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('affiliate_click_id');
            $table->dropConstrainedForeignId('payout_id');
            $table->dropColumn(['approved_at', 'fraud_flagged_at', 'fraud_reason']);
        });
        Schema::table('orders', fn (Blueprint $table) => $table->dropConstrainedForeignId('affiliate_click_id'));
        Schema::dropIfExists('affiliate_clicks');
        Schema::table('affiliate_referrals', fn (Blueprint $table) => $table->dropColumn('is_active'));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['affiliate_status', 'affiliate_fraud_flagged_at', 'affiliate_fraud_reason']));
        Schema::table('content_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('review_id');
            $table->dropConstrainedForeignId('reviewer_id');
            $table->dropColumn(['evidence', 'counter_notice', 'resolution', 'taken_down_at', 'countered_at', 'resolved_at']);
        });
        Schema::table('reviews', fn (Blueprint $table) => $table->dropColumn(['status', 'creator_responded_at', 'flagged_at', 'moderation_notes']));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['affiliate_enabled', 'affiliate_commission_rate', 'moderation_status', 'moderation_notes', 'taken_down_at']));
    }
};
