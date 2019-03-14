"use strict";

app.component('notFound', {
    templateUrl: 'components/not-found.html',
    controller: 'NotFoundController',
    bindings: {}
});

app.controller("NotFoundController", function ($log) {
    $log.debug("NotFoundController()");

});