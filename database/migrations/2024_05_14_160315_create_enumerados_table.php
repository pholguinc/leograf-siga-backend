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
        Schema::create('enumerados', function (Blueprint $table) {
            $table->id();
            $table->integer('id_tipo_enumerado');
            $table->integer('descripcion');
            $table->boolean('estado')->nullable(false)->default(0);
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enumerados');
    }
};
