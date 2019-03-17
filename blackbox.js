/**
 * @file
 * Provide Javascript for blackbox module
 */
jQuery(function($) {

    var myFirstTimeout;

    function init() {
        myFirstTimeout = setTimeout(myBlackbox, 1000);
    }

    function myBlackbox() {
        var showTime = Drupal.settings.blackbox.showTime;
        var currentTime = Math.round((new Date).getTime() / 1000);
        var stop = false;
        if (currentTime > showTime) {
            $("#blackbox_call").trigger("click");
            stop = true;
        }
        if (stop === false) {
            setTimeout(myBlackbox, 2000); // LOOP
        }
    }

    $(document).ready(function() {

        $("#blackbox_call").once('init-colorbox-node-processed', function() {
            $(this).addClass("colorbox-node");
            $(this).colorboxNode({'launch': false});
        });

        if (Drupal.settings.blackbox !== undefined) {
            init();
        }

    });

});
