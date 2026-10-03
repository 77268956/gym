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
        Schema::table('landing_configs', function (Blueprint $table) {
            $table->string('equipment_heading')->nullable();
            $table->text('equipment_intro')->nullable();
            $table->string('trainers_heading')->nullable();
            $table->text('trainers_intro')->nullable();
            $table->string('facilities_heading')->nullable();
            $table->text('facilities_intro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_configs', function (Blueprint $table) {
            $table->dropColumn([
                'equipment_heading',
                'equipment_intro',
                'trainers_heading',
                'trainers_intro',
                'facilities_heading',
                'facilities_intro',
            ]);
        });
    }
};
