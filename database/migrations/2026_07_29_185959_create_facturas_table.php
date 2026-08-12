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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();

            // Relación con el pedido correspondiente
            $table->foreignId('pedido_id')
                ->constrained('pedidos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('numero_factura', 50)->unique();
            
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2)->default(0.00); // Ej: IVA
            $table->decimal('total', 10, 2);

            // Método de pago utilizado
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'otros'])->default('efectivo');

            // Estado del cobro
            $table->enum('estado_pago', ['pagado', 'pendiente', 'anulado'])->default('pagado');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
