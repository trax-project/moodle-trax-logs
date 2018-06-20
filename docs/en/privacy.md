# Data privacy

Data privacy is an important aspect of this project.

In order to keep the control on personal data, Trax Logs applies a strict separation of responsabilities between the LMS and the LRS:
* The LRS collects anonymous data, which can't be associated by itself with real users.
* The LMS manages real users and is responsible to protect data privacy.

In order to anonymize the Statements which are sent to the LRS, Trax Logs associates each user with a permanent and anonymous ID (an UUID).
This ID is used by the "actor.account.name" property. 

So Trax Logs maintains a "actors" table which associates the Moodle internal ID of users, with their xAPI ID (the UUID).
This table may be used by Moodle reporting features in order to de-anonymize Statements, but this is under the LMS responsability.

When a user wants to remove all its personal data from the system, we just have to remove it from the "acotrs" table.
The related Statements can stay in the LRS as they are no longer associated with the real user, in any way.
Keeping these Statements may be of interest for statistical analytics.

Currently, Trax Logs plugin does not have the feature to present the personal data (i.e. Statements) of a user when this user requests it, except its UUID.
So the LRS must endorse this responsability, given the UUID of a user.
