# Core / Course Module Viewed

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
        "id": "http://vocab.xapi.fr/verbs/accessed"
    },
    "object": {
        "objectType": "Activity",
        "id": "http://xapi.moodle.test/xapi/activities/book/c1595f9a-4347-404b-9057-51c461fcd1b7",
        "definition": {
            "type": "http://vocab.xapi.fr/activities/web-content",
            "name": {
                "en": "Book 1"
            },
            "description": {
                "en": "Book 1 description"
            }
        }
    },
    "context": {
        "contextActivities": {
            "parent": [
                {
                    "objectType": "Activity",
                    "id": "http://xapi.moodle.test/xapi/activities/course/ba297687-b1aa-4477-9efd-a782c8fdb90a",
                    "definition": {
                        "type": "http://vocab.xapi.fr/activities/course"
                    }
                }
            ],
            "category": [
                {
                    "id": "http://vocab.xapi.fr/activities/learning-unit",
                    "definition": {
                        "type": "http://vocab.xapi.fr/activities/granularity-level"
                    }
                },
                {
                    "id": "http://vocab.xapi.fr/categories/vle-profile",
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
    "timestamp": "2018-06-20T16:04:18+08:00"
}
```
