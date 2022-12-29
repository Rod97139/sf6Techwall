<?php

namespace App\DataFixtures;

use App\Entity\Hobby;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HobbyFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

                  $data = ["Aller au cinéma, au théâtre, à des concerts ",
                    "Conduire un tracteur, une voiture",
                    "Décorer une salle des fêtes ",
                    "Faire la cuisine, la pâtisserie",
                    "Pratiquer la spéléologie ",
                    "Participer à un forum de discussion sur un thème d'actualité",
                    "Participer à la rénovation d'une maison ",
                    "Dessiner, faire des BD, tagger",
                    "Surfer sur le web ",
                    "Ecrire des lettres, des poèmes, des histoires",
                    "Faire un rallye ",
                    "Faire du théâtre, se déguiser",
                    "Jardiner ",
                    "Aider des personnes âgées pour les courses",
                    "S'occuper des animaux",
                    "Créer des jeux vidéo ",
                    "Faire du jogging",
                    "Préparer un voyage à l'étranger ",
                    "Bricoler dans la maison",
                    "Travailler le bois ",
                    "Aller dans les musées, à une exposition",
                    "Faire du skate, du roller, du VTT ",
                    "Être skipper sur un voilier",
                    "Rendre visite à des personnes seules, à des malades ",
                    "Aller à la chasse, à la pêche"];



        for ($i=0; $i < count($data); $i++) { 
            $hobby = new Hobby();
            $hobby->setDesignation($data[$i]);
            $manager->persist($hobby);
        }
 
        $manager->flush();
    }
}
