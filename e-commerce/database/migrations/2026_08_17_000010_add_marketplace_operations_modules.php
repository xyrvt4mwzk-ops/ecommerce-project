<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event')->index(); $table->nullableMorphs('subject'); $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 1000)->nullable(); $table->json('properties')->nullable(); $table->timestamps();
        });
        Schema::create('creator_profiles', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('store_name')->nullable(); $table->string('store_slug')->nullable()->unique(); $table->text('bio')->nullable();
            $table->string('payout_method')->nullable(); $table->text('payout_details')->nullable(); $table->string('verification_status')->default('unverified')->index();
            $table->timestamp('policy_accepted_at')->nullable(); $table->timestamps();
        });
        Schema::create('license_types', function (Blueprint $table) {
            $table->id(); $table->string('name')->unique(); $table->string('slug')->unique(); $table->text('rules')->nullable();
            $table->unsignedInteger('activation_limit')->default(1); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::table('licenses', function (Blueprint $table) {
            $table->foreignId('license_type_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            $table->unsignedInteger('activation_count')->default(0)->after('license_key'); $table->string('status')->default('active')->after('activation_count');
            $table->timestamp('revoked_at')->nullable()->after('status'); $table->text('revocation_reason')->nullable()->after('revoked_at');
        });
        Schema::create('license_activations', function (Blueprint $table) {
            $table->id(); $table->foreignId('license_id')->constrained()->cascadeOnDelete(); $table->string('device_hash')->nullable();
            $table->string('ip_address', 45)->nullable(); $table->timestamp('activated_at'); $table->timestamps();
        });
        Schema::create('product_files', function (Blueprint $table) {
            $table->id(); $table->foreignId('product_id')->constrained()->cascadeOnDelete(); $table->string('path');
            $table->string('name'); $table->string('version')->default('1.0'); $table->boolean('is_preview')->default(false); $table->timestamps();
        });
        Schema::create('download_links', function (Blueprint $table) {
            $table->id(); $table->foreignId('license_id')->constrained()->cascadeOnDelete(); $table->string('token')->unique();
            $table->unsignedInteger('remaining_downloads')->default(5); $table->timestamp('expires_at')->nullable(); $table->timestamp('last_downloaded_at')->nullable(); $table->timestamps();
        });
        Schema::create('reviews', function (Blueprint $table) {
            $table->id(); $table->foreignId('product_id')->constrained()->cascadeOnDelete(); $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); $table->text('body')->nullable(); $table->text('creator_response')->nullable(); $table->timestamps(); $table->unique(['product_id', 'user_id']);
        });
        Schema::create('content_reports', function (Blueprint $table) {
            $table->id(); $table->foreignId('product_id')->constrained()->cascadeOnDelete(); $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('dmca'); $table->text('reason'); $table->string('status')->default('open')->index(); $table->text('admin_notes')->nullable(); $table->timestamps();
        });
        Schema::create('coupons', function (Blueprint $table) {
            $table->id(); $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete(); $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique(); $table->string('type'); $table->decimal('value', 10, 2); $table->unsignedInteger('usage_limit')->nullable(); $table->unsignedInteger('usage_count')->default(0); $table->timestamp('starts_at')->nullable(); $table->timestamp('ends_at')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('bundles', function (Blueprint $table) {
            $table->id(); $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete(); $table->string('title'); $table->string('slug')->unique(); $table->decimal('price', 10, 2); $table->timestamp('sale_ends_at')->nullable(); $table->timestamps();
        });
        Schema::create('bundle_product', function (Blueprint $table) { $table->id(); $table->foreignId('bundle_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->constrained()->cascadeOnDelete(); $table->unique(['bundle_id','product_id']); });
        Schema::create('payouts', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->decimal('amount', 10, 2); $table->string('currency', 3)->default('USD'); $table->string('status')->default('requested')->index(); $table->string('reference')->nullable(); $table->text('notes')->nullable(); $table->timestamp('processed_at')->nullable(); $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payouts'); Schema::dropIfExists('bundle_product'); Schema::dropIfExists('bundles'); Schema::dropIfExists('coupons'); Schema::dropIfExists('content_reports'); Schema::dropIfExists('reviews'); Schema::dropIfExists('download_links'); Schema::dropIfExists('product_files'); Schema::dropIfExists('license_activations');
        Schema::table('licenses', function (Blueprint $table) { $table->dropConstrainedForeignId('license_type_id'); $table->dropColumn(['activation_count','status','revoked_at','revocation_reason']); }); Schema::dropIfExists('license_types'); Schema::dropIfExists('creator_profiles'); Schema::dropIfExists('activity_logs');
    }
};
