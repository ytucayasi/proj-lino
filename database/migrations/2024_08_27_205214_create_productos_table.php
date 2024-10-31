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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->string('nombre', 150);
            $table->text('descripcion');
            $table->string('imagen', 250)->nullable();
            $table->string('marca', 250);
            $table->integer('cantidad_base');
            $table->float('costo');
            $table->float('precio');
            $table->float('precio_oferta');
            $table->timestamps();
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
