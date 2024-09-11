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
        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('affiliate_id');
            $table->float('amount');
            $table->string('status');
            $table->string('gateway');
            $table->string('address')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamps();

            // Foreign key constraint to ensure orders are deleted when a user is deleted
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Foreign key constraint to ensure affiliate invites are deleted when a affiliate is deleted
            $table->foreign('affiliate_id')->references('id')->on('affiliates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliate_payouts');
    }
};
