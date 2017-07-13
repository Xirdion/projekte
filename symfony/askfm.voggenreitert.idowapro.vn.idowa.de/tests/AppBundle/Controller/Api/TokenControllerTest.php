<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 25.04.2017
 * Time: 16:16
 */

namespace AppBundle\Controller\Api;


use Tests\AppBundle\ApiTestCase;

class TokenControllerTest extends ApiTestCase
{
    public function testPOSTCreateToken() {
        $this->createUser('thomas', 'I<3Pizza');

        $response = $this->client->post('/api/tokens', [
            'auth' => ['thomas', 'I<3Pizza']
        ]);

        $this->debugResponse($response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, 'token');
    }

    public function testPOSTTokenInvalidCredentials() {
        $this->createUser('thomas', 'I<3Pizza');

        $response = $this->client->post('/api/tokens', [
            'auth' => ['thomas', 'IH8Pizza']
        ]);

        $this->debugResponse($response);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->asserter()->assertResponsePropertyEquals($response, 'type', 'about:blank');
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Unauthorized');
        $this->asserter()->assertResponsePropertyEquals($response, 'detail', 'Invalid credentials.');
    }
}