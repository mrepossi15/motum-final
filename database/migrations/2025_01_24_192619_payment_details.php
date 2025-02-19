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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade'); // Relación con el pago principal
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario receptor (entrenador o empresa)
            $table->decimal('amount', 10, 2); // Monto transferido
            $table->string('type'); // Tipo (e.g., empresa, entrenador)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
    }
};
