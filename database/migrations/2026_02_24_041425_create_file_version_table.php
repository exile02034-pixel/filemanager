<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('file_id')
                  ->constrained('files')
                  ->cascadeOnDelete();

            $table->string('stored_name'); 
            $table->string('path');
            $table->unsignedBigInteger('size');
            $table->string('mime_type');
            $table->integer('version_number');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_versions');
    }
};