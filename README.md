# Trax Logs for Moodle

> This plugin transforms Moodle logs into xAPI statements, and sends then to your LRS.


## Why this plugin?

The idea of transforming Moodle logs into xAPI Statements is not new. 
It has been experimented with [Logstore xAPI](https://moodle.org/plugins/view/logstore_xapi).

However, Trax Logs for Moodle aims to bring some key improvements:
* [Automatically synched with you Moodle history](doc/config.md),
* [Integration of H5P activities](doc/h5p.md),
* [Integration of LTI activities](doc/lti.md),
* [Reinforcement of data privacy](doc/privacy.md),
* [Simple and extensible architecture](doc/extend.md),
* [Code quality and unit testing](doc/test.md),
* [Statements designed with best practices in mind](doc/best-practices.md),
* [Documented xAPI profile](http://doc.xapi.fr/profiles/moodle),
* Extensive documentation (here).


## Currently supported events

The current version of this plugin supports [a limited number of events](doc/events.md):
* Login and logout,
* Course access, course module access,
* A few H5P events.

The number of supported events will increase during the next months, including:
* Progress, completion, success and competency development,
* Specific interactions for each type of course module.


## Plugin maturity

Trax Logs is currently in Alpha version, which means that significant changes may come.
For example, the structure of generated Statements may change, as well as the vocabulary used.
So we recommend not to use this version on a production server.

However, you are encouraged to [install](doc/install.md) the plugin, to test it, and to share feedbacks.


## Contents

* [Overview](README.md)
* [Installation](doc/install.md)
* [Configuration](doc/config.md)
* [Supported events](doc/events.md)
* [Supporting new events](doc/extend.md)
* [H5P integration](doc/h5p.md)
* [LTI integration](doc/lti.md)
* [xAPI Identification Services](doc/id.md)
* [Best pratices in designing Statements](doc/best-practices.md)
* [Data privacy](doc/privacy.md)
* [Coding style and unit testing](doc/test.md)

