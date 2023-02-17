<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EventsRepository;
use App\Entity\Events;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventController extends AbstractController
{
    #[Route('/api/events', name: 'events', methods: ['GET'])]
    public function getEventList(EventsRepository $eventRepository, SerializerInterface $serializer): JsonResponse
    {
        $eventList = $eventRepository->findAll();
        $jsonEventList = $serializer->serialize($eventList, 'json',['groups' => 'getEvents']);
        return new JsonResponse($jsonEventList, Response::HTTP_OK, [], true);
    }//fin function lister les évènements

    #[Route('/api/events/{id}', name: 'detailEvent', methods: ['GET'])]
    public function getDetailEvent(int $id, SerializerInterface $serializer, EventsRepository $EventRepository): JsonResponse 
    {
        $Event = $EventRepository->find($id);
        if ($Event) {
            $jsonEvent = $serializer->serialize($Event, 'json', ['groups' => 'getEvents']);
            return new JsonResponse($jsonEvent, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
   }//fin function affichage détail évènement

   #[Route('/api/events', name:"createEvent", methods: ['POST'])]
    public function createEvent(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse 
    {
        $Event = $serializer->deserialize($request->getContent(), Events::class, 'json');

        // On vérifie les erreurs
        $errors = $validator->validate($Event);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em = $doctrine->getManager();
        $em->persist($Event);
        $em->flush();

        $jsonEvent = $serializer->serialize($Event, 'json', ['groups' => 'getEvents']);
        
        $location = $urlGenerator->generate('detailEvent', ['id' => $Event->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonEvent, Response::HTTP_CREATED, ["Location" => $location], true);
   }//fin function create event

    #[Route('/api/events/{id}', name:"updateEvent", methods:['PUT'])]
    public function updateEvent(Request $request, SerializerInterface $serializer, Events $currentEvent, ManagerRegistry $doctrine): JsonResponse 
    {
        $updatedEvent = $serializer->deserialize($request->getContent(), 
                Events::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentEvent]);
        $em = $doctrine->getManager();
        $em->persist($updatedEvent);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

   #[Route('/api/events/{id}', name: 'deleteEvent', methods: ['DELETE'])]
    public function deleteEvent(int $id, EventsRepository $EventRepository, ManagerRegistry $doctrine): JsonResponse 
    {
        $em = $doctrine->getManager();
        $Event = $EventRepository->find($id);
        $em->remove($Event);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }//fin function supprimr évènement

}
