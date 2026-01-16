<?php

namespace Tests;

class TestCase {
    protected $app;

    public function __construct() {
        $this->app = app();
    }

    public function setUp() {
        // Setup test environment
    }

    public function tearDown() {
        // Cleanup
    }

    protected function call($method, $uri, $data = []) {
        return $this->app->handle($method, $uri, [], $data, $_SERVER, $_COOKIE);
    }

    protected function get($uri) {
        return $this->call('GET', $uri);
    }

    protected function post($uri, $data = []) {
        return $this->call('POST', $uri, $data);
    }

    protected function put($uri, $data = []) {
        return $this->call('PUT', $uri, $data);
    }

    protected function delete($uri) {
        return $this->call('DELETE', $uri);
    }

    protected function assertEquals($expected, $actual, $message = '') {
        assert($expected === $actual, $message);
    }

    protected function assertTrue($value, $message = '') {
        assert($value === true, $message);
    }

    protected function assertFalse($value, $message = '') {
        assert($value === false, $message);
    }

    protected function assertNotNull($value, $message = '') {
        assert($value !== null, $message);
    }

    protected function assertNull($value, $message = '') {
        assert($value === null, $message);
    }
}
