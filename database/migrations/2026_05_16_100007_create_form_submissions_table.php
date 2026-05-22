<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('form_template_id')->constrained();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('patient_name', 125);
            $table->string('id_number', 50)->nullable();
            $table->string('file_number', 50)->nullable();
            $table->string('doctor_name')->nullable();
            $table->string('language', 8)->default('ar');

            $table->string('status', 48)->default('completed')->index();
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->boolean('is_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->json('payload')->nullable();
            $table->json('tooth_states')->nullable();
            $table->json('plan_teeth')->nullable();
            $table->string('age_mode', 16)->nullable();
            $table->decimal('extra_discount', 12, 2)->default(0);
            $table->string('extra_discount_type', 16)->default('amount');
            $table->json('specialty_breakdown')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['branch_id', 'form_template_id', 'created_at']);
            $table->index(['patient_name', 'file_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
