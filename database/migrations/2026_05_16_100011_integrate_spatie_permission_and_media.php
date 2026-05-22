<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        Schema::dropIfExists('archive_files');

        if (Schema::hasTable('signatures')) {
            Schema::table('signatures', function (Blueprint $table) {
                if (Schema::hasColumn('signatures', 'image_data')) {
                    $table->dropColumn('image_data');
                }
                if (Schema::hasColumn('signatures', 'storage_path')) {
                    $table->dropColumn('storage_path');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('doctor')->after('email');
        });

        Schema::create('archive_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_submission_id')->constrained()->cascadeOnDelete();
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('file_name');
            $table->string('mime_type', 64)->default('application/pdf');
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('checksum', 64)->nullable();
            $table->unsignedSmallInteger('version')->default(1);
            $table->timestamps();
            $table->index('form_submission_id');
        });

        Schema::table('signatures', function (Blueprint $table) {
            $table->text('image_data')->nullable();
            $table->string('storage_path')->nullable();
        });
    }
};
