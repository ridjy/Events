<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EventsRepository;
use Symfony\Component\Serializer\SerializerInterface;

class EventController extends AbstractController
{
    #[Route('/api/events', name: 'events', methods: ['GET'])]
    public function getEventList(EventsRepository $eventRepository, SerializerInterface $serializer): JsonResponse
    {
        $eventList = $eventRepository->findAll();
        $jsonEventList = $serializer->serialize($eventList, 'json');
        return new JsonResponse($jsonEventList, Response::HTTP_OK, [], true);
    }

}
