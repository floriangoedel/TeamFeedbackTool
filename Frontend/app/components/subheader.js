"use strict";

app.component("subheader", {
    templateUrl: "components/subheader.html",
    controller: "SubheaderController",
    bindings: {
        whereAmI:"@"
    },
    transclude: true
});


app.controller("SubheaderController", function ($log, $rootScope) {
    $log.debug("SubheaderController()");

    this.$onInit = function() {
        $rootScope.projectMenueVisibility = false;
        this.whereAmI = this.whereAmI || 'Achtung: Sie tummeln sich auf einem unbekannten Pfad!';
    };

    $rootScope.$watch('projectMenueVisibility', () => {
        if ($rootScope.projectMenueVisibility) {
            let element = angular.element(document.querySelector('.subheader-without-project-menue'));
            element.addClass('subheader-with-project-menue');
            element.removeClass('subheader-without-project-menue');
        } else {
            let element = angular.element(document.querySelector('.subheader-with-project-menue'));
            element.addClass('subheader-without-project-menue');
            element.removeClass('subheader-with-project-menue');
        }
    });

});
