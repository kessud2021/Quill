<?php

namespace Database\Migrations;

use Framework\Database\Migrations\Migration;

class CreatePostsTable extends Migration {
    public function up() {
        $this->schema()->create('posts', function ($table) {
            $table->increments('id');
            $table->string('title');
            $table->text('content');
            $table->integer('user_id');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
        });
    }

    public function down() {
        $this->schema()->drop('posts');
    }
}
