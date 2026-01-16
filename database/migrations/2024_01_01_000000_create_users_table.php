<?php

namespace Database\Migrations;

use Framework\Database\Migrations\Migration;

class CreateUsersTable extends Migration {
    public function up() {
        $this->schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down() {
        $this->schema()->drop('users');
    }
}
