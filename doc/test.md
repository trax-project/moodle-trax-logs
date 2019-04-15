# Coding style and unit tests

## Coding style

We do our best to follow the [Moodle coding guidelines](https://docs.moodle.org/dev/Coding_style) and we recommend you to apply these guidelines if you need to modify or extend this plugin.

Trax Logs has been successfully checked with:
- [Code checker](http://moodle.org/plugins/view.php?plugin=local_codechecker)
- [PHPDoc checker](https://github.com/marinaglancy/moodle-local_moodlecheck)

Before running these tests, please exclude the plugin `vendor` folder which contains third-party libraries that follow their own quality standards.


## Unit tests

A test file is included with the plugin, so you can test all the supported events 
by generating the matching statements and sending them to your LRS.
However, the current test function doesn't check that the LRS recorded the statements.
So you will have to open your LRS and check it manually.

To launch the test:

1. Check that your Moodle environment is configured to run PHPUnit: https://docs.moodle.org/dev/PHPUnit.

2. Reinitialize the testing environment if it has not been done since the plugin installation: `php admin/tool/phpunit/cli/init.php`.

3. In `admin/tool/log/store/trax/tests/test_config.php`, change the LRS access settings at the begining of the script.

4. Launch the test with `vendor/bin/phpunit store_test admin/tool/log/store/trax/tests/store_test.php`.


## Contents

* [Overview](README.md)
* [Installation and configuration](doc/install.md)
* [Supported events](doc/events.md)
* [Supporting new events](extend.md)
* [Coding style and unit tests](test.md)
* [Best pratices in designing Statements](doc/best-practices.md)
* [Data privacy](doc/privacy.md)
