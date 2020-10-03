# snowtricks
<p>Projet 6 du parcours développeur d'application PHP/Symfony chez OpenClassrooms :</p>
<p>Création d'un site collaboratif de partage de figures de snowboard via le framework Symfony</p>
<p>Réalisé en PHP 7.3.5 et Symfony 5.1.2</p>
<hr />
<a href="https://codeclimate.com/github/glerique/snowtricks/maintainability"><img src="https://api.codeclimate.com/v1/badges/336362315ff88c62e1c8/maintainability" /></a>
<hr />
Installation

    - Clonez le repository GitHub
    
    - Configurez vos variables d'environnement dans le fichier .env :
    
      => MAILER_DSN=smtp://utilisateur:password@smtp.gmail.com:587 pour le mailer
      => DATABASE_URL=mysql://root:@127.0.0.1:3306/snowtricks?serverVersion=5.7 pour la base de données
      
    - Téléchargez et installez les dépendances du projet avec la commande Composer suivante : composer install
    
    - Créez la base de données en utilisant la commande suivante : php bin/console doctrine:database:create
    
    - Installer les fixtures pour avoir un jeu de données fictives avec la commande suivante : php bin/console doctrine:fixtures:load
    
    - Lancez le serveur à l'aide de la commande suivante : php -S localhost:8000 -t public
    
    - Vous pouvez désormais commencer à utiliser l'appication Snowtricks sur http://localhost:8000/
    

    
