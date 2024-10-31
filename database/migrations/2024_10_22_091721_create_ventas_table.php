<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('direccion');
            $table->string('dni', 8);
            $table->string('celular', length: 9);
            $table->string('nombre_cliente');  // Agregamos nombre del cliente
            $table->string('email')->nullable(); // Email opcional para confirmación
            $table->enum('estado', ['1', '2', '3', '4', '5']) // ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado']
                ->default('1');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
