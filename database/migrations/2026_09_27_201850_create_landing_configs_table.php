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
        Schema::create('landing_configs', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title')->default('Bienvenido a nuestro Gimnasio');
            $table->string('hero_subtitle')->default('Alcanza tus metas con nosotros');
            $table->string('hero_image')->nullable();

            $table->text('about_text')->nullable();
            $table->string('about_image')->nullable();

            $table->json('services')->nullable(); // Guardará array de [{icon, title, desc}]

            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('contact_facebook')->nullable();
            $table->string('contact_instagram')->nullable();
            $table->string('contact_whatsapp')->nullable();

            $table->string('primary_color')->default('#2563EB');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_configs');
    }
};
