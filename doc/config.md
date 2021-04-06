# Configuration

The plugin settings are generaly well documented on the configuration page (`Administration > Plugins > Logstore > Trax Logs`). But let's see a few complementary information...


## Sync mode

### Synchronous

Events are catched and sent to the LRS in real time. This is usefull when you want to make some tests. However, this mode has some important drawbacks. 

1. It has consequences on performances and can slow down user interactions. 

2. when a request to the LRS fails, the event is lost and will not be sent anymore. 

For these reasons, we do not recommend using this mode on your production server.


### Asynchronous

Events are read from the Moodle standard logstore and sent by CRON jobs. This is a better choice for production servers.

1. You can schedule synchronization tasks as you want on `Admin > Server > Scheduled Tasks > Trax Logs: push logs`, `Admin > Server > Scheduled Tasks > Trax Logs: define groups` and `Admin > Server > Scheduled Tasks > Trax Logs: define courses`.

2. You can play with 2 settings to refine the synchronization behaviour: **Database batch size** and **xAPI batch size**.

3. You can filter the events you want to track thanks to the **Logged events** settings.

4. When a request to the LRS fails, there may be a new attempt during the next CRON job. The number of attempts depends of the **attempts** setting.

5. You can automatically send your logs history. The **First logs** setting let you define the date of the first log. You can change this setting as you want. Each log will be sent only once.

6. This mode is **future-proof**. Events that are not currently supported by the plugin stay in the Moodle standard logstore. They may be supported and processed in the future.


## Data privacy

To conform with our [data privacy philosophy](privacy.md), the plugin provides 2 settings:

- **Actors identification** - Choose the `anonymous` option (default). This means that an UUID will be used to identify the actors.

- **Web services anonymization** - Check is (default). This means that client can't get the real actor real identity (name, username, email) when calling the plugin Web Services.


## Contents

* [Overview](../README.md)
* [Installation](install.md)
* [Configuration](config.md)
* [Supported events](events.md)
* [Supporting new events](extend.md)
* [Customizing statements](custom.md)
* [xAPI videos integration](vid.md)
* [H5P integration](h5p.md)
* [LTI integration](lti.md)
* [xAPI Identification Services](id.md)
* [Best pratices in designing Statements](best-practices.md)
* [Data privacy](privacy.md)
* [Coding style and unit testing](test.md)
