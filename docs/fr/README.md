# Trax Logs for Moodle

> Ce plugin génère les traces xAPI reflétant l'activité de l'apprenant dans Moodle, et les enregistre dans un LRS .


## Pourquoi ce plugin ?

L'idée de transformer des événements issus de Moodle en traces xAPI n'est pas nouvelle. 
Elle a été expérimentée avec le plugin [Logstore xAPI](https://moodle.org/plugins/view/logstore_xapi).

Trax Logs for Moodle est un nouveau plugin dont le but est d'apporter quelques améliorations significatives :
* [L'application de bonnes pratiques concernant la définition des traces xAPI](best-practices.md) ;
* [Un renforcement de la protection des données personnelles](privacy.md) ;
* [Une architecture simple à comprendre, à maintenir et à enrichir](tech.md).


## Evénements actuellement pris en charge

La version actuelle permet de tracer la [navigation générale de l'apprenant au sein de la plateforme](events.md) :
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

Je vous encourage toutefois à l'[installer](tech.md), à le tester, et à partager vos impressions.



