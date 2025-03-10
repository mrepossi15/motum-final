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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->rememberToken();
            $table->string('certification')->nullable();
            $table->string('certification_pic')->nullable();
            $table->string('certification_pic_description')->nullable();
            $table->string('role')->default('alumno'); // Rol por defecto
            $table->text('biography')->nullable();     // Biografía
            $table->text('especialty')->nullable();     // Especialidad
            $table->date('birth')->nullable();     
            $table->string('profile_pic')->nullable();
            $table->string('profile_pic_description')->nullable();
            $table->string('mercado_pago_email')->nullable();
            $table->string('medical_fit')->nullable();
            $table->string('medical_fit_description')->nullable();
           
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

