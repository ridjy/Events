# Events

API gestion d'évènements

php bin/console doctrine:schema:update --force

@Todo
Ajouter des fixtures
php bin/console doctrine:fixtures:load

Utiliser paramConverter

POST creation event
{
"nom":"mon évènement",
"date_debut":"2023-06-03",
"date_fin":"2023-06-03",
"nbr_max_participants":25
}
