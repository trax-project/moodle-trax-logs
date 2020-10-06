# LTI integration


## LTI and xAPI

LTI activities, also known as "external activities", have the capability to support a wide variety of learning activities,
from simple content to complex and highly interactive applications.

LTI and xAPI, when used together, can be a powerfull solution:
- To provide a fluent learning experience thanks to the LTI integration approach,
- To collect more relevant learning data thanks to xAPI.


## Integration issue

LTI and xAPI have not been specifically designed to work together. However, an LTI activity could build its own statements and send them directly to an LRS.

In order to build its own statements, the LTI activity needs some pieces of information:

- The user identity and its xAPI representation,
- Contextual activities (LTI module, Moodle course, Moodle instance) and their xAPI representation.

Moodle provides some usefull information to LTI activities, such as:

- `user_id`: the Moodle internal ID of the user,
- `resource_link_id`: the Moodle internal ID of the LTI module,
- `context_id`: the Moodle internal ID of the embedding course.

The question is: **how does the LTI activity transform this information into well structured xAPI data?**


## xAPI Identification Services

LTI activities should use the [xAPI Identification Services](id.md) in order to get xAPI objects from Moodle IDs.


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
