define([
    'jquery'
], function ($) {
    'use strict';
    return function (data) {
        $("#clearactivitylog").click(function (){
            new Ajax.Request(data.controllerUrl, {
                loaderArea:     true,
                asynchronous:   true,
                onSuccess: function() {
                    location.reload();
                }
            });
        });
    };
});
