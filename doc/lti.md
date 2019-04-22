# LTI integration


## Why LTI?

LTI activities, also known as external activities, have the capability support a wide variety of learning activities,
from simple content to complex and highly interactive applications.

LTI and xAPI, when used together, can be a powerfull solution:
- To provide a fluent learning experience thanks to the LTI integration approach,
- To collect more learning data thanks to xAPI.


## What's the problem?

One option to collect xAPI data from an LTI activity is to let the LTI activity send its own statements directly to the LRS.

In order to build its own statements, the LTI activity needs some pieces of information:

- The user identity and how it should be represented using xAPI,
- Contextual activities (LTI module, Moodle course, Moodle instance) and how they should be represented using xAPI.

Moodle provides some usefull information to LTI activities, such as:

- `user_id`: the Moodle internal ID of the user,
- `resource_link_id`: the Moodle internal ID of the LTI module,
- `context_id`: the Moodle internal ID of the embedding course.

The question is: **how does the LTI activity know how to transform these ID into well structured xAPI data?**


## xAPI Identification Services

Trax Logs provides something we call the **xAPI Identification Services**.

Internally, these services are responsible for managing a stable and permanent identification of all the Moodle activities and users, as well as the anonymization of users.

From an external point of view, these services can be called like any other Moodle **Web Services**. Given a user or activity ID, they will return a fully qualified xAPI structure that can be used to build statements.


## Prerequisites

As any other Web Service in Moodle, there are a number of things to do in order to make the services available:

1. Enable and configure Web Services in Moodle: `Administration > Plugins > Web Services > Overview`.
2. Choose the `REST` protocol if you want to check that these services work with unit testing.
3. Select the `logstore_trax_get_activities` and `logstore_trax_get_actors` functions.
4. Don't forget to create a user with a token.


## Available services

There are 2 services with a similar behavior: 
- `logstore_trax_get_activities` which provides users and groups xAPI data,
- `logstore_trax_get_actors` which provides activities xAPI data.


## Endpoints

The endpoint is `http://my-moodle-instance/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=xxx&wstoken=yyy`, where:

- `xxx` is the name of the function: `logstore_trax_get_activities` or `logstore_trax_get_actors`,
- `yyy` is the token number of an authorized user account.


## Input data

The endpoint must be used with a **POST** request, providing a list of Moodle IDs and types.

### Activities request example

Note that `system` ID is always `0`. 

```json
{
    "items": [
        {
            "type": "system",
            "id": 0
        },
        {
            "type": "course",
            "id": 2
        },
        {
            "type": "lti",
            "id": 1
        }
    ]
}
```

### Agents request example

```json
{
    "items": [
        {
            "type": "user",
            "id": 2
        },
        {
            "type": "user",
            "id": 10
        }
    ]
}
```


## Output data

### Activities request example

```json
[
    {
        "type": "system",
        "id": 0,
        "xapi": "{\"objectType\":\"Activity\",\"id\":\"http:\\\/\\\/xapi.moodle.test\\\/xapi\\\/activities\\\/system\",\"definition\":{\"type\":\"http:\\\/\\\/vocab.xapi.fr\\\/activities\\\/system\"}}"
    },
    {
        "type": "course",
        "id": 2,
        "xapi": "{\"objectType\":\"Activity\",\"id\":\"http:\\\/\\\/xapi.moodle.test\\\/xapi\\\/activities\\\/course\\\/8acfd7a3-2490-40c8-9b61-ec65d518f7da\",\"definition\":{\"type\":\"http:\\\/\\\/vocab.xapi.fr\\\/activities\\\/course\"}}"
    },
    {
        "type": "lti",
        "id": 1,
        "xapi": "{\"objectType\":\"Activity\",\"id\":\"http:\\\/\\\/xapi.moodle.test\\\/xapi\\\/activities\\\/lti\\\/e403e7ee-4cdd-4d25-b7d9-5de3569a1cc2\",\"definition\":{\"type\":\"http:\\\/\\\/vocab.xapi.fr\\\/activities\\\/external-activity\"}}"
    }
]```

#### Agents request example

```json
[
    {
        "type": "user",
        "id": 2,
        "xapi": "{\"objectType\":\"Agent\",\"account\":{\"homePage\":\"http:\\\/\\\/xapi.moodle.test\",\"name\":\"23a5bb2e-80c5-464a-8472-632261df912d\"}}"
    },
    {
        "type": "user",
        "id": 10,
        "xapi": "{\"objectType\":\"Agent\",\"account\":{\"homePage\":\"http:\\\/\\\/xapi.moodle.test\",\"name\":\"564642e-80c5-464a-8472-632264564564\"}}"
    }
]```


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
