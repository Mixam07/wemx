<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_pluses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('slug')->unique();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('page_pluses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_pluses');
    }
};
