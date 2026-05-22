<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_submission_id')->constrained()->cascadeOnDelete();
            $table->string('service_code')->nullable();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('category', 64)->nullable();
            $table->unsignedSmallInteger('tooth_number')->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->string('discount_type', 16)->default('amount');
            $table->decimal('line_total', 12, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_plan_items');
    }
};
