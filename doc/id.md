# xAPI Identification Services


## Purpose

**xAPI Identification Services** are web services provided by Trax Logs.

Internally, these services are responsible for managing a stable and permanent identification of all the Moodle activities and users, as well as the anonymization of users.

From an external point of view, these services can be called like any other Moodle **Web Service**. Given a user or activity identity, they will return a fully qualified xAPI structure that can be used to build statements.


## Prerequisites

As any other Web Service in Moodle, there are a number of things to do in order to make the services available:

1. Enable and configure Web Services in Moodle: `Administration > Plugins > Web Services > Overview`.
2. Choose the `REST` protocol if you want to check that these services work with unit testing.
3. Select the `logstore_trax_get_activities` and `logstore_trax_get_actors` functions.
4. Don't forget to create a user account with a token.


## Available services

There are 2 services with a similar behavior: 
- `logstore_trax_get_activities` which provides users xAPI data,
- `logstore_trax_get_actors` which provides activities xAPI data.


## Endpoint

The endpoint is `http://my-moodle-instance/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=xxx&wstoken=yyy`, where:

- `xxx` is the name of the function: `logstore_trax_get_activities` or `logstore_trax_get_actors`,
- `yyy` is the token of an authorized user account.

Use this endpoint to **POST** input data as form data.


## Input data

### items

This is the only required input data. It is a list of items identified with one of the following methods:
- Moodle ID (**id**) and Moodle type (**type**),
- Associated UUID (**uuid**),
- Associated Email (**email**).

### full

This is an optional setting. Its default value is *0*. When set to *1*, all known information is added to the xAPI object, like the name of the user, or the full definition of the activity.

This option has no effect on actors requested by their **email**. 


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
        },
        {
            "uuid": "857dcc1b-aaed-4f22-a6bd-ae00eace9211"
        }
    ],
    "full": 1
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
            "uuid": "9f3a73fe-ff56-435b-8052-2c361686942d"
        },
        {
            "email": "john@moodle.org"
        }
    ],
    "full": 1
}
```


## Output data

### Activities response example

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
    },
    {
        "uuid": "857dcc1b-aaed-4f22-a6bd-ae00eace9211",
        "xapi": "{\"objectType\":\"Activity\",\"id\":\"http:\\\/\\\/xapi.moodle.test\\\/xapi\\\/activities\\\/lti\\\/e403e7ee-4cdd-4d25-b7d9-5de3569a1cc2\",\"definition\":{\"type\":\"http:\\\/\\\/vocab.xapi.fr\\\/activities\\\/external-activity\"}}"
    }
]
```

### Agents response example

Note that actors requested by their **email** does not return a single actor but a list of actors matching with the provided email.
Other methods return a single actor.

```json
[
    {
        "type": "user",
        "id": 2,
        "xapi": "{\"objectType\":\"Agent\",\"name\":\"Jon Snow\",\"account\":{\"homePage\":\"http:\\\/\\\/xapi.moodle.test\",\"name\":\"23a5bb2e-80c5-464a-8472-632261df912d\"}}"
    },
    {
        "uuid": "9f3a73fe-ff56-435b-8052-2c361686942d",
        "xapi": "{\"objectType\":\"Agent\",\"name\":\"Dany\",\"account\":{\"homePage\":\"http:\\\/\\\/xapi.moodle.test\",\"name\":\"564642e-80c5-464a-8472-632264564564\"}}"
    },
    {
        "email": "john@moodle.org",
        "xapi": "[{\"objectType\":\"Agent\",\"name\":\"Dany\",\"account\":{\"homePage\":\"http:\\\/\\\/xapi.moodle.test\",\"name\":\"564642e-80c5-464a-8472-632247564564\"}}]"
    }
]
```


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
