<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfileFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $profile = new Profile();
        $profile->setRs('Linkedin');
        $profile->setUrl('https://www.linkedin.com/in/rod-mingo-8b3699251/');

        $profile1 = new Profile();
        $profile1->setRs('Github');
        $profile1->setUrl('https://github.com/Rod97139');
        
        $manager->persist($profile);
        $manager->persist($profile1);

        $manager->flush();
    }
}
