<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase {
    public function testHomePageLoads() {
        $response = $this->get('/');
        
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testLoginPageLoads() {
        $response = $this->get('/login');
        
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRegisterPageLoads() {
        $response = $this->get('/register');
        
        $this->assertEquals(200, $response->getStatusCode());
    }
}
