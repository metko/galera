<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlrConversationUserTable extends Migration
{
    public function __construct()
    {
        $prefix = config('galera.table_prefix');
        $this->tableName = $prefix.'conversation_user';
        $this->conversationTable = $prefix.'conversations';
        $this->messageTable = $prefix.'messages';
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('conversation_id');
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on($this->conversationTable)->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
