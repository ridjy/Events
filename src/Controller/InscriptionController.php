<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Participants;

class InscriptionController extends AbstractController
{
    #[Route('/api/insription', name:"createparticipant", methods: ['POST'])]
    public function createEvent(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, UrlGeneratorInterface $urlGenerator): JsonResponse 
    {
        $Participant = $serializer->deserialize($request->getContent(), Participants::class, 'json');
        $em = $doctrine->getManager();
        $em->persist($Participant);
        $em->flush();

        $jsonParticipant = $serializer->serialize($Participant, 'json', ['groups' => 'getEvents']);
        
        $location = $urlGenerator->generate('detailEvent', ['id' => $Participant->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonParticipant, Response::HTTP_CREATED, ["Location" => $location], true);
   }//fin function create event
}
