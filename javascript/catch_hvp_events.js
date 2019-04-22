
// We wait until the page is loaded to check that JQuery exists.
// This is not always the case. But it always exists on H5P pages.
window.onload = function () {
    if (window.$) {

        // We try to find the H5P event dispatcher.
        if (typeof H5P !== 'undefined' && H5P.externalDispatcher) {

            // We listen xAPI events
            H5P.externalDispatcher.on('xAPI', function (event) {

                // Debug.
                console.log(event.data.statement);

                // We keep only some types of events.
                var supportedVerbs = [
                    'http://adlnet.gov/expapi/verbs/answered',
                    'http://adlnet.gov/expapi/verbs/completed',
                ];
                if (supportedVerbs.indexOf(event.data.statement.verb.id) === -1) {
                    return;
                }

                // We keep only some library types.
                if (event.data.statement.context.contextActivities.category === undefined) {

                    // H5P.SingleChoiceSet has currently no context category and seems to be the only one.
                    libraryType = 'H5P.SingleChoiceSet';

                } else {

                    // Check category.
                    var category = event.data.statement.context.contextActivities.category[0].id;
                    var libraryType;
                    var supportedTypes = [
                        'H5P.DragQuestion',
                        'H5P.Blanks',
                        'H5P.MarkTheWords',
                        'H5P.DragText',
                        'H5P.TrueFalse',
                        'H5P.MultiChoice',
                        'H5P.SingleChoiceSet',
                        'H5P.QuestionSet',
                    ];
                    for (var index in supportedTypes) {
                        if (category.indexOf(supportedTypes[index]) === -1) {
                            continue;
                        }
                        libraryType = supportedTypes[index];
                        break;
                    }
                }
                if (libraryType === undefined) {
                    return;
                }

                // We send them to Trax Logs plugin.
                var endpoint = window.location.href.split('mod/hvp')[0] + 'admin/tool/log/store/trax/ajax/hvp_xapi_event.php';
                var statementString = JSON.stringify(event.data.statement);
                var jqxhr = $.post(endpoint, { statement: statementString })
                    .fail(function () {
                        console.log('Error sending H5P xAPI Statement to TRAX Logs plugin.');
                    })
            });
        }
    }
}



    
