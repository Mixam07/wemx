<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_plus_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('page_pluses')->onDelete('cascade');
            $table->string('key');
            $table->text('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_plus_metas');
    }
};
