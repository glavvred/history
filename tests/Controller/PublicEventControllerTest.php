<?php

namespace App\Test\Controller;

use App\Entity\PublicEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicEventControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/public/event/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(PublicEvent::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PublicEvent index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'public_event[name]' => 'Testing',
            'public_event[startDate]' => 'Testing',
            'public_event[duration]' => 'Testing',
            'public_event[address]' => 'Testing',
            'public_event[link]' => 'Testing',
            'public_event[description]' => 'Testing',
            'public_event[mainPhoto]' => 'Testing',
            'public_event[additionalPhotos]' => 'Testing',
            'public_event[prequisites]' => 'Testing',
            'public_event[toll]' => 'Testing',
            'public_event[owner]' => 'Testing',
            'public_event[category]' => 'Testing',
            'public_event[region]' => 'Testing',
            'public_event[collections]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new PublicEvent();
        $fixture->setName('My Title');
        $fixture->setStartDate('My Title');
        $fixture->setDuration('My Title');
        $fixture->setAddress('My Title');
        $fixture->setLink('My Title');
        $fixture->setDescription('My Title');
        $fixture->setMainPhoto('My Title');
        $fixture->setAdditionalPhotos('My Title');
        $fixture->setPrequisites('My Title');
        $fixture->setToll('My Title');
        $fixture->setOwner('My Title');
        $fixture->setCategory('My Title');
        $fixture->setRegion('My Title');
        $fixture->setCollections('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PublicEvent');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new PublicEvent();
        $fixture->setName('Value');
        $fixture->setStartDate('Value');
        $fixture->setDuration('Value');
        $fixture->setAddress('Value');
        $fixture->setLink('Value');
        $fixture->setDescription('Value');
        $fixture->setMainPhoto('Value');
        $fixture->setAdditionalPhotos('Value');
        $fixture->setPrequisites('Value');
        $fixture->setToll('Value');
        $fixture->setOwner('Value');
        $fixture->setCategory('Value');
        $fixture->setRegion('Value');
        $fixture->setCollections('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'public_event[name]' => 'Something New',
            'public_event[startDate]' => 'Something New',
            'public_event[duration]' => 'Something New',
            'public_event[address]' => 'Something New',
            'public_event[link]' => 'Something New',
            'public_event[description]' => 'Something New',
            'public_event[mainPhoto]' => 'Something New',
            'public_event[additionalPhotos]' => 'Something New',
            'public_event[prequisites]' => 'Something New',
            'public_event[toll]' => 'Something New',
            'public_event[owner]' => 'Something New',
            'public_event[category]' => 'Something New',
            'public_event[region]' => 'Something New',
            'public_event[collections]' => 'Something New',
        ]);

        self::assertResponseRedirects('/public/event/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getStartDate());
        self::assertSame('Something New', $fixture[0]->getDuration());
        self::assertSame('Something New', $fixture[0]->getAddress());
        self::assertSame('Something New', $fixture[0]->getLink());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getMainPhoto());
        self::assertSame('Something New', $fixture[0]->getAdditionalPhotos());
        self::assertSame('Something New', $fixture[0]->getPrequisites());
        self::assertSame('Something New', $fixture[0]->getToll());
        self::assertSame('Something New', $fixture[0]->getOwner());
        self::assertSame('Something New', $fixture[0]->getCategory());
        self::assertSame('Something New', $fixture[0]->getRegion());
        self::assertSame('Something New', $fixture[0]->getCollections());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new PublicEvent();
        $fixture->setName('Value');
        $fixture->setStartDate('Value');
        $fixture->setDuration('Value');
        $fixture->setAddress('Value');
        $fixture->setLink('Value');
        $fixture->setDescription('Value');
        $fixture->setMainPhoto('Value');
        $fixture->setAdditionalPhotos('Value');
        $fixture->setPrequisites('Value');
        $fixture->setToll('Value');
        $fixture->setOwner('Value');
        $fixture->setCategory('Value');
        $fixture->setRegion('Value');
        $fixture->setCollections('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/public/event/');
        self::assertSame(0, $this->repository->count([]));
    }
}
