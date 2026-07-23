# FiaJou3 — Plateforme de commande de repas

Réorganisation en architecture MVC PHP. Voir `RAPPORT_MIGRATION.md` (fourni séparément)
pour le détail complet de la migration.

## Structure

```
FiaJou3/
├── assets/
│   ├── css/        Feuilles de style (app.css, auth.css)
│   ├── js/          Scripts JS (lang-switch.js)
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
