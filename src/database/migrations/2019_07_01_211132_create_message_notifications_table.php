<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageNotificationsTable extends Migration
{
    public function __construct()
    {
        $prefix = config('galera.table_prefix');
        $this->tableName = $prefix.'message_notifications';
        $this->conversationTable = $prefix.'conversations';
        $this->messageTable = $prefix.'messages';
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->unsignedBigInteger('to_user_id');
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('conversation_id');
            $table->dateTime('read_at')->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on($this->messageTable)->onDelete('cascade');
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
