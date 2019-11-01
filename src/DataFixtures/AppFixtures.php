<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Job;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $job = new Job();
        $job->setTitle('Fix First JOb');
        $job->setDescription('Fix Desc First JOb');
        $job->setEmail('fix@fix.com');
        $job->setStatus(1);

        $manager->persist($job);

        $manager->flush();
    }
}
