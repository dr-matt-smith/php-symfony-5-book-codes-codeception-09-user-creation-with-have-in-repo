<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
     private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {
        // create objects
        $userUser = $this->createUser('user@user.com', 'user');
        $userAdmin = $this->createUser('admin@admin.com', 'admin', 'ROLE_ADMIN');
        $userMatt = $this->createUser('matt.smith@smith.com', 'smith', 'ROLE_SUPER_ADMIN');

        // add to DB queue
        $manager->persist($userUser);
        $manager->persist($userAdmin);
        $manager->persist($userMatt);

        // send query to DB
        $manager->flush();

    }

    private function createUser($username, $plainPassword, $role = 'ROLE_USER'):User
    {
        $user = new User();
        $user->setEmail($username);
        $user->setRole($role);

        // password - and encoding
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        return $user;
    }
}