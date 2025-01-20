<?php

namespace App\Test\Controller;

use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewsControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/news/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(News::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('News index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'news[name]' => 'Testing',
            'news[photo]' => 'Testing',
            'news[createdAt]' => 'Testing',
            'news[description]' => 'Testing',
            'news[url]' => 'Testing',
            'news[published]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new News();
        $fixture->setName('My Title');
        $fixture->setPhoto('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setDescription('My Title');
        $fixture->setUrl('My Title');
        $fixture->setPublished('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('News');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new News();
        $fixture->setName('Value');
        $fixture->setPhoto('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setDescription('Value');
        $fixture->setUrl('Value');
        $fixture->setPublished('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'news[name]' => 'Something New',
            'news[photo]' => 'Something New',
            'news[createdAt]' => 'Something New',
            'news[description]' => 'Something New',
            'news[url]' => 'Something New',
            'news[published]' => 'Something New',
        ]);

        self::assertResponseRedirects('/news/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getPhoto());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getUrl());
        self::assertSame('Something New', $fixture[0]->getPublished());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new News();
        $fixture->setName('Value');
        $fixture->setPhoto('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setDescription('Value');
        $fixture->setUrl('Value');
        $fixture->setPublished('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/news/');
        self::assertSame(0, $this->repository->count([]));
    }
}
