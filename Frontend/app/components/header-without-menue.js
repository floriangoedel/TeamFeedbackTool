"use strict";

app.component("headerWithoutMenue", {
    templateUrl: "components/header-without-menue.html",
    controller: "HeaderWithoutMenueController",
    bindings: {}
});


app.controller("HeaderWithoutMenueController", function ($log) {
    $log.debug("HeaderWithoutMenueController()");


});
