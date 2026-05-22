<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->decimal('price', 12, 2)->default(0);
            $table->string('category', 64)->index();
            $table->string('category_ar');
            $table->string('category_en');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_catalog');
    }
};
