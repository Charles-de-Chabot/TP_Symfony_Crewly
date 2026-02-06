<?php

namespace App\DataFixtures;

use App\Entity\Type;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {}
    public function load(ObjectManager $manager): void
    {
        $this->loadUser($manager);
        $this->loadType($manager);
        $this->loadModel($manager);

        $manager->flush();
    }

    public function loadUser(ObjectManager $manager){
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $admin->setFirstname('amdin');
        $admin->setLastname('nomAdmin');
        $admin->setCreatedAt(new DateTime());
        $admin->setIsActive(true);

        $manager->persist($admin);

        //Création d'utilisateurs
        $arrayUser = [
            ['email' => 'user1@user.com', 'firstname' => 'User1', 'lastname' => 'nomUser1'],
            ['email' => 'user2@user.com', 'firstname' => 'User2', 'lastname' => 'nomUser2'],
            ['email' => 'user3@user.com', 'firstname' => 'User3', 'lastname' => 'nomUser3'],
            ['email' => 'user4@user.com', 'firstname' => 'User4', 'lastname' => 'nomUser4'],
            ['email' => 'user5@user.com', 'firstname' => 'User5', 'lastname' => 'nomUser5']
        ];

        foreach($arrayUser as $key => $value){
            $user = new User();
            $user->setEmail($value['email']);
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
            $admin->setRoles(['ROLE_USER']);
            $admin->setFirstname($value['firstname']);
            $admin->setLastname($value['lastname']);
            $admin->setCreatedAt(new DateTime());
            $admin->setIsActive(true);

            $manager->persist($user);
            $this->addReference('user_' . $key, $user);
        }
    }

    public function loadType(ObjectManager $manager){
        $arrayType = ['Monocoque', 'Catamaran', 'MotorYatch', 'Pêche', 'Jetski', 'Kayak' ];
        foreach($arrayType as $value){
            $type = new Type();
            $type->setLabel($value);

            $manager->persist($type);

            $this->addReference("type_" . $value, $type);
        }
    }

    public function loadModel(ObjectManager $manager){
        $arrayModel = ['Beneteau', 'Jeanneau', 'Dufour', 'Pêche', 'Catalina', 'Hallberg-Rassy', 'Lagoon 420', 'Fountaine Pajot', 'Leopard', 'Bali', 'Nautitech', 'Prestige', 'Azimut', 'Sunseeker', 'Beneteau', 'Princess', 'FishHawk', 'Trophy', 'FunYak', 'Abaco', 'Yamaha', 'Kawasaki', 'Perception', 'Ocean'];
        foreach($arrayModel as $value){
            $model = new Type();
            $model->setLabel($value);

            $manager->persist($model);

            $this->addReference("model_" . $value, $model);
        }
    }

    
}
