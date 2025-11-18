Digitalfy - Site Freelance (Symfony / Twig / Tailwind)

Ce projet est le site vitrine et portfolio pour Digitalfy, activité de développeur freelance (Web & Mobile) basé à Nîmes. Le site est conçu avec une approche SEO-first (Rendu serveur) et Mobile-first.

🛠 Stack Technique

Framework Back-end : Symfony 7.3

Langage : PHP 8.2+

Base de données : MariaDB / MySQL

Front-end : Twig (Moteur de template) + Tailwind CSS

Administration : EasyAdmin 4

Gestion des Assets : AssetMapper (ou Webpack Encore)

Uploads : VichUploaderBundle

🚀 Installation & Configuration

Pré-requis

PHP 8.2 ou supérieur

Composer

Serveur SQL (MariaDB ou MySQL)

Node.js & NPM (si utilisation de build pour Tailwind)

Étapes d'installation


Installer les dépendances PHP

composer install


Configuration de l'environnement
Dupliquer le fichier .env en .env.local et configurer la connexion base de données :

# .env.local
DATABASE_URL="mysql://user:password@127.0.0.1:3306/digitalfy_db?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
MAILER_DSN=...


Base de données

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate


Assets & Styles

php bin/console asset-map:compile
# OU si Webpack Encore :
npm install
npm run build


Lancer le serveur local

symfony server:start


📚 Document Technique : Architecture des Données & Spécifications EasyAdmin

Cette section détaille la structure de la base de données et le comportement attendu du Dashboard d'administration (/admin).

1. Modélisation des Données (Entités)

A. Entité BlogPost (Articles de Blog)

Objectif : Gérer le contenu éditorial pour le SEO local et métier.

Propriété

Type

Description / Contraintes

id

Integer

Primary Key

title

String (255)

H1 de l'article. Obligatoire.

slug

String (255)

Unique. Généré auto (Gedmo) depuis le titre.

summary

Text

Chapeau/Extrait pour les listes (Card).

content

Text (Long)

Contenu HTML (WYSIWYG / CKEditor).

imageName

String (255)

Géré via VichUploader.

publishedAt

DateTimeImm

Date de publication visible.

status

String (Enum)

DRAFT ou PUBLISHED.

metaTitle

String (70)

Optimisation SEO (balise <title>).

metaDesc

String (160)

Optimisation SEO (balise meta description).

category

ManyToOne

Relation vers BlogCategory. Obligatoire.

createdAt

DateTimeImm

Timestampable.

B. Entité BlogCategory

Objectif : Catégoriser les articles (ex: "Dev Mobile", "Restaurateurs").

Propriété

Type

Description / Contraintes

id

Integer

Primary Key

name

String (255)

Nom de la catégorie.

slug

String (255)

Unique. URL : /blog/categorie/{slug}.

description

Text

Description pour le SEO de la page catégorie.

C. Entité Project (Portfolio)

Objectif : Présenter les études de cas et réalisations.

Propriété

Type

Description / Contraintes

id

Integer

Primary Key

title

String (255)

Titre du projet.

slug

String (255)

Unique.

clientName

String (255)

Nom du client (facultatif).

summary

Text

Intro courte.

description

Text

Étude de cas complète (Problème/Solution/Résultat).

stack

Json (Array)

Ex: ["Symfony", "React Native", "Expo"].

imageName

String (255)

Image de couverture du projet.

url

String (255)

Lien vers le projet en ligne (si applicable).

isFeatured

Boolean

Si true, s'affiche sur la page d'accueil.

D. Entité ContactRequest

Objectif : Historiser les leads entrants depuis le formulaire de contact.

Propriété

Type

Description / Contraintes

id

Integer

Primary Key

fullName

String (255)

Nom complet du prospect.

email

String (255)

Email valide.

phone

String (20)

Téléphone (facultatif).

projectType

String (50)

Type (Site Vitrine, App Mobile, Resto, Autre).

budget

String (50)

Tranche budgétaire estimée.

message

Text

Message libre du prospect.

status

String (Enum)

NEW (défaut), IN_PROGRESS, CLOSED.

adminNotes

Text

Notes internes invisibles pour le client.

createdAt

DateTimeImm

Date de réception.

2. Spécifications Dashboard (EasyAdmin)

Le Backoffice est accessible via /admin. Il permet la gestion complète du contenu sans toucher au code.

Navigation (Menu Latéral)

Dashboard (Vue d'ensemble, KPI rapides).

Blog

Articles (BlogPost)

Catégories (BlogCategory)

Portfolio

Projets (Project)

Business

Demandes de contact (ContactRequest) - Badge compteur "Nouveaux"

Règles Métier & UX Admin

Gestion des Articles (BlogPostController) :

Formulaire : Utilisation d'onglets (Tabs) pour séparer le contenu rédactionnel (Content, Summary, Image) des champs techniques SEO (MetaTitle, MetaDesc, Slug).

Liste : Filtres obligatoires sur Category et Status. Recherche textuelle sur Title.

Images : Prévisualisation des thumbnails dans la liste.

Gestion des Contacts (ContactRequestController) :

Accès : Mode "Lecture seule" pour les données du client (Nom, Email, Message) pour éviter toute altération accidentelle.

Traitement : Seuls les champs Status et AdminNotes sont modifiables par l'administrateur.

Tri : Par défaut, les demandes les plus récentes (createdAt DESC) apparaissent en haut.

Actions : Suppression (DELETE) possible pour le nettoyage RGPD. Création (NEW) désactivée.

3. Logique Technique Spécifique

Workflow de Contact

Lorsqu'un utilisateur soumet le formulaire sur /contact :

Données validées par Symfony Form.

Persistance de l'entité ContactRequest en base de données (Statut = NEW).

Déclenchement d'un Event ou appel Mailer :

Envoi d'un email de notification à l'administrateur.

(Optionnel) Email d'accusé de réception au prospect.

SEO Dynamique

Les contrôleurs Front-end injectent les métadonnées dynamiquement dans les templates Twig (base.html.twig) :

Si BlogPost.metaTitle est rempli → Utiliser cette valeur.

Sinon → Utiliser BlogPost.title.

Idem pour la metaDescription (fallback sur summary).

🧪 Tests & Qualité

Linting Twig : php bin/console lint:twig templates

Analyse Statique : PHPStan (niveau recommandé : 5)

Tests : PHPUnit (Tests unitaires Entités + Tests fonctionnels Routes)
