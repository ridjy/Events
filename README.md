# Events

API gestion d'évènements

les routes disponibles :
events GET ANY ANY /api/events
detailEvent GET ANY ANY /api/events/{id}
createEvent POST ANY ANY /api/events
updateEvent PUT ANY ANY /api/events/{id}
deleteEvent DELETE ANY ANY /api/events/{id}
createparticipant POST ANY ANY /api/inscription

Environnement :
Symfony 6.1
PHP >= 8.0
Mysql 5 or PostgreSQL > 11

1. cloner ce repository
2. composer install (il faut composer à jour et tournant sous PHP8 )
3. configuration de l'accès base de données dans .env

- mettre à jour la base de données en éxecutant les commandes suivantes
  php bin/console doctrine:database:create
  php bin/console doctrine:schema:update --force

4.  lancer l'application dans un serveur ou à l'aide de symfoony CLI
5.  Créer automatiquement des données test pour la base de données acvec la cmd suivante :
    php bin/console doctrine:fixtures:load
6.  créer un évènement
    url POST /api/events
    exemple json posté
    {
    "nom":"mon évènement",
    "date_debut":"2023-06-03T00:00:00+00:00",
    "date_fin":"2023-06-03T00:00:00+00:00",
    "nbr_max_participants":25
    }
7.  modification évènement
    PUT /api/events/{id}
    exemple json posté
    {
    "nbr_max_participants":50
    }
8.  suppression évènement
    DELETE /api/events/{id}
9.  inscription via le nom d'évènement
    POST /api/inscription
    {
    "nom":"rakotmalala",
    "prenom":"eddy",
    "email":"r.ridjy@gmail.com",
    "telephone":"261335885251",
    "event":"mon évènement"
    }
10. détail d'un évènement
    GET /api/events/{id}
    Headers Accept=application/json:version=2.0
11. utilisation du jwt
    creer le dossier /config/jwt/
    openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
    creer le fichier .env.local
    ###> lexik/jwt-authentication-bundle ###

        JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
        JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
        JWT_PASSPHRASE={votre passphrase tapé lors de la cmd creation du clé}

        ###< lexik/jwt-authentication-bundle ###

    /api/login_check POST header Content-Type:application/json
    {
    "username":"user",
    "password":"password"
    }
    on obtiendra un token en retour

12. doc dispo à l'adresse web
    http://127.0.0.1:8000/api/doc

    utiliser la route de login pour s'authentifier
    copier le token généré, sur l'entête insérer le token après le mot clé bearer
    ex : bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9....

Contraintes :

- l'accès aux évènements (CRUD) est maintenant géré par authentification, l'inscription est cependant ouvert sans authentification
- même si l'utilisateur s'inscrit plusieurs fois à un même évènement,
  l'enregistrement sera toujours unique; cette contrainte est gérée par le SGBDR
- après l'étape 11, il faut ajouter un header 'Authorization' puis valeur 'bearer {le token}
- seul un utilisateur adlin peut créer des évènements

@Todo

- refactoring
- reglage affichage date lors détail évènement

13. apiplatform a été installé
