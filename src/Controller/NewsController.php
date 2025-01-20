<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/news')]
class NewsController extends AbstractController
{

    #[Route('/{id}', name: 'app_news_show', methods: ['GET'])]
    public function show(News $news): Response
    {

        return $this->render('news/show.html.twig', [
            'news_one' => $news,
        ]);
    }

    #[Route('/', name: 'app_news_list', options: ['sitemap' => true], methods: ['GET'])]
    public function list(NewsRepository $newsRepository): Response
    {
        return $this->render('news/list.html.twig', [
            'news_array' => $newsRepository->findBy([], ['createdAt' => 'DESC']),
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
