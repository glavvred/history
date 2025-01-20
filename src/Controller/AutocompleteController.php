<?php

namespace App\Controller;

use App\Service\DaDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AutocompleteController extends AbstractController
{

    #[Route('/autocomplete/address', name: 'autocomplete_address', methods: ['GET'])]
    public function address(Request $request): JsonResponse
    {
        $daDataService = new DaDataService($this->getParameter('DADATA_API_KEY'));

        $query = $request->query->get('query');
        $suggestions = $daDataService->suggestAddress($query);

        return new JsonResponse($suggestions);
    }
}
