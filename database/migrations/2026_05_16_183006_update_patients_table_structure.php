<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'nationality_id')) {
                $table->foreignId('nationality_id')->nullable()->after('phone')->constrained('nationalities')->nullOnDelete();
            }
            if (!Schema::hasColumn('patients', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('nationality_id');
            }
            
            // Cleanup old columns if they exist
            if (Schema::hasColumn('patients', 'nationality')) {
                $table->dropColumn('nationality');
            }
            if (Schema::hasColumn('patients', 'age')) {
                $table->dropColumn('age');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('nationality')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->dropForeign(['nationality_id']);
            $table->dropColumn(['nationality_id', 'date_of_birth']);
        });
    }
};
