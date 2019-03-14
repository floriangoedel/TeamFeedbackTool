"use strict";

app.component("headerNotLoggedIn", {
    templateUrl: "components/header-not-logged-in.html",
    controller: "HeaderNotLoggedInController",
    bindings: {}
});


app.controller("HeaderNotLoggedInController", function ($log) {
    $log.debug("HeaderNotLoggedInController()");



});
