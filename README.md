# Trax Logs for Moodle

> Ce plugin génère les traces xAPI reflétant l'activité de l'apprenant dans Moodle, et les enregistre dans un LRS .


## Pourquoi ce plugin ?

L'idée de transformer des événements issus de Moodle en traces xAPI n'est pas nouvelle. 
Elle a été expérimentée avec le plugin [Logstore xAPI](https://moodle.org/plugins/view/logstore_xapi).

Les retours d'expérience que j'ai pu collecter m'ont toutefois conduit à imaginer un nouveau plugin apportant plusieurs améliorations clés :
* [L'application de bonnes pratiques concernant la définition des traces xAPI](docs/best-practices.md) ;
* [Un renforcement de la protection des données personnelles](docs/privacy.md) ;
* [Une architecture simple à comprendre, à maintenir et à enrichir](docs/tech.md).


## Evénements actuellement pris en charge

La version actuelle permet de tracer la [navigation générale de l'apprenant au sein de la plateforme](docs/events.md) :
* Connexion et déconnexion à la plateforme ;
* Accès aux cours ;
* Accès à tous les types de ressources et activités standards de Moodle (hors devoir).

La palette des traces générées sera progressivement élargie aux domaines suivants :
* Progression, complétion, réussite et acquisition de compétences ;
* Interactions spécifiques à chaque type d'activité.


## Maturité du plugin

Trax Logs est actuellement en version Alpha. Il est susceptible de subir des transformations significatives. 
C'est notamment le cas du formatage des traces xAPI dont la définition va s'affiner dans les prochains mois.
A ce stade, il n'est donc pas conseillé de l'utiliser en production.

Je vous encourage toutefois à l'[installer](docs/tech.md), à le tester, et à partager vos impressions.


## Roadmap

* Gestion des échecs de transmission des Statements avec possibilité de reprise
* Envoi des Statements par tâche planifiée (CRON)
* Traitement des logs passés issus de la table de logs standard de Moodle
* Fonction de droit à l'oubli / refus de suivi d'un utilisateur
* Import/export de la table des acteurs 
* Import/export de la table des activités 
* Ajout d'un système de génération de Registration
* Définition des extensions d'activités, pour tous les types d'activités (incl. local-type = Moodle type)
* Prise en charge des catégories de cours et sections de cours dans les Statements
* Prise en charge des informations de progression, complétion, réussite et acquisition de compétences
* Prise en charge des événements spécifiques à chaque activité standard de Moodle
* Activation ou désactivation des groupes d'événements à traiter
* Finalisation du vocabulaire utilisé


