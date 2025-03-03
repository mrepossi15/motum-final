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
        Schema::create('park_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('park_id')->constrained()->onDelete('cascade'); // Relación con parks
            $table->string('author');
            $table->decimal('rating', 3, 2);
            $table->text('text')->nullable();
            $table->string('time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_reviews');
    }
};
