<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency', 3)->nullable()->after('amount');
            $table->string('khqr_md5', 32)->nullable()->unique()->after('provider_reference');
            $table->text('khqr_payload')->nullable()->after('khqr_md5');
            $table->string('transaction_id')->nullable()->unique()->after('khqr_payload');
            $table->timestamp('paid_at')->nullable()->after('transaction_id');
            $table->timestamp('verified_at')->nullable()->after('paid_at');
            $table->timestamp('expires_at')->nullable()->index()->after('verified_at');
            $table->json('verification_response')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['khqr_md5']);
            $table->dropUnique(['transaction_id']);
            $table->dropIndex(['expires_at']);
            $table->dropColumn(['currency', 'khqr_md5', 'khqr_payload', 'transaction_id', 'paid_at', 'verified_at', 'expires_at', 'verification_response']);
        });
    }
};
