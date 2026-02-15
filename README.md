# ⚓ CREWLY

Application web de location de bateaux développée avec Symfony 6.4 et Docker.

---

## 📋 Description

**CREWLY** est une plateforme permettant aux particuliers de louer des bateaux pour des durées variables (journée, semaine ou mixte). L'application offre une gestion complète des réservations avec une tarification dynamique, un espace utilisateur personnel et une administration pour la flotte.

Le projet est entièrement conteneurisé via Docker pour garantir un environnement de développement iso-prod.

---

## 👥 Équipe de développement

| Membre | Rôle |
|--------|------|
| **Martin BAUCHARD** | Développeur Fullstack |
| **Charles DE CHABOT** | Développeur Fullstack |

---

## ✨ Fonctionnalités

### 👤 Utilisateurs
- **Authentification :** Inscription, connexion et hachage sécurisé des mots de passe.
- **Profil :** Gestion des informations personnelles et de l'adresse postale.
- **Tableau de bord :** Vue d'ensemble des locations passées et à venir.

### ⛵ Locations & Tarification
- **Réservation :** Sélection de dates via calendrier interactif.
- **Calcul de prix intelligent :**
  - *Journée* : Prix unitaire journalier.
  - *Semaine* : Tarif préférentiel à la semaine.
  - *Mixte* : Combinaison automatique (ex: 10 jours = 1 semaine + 3 jours).
- **Gestion :** Modification des dates et annulation (sous conditions de délais).

### 🛠 Administration
- Gestion de la flotte de bateaux (Ajout, Édition, Suppression logique).
- Suivi des utilisateurs et des réservations.

---

## 🛠️ Technologies

| Catégorie | Technologie | Version |
|-----------|-------------|---------|
| Framework | Symfony | 6.4 |
| Langage | PHP | 8.2+ |
| Base de données | MariaDB | 11.3 |
| Serveur Web | Apache | 2.4 |
| Conteneurisation | Docker | Compose |
| Frontend | Twig / TailwindCSS | - |
| ORM | Doctrine | - |

---

## 📁 Structure du projet

```
TP_Symfony_Crewly/
├── apache/                   # Configuration Docker Apache/PHP
│   ├── Dockerfile
│   └── custom-php.ini
├── db/                       # Scripts de maintenance BDD
│   ├── backup.sh
│   └── restore.sh
├── docker-compose.yml        # Orchestration des conteneurs
├── www/                      # Code source Symfony
│   ├── src/
│   │   ├── Controller/
│   │   │   ├── ProfileController.php      # Gestion compte user
│   │   │   ├── RegistrationController.php # Inscription
│   │   │   ├── RentalController.php       # Logique de location
│   │   │   └── ...
│   │   ├── Entity/
│   │   │   ├── User.php
│   │   │   ├── Boat.php
│   │   │   ├── Rental.php
│   │   │   └── ...
│   │   ├── Form/
│   │   │   ├── ProfileType.php
│   │   │   └── RegistrationFormType.php
│   │   └── Repository/
│   └── templates/            # Vues Twig
└── .env.example              # Modèle de configuration
```

---

## ⚙️ Installation

### Prérequis

- Docker Desktop & Docker Compose
- Git

### Étapes d'installation

**1. Cloner le repository**
```bash
git clone <URL_DU_DEPOT>
cd TP_Symfony_Crewly
```

**2. Configurer l'environnement**
Copiez le fichier d'exemple et ajustez si nécessaire (ports, mots de passe).
```bash
cp .env.example .env
```

**3. Lancer les conteneurs**
```bash
docker-compose up -d --build
```

**4. Installer les dépendances (dans le conteneur)**
```bash
docker-compose exec apache_crewly composer install
```

**5. Initialiser la base de données**
```bash
docker-compose exec apache_crewly php bin/console doctrine:migrations:migrate
```

Application accessible sur : `http://localhost:8000` (ou le port défini dans `.env`).

---

## 🔐 Rôles et permissions

| Fonctionnalité | Visiteur | ROLE_USER | ROLE_ADMIN |
|----------------|:--------:|:---------:|:----------:|
| Voir les bateaux | ✅ | ✅ | ✅ |
| S'inscrire / Connexion | ✅ | ❌ | ❌ |
| Réserver un bateau | ❌ | ✅ | ✅ |
| Modifier son profil | ❌ | ✅ | ✅ |
| Annuler sa location | ❌ | ✅ | ✅ |
| Gérer la flotte | ❌ | ❌ | ✅ |
| Accès Back-office | ❌ | ❌ | ✅ |

---

## 🗄️ Modèle de données (Extraits)

### User
| Champ | Type | Description |
|-------|------|-------------|
| id | int | Identifiant unique |
| email | string | Email de connexion |
| pseudo | string | Nom d'affichage |
| password | string | Hash Argon2 |
| adress | Relation | Lien vers l'entité Adress |

### Rental (Location)
| Champ | Type | Description |
|-------|------|-------------|
| id | int | Identifiant unique |
| rentalStart | datetime | Début de la location |
| rentalEnd | datetime | Fin de la location |
| rentalPrice | int | Prix total calculé |
| formulas | Relation | Formules appliquées (Jour/Semaine) |
| user | Relation | Locataire |
| boat | Relation | Bateau loué |

### Boat
| Champ | Type | Description |
|-------|------|-------------|
| id | int | Identifiant unique |
| name | string | Nom du bateau |
| description | text | Détails techniques |
| isActive | bool | Disponibilité |

---

## 📝 Commandes utiles

Toutes les commandes doivent être exécutées via Docker Compose.

**Vider le cache Symfony**
```bash
docker-compose exec apache_crewly php bin/console cache:clear
```

**Créer une migration (après modif entité)**
```bash
docker-compose exec apache_crewly php bin/console make:migration
```

**Sauvegarder la BDD**
```bash
docker-compose exec mariadb_crewly /docker-entrypoint-initdb.d/backup.sh
```

*Restaurer la BDD**
```bash
docker-compose exec mariadb_crewly /docker-entrypoint-initdb.d/restore.sh
```

---

## 🐛 Résolution de problèmes

### Permissions de fichiers
Si vous rencontrez des erreurs d'écriture dans `var/` ou `public/uploads/`, assurez-vous que les permissions sont correctes sur l'hôte ou que l'utilisateur Docker correspond (voir `.env`).

### Base de données inaccessible
Vérifiez que le conteneur `mariadb_crewly` est "healthy" :
```bash
docker-compose ps
```

---

## 📄 Licence

Projet réalisé dans un cadre scolaire - Tous droits réservés.

---

## 👨‍💻 Auteurs

Développé avec ❤️ par **Martin Bauchard** et **Charles de Chabot**.