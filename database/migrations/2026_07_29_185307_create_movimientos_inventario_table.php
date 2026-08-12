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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();

            // Relación con el inventario/bodega origen o destino
            $table->foreignId('inventario_id')
                ->constrained('inventarios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Relación con el usuario que realiza el movimiento
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Tipo de movimiento
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste'])->default('entrada');

            $table->text('observaciones')->nullable(); // Ej: "Compra de insumos", "Ajuste por merma"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
