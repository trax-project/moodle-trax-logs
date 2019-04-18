# How to support new events

Trax Logs has a flexible architecture that you can extend in order to support new events.
This may be usefull if you want to support Moodle events that are not already supported, 
or if you want to support events handled by your own Moodle plugins.

Of course, you will need to be familiar with PHP coding, 
as well as understanding how to design xAPI Statements following [best practices](best-practices.md).

Once you have identified the events you want to transform into xAPI statements,
you can start implementing statement classes following this guide. 


## Implementing the statement class

To support a new event, you need to implement a single class which inherits from the abstract class `logstore_trax\src\statements\statement`. In this class, you must implement the `statement()` function, which returns the statement to be sent to the LRS, given a Moodle event which is stored in `$this->event`.

Your implementation should use the following services:
* `$this->actors`, which generates actors given a type and a Moodle ID,
* `$this->verbs`, which generates verbs given their code,
* `$this->activities`, which generates activities given a type and a Moodle ID.

For furthur details, you can look at the source code which is documented.


## Naming the statement class

Your statement class must be correctly named in order to be detected by the plugin.
The name is based on the name of the Moodle event. For example:
* `core\event\course_module_viewed` must be implemented by a class named `course_module_viewed`.
* `mod_forum\event\course_module_viewed` must also be implemented by a class named `course_module_viewed`.
* `core\event\user_loggedout` must be implemented by a class named `user_loggedout`.


## Placing the statement class in the right folder

For Moodle native events, statement classes must be located in `TRAX_PLUGIN/classes/src/statements/CORE_OR_PLUGIN_NAME/`.
For example:
* `core\event\course_module_viewed` must be implemented by a class located in `TRAX_PLUGIN/classes/src/statements/core/`.
* `mod_forum\event\course_module_viewed` may be implemented by a class located in `TRAX_PLUGIN/classes/src/statements/mod_forum/`, or in `TRAX_PLUGIN/classes/src/statements/core/`.

For events coming from third-party plugins, statement classes must be located in `THIRD_PARTY_PLUGIN/classes/xapi/statements/`.


## Using the right namespace

The namespace of the statement class must be consistent with its location.
For instance:
* If the class is located in `TRAX_PLUGIN/classes/src/statements/core/`, the namespace must be `logstore_trax\src\statements\core`.
* If the class is located in `TRAX_PLUGIN/classes/src/statements/mod_forum/`, the namespace must be `logstore_trax\src\statements\mod_forum`.
* If the class is located in `PLUGIN_TIERS/classes/xapi/statements/`,the namespace must be `THIRD_PARTY_PLUGIN\xapi\statements`.


## Implementing a custom activity class

Activity classes are implemented in `TRAX_PLUGIN/classes/src/activities`.

For events coming from third-party plugins, you can implement custom activity classes in `THIRD_PARTY_PLUGIN/classes/xapi/activities/`.


## Contents

* [Overview](../README.md)
* [Installation and configuration](install.md)
* [Supported events](events.md)
* [Supporting new events](extend.md)
* [Coding style and unit testing](test.md)
* [Best pratices in designing Statements](best-practices.md)
* [Data privacy](privacy.md)
