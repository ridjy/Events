<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Participants;
use App\Entity\Events;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InscriptionController extends AbstractController
{
    #[Route('/api/inscription', name:"createparticipant", methods: ['POST'])]
    public function createParticipant(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, UrlGeneratorInterface $urlGenerator): JsonResponse 
    {
        $em = $doctrine->getManager();
        $Participant = $serializer->deserialize($request->getContent(), Participants::class, 'json');
        //test si le mail du participant est déjà dans la base de données
        $content = $request->toArray();
        //chercher l'évènement désiré par le nom 
        $o_eventInscrit = $doctrine->getRepository(Events::class)->findOneBy(array('nom' => $content['event']));
        if($o_eventInscrit)
        {
            $b_inscription = $this->saveParticipant($Participant,$doctrine,$o_eventInscrit);
        } else {
            //l'évènement n'existe pas
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        if ($b_inscription)
        {
            $jsonEvent = $serializer->serialize($o_eventInscrit, 'json', ['groups' => 'getEvents']);
            //on affichera l'évènement
            $location = $urlGenerator->generate('detailEvent', ['id' => $o_eventInscrit->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            return new JsonResponse($jsonEvent, Response::HTTP_CREATED, ["Location" => $location], true);
        } else {
            //guichet fermé
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
        
   }//fin function create event

    private function saveParticipant(Participants $Participant,ManagerRegistry $doctrine,Events $o_eventInscrit) : bool
    {
        $em = $doctrine->getManager();
        //tester si le participant est déjà dans la base de données
        $o_ParticipantExist = $doctrine->getRepository(Participants::class)->findOneBy(array('email' => $Participant->getEmail()));
        $n_inscrit = $doctrine->getRepository(Events::class)->countParticipantByEvent($o_eventInscrit->getId());
        if ($o_ParticipantExist)
        {
            //on n'enregistre pas dans la base participant mais directement vers participation à l'évènement
            $o_ParticipantExist->addEventsParticipant($o_eventInscrit);
            $FinalParticipant = $o_ParticipantExist;
        } else {
            $Participant->addEventsParticipant($o_eventInscrit);
            $FinalParticipant = $Participant;
        }
        if ($n_inscrit < $o_eventInscrit->getNbrMaxParticipants())
        {
            $em->persist($FinalParticipant);
            $em->flush();
            return true;
        } else {
            return false;
        }
    }//fin saveParticipant
}
