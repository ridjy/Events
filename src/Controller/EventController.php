<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route('/api/events/{id}', name: 'detailEvent', methods: ['GET'])]
    public function getDetailEvent(int $id, SerializerInterface $serializer, EventsRepository $EventRepository): JsonResponse 
    {
        $Event = $EventRepository->find($id);
        if ($Event) {
            $jsonEvent = $serializer->serialize($Event, 'json');
            return new JsonResponse($jsonEvent, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
   }

}
