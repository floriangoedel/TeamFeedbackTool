app.component("logout", {
    templateUrl: "components/logout.html",
    controller: "LogoutController"
});

app.controller("LogoutController", function ($log, $http, $rootScope, $timeout, $window) {
    $log.debug("LogoutController()");

    this.$onInit = function() {
        this.initializing = true;
        angular.element(document.querySelector('.logout')).hide();
        $rootScope.profileVisibility = false;
    };

    $rootScope.$watch('profileVisibility', (oldVal, newVal) => {
        if (this.initializing) {
            $timeout(() => { this.initializing = false; });
        } else {
            if (newVal) {
                angular.element(document.querySelector('.logout')).slideDown();
            } else {
                angular.element(document.querySelector('.logout')).slideUp();
            }
        }
    });

    this.submit = () => {

        let url = "../../Backend/LogoutUser.php";

        $http({
            method: 'POST',
            url: url
        });

        location.reload();


    };

    this.userdata = () => {
        $window.location.href = '#!/userdata'
    }
});