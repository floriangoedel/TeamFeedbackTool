"use strict";

app.service("UserdataService", function ($log, $http, Userdata) {
    this.getUserdata = () => {
        let userinfo;
        let recievingUrlUserInformation = "../../Backend/IndividualProfile.php";

        return $http({
            method: 'POST',
            url: recievingUrlUserInformation
        }).then(
            (response) => {
                userinfo = new Userdata(response.data);
                return userinfo;
            }, function (error) {
                console.log(error);
            });
    };
});