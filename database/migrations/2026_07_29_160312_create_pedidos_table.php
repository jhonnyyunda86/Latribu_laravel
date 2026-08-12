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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            // Relación con el cliente (opcional por si es venta rápida)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Relación con la mesa (opcional por si es para llevar/domicilio)
            $table->foreignId('mesa_id')
                ->nullable()
                ->constrained('mesas')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Relación con el mesero/empleado que tomó el pedido
            $table->foreignId('mesero_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->decimal('total', 10, 2)->default(0.00);

            // Estado del pedido
            $table->enum('estado', ['pendiente', 'en_preparacion', 'entregado', 'pagado', 'cancelado'])
                ->default('pendiente');

            // Tipo de pedido
            $table->enum('tipo_pedido', ['mesa', 'llevar', 'domicilio'])
                ->default('mesa');

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
