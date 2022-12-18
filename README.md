# P07_IncafAitOumaksa_251122
Projet BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.


## Environnement nécessaire :

    - Symfony 6.1
    
    - Composer 2.4.2
    
    - MySql 5.7.24

## Suivre les étapes suivantes :

## Installation :

Etape 1 : 

1 - Clonez ou téléchargez le repository GitHub dans le dossier voulu :

     composer install 
     
Etape 2 : 
 
2 - pour renseigner vos paramètres de connexion à votre base de donnée dans la variable DATABASE_URL.

  1 - Démarrer votre environnement local (Par exemple : Wamp Server).
  
  2 -  Exécuter les commandes symfony suivantes depuis votre terminal.
  
        symfony console doctrine:database:create (ou php bin/console d:d:c si vous n'avez pas installé le client symfony)
       
        symfony console doctrine:migrations:migrate
        
        symfony console doctrine:fictures:load  
        
  3 - Générer vos clés pour l'utilisation de JWT Token .

        $ mkdir -p config/jwt
        
        $ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
        
        $ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
        
   4 - Renseigner vos paramètres de configuration dans votre ficher .env.local .
   
       
        ###> lexik/jwt-authentication-bundle ###
        
        JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
        
        JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
        
        JWT_PASSPHRASE=VotrePassePhrase
        
        ###< lexik/jwt-authentication-bundle ###
        
   
 ## Lancez l'application :
   
 - Lancez l'environnement d'exécution Apache / Php en utilisant:
   
       php bin/console server:start
 
 ## Documentation API :
 
       https://localhost:8000/api/doc
    

