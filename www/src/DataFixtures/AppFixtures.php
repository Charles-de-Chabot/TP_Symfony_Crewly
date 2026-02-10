<?php

namespace App\DataFixtures;

use App\Entity\Adress;
use App\Entity\Boat;
use App\Entity\Media;
use App\Entity\Model;
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
        $this->loadAdress($manager);
        $this->loadBoat($manager);


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
            $user->setPassword($this->passwordHasher->hashPassword($user, 'admin'));
            $user->setRoles(['ROLE_USER']);
            $user->setFirstname($value['firstname']);
            $user->setLastname($value['lastname']);
            $user->setCreatedAt(new DateTime());
            $user->setIsActive(true);

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
        $arrayModel = ['Beneteau', 'Jeanneau', 'Dufour', 'Catalina', 'Hallberg-Rassy', 'Lagoon', 'Fountaine Pajot', 'Leopard', 'Bali', 'Nautitech', 'Prestige', 'Azimut', 'Sunseeker', 'Princess', 'FishHawk', 'Trophy', 'FunYak', 'Abaco', 'Yamaha', 'Kawasaki', 'Perception', 'Ocean'];
        foreach($arrayModel as $value){
            $model = new Model();
            $model->setLabel($value);

            $manager->persist($model);

            $this->addReference("model_" . $value, $model);
        }
    }

    public function loadAdress(ObjectManager $manager)
    {
        $adresses = [
            ['house_number' => '1', 'street_name' => 'Quai du Port', 'postcode' => '13002', 'city' => 'Marseille'],
            ['house_number' => '10', 'street_name' => 'Marina du Château', 'postcode' => '29200', 'city' => 'Brest'],
            ['house_number' => '2', 'street_name' => 'Place de la Bourse', 'postcode' => '33000', 'city' => 'Bordeaux'],
            ['house_number' => '1', 'street_name' => 'Quai Pierre Forgas', 'postcode' => '66660', 'city' => 'Port-Vendres']
        ];

        foreach ($adresses as $data) {
            $adress = new Adress();
            $adress->setHouseNumber($data['house_number']);
            $adress->setStreetName($data['street_name']);
            $adress->setPostcode($data['postcode']);
            $adress->setCity($data['city']);

            $manager->persist($adress);
            $this->addReference('adress_' . $data['city'], $adress);
        }
    }

    public function loadBoat(ObjectManager $manager){
        $arrayBoat = [
            //TYPE: Monocoque
            //L'élégance et les sentations de navigation pure
            [
            'type' => "Monocoque",
            'model' => "Beneteau",
            'name' => "Ocean Spirit",
            'description' => "Oceanis 40.1 – Un croiseur moderne alliant confort et performance. Profitez d'un large cockpit ergonomique et d'un intérieur lumineux pour des croisières inoubliables.",
            'max_user' => "8",
            'boat_length' => "12.50",
            'boat_width' => "3.99",
            'boat_draught' => "2.10",
            'cabine_number' => "3", 
            'bed_number' => "6",
            'fuel' => "Diesel",
            'power_engine' => "45"
            ],
            [
            'type' => "Monocoque",
            'model' => "Jeanneau",
            'name' => "Blue Velvet",
            'description' => "Sun Odyssey 440 – Une carène fluide pour une navigation sereine en famille. Son plan de pont sans obstacle et ses passavants inclinés offrent une sécurité et un confort inégalés.",
            'max_user' => "10",
            'boat_length' => "13.34",
            'boat_width' => "4.29",
            'boat_draught' => "2.20",
            'cabine_number' => "4", 
            'bed_number' => "8",
            'fuel' => "Diesel",
            'power_engine' => "57"
            ],
            [
            'type' => "Monocoque",
            'model' => "Dufour",
            'name' => "L'Escale",
            'description' => "Dufour 360 Grand Large – Un intérieur baigné de lumière et un pont spacieux. Idéal pour les escapades côtières, il dispose d'une cuisine extérieure avec plancha pour vos mouillages.",
            'max_user' => "6",
            'boat_length' => "10.30",
            'boat_width' => "3.54",
            'boat_draught' => "1.90",
            'cabine_number' => "2", 
            'bed_number' => "4",
            'fuel' => "Diesel",
            'power_engine' => "30"
            ],
            [
            'type' => "Monocoque",
            'model' => "Catalina",
            'name' => "Horizon",
            'description' => "Catalina 425 – La robustesse américaine au service de votre confort. Ce voilier marin est doté d'un cockpit profond et d'un gréement performant pour les longues traversées.",
            'max_user' => "8",
            'boat_length' => "11.50",
            'boat_width' => "3.60",
            'boat_draught' => "1.65",
            'cabine_number' => "2", 
            'bed_number' => "5",
            'fuel' => "Diesel",
            'power_engine' => "40"
            ],
            [
            'type' => "Monocoque",
            'model' => "Hallberg-Rassy",
            'name' => "North Star",
            'description' => "Hallberg-Rassy 40C – Le luxe scandinave pour affronter toutes les mers. Sa qualité de finition artisanale et sa protection centrale du cockpit garantissent une navigation haut de gamme par tous les temps.",
            'max_user' => "6",
            'boat_length' => "12.30",
            'boat_width' => "3.90",
            'boat_draught' => "1.90",
            'cabine_number' => "2", 
            'bed_number' => "5",
            'fuel' => "Diesel",
            'power_engine' => "75"
            ],
            //TYPE: Catamaran
            //L'espace et la stabilité, idéal pour des vacances entre amis
            [
            'type' => "Catamaran",
            'model' => "Lagoon",
            'name' => "Sea Cloud",
            'description' => "Lagoon 420 – Une véritable villa flottante avec vue à 360°. Son salon de plain-pied et son immense flybridge en font le choix numéro 1 pour le farniente au soleil.",
            'max_user' => "12",
            'boat_length' => "12.80",
            'boat_width' => "7.70",
            'boat_draught' => "1.25",
            'cabine_number' => "4", 
            'bed_number' => "10",
            'fuel' => "Diesel",
            'power_engine' => "114"
            ],
            [
            'type' => "Catamaran",
            'model' => "Fountaine Pajot",
            'name' => "Aura",
            'description' => "Isla 40 – Design élégant et zones de détente immenses. Ce modèle se distingue par ses lignes inversées et son salon de pont avant incroyablement convivial pour l'apéritif.",
            'max_user' => "10",
            'boat_length' => "11.73",
            'boat_width' => "6.63",
            'boat_draught' => "1.20",
            'cabine_number' => "3", 
            'bed_number' => "8",
            'fuel' => "Diesel",
            'power_engine' => "60"
            ],
            [
            'type' => "Catamaran",
            'model' => "Leopard",
            'name' => "Wild Cat",
            'description' => "Leopard 45 – Performance et ergonomie pour les aventuriers. Il dispose d'un cockpit avant unique accessible directement depuis le salon, parfait pour une ventilation naturelle.",
            'max_user' => "10",
            'boat_length' => "13.70",
            'boat_width' => "7.35",
            'boat_draught' => "1.25",
            'cabine_number' => "4", 
            'bed_number' => "8",
            'fuel' => "Diesel",
            'power_engine' => "90"
            ],
            [
            'type' => "Catamaran",
            'model' => "Bali",
            'name' => "Open Sky",
            'description' => "Bali 4.2 – Un cockpit immense sans cloisons pour vivre dehors. Grâce à sa célèbre porte oscillo-basculante, l'espace intérieur et extérieur ne font plus qu'un.",
            'max_user' => "12",
            'boat_length' => "12.10",
            'boat_width' => "6.70",
            'boat_draught' => "1.12",
            'cabine_number' => "4", 
            'bed_number' => "10",
            'fuel' => "Diesel",
            'power_engine' => "80"
            ],
            [
            'type' => "Catamaran",
            'model' => "Nautitech",
            'name' => "Swift Breeze",
            'description' => "Nautitech 40 Open – Le plaisir de la barre et un salon hyper convivial. Conçu pour ceux qui aiment naviguer vite tout en profitant d'un espace de vie ouvert et moderne.",
            'max_user' => "8",
            'boat_length' => "11.98",
            'boat_width' => "6.91",
            'boat_draught' => "1.35",
            'cabine_number' => "3", 
            'bed_number' => "6",
            'fuel' => "Diesel",
            'power_engine' => "60"
            ],

            //TYPE: MotorYacht
            //Puissance, luxe et confort absolu
            [
            'type' => "MotorYatch",
            'model' => "Prestige",
            'name' => "Luxury Dream",
            'description' => "Prestige 520 – Le raffinement absolu pour des soirées chics. Admirez le coucher de soleil depuis son flybridge format XXL et profitez d'une suite propriétaire digne d'un hôtel 5 étoiles.",
            'max_user' => "12",
            'boat_length' => "16.10",
            'boat_width' => "4.50",
            'boat_draught' => "1.20",
            'cabine_number' => "3", 
            'bed_number' => "6",
            'fuel' => "Diesel",
            'power_engine' => "870"
            ],
            [
            'type' => "MotorYatch",
            'model' => "Azimut",
            'name' => "Italian Star",
            'description' => "Azimut Fly 50 – Le style italien pur pour briller dans la marina. Son design extérieur sculptural cache un intérieur sophistiqué avec des finitions en matériaux nobles.",
            'max_user' => "10",
            'boat_length' => "15.00",
            'boat_width' => "4.30",
            'boat_draught' => "1.10",
            'cabine_number' => "3", 
            'bed_number' => "6",
            'fuel' => "Diesel",
            'power_engine' => "720"
            ],
            [
            'type' => "MotorYatch",
            'model' => "Sunseeker",
            'name' => "Silver Bullet",
            'description' => "Predator 60 – Un monstre de puissance au design agressif. Vivez une expérience haute performance avec son toit ouvrant sport et son garage à annexe motorisé.",
            'max_user' => "12",
            'boat_length' => "18.20",
            'boat_width' => "5.00",
            'boat_draught' => "1.30",
            'cabine_number' => "3", 
            'bed_number' => "6",
            'fuel' => "Diesel",
            'power_engine' => "1600"
            ],
            [
            'type' => "MotorYatch",
            'model' => "Princess",
            'name' => "Royal Lady",
            'description' => "Princess F45 – Un yacht de prestige offrant un confort royal. Sa technologie de stabilisation de pointe assure des nuits paisibles même au mouillage.",
            'max_user' => "12",
            'boat_length' => "14.50",
            'boat_width' => "4.20",
            'boat_draught' => "1.15",
            'cabine_number' => "2", 
            'bed_number' => "4",
            'fuel' => "Diesel",
            'power_engine' => "600"
            ],

            //TYPE: Pêche
            //Equipements spécialisés pour mordre à l'hameçon
            [
            'type' => "Pêche",
            'model' => "FishHawk",
            'name' => "Angler Pro",
            'description' => "FishHawk 210 – Tout l'équipement professionnel pour une pêche record. Doté de viviers aérés, de multiples porte-cannes et d'un sondeur de dernière génération.",
            'max_user' => "6",
            'boat_length' => "6.50",
            'boat_width' => "2.40",
            'boat_draught' => "0.40",
            'cabine_number' => "0", 
            'bed_number' => "0",
            'fuel' => "Essence",
            'power_engine' => "150"
            ],
            [
            'type' => "Pêche",
            'model' => "Trophy",
            'name' => "The Hunter",
            'description' => "Trophy 2202 WA – Conçu pour la haute mer et les combats épiques. Sa cabine Walk-Around permet de circuler facilement tout autour du bateau pour ne jamais perdre votre prise.",
            'max_user' => "8",
            'boat_length' => "7.80",
            'boat_width' => "2.55",
            'boat_draught' => "0.60",
            'cabine_number' => "1", 
            'bed_number' => "2",
            'fuel' => "Essence",
            'power_engine' => "300"
            ],
            [
            'type' => "Pêche",
            'model' => "FunYak",
            'name' => "Robust",
            'description' => "FunYak Secu 12 – Insubmersible et ultra-pratique pour explorer les zones rocheuses. Sa coque en polyéthylène double paroi est pratiquement indestructible.",
            'max_user' => "4",
            'boat_length' => "4.50",
            'boat_width' => "1.90",
            'boat_draught' => "0.20",
            'cabine_number' => "0", 
            'bed_number' => "0",
            'fuel' => "Essence",
            'power_engine' => "40"
            ],
            [
            'type' => "Pêche",
            'model' => "Abaco",
            'name' => "Island King",
            'description' => "Abaco 242 – Le compromis parfait entre confort et sportivité. Un bateau de pêche côtière qui sait aussi accueillir la famille avec sa banquette modulable.",
            'max_user' => "8",
            'boat_length' => "9.20",
            'boat_width' => "2.80",
            'boat_draught' => "0.50",
            'cabine_number' => "1", 
            'bed_number' => "2",
            'fuel' => "Essence",
            'power_engine' => "350"
            ],

            //TYPE: Jetski
            //Le plein d'adrénaline
            [
            'type' => "Jetski",
            'model' => "Yamaha",
            'name' => "Wave Runner",
            'description' => "VX Cruiser HO – Des accélérations foudroyantes sur l'eau. Alliez confort de selle et technologie de pointe avec son système de commande RiDE pour des manœuvres intuitives.",
            'max_user' => "3",
            'boat_length' => "3.35",
            'boat_width' => "1.22",
            'boat_draught' => "0.30",
            'cabine_number' => "0", 
            'bed_number' => "0",
            'fuel' => "Essence",
            'power_engine' => "180"
            ],
            [
            'type' => "Jetski",
            'model' => "Kawasaki",
            'name' => "Ultra Jet",
            'description' => "Ultra 310LX – La référence pour les amateurs de sensations fortes. Équipé d'un compresseur, c'est l'un des jetskis les plus puissants du marché avec son système audio intégré.",
            'max_user' => "3",
            'boat_length' => "3.50",
            'boat_width' => "1.18",
            'boat_draught' => "0.30",
            'cabine_number' => "0", 
            'bed_number' => "0",
            'fuel' => "Essence",
            'power_engine' => "310"
            ],

            //TYPE: Kayak
            //Silence et nature
            [
            'type' => "Kayak",
            'model' => "Perception",
            'name' => "Flow",
            'description' => "Perception Expression 11 – Glissez en silence au plus près de la nature. Un kayak de randonnée stable, doté d'un siège ergonomique pour de longues explorations sans fatigue.",
            'max_user' => "1",
            'boat_length' => "3.00",
            'boat_width' => "0.75",
            'boat_draught' => "0.15",
            'cabine_number' => "0", 
            'bed_number' => "0",
            'fuel' => "/",
            'power_engine' => "0"
            ],
            [
            'type' => "Kayak",
            'model' => "Ocean",
            'name' => "Explorer",
            'description' => "Ocean Kayak Malibu Two – Stable et rapide, idéal pour l'exploration côtière. Ce kayak Sit-on-top est le favori des familles pour sa facilité d'utilisation.",
            'max_user' => "2",
            'boat_length' => "4.20",
            'boat_width' => "0.85",
            'boat_draught' => "0.20",
            'cabine_number' => "0", 
            'bed_number' => "0",
            'fuel' => "/",
            'power_engine' => "0"
            ]
        ];
        foreach($arrayBoat as $value){
            $boat = new Boat();
            $boat->setName($value['name']);
            $boat->setDescription($value['description']);
            $boat->setMaxUser($value['max_user']);
            $boat->setBoatLength($value['boat_length']);
            $boat->setBoatWidth($value['boat_width']);
            $boat->setBoatDraught($value['boat_draught']);
            $boat->setCabineNumber($value['cabine_number']);
            $boat->setBedNumber($value['bed_number']);
            $boat->setFuel($value['fuel']);
            $boat->setPowerEngine($value['power_engine']);
            
            $createdAt = new DateTime();
            $createdAt->modify('-' . rand(0, 30) . 'days');
            $boat->setCreatedAt($createdAt);

            $boat->setIsActive(true);

            $cities = ['Marseille', 'Brest', 'Bordeaux', 'Port-Vendres'];
            $randomCity = $cities[array_rand($cities)];
            $boat->setAdress($this->getReference('adress_' . $randomCity, Adress::class));

            $boat->setType($this->getReference('type_' . $value['type'], Type::class));
            $boat->setModel($this->getReference('model_' . $value['model'], Model::class));

            $manager->persist($boat);

            $media = new Media;
            $media->setImgPath('/images/' . strtolower($value['type']) . '.png');

            $manager->persist($media);
        }
    }
    
}
