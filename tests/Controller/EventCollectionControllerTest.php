<?php

namespace App\Test\Controller;

use App\Entity\EventCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventCollectionControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/event/collection/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(EventCollection::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('EventCollection index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'event_collection[name]' => 'Testing',
            'event_collection[description]' => 'Testing',
            'event_collection[main_photo]' => 'Testing',
            'event_collection[events]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new EventCollection();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setMain_photo('My Title');
        $fixture->setEvents('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('EventCollection');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new EventCollection();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setMain_photo('Value');
        $fixture->setEvents('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'event_collection[name]' => 'Something New',
            'event_collection[description]' => 'Something New',
            'event_collection[main_photo]' => 'Something New',
            'event_collection[events]' => 'Something New',
        ]);

        self::assertResponseRedirects('/event/collection/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getMain_photo());
        self::assertSame('Something New', $fixture[0]->getEvents());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new EventCollection();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setMain_photo('Value');
        $fixture->setEvents('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/event/collection/');
        self::assertSame(0, $this->repository->count([]));
    }
}
