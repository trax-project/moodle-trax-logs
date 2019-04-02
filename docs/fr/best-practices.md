[This page has been translated in english.](../en/best-practices.md)

# Bonnes pratiques relatives à la conception des Statements

## Vocabulaire utilisé

Il n’existe actuellement aucun profil xAPI officiel concernant le suivi des activités d’un LMS. 
Ce projet doit donc contribuer à la définition d’un tel profil.

A de rares exceptions près, le vocabulaire actuellement utilisé est fédéré et documenté sous le domaine http://vocab.xapi.fr.
Bien qu'il ne s'agisse pas d'un domaine officiellement reconnu pour définir un vocabulaire xAPI, 
nous l'utilisons comme un cadre de travail, permettant de définir et de documenter avec une terminologie complète et cohérente pour le sujet qui nous concerne.

Ce domaine doit être considéré comme un alias temporaire. A terme, l'objectif est de la remplacer par une identification officielle, 
idéalement fédérée sous le domaine http://w3id.org/xapi. Une table de correspondance définitive sera alors établie afin de faciliter les migrations.


## Identification des acteurs

Le plugin Trax Logs identifie les acteurs en utilisant le format "account".

La propriété "account.homePage" fait référence à la plateforme Moodle utilisée. 
Elle s’appuie sur un identifiant de plateforme (IRI) qui doit être précisé dans les réglages du plugin. 
L’IRI renseignée doit être définitive. Elle diffère en cela de l’URL de la plateforme Moodle 
qui est susceptible de changer dans le temps (ex. migration vers un autre domaine ou sous-domaine).

La propriété "account.name" permet d’identifier l’acteur au sein de la plateforme Moodle. 
Le plugin utilise pour cela un UUID attribué à chaque acteur, identifiant qui peut être considéré comme stable dans le temps. 
L’UUID attribué diffère en cela du nom d'utilisateur, de l'adresse email, et même de l’identifiant interne à Moodle, 
qui sont tous susceptibles de changer dans le temps.

L'autre avantage de cette technique est que l'UUID garantit que l'utilisateur reste identifié de manière anonyme.
Se référer à la documentation sur la [protection des données personnelles](privacy.md) pour plus de détails.


## Identification des activités

Trax Logs génère des IRI pour toutes les activités concernées en suivant un schéma systématique. 
Ce schéma s’appuie sur un identifiant de plateforme (IRI) qui doit être renseigné dans les réglages du plugin, 
mais aussi sur les types d’activités impliqués, ainsi que sur un UUID généré pour chaque activité.

Cette approche permet de définir des IRI stables dans le temps, contrairement aux URL des activités 
qui sont susceptibles d’évoluer (ex. changement de domaine, migration entre 2 plateformes).


## Types d'activités

Trax Logs injecte les types d’activité dans les Statements de manière systématique. 

Les types d’activités sont répartis entre :
* Activités structurelles : système, cours, unité d'apprentissage ;
* Types d’unités d’apprentissage : forum, chat, devoir, etc.
* Autres types : profil xAPI, niveau de granularité.

Ces types d’activités ont été définis de la manière la plus générique possible. En particulier : 
* Aucune référence à des concepts qui seraient propres à Moodle ;
* Généralisation de certains concepts trop spécifiques ou techniques (ex. les packages IMSCP et SCORM sont indifféremment typés "contenus Web").

Pour plus de détails, se référer à http://vocab.xapi.fr.


## Activités contextuelles

Trax Logs définit pour tous les Statements :
* Une activité "category" précisant le profil xAPI, ici "http://vocab.xapi.fr/categories/vle-profile".

Trax Logs définit pour tous les Statements dont l’objet n'est la plateforme elle-même :
* Une activité "grouping" identifiant la plateforme Moodle impliquée, de type "système" ;

Trax Logs définit pour tous les Statements dont l’objet est une activité de type "unité d'apprentissage" (correspondant aux modules Moodle) :
* Une activité parente qui correspond au cours dans lequel l’activité a lieu ;
* Une activité "category" de type "unité d'apprentissage", qui permet de matérialiser le niveau de granularité ;
 

## Eléments descriptifs

Trax Logs ne renseigne pas les noms des acteurs pour garantir l'anonymisation.

Les intitulés des verbes ne sont pas non plus renseignés pour alléger les Statements. Les outils de reporting seront chargés d'associer les intitulés dans la langue souhaitée.

Les noms et descriptions des activités sont transmis chaque fois que l’activité est l’objet du Statement. 
Ils ne sont en revanche pas transmis pour les activités contextuelles, afin d’alléger le poids des Statements.
On part du principe que les activités contextuelles importantes (ex. système, cours) seront transmises en tant qu'objet de Statements spécifiques (ex. connexion au système, inscription ou accès au cours, etc.), et que le LRS accèdera à leurs noms et descriptions par ce biais.


## Extensions

3 extensions d'activités sont actuellement utilisées :
- `http://vocab.xapi.fr/extensions/platform-concept` pour définir le type d'activité tel qu'il apparait dans Moodle (ex. book) ;
- `http://vocab.xapi.fr/extensions/concept-familly` pour classer l'activité dans une famille plus large ;
- `http://vocab.xapi.fr/extensions/standard` pour préciser si l'activité se conforme à un standard officiel (ex. scorm).

Pour plus de détails, se référer à http://vocab.xapi.fr.


## Registration

Actuellement, Trax Logs ne transmet pas de "registration", mais cette possibilité doit être étudiée pour les activités de type "unités d'apprentissage", 
afin de faciliter le regroupement des Statements décrivant l'expérience d'apprentissage d'un utilisateur pour chaque unité d'apprentissage.


## Autres éléments

A ce stade, les seuls autres éléments d'information transmis par Trax Logs sont :
* Timestamp
* Context.platform (Moodle)

La génération des identifiants de Statements est laissée à l'initiative du LRS.


## Sommaire

* [En bref](README.md)
* [Evénements pris en charge par le plugin](events.md)
* [Bonnes pratiques relatives à la conception des Statements](best-practices.md)
* [Protection des données personnelles](privacy.md)
* [Documentation technique](tech.md)
