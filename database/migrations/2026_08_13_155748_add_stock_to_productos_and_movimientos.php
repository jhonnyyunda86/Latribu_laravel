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
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('disponible');
        });

        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->foreignId('producto_id')->nullable()->after('inventario_id')->constrained('productos')->cascadeOnUpdate()->nullOnDelete();
            $table->integer('cantidad')->default(1)->after('tipo_movimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('stock');
        });

        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->dropColumn(['producto_id', 'cantidad']);
        });
    }
};
