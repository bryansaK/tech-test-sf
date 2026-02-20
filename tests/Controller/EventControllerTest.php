<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }

    public function testGetEventsReturns200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/events');

        $this->assertResponseIsSuccessful();
    }

    public function testGetEventsReturnsJsonWithDataAndPaginationKeys(): void
    {
        $client = static::createClient();
        $client->request('GET', '/events');

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('pagination', $data);
    }

    public function testGetEventsWithPaginationParams(): void
    {
        $client = static::createClient();
        $client->request('GET', '/events?page=1&limit=5');

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $data);
    }

    public function testGetEventDetailReturns404ForUnknownId(): void
    {
        $client = static::createClient();
        $client->request('GET', '/events/0000000');

        $this->assertResponseStatusCodeSame(404);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }
}
