<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 26.04.2017
 * Time: 11:47
 */

namespace AppBundle\Controller\Api;


use Tests\AppBundle\ApiTestCase;

class BattleControllerTest extends ApiTestCase
{
    public function testPOSTCreateBattle() {
        $project    = $this->createProject('my_project');
        $programmer = $this->createProgrammer([
            'nickname' => 'Fred'
        ], 'thomas');

        $data = array(
            'projectId'    => $project->getId(),
            'programmerId' => $programmer->getId()
        );

        $response = $this->client->post('/api/battles', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->debugResponse($response);

        $this->assertEquals(201, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, 'didProgrammerWin');

        $this->asserter()->assertResponsePropertyEquals($response, 'project', $project->getId());
        //$this->asserter()->assertResponsePropertyEquals($response, 'programmer', 'Fred');

        $this->asserter()->assertResponsePropertyEquals($response, '_links.programmer', $this->adjustUri('/api/programmers/Fred'));

        $this->asserter()->assertResponsePropertyEquals($response, 'programmer.nickname', 'Fred');

        // TODO: later
        //$this->assertTrue($response->hasHeader('Location'));
    }

    public function testPOSTBattleValidationErrors()
    {
        // create a programmer owned by someone else
        $this->createUser('someone_else');

        $project    = $this->createProject('my_project');
        $programmer = $this->createProgrammer(['nickname' => 'Fred'], 'someone_else');

        $data = array(
            'projectId'    => null,
            'programmerId' => $programmer->getId()
        );

        $response = $this->client->post('/api/battles', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->debugResponse($response);

        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyExists($response, 'errors.projectId');
        $this->asserter()->assertResponsePropertyEquals($response, 'errors.projectId[0]', 'This value should not be blank.');
        $this->asserter()->assertResponsePropertyEquals($response, 'errors.programmerId[0]', 'This value is not valid.');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->createUser('thomas');
    }
}