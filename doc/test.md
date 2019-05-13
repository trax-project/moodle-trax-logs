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

3. In `admin/tool/log/store/trax/tests/test_config.php`, change the LRS access settings at the begining of the script.


### Sending statements to the LRS

This unit test triggers all the supported Moodle events, transform them into xAPI statements and send them to the LRS.
Currently, the test function doesn't check that the LRS received and recorded the statements.
So you will have to open your LRS and check it manually.

To run the test: `vendor/bin/phpunit store_test admin/tool/log/store/trax/tests/store_test.php`.


### Testing the xAPI Identification Services

This unit test checks the behaviour of the **xAPI Identification Services** which is used for the **LTI integration**.
It calls the plugin Web Services, so you have to activate these services before running the test.
Please, refer to the [LTI integration](lti.md) page if don't know how to do that.

To run the test: `vendor/bin/phpunit external_test admin/tool/log/store/trax/tests/external_test.php`.



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
