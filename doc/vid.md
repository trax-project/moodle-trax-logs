# xAPI videos integration


## xAPI Video Profile

The [xAPI Video Profile](https://liveaspankaj.gitbooks.io/xapi-video-profile/content/) 
defines a set of rules and statements to track video events such as:

- Video started, paused, resumed, seeked, stopped.
- Interactions with the video player like audio (un)mute or resolution change.
- Video completion, time spent and viewed sections.


## xAPI VideoJS

[xAPI VideoJS](https://github.com/jhaag75/xapi-videojs) is a reference 
implementation of the xAPI Video Profile integrated with the VideoJS player.


## Trax Video for Moodle

[Trax Video for Moodle](https://github.com/trax-project/moodle-trax-video) 
is a Moodle plugin that let's you create video activities into your Moodle courses,
using the xAPI VideoJS player in order to support the xAPI Video Profile. 

Trax Logs is required to run Trax Video because it performs some tasks
when videos are playing:

- It provides an LRS proxy used by Trax Video to communicate with the LRS.
- It performs some tasks to secure the communication with the LRS.
- It modifies statements in order to insert complementary contextual data.

Be aware that all video statements are always sent synchronously because the video player
need to check them in real-time.

However, video events are also triggered and stored in the Moodle standard log store.
So these events can be used to resend video statements in case of a resynchronization.


## Additional xAPI Rules

In addition to the xAPI video profile, a few rules are needed to ensure the consistancy
with the Moodle / VLE profile: [go further](http://doc.xapi.fr/profiles/moodle/events_vid).


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
