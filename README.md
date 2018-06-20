# Trax Logs for Moodle

> This plugin transforms Moodle logs into xAPI statements, and sends then to your LRS.

[Documentation en français](docs/fr/README)

## Why this plugin?

The idea of transforming Moodle logs into xAPI Statements is not new. 
It has been experimented with [Logstore xAPI](https://moodle.org/plugins/view/logstore_xapi).

Trax Logs for Moodle is a completely new plugin which aims to bring some key improvements:
* [Statements design following best practices](docs/en/best-practices.md),
* [Reinforcement of data privacy](docs/en/privacy.md),
* [Simple and extensible architecture](docs/en/tech.md).


## Currently supported events

The current version of the plugin supports [general navigation events](docs/en/events.md) :
* Login and logout,
* Course access,
* Course module access: all standard modules supported, except Assignement.

The number of supported events will increase during the next months, including:
* Progress, completion, success and competency development,
* Specific interactions for each type of course module.


## Plugin maturity

Trax Logs is currently in Alpha version, which means that significant changes may come.
For example, the structure of generated Statements may change, as well as the vocabulary used.
So we recommend not to use this version on a production server.

However, you are encouraged to [install](docs/en/tech.md) the plugin, to test it, and to share feedbacks.



