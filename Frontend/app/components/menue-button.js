"use strict";

app.component("menueButton", {
    templateUrl: "components/menue-button.html",
    controller: "MenueButtonController",
    bindings: {}
});


app.controller("MenueButtonController", function ($log, $rootScope) {
    $log.debug("MenueButtonController()");

    this.menu_open = false;

    this.$onInit = function () {
        $rootScope.projectMenueVisibility = false;
    };

    this.toggle_menu = () => {
        $rootScope.projectMenueVisibility = !$rootScope.projectMenueVisibility;
        if (this.menu_open === false) {
            this.menu_open = true;
            angular.element(document.querySelector('.menue-icon')).addClass('button-active');
        } else {
            this.menu_open = false;
            angular.element(document.querySelector('.menue-icon')).removeClass('button-active');
        }
    };

});
