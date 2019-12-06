<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlrMessagesTable extends Migration
{
    public function __construct()
    {
        $prefix = config('galera.table_prefix');
        $this->tableName = $prefix.'messages';
        $this->conversationTable = $prefix.'conversations';
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('message');
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('conversation_id');
            $table->uuid('reffer_to')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('conversation_id')->references('id')->on($this->conversationTable)->onDelete('cascade');
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
