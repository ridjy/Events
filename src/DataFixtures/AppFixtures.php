<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Events;
use App\Entity\Participants;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // création de 20 évènements
        for ($i=1;$i<21;$i++)
        {
            $event = new Events();
            $event->setNom('Mon super event'.$i);
            $d_deb = new \DateTime('2023-06-03');
            $event->setDateDebut($d_deb);
            $d_fin = new \DateTime('2023-06-09');
            $event->setDateFin($d_fin);
            $event->setNbrMaxParticipants(150);
            $event->setCommentaire('Evènement de l\'année');
            $manager->persist($event);
        }  
        
        //création 10 participants
        for ($i=1;$i<11;$i++)
        {
            $participant = new Participants();
            $participant->setNom('nom '.$i);
            $participant->setPrenom('prenom '.$i);
            $participant->setEmail('mail'.$i.'@yopmail.com');
            $participant->setTelephone('06 06 06 06 06');
            $participant->addEventsParticipant($event);
            $manager->persist($participant);
        }

        // Création d'un user "normal"
        $user = new User();
        $user->setUsername("user");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $manager->persist($user);
        
        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setUsername("admin");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);
        
        $manager->flush();
    }
}
