<?php

namespace App\DataFixtures;

use App\Constants\FixtureOrderConstants;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('API');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'API'));

        $manager->persist($user);
        $manager->flush();
    }

    public function getOrder(): int
    {
        return FixtureOrderConstants::USER_FIXTURES;
    }
}
