<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->string('status')->default('active')->after('display_name_ar');
            $table->string('avatar_url')->nullable()->after('status');
            $table->softDeletes();
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->string('status')->default('active')->after('name_en');
            $table->text('description')->nullable()->after('status');
            $table->softDeletes();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->string('address')->nullable()->after('name_en');
            $table->string('phone')->nullable()->after('address');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['phone', 'status', 'avatar_url', 'deleted_at']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['status', 'description', 'deleted_at']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['address', 'phone', 'deleted_at']);
        });
    }
};
