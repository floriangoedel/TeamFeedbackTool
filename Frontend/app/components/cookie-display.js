"use strict";

app.component("cookieDisplay", {
    templateUrl: "components/cookie-display.html",
    controller: "CookieDisplayController",
    bindings: {}
});


app.controller("CookieDisplayController", function ($log, $scope) {
    $log.debug("CookieDisplayController()");

    this.$onInit = function () {
        $scope.cookieDisplay = {isVisible: true};
    };

    this.removeCookieDisplay = () => {
        if ($scope.cookieDisplay.isVisible) {
            $scope.cookieDisplay.isVisible = false;
        }
    };

});
