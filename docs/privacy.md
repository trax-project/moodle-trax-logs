# Protection des données personnelles

La protection des données personnelles est au coeur des préoccupations de ce projet.

Afin d'exercer un meilleur contrôle, Trax Logs applique un principe de séparation claire des responsabilités entre LMS et LRS :
* Le LRS n'accueille que des données anonymisées, ne permettant à elles seules de faire aucun lien avec les utilisateurs qui sont à leur origine.
* Le LMS gère des utilisateurs clairement identifiés, et est à ce titre responsable de la protection des données personnelles.

Afin d'anonymiser les données envoyées au LRS, Trax Logs attribue à chaque utilisateur Moodle un identifiant anonyme (UUID).
C'est cet identifiant qui est utilisé au sein des Statements, dans la propriété "actor.account.name". 
Cet identifiant est stable dans le temps, y compris si l'utilisateur change de nom d'utilisateur ou d'adresse email au sein de Moodle.

Trax Logs maintient donc une table de correspondance entre identifiants Moodle et UUID attribués. 
Cette table de correspondance est susceptible d’être utilisée par des fonctions de reporting au sein de Moodle afin d'associer les traces anonymisées aux utilisateurs réels.

Lorsqu’un utilisateur exerce son droit à l’oubli, il est tout simplement retiré de la table de correspondance. 
Les Statements du LRS peuvent ainsi être préservés puisque plus rien ne les relie aux individus concernés.
Cette concervation présente un intérêt en terme d'analyse de données statistiques.

Actuellement, le plugin ne permet pas de présenter à l'utilisateur les données qui le concernent, à l'exception de son UUID.
C'est donc au LRS d'assurer le droit d'accès aux données personnelles et leur transférabilité.
Il suffit pour cela à l'utilisateur de fournir son UUID.

