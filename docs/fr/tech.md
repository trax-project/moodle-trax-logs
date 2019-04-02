[This page has been translated in english.](../en/tech.md)

# Documentation technique

## Installation

[Télécharger la dernière version du plugin pour Moodle 3.5.](https://github.com/trax-project/moodle-trax-logs/releases)

Faites glisser le fichier ZIP dans "http://mon-adresse-moodle.com/admin/tool/installaddon/index.php". 

Pour une installation manuelle, dézippez le fichier dans le dossier "mon-install-moodle/admin/tool/log/store/",
puis allez dans l'administration de Moodle qui détectera la présence du plugin.

Dans les 2 cas, confirmez l'installation du plugin et suivez les indications de paramétrage.

Une fois installé, le plugin doit être activité dans "Administration > Plugins > Logstore".


## Tests unitaires

Un jeu de tests est fourni avec le plugin.
Il permet de tester la génération et l'envoi au LRS de tous les types d'événements supportés.
Les tests ne vérifient toutefois pas que le LRS a bien enregistré les Statements. 
Pour le moment, cette vérification doit se faire manuellement dans le LRS.

Pour lancer le test :

1. Assurez-vous que Moodle est configuré pour utiliser PHPUnit : https://docs.moodle.org/dev/PHPUnit.
2. Si cela n'a pas été fait depuis l'installation du plugin, réinitialisez l'environnement de test en exécutant :
```
php admin/tool/phpunit/cli/init.php
```
2. Dans "admin/tool/log/store/trax/tests/store_test.php", modifiez les paramètres d'accès au LRS en début de script.
3. Lancez le test en exécutant : 
```
vendor/bin/phpunit store_test admin/tool/log/store/trax/tests/store_test.php
```

## Ajout de nouveaux événements

### Classe à implémenter

Chaque événement émis par Moodle peut être pris en charge en implémentant une classe héritant de la classe abstraite "logstore_trax\statements\Statement".
Cette classe doit au minimum implémenter la fonction "statement()", qui retourne la structure du Statement à envoyer au LRS,
définie à partir de l'événement Moodle accessible par "$this->event".

La classe ainsi définie peut s'appuyer sur plusieurs services :
* $this->actors, qui génère des acteurs à partir de leur type et de leur identifiant interne Moodle ;
* $this->verbs, qui génère des verbes à partir de leur code ;
* $this->activities, qui génère des activités à partir de leur type et de leur identifiant interne Moodle.

Pour plus de détail, se reporter au code qui est documenté.

### Nommage de la classe

Pour être détectée, la classe implémentée doit être correctement nommée.
Son nom doit reprendre le nom de l'événement, sans les underscore, et avec une majuscule au début de chaque mot.
Par exemple :
* "core\event\course_module_viewed" est traité avec une classe nommée CourseModuleViewed.
* "mod_forum\event\course_module_viewed" est aussi traité avec une classe nommée CourseModuleViewed.
* "core\event\user_loggedout" est  traité avec une classe nommée CourseLoggedout.

### Localisation du fichier

Les événements issus du code natif de Moodle doivent être traités au sein du plugin Trax Logs,
dans le dossier "TRAX_PLUGIN/classes/statements/CORE_OU_NOM_DU_PLUGIN/".
Par exemple :
* Pour "core\event\course_module_viewed", la classe doit être localisée dans "TRAX_PLUGIN/classes/statements/core/".
* Pour "mod_forum\event\course_module_viewed", la classe peut être localisée dans "TRAX_PLUGIN/classes/statements/mod_forum/". Si elle n'y est pas, elle sera cherchée dans "TRAX_PLUGIN/classes/statements/core/".

Les événements issus de plugins tiers doivent être traités au sein du plugin tiers, dans le dossier "PLUGIN_TIERS/classes/xapi/statements/".

### Namespace de la classe

Le namespace de la classe doit être cohérent avec sa localisation. Par exemple :
* Si la classe est localisée dans "TRAX_PLUGIN/classes/statements/core/", le namespace est "logstore_trax\statements\core".
* Si la classe est localisée dans "TRAX_PLUGIN/classes/statements/mod_forum/", le namespace est "logstore_trax\statements\mod_forum".
* Si la classe est localisée dans "PLUGIN_TIERS/classes/xapi/statements/", le namespace est "PLUGIN_TIERS\xapi\statements".

### Implémentation d'une classe d'activité

Les classes d'activités implémentées au sein du plugin sont localisée dans "TRAX_PLUGIN/classes/activities".

Pour les plugins tiers, il est possible d'implémenter des classes d'activités spécifiques
qui doivent être localisées dans "PLUGIN_TIERS/classes/xapi/activities/".


## Sommaire

* [En bref](README.md)
* [Evénements pris en charge par le plugin](events.md)
* [Bonnes pratiques relatives à la conception des Statements](best-practices.md)
* [Protection des données personnelles](privacy.md)
* [Documentation technique](tech.md)

