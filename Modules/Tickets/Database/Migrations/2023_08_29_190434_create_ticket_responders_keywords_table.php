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
        Schema::create('ticket_responder_keywords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('responder_id');
            $table->json('keywords');
            $table->enum('method', ['contains', 'containsAll'])->default('contains');
            $table->timestamps();

            $table->foreign('responder_id')->references('id')->on('ticket_responders')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_responder_keywords');
    }
};