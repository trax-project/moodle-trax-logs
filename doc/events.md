# Supported events

Trax Logs for Moodle implements both the [xAPI VLE Profile](http://doc.xapi.fr/profiles/vle) and the [xAPI Moodle Profile](http://doc.xapi.fr/profiles/moodle). 

Please, refer to these documentations to understand xAPI data structures and rules.


## Moodle core events

- `\core\event\user_loggedin`: a user logged into Moodle (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_auth#logged-in)).

- `\core\event\user_loggedout`: a user logged out from Moodle (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_auth#logged-out)).

- `\core\event\course_viewed`: a user navigated in a Moodle course (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_nav#nav-in-course)).

- `\core\event\course_category_viewed`: a user navigated in a Moodle course category (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_nav#nav-in-course-category)).

- `\core\event\course_completed`: a user completed a Moodle course (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_comp#course-completed)).

- `\core\event\course_module_completion_updated`: the completion status of a course module changed. A Statement is sent when the status is *completed* (
    [JSON example](http://doc.xapi.fr/profiles/moodle/events_comp#module-completed)).

- `\core\event\user_graded`: a user got a grade in the Moodle gradebook. The resulting Statement depends on the success status (
    [scored](http://doc.xapi.fr/profiles/moodle/events_result#module-scored),
    [passed](http://doc.xapi.fr/profiles/moodle/events_result#module-passed), 
    [failed](http://doc.xapi.fr/profiles/moodle/events_result#module-failed)
    JSON examples).


## Common module events

- `\mod_xxx\event\course_module_viewed`: a user navigated in a Moodle course module, `xxx` being the type of the module ([JSON example](http://doc.xapi.fr/profiles/moodle/events_nav#nav-in-module)).


## H5P events

- `\mod_hvp\event\course_module_viewed`: a user navigated in a H5P activity ([JSON example](http://doc.xapi.fr/profiles/moodle/events_hvp#course-module-viewed)).

- `\logstore_trax\event\hvp_quiz_completed`: a user completed an H5P quiz or question set ([JSON example](http://doc.xapi.fr/profiles/moodle/events_hvp#quiz-completed)).

- `\logstore_trax\event\hvp_quiz_question_answered`: a user answered a question of an H5P quiz or question set ([JSON example](http://doc.xapi.fr/profiles/moodle/events_hvp#quiz-question-answered)).

- `\logstore_trax\event\hvp_single_question_answered`: a user answered a H5P single question activity ([JSON example](http://doc.xapi.fr/profiles/moodle/events_hvp#single-question-answered)).


## Risky events

Some of the above events should be considered as risky when you want to transfer you **Moodle history**.
The reason is that these events are based on data that may change after the events occur 
and before they are processed by the plugin. 
In this case, some information contained in the Statements may be inaccurate,
or other Statements may be impossible to build.

- `\core\event\course_module_completion_updated`: the completion status may change.
- `\core\event\user_graded`: the grade and success status may change.



## Contents

* [Overview](../README.md)
* [Installation](install.md)
* [Configuration](config.md)
* [Supported events](events.md)
* [Supporting new events](extend.md)
* [H5P integration](h5p.md)
* [LTI integration](lti.md)
* [xAPI Identification Services](id.md)
* [Best pratices in designing Statements](best-practices.md)
* [Data privacy](privacy.md)
* [Coding style and unit testing](test.md)

