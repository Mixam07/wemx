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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('department_id');
            $table->string('subject');
            $table->boolean('is_subscribed')->default(false);
            $table->boolean('is_open')->default(true);
            $table->boolean('is_locked')->default(false);
            $table->string('webhook_url')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();

            // Foreign key constraint to ensure orders are deleted when a user is deleted
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');

            $table->foreign('department_id')->references('id')->on('ticket_departments')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};