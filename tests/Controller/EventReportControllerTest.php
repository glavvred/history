<?php

namespace App\Test\Controller;

use App\Entity\EventReport;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventReportControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/event/report/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(EventReport::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('EventReport index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'event_report[name]' => 'Testing',
            'event_report[startDate]' => 'Testing',
            'event_report[duration]' => 'Testing',
            'event_report[address]' => 'Testing',
            'event_report[link]' => 'Testing',
            'event_report[description]' => 'Testing',
            'event_report[mainPhoto]' => 'Testing',
            'event_report[additionalPhotos]' => 'Testing',
            'event_report[prequisites]' => 'Testing',
            'event_report[toll]' => 'Testing',
            'event_report[reporter]' => 'Testing',
            'event_report[category]' => 'Testing',
            'event_report[region]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new EventReport();
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
        $fixture->setReporter('My Title');
        $fixture->setCategory('My Title');
        $fixture->setRegion('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('EventReport');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new EventReport();
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
        $fixture->setReporter('Value');
        $fixture->setCategory('Value');
        $fixture->setRegion('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'event_report[name]' => 'Something New',
            'event_report[startDate]' => 'Something New',
            'event_report[duration]' => 'Something New',
            'event_report[address]' => 'Something New',
            'event_report[link]' => 'Something New',
            'event_report[description]' => 'Something New',
            'event_report[mainPhoto]' => 'Something New',
            'event_report[additionalPhotos]' => 'Something New',
            'event_report[prequisites]' => 'Something New',
            'event_report[toll]' => 'Something New',
            'event_report[reporter]' => 'Something New',
            'event_report[category]' => 'Something New',
            'event_report[region]' => 'Something New',
        ]);

        self::assertResponseRedirects('/event/report/');

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
        self::assertSame('Something New', $fixture[0]->getReporter());
        self::assertSame('Something New', $fixture[0]->getCategory());
        self::assertSame('Something New', $fixture[0]->getRegion());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new EventReport();
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
        $fixture->setReporter('Value');
        $fixture->setCategory('Value');
        $fixture->setRegion('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/event/report/');
        self::assertSame(0, $this->repository->count([]));
    }
}
