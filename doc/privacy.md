# Data privacy

Data privacy is an important aspect of this project.

In order to keep the control on personal data, Trax Logs applies a strict separation of responsabilities between the LMS and the LRS:
* **The LRS** collects anonymous data, which can't be associated by itself with real users.
* **The LMS** manages real users and is responsible to protect data privacy.

In order to anonymize the Statements, Trax Logs associates each user with a permanent and anonymous ID (an UUID).
This ID is used in the `actor.account.name` property. 

So Trax Logs maintains an `actors` table which associates the Moodle internal ID of users with their LRS ID (the UUID).
This table may be used by Moodle reporting features in order to de-anonymize Statements.

When a user wants to remove all its personal data from the system, we just have to remove it from the `actors` table.
The related Statements can stay in the LRS as they are no longer associated with the real user, in any way.
Keeping these Statements may be of interest for statistical analytics.

Currently, Trax Logs plugin does not have the feature to present personal data (i.e. Statements) of a user when the user requests it, except its UUID.
So the LRS must endorse this responsability, given the UUID of the user.


## Contents

* [Overview](../README.md)
* [Installation and configuration](install.md)
* [Supported events](events.md)
* [Supporting new events](extend.md)
* [Coding style and unit tests](test.md)
* [Best pratices in designing Statements](best-practices.md)
* [Data privacy](privacy.md)
