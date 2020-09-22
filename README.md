# snowtricks
Site communautaire SnowTricks
<hr />
<a href="https://codeclimate.com/github/glerique/snowtricks/maintainability"><img src="https://api.codeclimate.com/v1/badges/336362315ff88c62e1c8/maintainability" /></a>
<hr />
Projet 5 de mon parcours Développeur d'application PHP/Symfony chez OpenClassrooms. Création d'un site collaboratif pour faire connaître ce sport auprès du grand public et aider à l'apprentissage des figures (tricks).

Installation

- Clonez le repository GitHub
- Configurez vos variables d'environnement tel que la connexion à la base de données dans le fichier .env.
- Téléchargez et installez les dépendances du projet avec la commnde Composer suivante : composer instal
- Créez la base de données en utilisant la commande suivante : php bin/console doctrine:database:create
- Installer les fixtures pour avoir un jeu de données fictives avec la commande suivante : php bin/console doctrine:fixtures:load
- Vous pouvez désormais commencer à utiliser l'appication Snowtricks 
