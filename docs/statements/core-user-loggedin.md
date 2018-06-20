# Core / User Loggedin

```json
{
    "actor": {
        "objectType": "Agent",
        "account": {
            "name": "d0d6cd21-bbea-4179-a7e9-affdea1a1d84",
            "homePage": "http://xapi.moodle.test"
        }
    },
    "verb": {
        "id": "https://w3id.org/xapi/adl/verbs/logged-in"
    },
    "object": {
        "objectType": "Activity",
        "id": "http://xapi.moodle.test/xapi/activities/system",
        "definition": {
            "type": "http://vocab.xapi.fr/activities/system",
            "name": {
                "en": "Moodle test site"
            }
        }
    },
    "context": {
        "contextActivities": {
            "category": [
                {
                    "id": "http://vocab.xapi.fr/profiles/vle",
                    "definition": {
                        "type": "http://adlnet.gov/expapi/activities/profile"
                    }
                }
            ],
            "grouping": [
                {
                    "objectType": "Activity",
                    "id": "http://xapi.moodle.test/xapi/activities/system",
                    "definition": {
                        "type": "http://vocab.xapi.fr/activities/system"
                    }
                }
            ]
        },
        "platform": "Moodle"
    },
    "version": "1.0.3",
    "timestamp": "2018-06-20T16:04:17+08:00"
}
```
