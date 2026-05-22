<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('id_number')->nullable()->index();
            $table->string('file_number')->nullable()->index();
            $table->string('phone')->nullable();
            $table->foreignId('nationality_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date_of_birth')->nullable();
            $table->string('sex', 16)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'file_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
