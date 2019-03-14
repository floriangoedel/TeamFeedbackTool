app.component("employeeDropdown", {
    templateUrl: "components/employee-dropdown.html",
    controller: "employeeDropdownController"
});

app.controller("employeeDropdownController", function ($http, $rootScope, $scope) {

    $rootScope.$watch('projectId', () => {
        if($rootScope.projectId !== undefined){
            getEmployees();
            $scope.selectedUser = "";
        }
    });

    let getEmployees = () => {

        this.users = [];

        let url = "../../Backend/SendFirstnameSurnameWithoutLeader.php";

        let parameter = JSON.stringify({
            projectId: $rootScope.projectId
        });

        $http({
            method: 'POST',
            url: url,
            data: parameter
        }).then(
            (response) => {
                this.firstnames = response.data.firstnames.split(";");
                this.surnames = response.data.surnames.split(";");
                this.userIds = response.data.userIds.split(";");
            }, function (error) {
                console.log(error);
            }).then(() => {
            for (let i = 0; i < this.surnames.length; i++) {
                this.users.push((this.firstnames[i] + " " + this.surnames[i]));
            }
        });

        $scope.selectChanged = function () {
            let url = "../../Backend/SendFirstnameSurnameWithoutLeader.php";

            let parameter = JSON.stringify({
                projectId: $rootScope.projectId
            });

            $http({
                method: 'POST',
                url: url,
                data: parameter
            }).then(
                (response) => {
                    this.firstnames = response.data.firstnames.split(";");
                    this.surnames = response.data.surnames.split(";");
                    this.userIds = response.data.userIds.split(";");
                }, function (error) {
                    console.log(error);
                }).then(() => {
                for (let i = 0; i < this.surnames.length; i++) {
                    if ($scope.selectedUser === (this.firstnames[i] + " " + this.surnames[i])) {
                        $rootScope.currentEmployee = this.userIds[i];
                    }
                }
            });
        };
    }
});