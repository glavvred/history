<?php

namespace App\Controller;

use App\Entity\OrganisationCategory;
use App\Entity\Organisation;
use App\Form\OrganisationType;
use App\Repository\OrganisationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class OrganisationController extends AbstractController
{
    #[Route('/organisation/', name: 'app_organisation_index', methods: ['GET'])]
    public function index(OrganisationRepository $organisationRepository): Response
    {
        return $this->render('organisation/index.html.twig', [
            'organisation_type' => 'Все',
            'organisations' => $organisationRepository->findBy(['verified' => true]),
        ]);
    }

    #[Route('/organisation/list/{type}', name: 'app_organisation_list', methods: ['GET'])]
    public function list(OrganisationRepository $organisationRepository, EntityManagerInterface $entityManager, ?string $type = null): Response
    {
        $organisationsList = [];

        if (!empty($type)) {
            $typeFound = $entityManager->getRepository(OrganisationCategory::class)->findOneBy(['short' => $type]);

            if (!$typeFound) {
                return $this->render('organisation/index.html.twig', [
                    'flash' => 'У нас пока нет такого типа организаций. Возвращаем вас в общий список',
                    'organisation_type' => 'Все',
                    'organisations' => $organisationRepository->findBy(['verified' => true])
                ]);
            }
            $organisations = $organisationRepository->findBy(['category' => $typeFound, 'verified' => true]);
            foreach ($organisations as $organisation){
                if ($organisation->getMainPhoto()){
                    $organisation->mainPhotoDimensions = getimagesize('upload/images/'.$organisation->getMainPhoto());
                }
                $organisationsList[] = $organisation;
            }
            
            return $this->render('organisation/index.html.twig', [
                'organisation_type' => $typeFound->getName(),
                'organisations' =>  $organisationsList
            ]);
        }

        $organisations = $organisationRepository->findBy(['verified' => true]);
        foreach ($organisations as $organisation){
            if ($organisation->getMainPhoto()){
                $organisation->mainPhotoDimensions = getimagesize('upload/images/'.$organisation->getMainPhoto());
            } 
            $organisationsList[] = $organisation;
        }
        return $this->render('organisation/index.html.twig', [
            'organisation_type' => 'Все',
            'organisations' => $organisationsList,
        ]);
    }

    #[Route('/organisation/new', name: 'app_organisation_new', methods: ['GET', 'POST'])]
    public function new(Request                $request,
                        EntityManagerInterface $entityManager,
                        SluggerInterface       $slugger,
                        OrganisationRepository $organisationRepository): Response
    {
        $organisation = new Organisation();
        $organisation->setVerified(false);

        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $mainPhoto */
            $mainPhoto = $form->get('main_photo')->getData();

            if ($mainPhoto) {
                $originalFilename = pathinfo($mainPhoto->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mainPhoto->guessExtension();

                try {
                    $mainPhoto->move('upload/images/', $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $organisation->setMainPhoto($newFilename);
            }

            /** @var UploadedFile[] $additionalPhotos */
            $additionalPhotos = $form->get('additional_photos')->getData();

            if ($additionalPhotos) {
                $additionalPhotosArray = [];
                foreach ($additionalPhotos as $additionalPhoto) {
                    $originalFilename = pathinfo($additionalPhoto->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $additionalPhoto->guessExtension();

                    try {
                        $additionalPhoto->move('/upload/images/', $newFilename);
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $additionalPhotosArray[] = $newFilename;
                }
                $organisation->setAdditionalPhotos($additionalPhotosArray);
            }

            $entityManager->persist($organisation);
            $entityManager->flush();


            return $this->render('organisation/index.html.twig', [
                'flash' => 'Организация добавлена и отправлена на модерацию. Возвращаем вас в общий список',
                'organisation_type' => 'Все',
                'organisations' => $organisationRepository->findBy(['verified' => true])
            ]);
        }

        return $this->render('organisation/new.html.twig', [
            'organisation' => $organisation,
            'form' => $form,
        ]);
    }

    #[Route('/organisation/{id}', name: 'app_organisation_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Organisation $organisation, OrganisationRepository $organisationRepository): Response
    {
        if (!$organisation->isVerified()) {
            return $this->render('organisation/index.html.twig', [
                'flash' => 'Организации не существует или она еще не прошла модерацию. Возвращаем вас в общий список',
                'organisation_type' => 'Все',
                'organisations' => $organisationRepository->findBy(['verified' => true])
            ]);
        }

        return $this->render('organisation/show.html.twig', [
            'organisation_type' => $organisation->getCategory()->getName(),
            'organisation' => $organisation,
        ]);
    }

    #[Route('/organisation/{slug}', name: 'app_organisation_show_slug', methods: ['GET'])]
    public function showSlug(Organisation $organisation, OrganisationRepository $organisationRepository): Response
    {
        if (!$organisation->isVerified()) {
            return $this->render('organisation/index.html.twig', [
                'flash' => 'Организации не существует или она еще не прошла модерацию. Возвращаем вас в общий список',
                'organisation_type' => 'Все',
                'organisations' => $organisationRepository->findBy(['verified' => true])
            ]);
        }

        return $this->render('organisation/show.html.twig', [
            'organisation_type' => $organisation->getCategory()->getName(),
            'organisation' => $organisation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_organisation_edit', methods: ['GET', 'POST'])]
    public function edit(Request                $request,
                         Organisation           $organisation,
                         EntityManagerInterface $entityManager,
                         Security               $security,
                         SluggerInterface       $slugger): Response
    {
        if (!$security->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if (!$organisation->getOwner()->contains($security->getUser()) && !in_array('ROLE_SUPER_ADMIN', $security->getUser()->getRoles())) {
            return $this->render('organisation/show.html.twig', [
                'flash' => 'Недостаточно прав на редактирование этой организации',
                'organisation_type' => $organisation->getCategory()->getName(),
                'organisation' => $organisation,
            ]);
        }

        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $mainPhoto */
            $mainPhoto = $form->get('main_photo_file')->getData();

            if ($mainPhoto) {
                $originalFilename = pathinfo($mainPhoto->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mainPhoto->guessExtension();

                try {
                    $mainPhoto->move('upload/images/', $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $organisation->setMainPhoto($newFilename);
            }

            /** @var UploadedFile[] $additionalPhotos */
            $additionalPhotos = $form->get('additional_photos_file')->getData();

            if (!empty($additionalPhotos)) {
                $additionalPhotosArray = [];
                foreach ($additionalPhotos as $additionalPhoto) {
                    $originalFilename = pathinfo($additionalPhoto->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $additionalPhoto->guessExtension();

                    try {
                        $additionalPhoto->move('upload/images/', $newFilename);
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $additionalPhotosArray[] = $newFilename;
                }
                $organisation->setAdditionalPhotos($additionalPhotosArray);
            }

            if (!$security->getUser()->isAdmin()) {
                $organisation->setVerified(false);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_organisation_show_slug', [
                'slug' => $organisation->getSlug(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('organisation/edit.html.twig', [
            'organisation' => $organisation,
            'form' => $form,
        ]);
    }

}
