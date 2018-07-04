<?php

namespace Tests\Functional;

use Tests\Functional\BaseTestCase;

class NumberTest extends BaseTestCase
{

    public function testGetHomepageWithoutName()
    {
        $response = $this->runApp('GET', '/number_evolution');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Number Evolution Test', (string)$response->getBody());
        $this->assertContains('1', (string)$response->getBody());
    }
}
