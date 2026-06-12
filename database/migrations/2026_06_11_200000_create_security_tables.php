<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. security_events — audit log for all security-related events
        if (! Schema::hasTable('security_events')) {
            Schema::create('security_events', function (Blueprint $table) {
                $table->id();
                $table->string('type', 100)->index();
                // info | low | medium | high | critical
                $table->string('severity', 20)->default('info')->index();
                $table->string('ip_address', 45)->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->text('user_agent')->nullable();
                $table->text('url')->nullable();
                $table->string('method', 10)->nullable();
                $table->text('message');
                $table->json('context')->nullable();
                $table->timestamp('created_at')->useCurrent()->index();
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            });
        }

        // 2. login_attempts — track all login successes and failures
        if (! Schema::hasTable('login_attempts')) {
            Schema::create('login_attempts', function (Blueprint $table) {
                $table->id();
                $table->string('email', 255)->nullable()->index();
                $table->string('ip_address', 45)->index();
                $table->text('user_agent')->nullable();
                $table->boolean('successful')->default(false)->index();
                $table->string('failure_reason', 255)->nullable();
                $table->timestamp('created_at')->useCurrent()->index();
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            });
        }

        // 3. blocked_ips — IPs manually or automatically blocked
        if (! Schema::hasTable('blocked_ips')) {
            Schema::create('blocked_ips', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address', 45)->unique();
                $table->text('reason')->nullable();
                $table->timestamp('blocked_until')->nullable()->index();
                $table->boolean('is_permanent')->default(false);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 4. security_settings — key/value store for security config
        if (! Schema::hasTable('security_settings')) {
            Schema::create('security_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 100)->unique();
                $table->json('value')->nullable();
                $table->timestamps();
            });
        }

        // 5. file_scan_results — results from security:scan
        if (! Schema::hasTable('file_scan_results')) {
            Schema::create('file_scan_results', function (Blueprint $table) {
                $table->id();
                $table->string('scan_id', 50)->index();
                $table->text('path');
                // suspicious | modified | new | deleted | ok | reviewed
                $table->string('type', 30)->index();
                // info | low | medium | high | critical
                $table->string('severity', 20)->index();
                $table->text('message')->nullable();
                $table->string('hash', 64)->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('file_scan_results');
        Schema::dropIfExists('security_settings');
        Schema::dropIfExists('blocked_ips');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('security_events');
    }
};
