<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\EventReport;
use App\Entity\News;
use App\Entity\Region;
use App\Entity\User;
use App\Form\EventReportType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/report')]
class EventReportController extends AbstractController
{
    #[Route('/', name: 'app_event_report_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security, SluggerInterface $slugger): Response
    {
        $eventReport = new EventReport();
        $form = $this->createForm(EventReportType::class, $eventReport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $mainPhoto */
            $mainPhoto = $form->get('mainPhotoFile')->getData();

            if ($mainPhoto) {
                $originalFilename = pathinfo($mainPhoto->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mainPhoto->guessExtension();

                try {
                    $mainPhoto->move('upload/images', $newFilename);
                } catch (FileException $e) {
                    var_dump($e->getMessage());
                }

                $eventReport->setMainPhoto($newFilename);
            }

            /** @var UploadedFile $additionalPhoto */
            $additionalPhotos = $form->get('additionalPhotosFiles')->getData();

            if (!empty($additionalPhotos)) {
                $additionalPhotoLinks = [];

                foreach ($additionalPhotos as $additionalPhoto) {
                    $originalFilename = pathinfo($additionalPhoto->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $additionalPhoto->guessExtension();

                    try {
                        $additionalPhoto->move('upload/images', $newFilename);
                    } catch (FileException $e) {
                        var_dump($e->getMessage());
                    }
                    $additionalPhotoLinks[] = $newFilename;
                }
                $eventReport->setAdditionalPhotos($additionalPhotoLinks);
            }

            if (true === $this->isGranted('ROLE_USER')) {
                $reporter = $security->getUser();
            } else {
                $reporter = $entityManager->getRepository(User::class)->findOneBy(['email' => 'anonymous@reporter']);
            }
            $eventReport->setReporter($reporter);
            $entityManager->persist($eventReport);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_report_thanks', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event_report/new.html.twig', [
            'user' => $this->getUser(),
            'event_report' => $eventReport,
            'form' => $form,
        ]);
    }

    #[Route('/api', name: 'app_event_report_api', methods: ['POST'])]
    public function newFromApi(Request $request, EntityManagerInterface $entityManager, Security $security, SluggerInterface $slugger): Response
    {
        $data = $request->getContent();
        $data = json_decode($data);
        if (empty($data)) {
            return $this->json(['bad' => 'redo'], Response::HTTP_BAD_REQUEST);
        }

        $startDate = DateTime::createFromFormat('Y-m-d', $data->startDate);
        if (empty($startDate)) {
            return $this->json(['bad' => 'Y-m-d'], Response::HTTP_BAD_REQUEST);
        }

        $eventReport = new EventReport();
        $reporter = $entityManager->getRepository(User::class)->findOneBy(['email' => 'anonymous@reporter']);
        $eventReport->setReporter($reporter);
        $eventReport->setName($data->name);
        $eventReport->setLink($data->link);
        $eventReport->setStartDate($startDate);
        $eventReport->setAddress($data->address);
        $eventReport->setCategory($entityManager->getRepository(Category::class)->find(5));
        $eventReport->setRegion($entityManager->getRepository(Region::class)->find(1));
        $eventReport->setMainPhoto('150-677287d0e0de6.jpg');
        $entityManager->persist($eventReport);
        $entityManager->flush();

        return $this->json(['good' => 'thanks'], Response::HTTP_ACCEPTED);

    }

    #[Route('/thanks', name: 'app_event_report_thanks', methods: ['GET'])]
    public function thanks(): Response
    {
        return $this->render('event_report/thanks.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
