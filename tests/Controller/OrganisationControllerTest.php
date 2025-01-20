<?php

namespace App\Test\Controller;

use App\Entity\Organisation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrganisationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/organisation/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Organisation::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Organisation index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'organisation[name]' => 'Testing',
            'organisation[address]' => 'Testing',
            'organisation[coordinates]' => 'Testing',
            'organisation[type]' => 'Testing',
            'organisation[main_photo]' => 'Testing',
            'organisation[additional_photos]' => 'Testing',
            'organisation[description]' => 'Testing',
            'organisation[contacts]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Organisation();
        $fixture->setName('My Title');
        $fixture->setAddress('My Title');
        $fixture->setCoordinates('My Title');
        $fixture->setType('My Title');
        $fixture->setMain_photo('My Title');
        $fixture->setAdditional_photos('My Title');
        $fixture->setDescription('My Title');
        $fixture->setContacts('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Organisation');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Organisation();
        $fixture->setName('Value');
        $fixture->setAddress('Value');
        $fixture->setCoordinates('Value');
        $fixture->setType('Value');
        $fixture->setMain_photo('Value');
        $fixture->setAdditional_photos('Value');
        $fixture->setDescription('Value');
        $fixture->setContacts('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'organisation[name]' => 'Something New',
            'organisation[address]' => 'Something New',
            'organisation[coordinates]' => 'Something New',
            'organisation[type]' => 'Something New',
            'organisation[main_photo]' => 'Something New',
            'organisation[additional_photos]' => 'Something New',
            'organisation[description]' => 'Something New',
            'organisation[contacts]' => 'Something New',
        ]);

        self::assertResponseRedirects('/organisation/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getAddress());
        self::assertSame('Something New', $fixture[0]->getCoordinates());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getMain_photo());
        self::assertSame('Something New', $fixture[0]->getAdditional_photos());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getContacts());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Organisation();
        $fixture->setName('Value');
        $fixture->setAddress('Value');
        $fixture->setCoordinates('Value');
        $fixture->setType('Value');
        $fixture->setMain_photo('Value');
        $fixture->setAdditional_photos('Value');
        $fixture->setDescription('Value');
        $fixture->setContacts('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/organisation/');
        self::assertSame(0, $this->repository->count([]));
    }
}
