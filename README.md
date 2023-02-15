# Events
API gestion d'évènements

Environnement : 
Symfony 6.1
PHP >= 8.0
Mysql 5 or PostgreSQL 13

1) cloner ce repository
2) composer install (il faut composer à jour et tournant sous PHP8 )
3) configuration de l'accès base de données dans .env
 * mettre à jour la base de données en éxecutant les commandes suivantes 
 php bin/console doctrine:database:create
 php bin/console doctrine:schema:update --force
4) lancer l'application dans un serveur ou à l'aide de symfoony CLI
5) créer un évènement
url POST /api/events
exemple json posté
{
"nom":"mon évènement",
"date_debut":"2023-06-03",
"date_fin":"2023-06-03",
"nbr_max_participants":25
}
6) modification évènement
PUT /api/events/{id}
exemple json posté
{
"nbr_max_participants":50
}
7) suppression évènement
DELETE /api/events/{id}
8) inscription via le nom d'évènement
POST /api/inscription
{
"nom":"rakotmalala",
"prenom":"eddy",
"email":"r.ridjy@gmail.com",
"telephone":"261335885251",
"event":"mon évènement"
}

Contraintes : 
- même si l'utilisateur s'inscrit plusieurs fois à un même évènement, 
l'enregistrement sera toujours unique; cette contrainte est gérée par le SGBDR

@Todo
- gestion d'erreurs avec Validator
- Ajouter des fixtures
php bin/console doctrine:fixtures:load
- reglage affichage date lors détail évènement


