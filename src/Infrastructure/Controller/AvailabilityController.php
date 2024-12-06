<?php
namespace App\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Application\UseCase\FetchAvailabilityUseCase;

class AvailabilityController extends AbstractController
{
    private FetchAvailabilityUseCase $fetchAvailability;

    public function __construct(FetchAvailabilityUseCase $fetchAvailability)
    {
        $this->fetchAvailability = $fetchAvailability;
    }

    //obtenemos json a traves de la ruta /api/avail
    public function getAvailability(Request $request): JsonResponse
    {
        $origin = $request->query->get('origin');
        $destination = $request->query->get('destination');
        $date = $request->query->get('date');

        $flights = $this->fetchAvailability->execute($origin, $destination, $date);

        return $this->json($flights);
    }
}
