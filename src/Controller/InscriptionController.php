<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Participants;
use Doctrine\Persistence\ManagerRegistry;

class InscriptionController extends AbstractController
{
    #[Route('/api/insription', name:"createparticipant", methods: ['POST'])]
    public function createParticipant(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, UrlGeneratorInterface $urlGenerator): JsonResponse 
    {
        $Participant = $serializer->deserialize($request->getContent(), Participants::class, 'json');
        $em = $doctrine->getManager();
        //test si le mail du participant est déjà dans la base de données
        $content = $request->toArray();
        //chercher l'évènement désiré par le nom 
        $o_eventInscrit = $doctrine->getRepository(Events::class)->findOneBy(array('nom' => $content['nom']));
        if($o_eventInscrit)
        {
            //tester si le participant est déjà dans la base de données
            $o_ParticipantExist = $doctrine->getRepository(Participants::class)->findOneBy(array('email' => $Participant->getEmail()));
            if ($o_ParticipantExist) {
                //on n'enregistre pas dans la base participant mais directement vers l'évènement
                $o_ParticipantExist->addEventsParticipant($o_eventInscrit);
                $em->persist($o_ParticipantExist);
            } else {
                $Participant->addEventsParticipant($o_eventInscrit);
                $em->persist($Participant);
            }
            $em->flush();
        } else {
            //l'évènement n'existe pas
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
        
        $em->persist($Participant);
        $em->flush();

        $jsonParticipant = $serializer->serialize($Participant, 'json', ['groups' => 'getEvents']);
        
        $location = $urlGenerator->generate('detailEvent', ['id' => $Participant->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonParticipant, Response::HTTP_CREATED, ["Location" => $location], true);
   }//fin function create event
}
