<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/news')]
#[Cache(maxage: 3600, public: true, mustRevalidate: true)]
class NewsController extends AbstractController
{

    #[Route('/{id}', name: 'app_news_show', methods: ['GET'])]
    public function show(News $news, NewsRepository $newsRepository): Response
    {
        if (!$news->isPublished()){
            return $this->render('news/list.html.twig', [
                'news_array' => $newsRepository->findBy(['published' => true], ['createdAt' => 'DESC']),
            ]);
        }
        return $this->render('news/show.html.twig', [
            'news_one' => $news,
        ]);
    }

    #[Route('/', name: 'app_news_list', options: ['sitemap' => true], methods: ['GET'])]
    public function list(NewsRepository $newsRepository): Response
    {
        return $this->render('news/list.html.twig', [
            'news_array' => $newsRepository->findBy(['published' => true], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('test_send_emails', name: 'app_send_emails', methods: ['GET'])]
    public function sendEmails(string $name): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'message' => 'Рассылка "'.$name.'" успешно отправлена',
        ]);
    }

}
