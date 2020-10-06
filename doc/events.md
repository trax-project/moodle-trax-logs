# Supported events

Trax Logs for Moodle implements the [xAPI Moodle / VLE Profile](http://doc.xapi.fr/profiles/moodle). 

Please, refer to this documentation to understand xAPI data structures and rules.


## Moodle core events

- `\core\event\user_loggedin`: a user logged into Moodle (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_auth#logged-in)).

- `\core\event\user_loggedout`: a user logged out from Moodle (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_auth#logged-out)).

- `\core\event\user_enrolment_created`: a user registered to a course (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_management#user-registered)).

- `\core\event\course_viewed`: a user navigated in a Moodle course (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_nav#nav-in-course)).

- `\core\event\course_category_viewed`: a user navigated in a Moodle course category (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_nav#nav-in-course-category)).

- `\core\event\course_completed`: a user completed a Moodle course (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_comp#course-completed)).

- `\core\event\course_module_completion_updated`: the completion status of a course module changed. Regarding auto-completion, a statement is sent only when the status is *completed* (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_comp#module-completed)). Regarding declarative completion, a statement is sent when the status is *completed* or *incomplete* ([JSON example](http://doc.xapi.fr/profiles/moodle/events_comp#module-completion-marked)).

- `\core\event\user_graded`: a user got a grade in the Moodle gradebook. A Statement is sent only when the type of grade is *value* or *scale* and when the grade is associated with a *course module* ([JSON example](http://doc.xapi.fr/profiles/moodle/events_result#module-graded)).


## Common module events

- `\mod_xxx\event\course_module_viewed`: a user navigated in a Moodle course module, `xxx` being the type of the module ([JSON example](http://doc.xapi.fr/profiles/moodle/events_nav#nav-in-module)).


## SCORM module

- `\mod_scorm\event\sco_launched`: a user launched a SCO of the SCORM package ([JSON example](http://doc.xapi.fr/profiles/moodle/events_scorm#launched-sco)).


## Forum module

- `\mod_forum\event\discussion_viewed`: a user viewed a forum discussion ([JSON example](http://doc.xapi.fr/profiles/moodle/events_forum#viewed-discussion)).

- `\mod_forum\event\discussion_created`: a user created a forum discussion ([JSON example](http://doc.xapi.fr/profiles/moodle/events_forum#created-discussion)).

- `\mod_forum\event\post_created`: a user created a post in a forum discussion ([JSON example](http://doc.xapi.fr/profiles/moodle/events_forum#created-post)).


## CRON-based statements

Some statements are sent on a regular basis by CRON jobs:

- **Course defined:** a course has been created or updated. The statement defines the structure of the course (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_management#course-defined)).

- **Group defined:** a group has been created or updated. The statement defines the members of the group (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_management#group-defined)).



## Third-party events

- [xAPI video profile](vid.md)
- [H5P xAPI events](h5p.md)
- [SCORM Lite](http://doc.xapi.fr/profiles/moodle/events_scormlite)
- [Assessment Path](http://doc.xapi.fr/profiles/moodle/events_assessmentpath)
- [Training Path](http://doc.xapi.fr/profiles/moodle/events_trainingpath)



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

