<?php

namespace App\Controller;

use App\Repository\VeteranRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VeteranApiController extends AbstractController
{
    #[Route('/api/veterans/filter', name: 'api_veterans_filter', methods: ['GET'])]
    public function filterVeterans(Request $request, VeteranRepository $veteranRepository): JsonResponse
    {
        $warType = $request->query->get('warType');
        $rankId = $request->query->get('rankId');
        $awardId = $request->query->get('awardId');
        $birthYear = $request->query->get('birthYear');
        $deathYear = $request->query->get('deathYear');
        $searchQuery = $request->query->get('search');

        $veterans = $veteranRepository->findByFilters(
            $warType,
            $rankId ? (int)$rankId : null,
            $awardId ? (int)$awardId : null,
            $birthYear ? (int)$birthYear : null,
            $deathYear ? (int)$deathYear : null,
            $searchQuery
        );

        return $this->json($veterans, 200, [], ['groups' => ['veteran:read']]);
    }
}
