"use strict";

app.component("header", {
    templateUrl: "components/header.html",
    controller: "HeaderController",
    bindings: {}
});


app.controller("HeaderController", function ($log) {
    $log.debug("HeaderController()");


});
