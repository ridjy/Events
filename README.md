# Events
API gestion d'évènements

1) cloner ce repository
2) composer install
3) configuration de l'accès base de données dans .env
4) lancer l'application dans un serveur ou àç l'aide de symfoony CLI

php bin/console doctrine:schema:update --force

@Todo
- Ajouter des fixtures
php bin/console doctrine:fixtures:load

- Utiliser paramConverter
- migrer vers postgreSQL

- POST creation event
{
"nom":"mon évènement",
"date_debut":"2023-06-03",
"date_fin":"2023-06-03",
"nbr_max_participants":25
}
