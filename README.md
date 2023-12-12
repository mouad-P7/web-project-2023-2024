# ENAK EVENTS

-SiteWeb complet pour la gestion d’événements, offrant des fonctionnalités aux organisateurs pour créer, gérer, et promouvoir leurs événements, ainsi qu'aux participants pour s'informer et s'inscrire.

## FEATURES WE IMPLEMENTED

- Page d'accueil présentant les événements à venir. (index.php)
- Système d'authentification. (login.php / logout.php / register.php)
- Page pour chaque événement avec informations complètes. (event.php)
- Système d'insciption au événement. (registerEvent.php / unregisterEvent.php)
- Panneau d'administration pour CRUD toutes les événements. (createEvent.php / viewEvents.php / editEvent.php / deleteEvent.php / eventDashboard.php)

## INSTALATION

- Create a database called: database_1
- Import the file: sql/database.sql
- Start Apache and MySQL.
- If you want to upload your own photos, add them in images folder.
- If you want to use google maps localisation, create config.php file in utils folder and copy paste the code below:

```
<?php
$googleMapsApiKey = "PUT YOUR GOOGLE MAPS API KEY HERE";
if (!$googleMapsApiKey) {
  die('Google Maps API key not set.');
}
?>
```

If you want to customize the style of the website add you CSS files in styles folder.

Now you're ready to go 😀.
