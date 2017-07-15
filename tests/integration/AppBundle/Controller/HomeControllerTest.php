<?php

namespace Tests\Integration\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }
    
    /** @test */
    public function indexPageGet()
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /** @test */
    public function indexPageShouldReturnUTF8Json()
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('content-type'));
        $this->assertEquals('UTF-8', $this->client->getResponse()->getCharset());
    }

    /** @test */
    public function indexPageShouldOnlySupportGetMethod()
    {
        $crawler = $this->client->request('POST', '/');
        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
    }
}
