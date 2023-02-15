# Events
API gestion d'évènements

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


@Todo
- Ajouter des fixtures
php bin/console doctrine:fixtures:load
- Utiliser paramConverter
- migrer vers postgreSQL

