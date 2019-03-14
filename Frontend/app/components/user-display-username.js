"use strict";

app.component("userDisplayUsername", {
    templateUrl: "components/user-display-username.html",
    controller: "UserDisplayUsernameController",
    bindings: {
        username: '@'
    }
});


app.controller("UserDisplayUsernameController", function ($http, $log, UserdataService, $rootScope) {
    $log.debug("UserDisplayUsernameController()");

    UserdataService.getUserdata().then(x => {
        this.username = x.firstname + ' ' + x.surname;
    });
    
    this.toggleProfileMenue = () => {
        $rootScope.profileVisibility = !$rootScope.profileVisibility;
    };

});
