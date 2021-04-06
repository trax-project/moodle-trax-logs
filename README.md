# Trax Logs for Moodle

> This plugin transforms Moodle logs into xAPI statements, and sends then to your LRS.


## Why this plugin?

[xAPI](https://adlnet.gov/research/performance-tracking-analysis/experience-api) 
is the leading open standard to leverage your learning data and build learning analytics.

As Moodle is not xAPI-compliant, you need a plugin to transform Moodle data into xAPI data,
and Trax Logs aims to be the best plugin for this job, bringing some key benefits:

* Synchronous & asynchronous communication with the LRS (real-time vs CRON job),
* Automated history transfer from Moodle logs,
* Reliability: communication errors and retry management,
* Configurable list of events to be processed by the plugin,
* Support of the main Moodle events,
* Support of xAPI video activities,
* Support of H5P activities,
* Support of LTI activities,
* Statements designed with best practices in mind,
* Reinforcement of data privacy,
* Simple and extensible architecture,
* Code quality and unit testing,
* Extensive plugin documentation,
* Extensive xAPI documentation ([here](http://doc.xapi.fr/profiles/moodle)).


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
* [Customizing statements](doc/custom.md)
* [xAPI videos integration](doc/vid.md)
* [H5P integration](doc/h5p.md)
* [LTI integration](doc/lti.md)
* [xAPI Identification Services](doc/id.md)
* [Best pratices in designing Statements](doc/best-practices.md)
* [Data privacy](doc/privacy.md)
* [Coding style and unit testing](doc/test.md)

