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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('adress')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('recovery_code')->nullable();
            $table->timestamp('code_expires_at')->nullable();
            $table->string('rol');
            $table->string('numero_tarjeta', 20)->nullable();
            $table->string('nombre_titular', 100)->nullable();
            $table->string('cvv', 3)->nullable();
            $table->string('fecha_vencimiento')->nullable();
            $table->rememberToken();
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
