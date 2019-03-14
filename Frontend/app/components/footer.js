"use strict";

app.component("footer", {
    templateUrl: "components/footer.html",
    controller: "FooterController",
    bindings: {}
});


app.controller("FooterController", function ($log) {
    $log.debug("FooterController()");
});
