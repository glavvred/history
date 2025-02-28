<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\EventCollection;
use App\Entity\News;
use App\Entity\Organisation;
use App\Entity\PublicEvent;
use App\Repository\CategoryRepository;
use App\Repository\EventCollectionRepository;
use App\Repository\NewsRepository;
use App\Repository\OrganisationRepository;
use App\Repository\PublicEventRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Cache(maxage: 3600, public: true, mustRevalidate: true)]
class SitemapController extends AbstractController
{
    public function __construct(PublicEventRepository     $publicEventRepository,
                                EventCollectionRepository $eventCollectionRepository,
                                CategoryRepository        $categoryRepository,
                                NewsRepository            $newsRepository,
                                OrganisationRepository    $organisationRepository,
    )
    {
        $this->publicEventRepository = $publicEventRepository;
        $this->eventCollectionRepository = $eventCollectionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->newsRepository = $newsRepository;
        $this->organisationRepository = $organisationRepository;
    }

    #[Route('/sitemap.xml', name: 'sitemap')]
    public function index(): Response
    {
        //static
        $urls = [
            [
                'loc' => $this->generateUrl('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => (new DateTime())->format('Y-m-d'),
                'priority' => 1
            ],
            [
                'loc' => $this->generateUrl('app_about_archeo', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => (new DateTime())->format('Y-m-d'),
                'priority' => '0.7'
            ],
            [
                'loc' => $this->generateUrl('app_about', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => (new DateTime())->format('Y-m-d'),
                'priority' => '0.7'
            ],
            [
                'loc' => $this->generateUrl('app_partners', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => (new DateTime())->format('Y-m-d'),
                'priority' => '0.7'
            ],
            [
                'loc' => $this->generateUrl('app_map', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => (new DateTime())->format('Y-m-d'),
                'priority' => '0.7'
            ],
            [
                'loc' => $this->generateUrl('app_public_event_best', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => (new DateTime())->format('Y-m-d'),
                'priority' => '0.7'
            ],
            [
                'loc' => $this->generateUrl('app_news_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => (new DateTime())->format('Y-m-d'),
                'priority' => '0.7'
            ],
        ];

        //event collection
        $eventCollections = $this->eventCollectionRepository->findAll();
        foreach ($eventCollections as $eventCollection) {
            /* @var EventCollection $eventCollection */
            $urls[] = [
                'loc' => $this->generateUrl(
                    'app_event_collection_show',
                    ['id' => $eventCollection->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => (new DateTime())->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        }

        //category
        $categories = $this->categoryRepository->findAll();
        foreach ($categories as $category) {
            /* @var Category $category */
            $urls[] = [
                'loc' => $this->generateUrl(
                    'app_event_list',
                    ['category' => $category->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => (new DateTime())->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        }

        //news
        $news = $this->newsRepository->findAll();
        foreach ($news as $newsItem) {
            /* @var News $newsItem */
            $urls[] = [
                'loc' => $this->generateUrl(
                    'app_news_show',
                    ['id' => $newsItem->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => $newsItem->getCreatedAt()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        }

        //event
        $events = $this->publicEventRepository->findAll();
        foreach ($events as $event) {
            /* @var PublicEvent $event */
            $urls[] = [
                'loc' => $this->generateUrl(
                    'app_public_event_show_slug',
                    ['category' => $event->getCategory()->getShort(), 'slug' => $event->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => $event->getCreatedAt()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ];
        }

        //orgs
        $orgs = $this->organisationRepository->findAll();
        foreach ($orgs as $org) {
            /* @var Organisation $org */
            $urls[] = [
                'loc' => $this->generateUrl('app_organisation_show_slug',
                    ['slug' => $org->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => (new DateTime())->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];
        }

        $response = new Response(
            $this->renderView('./sitemap/sitemap.html.twig', ['urls' => $urls]),
            200
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
