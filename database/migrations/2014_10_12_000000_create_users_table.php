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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->integer('id_tipo_documento');
            $table->string('numero_documento');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('captcha')->nullable();
            $table->foreignId('rol_id')->nullable()->constrained('roles');
            $table->rememberToken();
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('id_genero')->nullable();
            $table->string('id_codigo_pais')->nullable();
            $table->string('celular')->nullable();
            $table->integer('id_estado_civil')->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('estado')->nullable()->default(0);
            $table->integer('login_attempts')->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
