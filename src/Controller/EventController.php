<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EventsRepository;
use App\Entity\Events;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Serializer\SerializerInterface as modifSerializer;
use App\Service\VersioningService;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class EventController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble des évènements.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des évènements",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Events::class, groups={"getEvents"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Events")
     *
     * @param EventsRepository $eventRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/events', name: 'events', methods: ['GET'])]
    public function getEventList(EventsRepository $eventRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $idCache = "getAllEvents-" . $page . "-" . $limit;
        $context = SerializationContext::create()->setGroups(['getEvents']);
        $jsonEventList = $cachePool->get($idCache, function (ItemInterface $item) use ($eventRepository, $page, $limit, $context,$serializer) {
            $item->tag("eventsCache");
            $eventList = $eventRepository->findAllWithPagination($page, $limit);
            return $serializer->serialize($eventList, 'json',$context);
        });
        return new JsonResponse($jsonEventList, Response::HTTP_OK, [], true);
    }//fin function lister les évènements

    #[Route('/api/events/{id}', name: 'detailEvent', methods: ['GET'])]
    public function getDetailEvent(Events $Event, SerializerInterface $serializer, VersioningService $versioningService): JsonResponse 
    {
        $version = $versioningService->getVersion();
        $context = SerializationContext::create()->setGroups(['getEvents']);
        $context->setVersion($version);
        $jsonEvent = $serializer->serialize($Event, 'json', $context);
        return new JsonResponse($jsonEvent, Response::HTTP_OK, [], true);
   }//fin function affichage détail évènement

   #[Route('/api/events', name:"createEvent", methods: ['POST'])]
   #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un évènement')]
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

        $context = SerializationContext::create()->setGroups(['getEvents']);
        $jsonEvent = $serializer->serialize($Event, 'json', $context);
        $location = $urlGenerator->generate('detailEvent', ['id' => $Event->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonEvent, Response::HTTP_CREATED, ["Location" => $location], true);
   }//fin function create event

    #[Route('/api/events/{id}', name:"updateEvent", methods:['PUT'])]
    public function updateEvent(Request $request, modifSerializer $serializer, Events $currentEvent, ManagerRegistry $doctrine, ValidatorInterface $validator): JsonResponse 
    {
        $updatedEvent = $serializer->deserialize($request->getContent(), 
                Events::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentEvent]);
        // On vérifie les erreurs
        $errors = $validator->validate($updatedEvent);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em = $doctrine->getManager();
        $em->persist($updatedEvent);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

   #[Route('/api/events/{id}', name: 'deleteEvent', methods: ['DELETE'])]
   #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer un évènement')]
    public function deleteEvent(int $id, EventsRepository $EventRepository, ManagerRegistry $doctrine,TagAwareCacheInterface $cachePool): JsonResponse 
    {
        $cachePool->invalidateTags(["eventsCache"]);
        $em = $doctrine->getManager();
        $Event = $EventRepository->find($id);
        $em->remove($Event);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }//fin function supprimr évènement

}
