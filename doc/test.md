# Coding style and unit testing

## Coding style

We do our best to follow the [Moodle coding guidelines](https://docs.moodle.org/dev/Coding_style) and we recommend you to apply these guidelines if you need to modify or extend this plugin.

Trax Logs has been successfully checked with:
- [Code checker](http://moodle.org/plugins/view.php?plugin=local_codechecker)
- [PHPDoc checker](https://github.com/marinaglancy/moodle-local_moodlecheck)

Before running these tests, please exclude the plugin `vendor` folder which contains third-party libraries that follow their own quality standards.


## Unit testing

A test folder is included with the plugin, so you can test most of the plugin features.


### Prerequisites

1. Check that your Moodle environment is configured to run PHPUnit: https://docs.moodle.org/dev/PHPUnit.

2. Reinitialize the testing environment if it has not been already done: `php admin/tool/phpunit/cli/init.php`.

3. In `admin/tool/log/store/trax/tests/lrs_config.php`, set the LRS access settings for your tests.


### Sending statements to the LRS

This unit tests check that Statements are correctly generated and sent to the LRS.

To run the tests: `vendor/bin/phpunit admin/tool/log/store/trax/tests/test_store.php`.


### Testing the xAPI Identification Services

This unit tests check the behaviour of the **xAPI Identification Services** which is used for the **LTI integration**.
It calls the plugin Web Services, so you have to activate these services before running the test.
Please, refer to the [LTI integration](lti.md) page if don't know how to do that.

To run the test: `vendor/bin/phpunit admin/tool/log/store/trax/tests/test_external.php`.



## Contents

* [Overview](../README.md)
* [Installation and configuration](install.md)
* [Supported events](events.md)
* [Supporting new events](extend.md)
* [LTI integration](lti.md)
* [H5P integration](h5p.md)
* [Best pratices in designing Statements](best-practices.md)
* [Data privacy](privacy.md)
* [xAPI Identification Services](id.md)
* [Coding style and unit testing](test.md)
