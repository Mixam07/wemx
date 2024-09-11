<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_plus_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('page_pluses')->onDelete('cascade');
            $table->string('locale', 10)->default('en');
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_plus_translations');
    }
};
