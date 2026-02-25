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
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->uuid('folder_id')->nullable(); 
            $table->foreign('folder_id')
                  ->references('id')
                  ->on('folders')
                  ->nullOnDelete(); 
            $table->string('original_name');
            $table->string('stored_name'); 
            $table->string('disk')->default('local'); 
            $table->string('path'); 
            $table->string('mime_type'); 
            $table->unsignedBigInteger('size'); 
            $table->integer('year'); 
            $table->integer('month'); 
            $table->timestamps(); 
         
            $table->index(['user_id', 'year', 'month']);
            $table->index(['folder_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};