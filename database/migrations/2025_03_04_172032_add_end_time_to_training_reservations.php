<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('training_reservations', function (Blueprint $table) {
            $table->time('end_time')->nullable()->after('time'); // 🔥 Agrega la columna después de 'time'
        });
    }

    public function down() {
        Schema::table('training_reservations', function (Blueprint $table) {
            $table->dropColumn('end_time');
        });
    }
};