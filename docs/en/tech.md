# Technical documentation

## Installation

Download the plugin for you Moodle version: https://moodle.org/plugins/logstore_trax.

Drag and drop the ZIP file in "http://my-moodle-address.com/admin/tool/installaddon/index.php". 

For a manual instllation, unzip the plugin file in "my-moodle-install-folder/admin/tool/log/store/".
Then, go to the Moodle administration area. The presence of the plugin will be detected.

In both cases, confirm the plugin installation and follow the procedure and settings instructions.

Once it is done, you must activate the plugin in "Administration > Plugins > Logstore".


## Testing

A test file is included with the plugin, so you can test all the supported events 
by generating the matching Statements and sending them to your LRS.
However, the test function doesn't check that the Statements are successfully sent to the LRS.
So you will have to open your LRS to see if the Statements are recorded.

To launch the test:

1. Check that your Moodle environment is configured to run PHPUnit: https://docs.moodle.org/dev/PHPUnit.
2. Reinitialize the testing environment if it has not been done since the plugin installation: 
```
php admin/tool/phpunit/cli/init.php
```
2. In "admin/tool/log/store/trax/tests/store_test.php", change the LRS access params at the begining of the script.
3. Launch the test with: 
```
vendor/bin/phpunit store_test admin/tool/log/store/trax/tests/store_test.php
```

## Supporting new events

### Implementing the Statement class

To support a new event, you need to implement a single class which inherits from the abstract class "logstore_trax\statements\Statement".
In this class, you must implement the "statement()" function, which returns the Statement to be sent to the LRS, given a Moodle event
which is stored in "$this->event".

Your implementation should use some available services:
* $this->actors, which generates actors given a type and a Moodle ID,
* $this->verbs, which generates verbs given their code,
* $this->activities, which generates activities given a type and a Moodle ID.

For furthur details, you can look at the source code which is documented.


### Naming the Statement class

Your Statement class must be correctly named in order to be detected by the plugin.
The name is based on the name of the Moodle event, without the underscores, and with an uppercase to the first letter of each word.
For example:
* "core\event\course_module_viewed" must be implemented by a class named CourseModuleViewed.
* "mod_forum\event\course_module_viewed" must also be implemented by a class named CourseModuleViewed.
* "core\event\user_loggedout" must be implemented by a class named CourseLoggedout.

### Placing the Statement class in the right folder

For Moodle native events, Statement classes must be located in "TRAX_PLUGIN/classes/statements/CORE_OR_PLUGIN_NAME/".
For example:
* "core\event\course_module_viewed" must be implemented by a class located in "TRAX_PLUGIN/classes/statements/core/".
* "mod_forum\event\course_module_viewed" may be implemented by a class located in "TRAX_PLUGIN/classes/statements/mod_forum/", or in "TRAX_PLUGIN/classes/statements/core/".

For events coming from third-party plugins, Statement classes must be located in "THIRD_PARTY_PLUGIN/classes/xapi/statements/".

### Using the right namespace

The namespace of the Statement class must be consistent with its location.
For instance:
* If the class is located in "TRAX_PLUGIN/classes/statements/core/", the namespace must be "logstore_trax\statements\core".
* If the class is located in "TRAX_PLUGIN/classes/statements/mod_forum/", the namespace must be "logstore_trax\statements\mod_forum".
* If the class is located in "PLUGIN_TIERS/classes/xapi/statements/",the namespace must be "THIRD_PARTY_PLUGIN\xapi\statements".

### Implementing a custom activity class

Activity classes are implemented in "TRAX_PLUGIN/classes/activities".

For events coming from third-party plugins, it is possible to implement custom activity classes in "THIRD_PARTY_PLUGIN/classes/xapi/activities/".



