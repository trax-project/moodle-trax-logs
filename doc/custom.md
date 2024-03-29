# Customizing statements

In some cases, you may want to customize the statements generated by TRAX Logs.
For example, you may want to add contextual data related to the learner or the activities.

This is possible, without modifying the Trax Logs plugin, thanks to the **TRAX Local plugin**:
https://github.com/trax-project/moodle-trax-local

1. Install the local plugin in `your-moodle-folder/local/trax`.
2. Customize the `local_trax_customize_statement` function of the lib.php file.
3. Go to your Moodle dashboard, accept the plugin and test your changes.


## Contents

* [Overview](../README.md)
* [Installation](install.md)
* [Configuration](config.md)
* [Supported events](events.md)
* [Supporting new events](extend.md)
* [xAPI videos integration](vid.md)
* [H5P integration](h5p.md)
* [LTI integration](lti.md)
* [xAPI Identification Services](id.md)
* [Best pratices in designing Statements](best-practices.md)
* [Data privacy](privacy.md)
* [Coding style and unit testing](test.md)

