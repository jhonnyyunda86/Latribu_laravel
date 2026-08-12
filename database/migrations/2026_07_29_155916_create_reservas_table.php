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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            // Relación con el cliente (usuario)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Relación con la mesa
            $table->foreignId('mesa_id')
                ->constrained('mesas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('fecha_reserva');
            $table->time('hora_reserva');
            $table->integer('cantidad_personas');
            
            // Estado de la reserva
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'completada'])
                ->default('pendiente');

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
