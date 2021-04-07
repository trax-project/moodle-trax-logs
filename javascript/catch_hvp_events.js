
// We wait until the page is loaded to check that JQuery exists.
// This is not always the case. But it always exists on H5P pages.

window.addEventListener("load", function(event) {
    catchH5PEvents();
});

catchH5PEvents = function () {
    if (window.$) {

        // We try to find the H5P event dispatcher.
        if (typeof H5P !== 'undefined' && H5P.externalDispatcher) {

            // We listen xAPI events
            H5P.externalDispatcher.on('xAPI', function (event) {

                // Debug.
                // console.log(event);

                // We keep only some types of events.
                var supportedVerbs = [
                    'http://adlnet.gov/expapi/verbs/answered',
                    'http://adlnet.gov/expapi/verbs/completed',
                    'http://adlnet.gov/expapi/verbs/progressed',
                ];
                if (supportedVerbs.indexOf(event.data.statement.verb.id) === -1) {
                    return;
                }

                // We keep only some library types.
                if (event.data.statement.context.contextActivities.category === undefined) {

                    // Accept statements with no category defined.
                    libraryType = 'Unknown';

                } else {

                    // Check category.
                    var category = event.data.statement.context.contextActivities.category[0].id;
                    var libraryType;
                    var supportedTypes = [

                        // Questions.
                        'H5P.DragQuestion',
                        'H5P.Blanks',
                        'H5P.MarkTheWords',
                        'H5P.DragText',
                        'H5P.TrueFalse',
                        'H5P.MultiChoice',

                        // Quiz.
                        'H5P.SingleChoiceSet',
                        'H5P.QuestionSet',

                        // Interactive Video.
                        'H5P.InteractiveVideo',

                        // Summary.
                        'H5P.Summary',

                        // Course Presentation.
                        'H5P.CoursePresentation',

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
                var endpoint = window.location.href.split('h5p/embed')[0] + 'admin/tool/log/store/trax/ajax/hvp_xapi_event.php';
                var statementString = JSON.stringify(event.data.statement);
                var jqxhr = $.post(endpoint, { statement: statementString })
                    .fail(function () {
                        console.log('Error sending H5P xAPI Statement to TRAX Logs plugin.');
                    })
            });
        }
    }
}



    
