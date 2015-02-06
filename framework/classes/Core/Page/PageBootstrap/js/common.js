$(document).ready(function() {
    setHandlerOnDebug();
});

/**
 * Set handler on debug link:
 * Render debug info in console on right mouse click
 */
function setHandlerOnDebug() {
    if ($('#link-modal-debug').length) {
        $('#link-modal-debug').on('contextmenu', function() {
            $('.list-debug').children().each(function() {
                console.log($(this).text());
            });
            return false;
        });
    }
}