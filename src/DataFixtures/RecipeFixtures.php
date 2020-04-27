<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RecipeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
//        $recipe = new Recipe();
//        $recipe->setTitle('Boston Cheesecake');
//        $recipe->setSteps('open package or bake with cheese');
//        $recipe->setTime(120);
//
//        $manager->persist($recipe);
//
//        $manager->flush();
    }
}
