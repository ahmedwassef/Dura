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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_template_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('key')->nullable();
            $table->string('label_ar')->nullable();
            $table->string('label_en')->nullable();
            $table->string('placeholder_ar')->nullable();
            $table->string('placeholder_en')->nullable();
            $table->text('content_ar')->nullable();
            $table->text('content_en')->nullable();
            $table->json('options')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
