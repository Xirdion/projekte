<?php

namespace AppBundle\DataFixtures\ORM;

//use Hautelook\AliceBundle\Alice\DataFixtureLoader;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(__DIR__.'/fixtures.yml', $manager);
    }

    /*protected function getFixtures()
    {
        return  array(
            __DIR__ . '/fixtures.yml',
        );
    }*/
}