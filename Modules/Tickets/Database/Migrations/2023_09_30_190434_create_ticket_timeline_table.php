<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_timeline', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('ticket_id');
            $table->string('type');
            $table->longtext('content');
            $table->json('data')->nullable();
            $table->timestamps();

            // Foreign key constraint to ensure orders are deleted when a user is deleted
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Foreign key constraint to ensure orders are deleted when a user is deleted
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_timeline');
    }
};