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
            $table->tinyInteger('role')->default(2);        
            $table->string('name');
            $table->string('device_type')->nullable();
            $table->string('device_token')->nullable();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('image');
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable(); 
            $table->timestamp('phone_verified_at')->nullable();    
            $table->tinyInteger('status');        
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
