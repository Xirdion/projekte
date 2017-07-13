<?php

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Battle\BattleManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Tests\AppBundle\ApiTestCase;

/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 19.04.2017
 * Time: 16:07
 */
class ProgrammerControllerTest extends ApiTestCase
{
    public function testPOSTProgrammerWorks() {

        $data = array(
            'nickname' => 'ObjectOrienter',
            'avatarNumber' => 4,
            'tagLine' => 'a test div!'
        );

        // 1) Create a programmer resource
        $response = $this->client->post('/api/programmers', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertStringEndsWith('/api/programmers/ObjectOrienter', $response->getHeader('Location')[0]);

        $finishData = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('nickname', $finishData);
        $this->assertEquals('ObjectOrienter', $finishData['nickname']);

        $this->assertEquals('application/vnd.codebattles+json', $response->getHeader('Content-Type')[0]);
    }

    public function testGETProgrammer() {
        $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3
        ));

        $response = $this->client->get('/api/programmers/UnitTester', [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $this->asserter()->assertResponsePropertiesExist($response, array(
            'nickname',
            'avatarNumber',
            'powerLevel',
            'tagLine'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'nickname', 'UnitTester');
        $this->asserter()->assertResponsePropertyEquals($response, '_links.self', $this->adjustUri('/api/programmers/UnitTester'));
    }

    public function testGETProgrammerDeep() {
        $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3
        ));

        $response = $this->client->get('/api/programmers/UnitTester?deep=1', [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->debugResponse($response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'user.username'
        ));
    }

    public function testGETProgrammerCollection() {
        $this->createProgrammer(array(
            'nickname'     => 'UnitTester',
            'avatarNumber' => 3
        ));
        $this->createProgrammer(array(
            'nickname'     => 'CowboyCoder',
            'avatarNumber' => 5
        ));

        $response = $this->client->get('/api/programmers', [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyIsArray($response, 'items');
        $this->asserter()->assertResponsePropertyCount($response, 'items', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'items[1].nickname', 'CowboyCoder');
    }

    public function testPUTProgrammer() {
        $this->createProgrammer(array(
            'nickname'     => 'CowboyCoder',
            'avatarNumber' => 5,
            'tagLine'      => 'foo'
        ));

        $data = array(
            'nickname'     => 'CowgirlCoder',
            'avatarNumber' => 2,
            'tagLine'      => 'foo'
        );

        $response = $this->client->put('/api/programmers/CowboyCoder', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'avatarNumber', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'nickname', 'CowboyCoder');
    }

    public function testDELETEProgrammer() {
        $this->createProgrammer(array(
            'nickname'     => 'UnitTester',
            'avatarNumber' => 3
        ));
        $response = $this->client->delete('/api/programmers/UnitTester', [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testPATCHProgrammer() {
        $this->createProgrammer(array(
            'nickname'     => 'CowboyCoder',
            'avatarNumber' => 5,
            'tagLine'      => 'foo'
        ));

        $data = array(
            'tagLine' => 'bar'
        );

        $response = $this->client->patch('/api/programmers/CowboyCoder', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'tagLine', 'bar');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->createUser('thomas');
    }

    public function testValidationErrors() {

        $data = array(
            'avatarNumber' => 2,
            'tagLine' => 'I\' from a test!'
        );

        // 1) Create a programmer resource
        $response = $this->client->post('/api/programmers', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'type',
            'title',
            'errors'
        ));
        $this->asserter()->assertResponsePropertyExists($response, 'errors.nickname');
        $this->asserter()->assertResponsePropertyEquals($response, 'errors.nickname[0]', 'Please enter a clever nickname');
        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'errors.avatarNumber');
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
    }

    public function testInvalidJson() {
        $invalidBody = <<<EOF
{
    "nickname": "JohnnyRobot",
    "avatarNumber": "2
    "tagLine": "I'm from a test!"
}
EOF;
        $response = $this->client->post('/api/programmers', [
            'body' => $invalidBody,
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyContains($response, 'type', 'invalid_body_format');
    }

    public function test404Exception() {
        $response = $this->client->get('/api/programmers/fake', [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->asserter()->assertResponsePropertyEquals($response, 'type', 'about:blank');
        $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Not Found');
        $this->asserter()->assertResponsePropertyEquals($response, 'detail', 'No programmer found with nickname "fake"');
    }

    public function testGETProgrammersCollectionPaginated() {
        $this->createProgrammer(array(
            'nickname'     => 'willnotmatch',
            'avatarNumber' => 5
        ));
        for ($i=0; $i<25; $i++) {
            $this->createProgrammer(array(
                'nickname'     => 'Programmer' . $i,
                'avatarNumber' => 3
            ));
        }

        //page1
        $response = $this->client->get('/api/programmers?filter=programmer', [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        //$this->debugResponse($response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'items[5].nickname', 'Programmer5');
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 10);
        $this->asserter()->assertResponsePropertyEquals($response, 'total', 25);
        $this->asserter()->assertResponsePropertyExists($response, '_links.next');

        $nextLink = $this->asserter()->readResponseProperty($response, '_links.next');
        $response = $this->client->get($nextLink, [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'items[5].nickname', 'Programmer15');
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 10);

        $lastLink = $this->asserter()->readResponseProperty($response, '_links.last');
        $response = $this->client->get($lastLink, [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'items[4].nickname', 'Programmer24');
        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'items[5].nickname');
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 5);
    }

    public function testRequiresAuthentication() {
        $response = $this->client->post('/api/programmers', [
            'body' => '[]'
        ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testBadToken()
    {
        $response = $this->client->post('/api/programmers', [
            'body' => '[]',
            'headers' => [
                'Authorization' => 'Bearer WRONG'
            ]
        ]);

        $this->debugResponse($response);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
    }

    public function testFollowProgrammerBattleLink() {
        $programmer = $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3
        ));
        $project = $this->createProject('cool_project');

        /** @var BattleManager $battleManager */
        $battleManager = $this->getService('battle.battle_manager');
        $battleManager->battle($programmer, $project);
        $battleManager->battle($programmer, $project);
        $battleManager->battle($programmer, $project);

        $response = $this->client->get('/api/programmers/UnitTester', [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->debugResponse($response);

        $url = $this->asserter()->readResponseProperty($response, '_links.battles');
        $response = $this->client->get($url, [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->debugResponse($response);

        $this->asserter()->assertResponsePropertyExists($response, 'items');
    }

    public function testEditTagline()
    {
        $this->createProgrammer(array(
            'nickname'     => 'UnitTester',
            'avatarNumber' => 3,
            'tagLine'      => 'The original UnitTester'
        ));

        $response = $this->client->put('/api/programmers/UnitTester/tagline', [
            'headers' => $this->getAuthorizationHeaders('thomas'),
            'body'    => 'New Tag Line'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'tagLine', 'New Tag Line');
    }

    public function testPowerUp()
    {
        $this->createProgrammer(array(
            'nickname'     => 'UnitTester',
            'avatarNumber' => 3,
            'powerLevel'   => 10
        ));

        $response = $this->client->post('/api/programmers/UnitTester/powerup', [
            'headers' => $this->getAuthorizationHeaders('thomas')
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $powerLevel = $this->asserter()->readResponseProperty($response, 'powerLevel');
        $this->assertNotEquals(10, $powerLevel, 'The level should change');
    }
}