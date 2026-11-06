<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->renameColumn('is_activate', 'is_active');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
        });
        Schema::table('member_types', function (Blueprint $table) {
            // Business rules use a stable code, never an editable display name.
            $table->string('code', 16)->nullable()->unique();
        });
        Schema::table('breeding_numbers', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::create('member_type_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_type_id')->constrained();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('amount_cents');
            $table->timestamps();
            $table->unique(['member_type_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_type_rates');
        Schema::table('breeding_numbers', fn (Blueprint $table) => $table->dropSoftDeletes());
        Schema::table('member_types', fn (Blueprint $table) => $table->dropColumn('code'));
        Schema::table('users', function (Blueprint $table) {
            $table->dropRememberToken();
            $table->dropColumn('email_verified_at');
        });
        Schema::table('members', fn (Blueprint $table) => $table->renameColumn('is_active', 'is_activate'));
    }
};
