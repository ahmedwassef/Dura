<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->morphs('signable');
            $table->string('role', 32);
            $table->string('signer_name')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->index(['signable_type', 'signable_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
