<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlrMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('glr_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('message');
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('reffer_to')->nullable();
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('glr_conversations')->onDelete('cascade');
            $table->foreign('reffer_to')->references('id')->on('glr_messages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
