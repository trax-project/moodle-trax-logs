# Installation and configuration

## LRS installation

First of all, you need a Learning Record Store (LRS) in order to use this plugin.
There are a lot of LRS on the market. If you don't already have one, we recommend to look at the [ADL certified products registry](https://adopters.adlnet.gov/products/all/0).

If you are familiar with the PHP stack, we recommand to install [TRAX LRS](https://github.com/trax-project/trax-lrs), which is Open Source and certified by ADL.

When it is done, create a new BasicHTTP client in your LRS. You will be prompted to choose a username and password for this client. Keep them in mind, you will need them later.


## Trax Logs installation

1. [Download the lastest version of the plugin for Moodle 3.5.](https://github.com/trax-project/moodle-trax-logs/releases)

2. Drag and drop the ZIP file in `http://my-moodle-address.com/admin/tool/installaddon/index.php`.
For a manual installation, unzip the plugin file in `my-moodle-install-folder/admin/tool/log/store/`.

3. Go to the Moodle administration area. The presence of the plugin will be detected.

4. Confirm the plugin installation and follow the configuration process.

5. Go to `Administration > Plugins > Logstore` in order to activate the plugin.

6. You can now navigate in your courses and see new Statements in your LRS.


## Trax Logs configuration

- `LRS endpoint` - This is the URL Trax Logs will use in order to communicate with the LRS. Check your LRS installation in order to get this setting.

- `LRS username` - This is the username you entered when you created the BasicHTTP client in your LRS. 

- `LRS password` - This is the password you entered when you created the BasicHTTP client in your LRS. 

- `Plateform IRI` - It looks like an URL and will permanently identify your Moodle instance. It may be your hosting URL or a symbolic URL that will stay unchanged, even if your hosting URL changes. 

- `Buffer size` - Number of statements which can be grouped to be sent in a single request. You should check your LRS capabilities. 


## Contents

* [Overview](../README.md)
* [Installation and configuration](install.md)
* [Supported events](events.md)
* [Supporting new events](extend.md)
* [Coding style and unit tests](test.md)
* [Best pratices in designing Statements](best-practices.md)
* [Data privacy](privacy.md)
