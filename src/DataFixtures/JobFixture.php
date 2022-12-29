<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
        "Entrepreneur",
        "Anesthésiologiste",
        "Développeur de logiciel",
        "Architecte",
        "Expert-comptable",
        "Médecin",
        "Responsable marketing",
        "Banquier d'affaires",
        "Data Scientist",
        "Data Analyst",
        "Copywriter"
        ];

        for ($i=0; $i < count($data); $i++) { 
            $job = new Job();
            $job->setDesignation($data[$i]);
            $manager->persist($job);
        }
 
        $manager->flush();
    }
}
