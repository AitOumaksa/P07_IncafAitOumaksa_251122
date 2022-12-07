<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Consumer;
use App\Entity\Phone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $userPasswordHasher;
 
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * LOAD FIXTURE
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        // USER FIXTURE

        $user = [
            1 => [
                'email' => 'admin@bilmo.com',
                'role' => 'ROLE_ADMIN',
                'password' => 'password',
                'name' => 'Willis',
            ],
            1 => [
                'email' => 'admin2@bilmo.com',
                'role' => 'ROLE_ADMIN',
                'password' => 'password',
                'name' => 'Willis',
            ]
        ];

        foreach ($user as $value) {
            $user = new Client();
            $user->setEmail($value['email']);
            $user->setRoles(array($value['role']));
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $value['password']));
            $user->setName($value['name']);
            $manager->persist($user);
        }

        $manager->flush();

        // CONSUMER

        $customer = [
            1 => [
                'email' => 'CoretteMathieu@teleworm.us',
                'fullName' => 'Corette Mathieu',
                'phoneNumber' => '+33523103505',
                'city' => 'SAINTES',
                'country' => 'France',
            ],
            2 => [
                'email' => 'ArminaBriard@teleworm.us',
                'fullName' => 'Christian Dervin',
                'phoneNumber' => '+33612244205',
                'city' => 'CLICHY-SOUS-BOIS',
                'country' => 'France',
            ],
            3 => [
                'email' => 'LothairBinet@rhyta.com',
                'fullName' => 'Chris Brown',
                'phoneNumber' => '+33143005850',
                'city' => 'LOOS',
                'country' => 'France'
            ],
            4 => [
                'email' => 'ChristianCyr@armyspy.com',
                'fullName' => 'John Franco',
                'phoneNumber' => '+3352319823',
                'city' => 'LIVRY-GARGAN',
                'country' => 'France',
            ],
            5 => [
                'email' => 'FayeSt-Jacques@armyspy.com',
                'fullName' => 'Jule Merguez',
                'phoneNumber' => '+33522991305',
                'city' => 'MIRAMAS',
                'country' => 'France',
            ],
        ];

        foreach ($customer as $value) {
            $customer = new Consumer();
            $customer->setClient($user);
            $customer->setEmail($value['email']);
            $customer->setFullName($value['fullName']);
            $customer->setPhoneNumber($value['phoneNumber']);
            $customer->setCity($value['city']);
            $customer->setCountry($value['country']);
            $manager->persist($customer);
        }

        $manager->flush();

        // Phone

        $product = [
            1 => [
                'name' => 'Galaxy S13',
                'price' => 90000,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat mauris est, ac pulvinar ante faucibus sed.',
                'brand' => 'Samsung',
                'reference' => 'N2483'
            ],
            2 => [
                'name' => 'Iphone 13',
                'price' => 120000,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat mauris est, ac pulvinar ante faucibus sed.',
                'brand' => 'Apple',
                'reference' => 'T2483'
            ],
            3 => [
                'name' => 'Black shark 4',
                'price' => 30000,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat mauris est, ac pulvinar ante faucibus sed.',
                'brand' => 'Xiaomi',
                'reference' => 'F2483'
            ],
            4 => [
                'name' => '3310',
                'price' => 3000,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat mauris est, ac pulvinar ante faucibus sed.',
                'brand' => 'Nokia',
                'reference' => 'B2483'
            ],
            5 => [
                'name' => 'Galaxy zoom',
                'price' => 10000,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat mauris est, ac pulvinar ante faucibus sed.',
                'brand' => 'Samsung',
                'reference' => 'A2483'
            ],
        ];

        foreach ($product as $value) {
            $product = new Phone();
            $product->setName($value['name']);
            $product->setPrice($value['price']);
            $product->setDescription($value['description']);
            $product->setReference($value['reference']);
            $product->setBrand($value['brand']);
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($product);
           
        }

        $manager->flush();
    }
}
