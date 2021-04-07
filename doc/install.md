# Installation

## LRS installation

First of all, you need a Learning Record Store (LRS) in order to use this plugin.
There are a lot of LRS on the market. If you don't already have one, we recommend to look at the [ADL certified products registry](https://adopters.adlnet.gov/products/all/0).

If you are familiar with the PHP stack, we recommend to install [TRAX LRS](http://traxlrs.com), which is Open Source and certified by ADL.

When it is done, create a new BasicHTTP client in your LRS. You will be prompted to choose a username and password for this client. Keep them in mind, you will need them later.


## Trax Logs installation

1. [Download the lastest version of the plugin matching with your Moodle version.](https://github.com/trax-project/moodle-trax-logs/releases)

2. Drag and drop the ZIP file in `http://my-moodle-address.com/admin/tool/installaddon/index.php`.
For a manual installation, unzip the ZIP file in `my-moodle-install-folder/admin/tool/log/store/`and rename the plugin folder as `trax`. Be sure that all the plugin files are located at the root of the `my-moodle-install-folder/admin/tool/log/store/trax` folder.

3. Go to the Moodle administration area. The presence of the plugin will be detected.

4. Confirm the plugin installation and follow the configuration process.

5. Go to `Administration > Plugins > Logging > Manage log stores` and activate **Trax Logs**.

6. On the same page, check that the **Standard log** store is activated and that its `Keep logs for` setting has an appropriate value. If you are not sure, choose `Never delete logs`. 

7. Go to `Administration > Server > Cleanup` and check that the `Disable grade history` is unchecked. Choose an appropriate value for `Grade history lifetime`. If you are not sure, choose `Never delete history`. 

8. Check that Moodle CRON job is configured and running. 

That's all. At this step, we recommend you to run the [unit tests](test.md) in order to check that everything works fine. 

Then, you can start to navigate in your courses and see the Statements recorded into your LRS.


## CRON jobs

This plugin may use CRON jobs in order to sent statements to the LRS on a regular basis. You can configure these jobs in `Administration > Server > Tasks > Scheduled tasks`.

- *Trax Logs: push logs*: this job sends the Moodle log store data to the LRS when the plugin is configured as `Asynchronous`. By default, these statements are sent each minute.

- *Trax Logs: define courses*: this job sends statements containing courses definitions to the LRS. By default, these statements are sent each day.

- *Trax Logs: define groups*: this job sends statements containing cohorts definitions to the LRS. By default, these statements are sent each day.


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
