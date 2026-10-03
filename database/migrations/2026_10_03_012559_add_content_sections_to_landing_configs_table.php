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
            $table->json('hero_slides')->nullable();
            $table->json('equipment')->nullable();
            $table->json('trainers')->nullable();
            $table->json('facilities')->nullable();
            $table->string('secondary_color')->default('#111214');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_configs', function (Blueprint $table) {
            $table->dropColumn([
                'hero_slides',
                'equipment',
                'trainers',
                'facilities',
                'secondary_color',
            ]);
        });
    }
};
