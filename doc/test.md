# Coding style and unit testing

## Coding style

We do our best to follow the [Moodle coding guidelines](https://docs.moodle.org/dev/Coding_style) and we recommend you to apply these guidelines if you need to modify or extend this plugin.

Trax Logs has been successfully checked with:
- [Code checker](http://moodle.org/plugins/view.php?plugin=local_codechecker)
- [PHPDoc checker](https://github.com/marinaglancy/moodle-local_moodlecheck)

Before running these tests, please exclude the **vendor** folder which contains third-party libraries that follow their own quality standards.


## Unit testing

A test folder is included with the plugin, so you can test most of the plugin features.


### Prerequisites

1. Check that your Moodle environment is configured to run PHPUnit: https://docs.moodle.org/dev/PHPUnit.

2. Reinitialize the testing environment if it has not been already done: `php admin/tool/phpunit/cli/init.php`.

3. In `admin/tool/log/store/trax/tests/utils/settings.php`, set the LRS access settings for your tests.

<aside class="warning">
    Unit tests will send a lot of fake Statements to your LRS. 
    You should setup a specific LRS environment to support your tests.
    Don't use your production LRS!
</aside>


### Running all the tests

To run all the tests: `vendor/bin/phpunit --testsuite logstore_trax_testsuite`.


### Running specific tests

To run a specific test: `vendor/bin/phpunit admin/tool/log/store/trax/tests/xxx_test.php`, where `xxx` can be:

- **store**: send a simple Statement to the LRS both with sync and async modes.
- **async**: test the asynchronous synchronization process.
- **events**: generate and send all supported events, except H5P.
- **settings**: test some of the plugin settings.
- **filters**: test the capability of the plugin to filter logs.
- **batches**: test the capability of the plugin to send batches of Statements.
- **errors**: test errors management.
- **external**: test the functions used by the plugin Web Services.


### Running a specific test function

To run a specific test function, add the option `--filter=yyy` to the previous command, 
where `yyy` is the name of the function you want to run.


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
