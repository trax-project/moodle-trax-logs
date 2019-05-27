# Supported events

Trax Logs for Moodle implements both the [xAPI VLE Profile](http://doc.xapi.fr/profiles/vle) and the [xAPI Moodle Profile](http://doc.xapi.fr/profiles/moodle). 

Please, refer to these documentations to understand xAPI data structures and rules.


## Moodle core events

- `\core\event\user_loggedin`: a user logged into Moodle (
    [JSON example](http://doc.xapi.fr/profiles/moodle/nav#logged-in)).

- `\core\event\user_loggedout`: a user logged out from Moodle (
    [JSON example](http://doc.xapi.fr/profiles/moodle/nav#logged-out)).

- `\core\event\course_viewed`: a user navigated in a Moodle course (
    [JSON example](http://doc.xapi.fr/profiles/moodle/nav#nav-in-course)).

- `\core\event\course_category_viewed`: a user navigated in a Moodle course category (
    [JSON example](http://doc.xapi.fr/profiles/moodle/nav#nav-in-course-category)).

- `\core\event\course_completed`: a user completed a Moodle course (
    [JSON example](http://doc.xapi.fr/profiles/moodle/comp#completed-course)).

- `\core\event\course_completion_updated`: the completion status of a course module changed. 
The resulting Statement depends of the completion status. (
    [Completed JSON example](http://doc.xapi.fr/profiles/moodle/comp#completed-module), 
    [Passed JSON example](http://doc.xapi.fr/profiles/moodle/comp#passed-module), 
    [Failed JSON example](http://doc.xapi.fr/profiles/moodle/comp#failed-module)).


## Moodle common events

- `\mod_xxx\event\course_module_viewed`: a user navigated in a Moodle course module, `xxx` being the type of the module ([JSON example](http://doc.xapi.fr/profiles/moodle/nav#nav-in-module)).


## H5P events

- `\logstore_trax\event\hvp_module_completed`: a user completed an H5P quiz or question set ([JSON example](http://doc.xapi.fr/profiles/moodle/hvp#completed-quiz)).

- `\logstore_trax\event\hvp_question_answered`: a user answered a question of an H5P quiz or question set ([JSON example](http://doc.xapi.fr/profiles/moodle/hvp#answered-quiz-question)).

- `\logstore_trax\event\hvp_module_answered`: a user answered a H5P single question activity ([JSON example](http://doc.xapi.fr/profiles/moodle/hvp#answered-single-question)).

- `\mod_hvp\event\course_module_viewed`: a user navigated in a H5P activity ([JSON example](http://doc.xapi.fr/profiles/moodle/hvp#course-module-viewed)).


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

