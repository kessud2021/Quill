<?php

namespace Framework\Database\Migrations;

use Framework\Database\Schema\Builder;

class Migration {
    protected $schemaBuilder;

    public function __construct() {
        $this->schemaBuilder = new Builder(app('db')->connection());
    }

    protected function schema() {
        return $this->schemaBuilder;
    }

    public function up() {
    }

    public function down() {
    }
}
