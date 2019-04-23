# H5P integration

## What is H5P?

H5P is fantastic tool to create interactive contents such as questions, quizzes, presentations and interactive videos in Moodle, Wordpress and Drupal.

Please, refer to https://h5p.org/ for further information.


## Why supporting H5P activities?

The H5P team made a great work to [implement the xAPI standard](https://h5p.org/documentation/x-api). As a result, H5P contents trigger a lot of valuable events which take the form of xAPI statements. We thought it was important to catch these statements.


## Why using Trax Logs with H5P? 

In the H5P implementation, events are triggered by the **Javascript layer**, which provides the xAPI statements.
But these statements are not sent to the LRS. They have to be catched from the front-end, and then sent to the LRS with a specific implementation.

We think the H5P choice is relevant for 2 reasons:
1. First, the front-end should not communicate directly with the LRS without strong security measures.
2. Then, statements may have to be modified before being sent to the LRS.

**Statements modification** may be needed for 2 reasons:
1. Adding contextual information such as the course or the Moodle instance in which the event happened.
2. Applying some rules to guarranty that all the statements have a consistent form (idealy, applying an xAPI profile).

That's precisely what Trax Logs does...


## Let's start 

As we already said, we need to catch the H5P Javascript events. The best place to do that is probably in a Moodle theme. Of course, you could modify the default Moodle theme. But a better solution would be to create your own theme. Refer to [Creating a theme based on boost](https://docs.moodle.org/dev/Creating_a_theme_based_on_boost) for further information.

For testing, we will just modify the default Boost theme by editing the `/theme/boost/config.php` file. Now, all you have to do is to add the following line at the end of the config file:

```php
$THEME->javascripts_footer = array('../../../admin/tool/log/store/trax/javascript/catch_hvp_events');
```

Then, save the config file and purge all the Moodle caches. You should have a link in the Moodle footer to do that. If not, go to this page: `http://your-moodle-url/admin/purgecaches.php`.

**That's all!** Now, you can use H5P contents such as questions or quizzes, and you should see some statements in you LRS.


## How does it work? 

1. The Javascript file that you inserted into your Moodle theme listens to the H5P xAPI events.

2. When an xAPI event is triggered, the Javascript file makes an AJAX request to send the statement to Trax Logs. The user authentication session is checked, so this request should be secured.

3. Trax Logs gets the statement and triggers a Moodle event with the embedded statement.

4. Moodle records the event and the embedded statement in its default logstore. 

5. Trax Logs handles the Moodle event (sync mode) or gets the event from the default logstore (async mode, coming soon).

6. Trax Logs transforms the statement in order to conform with the [xAPI VLE profile](http://doc.xapi.fr/profiles/vle) an the [xAPI Moodle profile](http://doc.xapi.fr/profiles/moodle).

7. Trax Logs send the statement to the LRS. This request is done from the back-end, so it should be secured.


## Which events are currently supported?

Here is the list of the H5P events which are currently (triggered and) handled by Trax Logs:

- `\logstore_trax\event\hvp_module_completed`: a user completed an H5P quiz or question set ([JSON example](http://doc.xapi.fr/profiles/moodle/hvp#completed-quiz)).

- `\logstore_trax\event\hvp_question_answered`: a user answered a question of an H5P quiz or question set ([JSON example](http://doc.xapi.fr/profiles/moodle/hvp#answered-quiz-question)).

- `\logstore_trax\event\hvp_module_answered`: a user answered a single question H5P activity ([JSON example](http://doc.xapi.fr/profiles/moodle/hvp#answered-single-question)).

- `\mod_hvp\event\course_module_viewed`: a user navigated in a H5P activity ([JSON example](http://doc.xapi.fr/profiles/moodle/hvp#course-module-viewed)).


## Contents

* [Overview](../README.md)
* [Installation and configuration](install.md)
* [Supported events](events.md)
* [Supporting new events](extend.md)
* [LTI integration](lti.md)
* [H5P integration](h5p.md)
* [Best pratices in designing Statements](best-practices.md)
* [Data privacy](privacy.md)
* [Coding style and unit testing](test.md)
