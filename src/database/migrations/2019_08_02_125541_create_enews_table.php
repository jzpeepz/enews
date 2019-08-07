<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('day')->nullable();
            $table->string('subject');
            $table->string('articles');
            $table->mediumText('html');
            $table->string('sponsored')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('send_at')->nullable();
            $table->integer('campaign_id')->nullable();
            $table->mediumText('text');
            $table->string('preview_text');
            $table->string('template');
            $table->string('sponsor_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('enews');
    }
}
