<?php

namespace Tests\Functional;

use Tests\Functional\BaseTestCase;

class HomepageTest extends BaseTestCase
{

    public function testGetHomepageWithoutName()
    {
        $response = $this->runApp('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Webolution', (string)$response->getBody());
        $this->assertNotContains('Hello', (string)$response->getBody());
    }

    public function testPostHomepageNotAllowed()
    {
        $response = $this->runApp('POST', '/', ['test']);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertContains('Method not allowed', (string)$response->getBody());
    }
}
