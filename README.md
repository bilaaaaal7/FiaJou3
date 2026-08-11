# FiaJou3 — Plateforme de commande de repas

Réorganisation en architecture MVC PHP. Voir `RAPPORT_MIGRATION.md` (fourni séparément)
pour le détail complet de la migration.

## Structure

```
FiaJou3/
├── assets/
│   ├── css/        Feuilles de style (app.css, auth.css)
│   ├── js/          Scripts JS (i18n.js — moteur FR/EN/AR, theme.js, modals…)
│   ├── inc/         Composants réutilisables (header, footer, navbar, session, auth_guard)
│   └── images/      Logo, favicons
├── controleur/
│   ├── admin/       Contrôleurs de l'espace administrateur
│   ├── auth/        Connexion / inscription / déconnexion
│   ├── client/       Menu, panier, commande
│   ├── cuisinier/    Espace cuisine
│   └── livreur/      Espace livraison
├── modele/          Accès aux données (PDO)
├── vue/             Templates HTML/PHP affichés par les contrôleurs
├── config/          Configuration (BD, constantes de l'application)
├── uploads/         Images de plats uploadées par l'admin
├── .htaccess        Réécriture d'URL + protection des dossiers internes
├── default.php      Route "/" (accueil, redirige selon la session)
├── index.php        Point d'entrée unique (front controller)
└── urlRewrite.php   Table de routage + dispatcher
```

## Installation

1. Copier le projet dans le dossier servi par Apache (ex: `/var/www/html/FiaJou3`).
2. Activer le module Apache `mod_rewrite` (`a2enmod rewrite`) et autoriser les
   `.htaccess` (`AllowOverride All` dans la config du vhost).
3. Créer la base de données et les tables (adapter si un export existe déjà) :

```sql
CREATE DATABASE fiajou3 CHARACTER SET utf8mb4;
USE fiajou3;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    prenom VARCHAR(100),
    nom VARCHAR(100),
    telephone VARCHAR(50),
    adresse VARCHAR(255),
    ville VARCHAR(100),
    role VARCHAR(20) NOT NULL DEFAULT 'client',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255)
);

CREATE TABLE plats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    disponible TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE delivery_zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    zone_id INT NOT NULL,
    date_commande DATE,
    date_livraison DATE,
    heure_livraison TIME,
    total DECIMAL(10,2),
    statut VARCHAR(30),
    commentaire TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (zone_id) REFERENCES delivery_zones(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantite INT NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES plats(id)
);
```

4. Adapter les identifiants de connexion dans `config/database.php` si besoin
   (par défaut : host `localhost`, base `fiajou3`, user `root`, pas de mot de passe).
5. Ouvrir `http://localhost/FiaJou3/` — vous êtes redirigé vers la page de connexion.

## Comptes

Il n'existe aucun compte par défaut : utilisez la page d'inscription pour créer un
compte client, puis changez son rôle directement en base (`UPDATE profiles SET role =
'admin' WHERE user_id = ...;`) ou depuis l'espace admin une fois un premier admin créé.

## Devenir partenaire (cuisinier / livreur)

La section "Rejoignez FiaJou3" de la page d'accueil propose deux boutons
"Je m'inscris" (cuisinier / livreur) qui utilisent un flux DÉDIÉ, distinct du
Register client :

1. L'utilisateur clique sur "Je m'inscris" → une modale lui demande uniquement
   son email (boutons Continuer / Annuler + X).
2. Un lien sécurisé et temporaire est envoyé à cet email. Le lien porte le type
   de partenariat choisi (cuisinier ou livreur) et contient un jeton unique
   (64 caractères hexadécimaux, impossible à deviner) stocké dans la table
   `partenaire_invitations` avec une date d'expiration (48 h, usage unique).
3. En cliquant sur le lien, le candidat ouvre le formulaire de complétion
   correspondant (prénom, nom, email pré-rempli non modifiable, téléphone,
   adresse, ville, mot de passe).
4. À la soumission, si l'email correspond déjà à un compte existant, celui-ci
   est mis à jour et promu au rôle partenaire (aucun doublon créé) ; sinon un
   compte partenaire est créé. L'utilisateur est ensuite connecté et redirigé
   vers son espace (cuisinier ou livreur).

Le Register classique des clients (`/inscription`) est totalement inchangé.

### Migration requise

```sql
-- database/migrations/20260810_000000_create_partenaire_invitations.sql
CREATE TABLE IF NOT EXISTS `partenaire_invitations` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(190) NOT NULL,
    `role` ENUM('cuisinier', 'livreur') NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `expire_le` DATETIME NOT NULL,
    `utilise` TINYINT(1) NOT NULL DEFAULT 0,
    `cree_le` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_id` INT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_token` (`token`),
    KEY `idx_email` (`email`),
    CONSTRAINT `fk_partenaire_invitations_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

### Envoi d'emails (config/email.php)

L'envoi est géré par `modele/MailerModele.php`.

- **Par défaut (`EMAIL_ENABLED = false`)** : aucun email réel n'est expédié.
  Chaque message est écrit dans `logs/emails/emails_YYYY-MM-DD.log` (lien
  inclus) : le flux partenaire reste 100 % testable en local.
- **Pour activer l'envoi réel** :
  1. passer `EMAIL_ENABLED` à `true` ;
  2. fournir un transport d'envoi : soit un serveur de messagerie local
     accepté par `mail()` (sous XAMPP : configurer
     `C:\xampp\sendmail\sendmail.ini` et décommenter `sendmail_path` dans
     `php.ini`), soit un relais SMTP réel en passant `EMAIL_UTILISER_SMTP`
     à `true` et en renseignant `EMAIL_SMTP_HOST/PORT/SECURE/USER/PASS` ;
  3. renseigner `EMAIL_FROM` / `EMAIL_FROM_NAME` avec une adresse d'expéditeur
     valide.

Même avec l'envoi actif, une copie est toujours conservée dans
`logs/emails/` pour la traçabilité.
