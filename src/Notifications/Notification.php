<?php

namespace Framework\Notifications;

abstract class Notification {
    protected $data = [];

    public function __construct($data = []) {
        $this->data = $data;
    }

    abstract public function via();

    abstract public function toMail();

    abstract public function toDatabase();

    public function getData() {
        return $this->data;
    }
}
