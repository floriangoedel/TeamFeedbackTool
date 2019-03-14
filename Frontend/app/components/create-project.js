app.component("createProject", {
    templateUrl: "components/create-project.html",
    controller: "createProjectController"
});

app.controller("createProjectController", function ($http, $scope, $mdDialog, $window) {

    $scope.showPrompt = function (ev, text) {
        var confirm = $mdDialog.prompt()
            .title('Geben Sie eine Projektnamen ein!')
            .textContent(text)
            .placeholder('Projektname')
            .targetEvent(ev)
            .required(true)
            .ok('Erstellen')
            .cancel('Abbrechen');

        $mdDialog.show(confirm).then(function (result) {
                if (result.length <= 40) {
                    let parameter = JSON.stringify({
                        projectName: result
                    });

                    let url = "../../Backend/CreateProject.php";

                    $http({
                        method: 'POST',
                        url: url,
                        data: parameter
                    }).then(
                        (response) => {
                            console.log(response);
                            $scope.info = response.data.status;

                            if ($scope.info === "50x") {
                                $scope.showAlert("Dieser Projektname wird bereits verwendet!");
                            }

                            if ($scope.info === "201") {
                                $window.location.reload(true);
                            }
                        })
                } else {
                    $scope.showPrompt(ev, 'Der Projektname darf maximal 40 Zeichen lang sein!');
                }
            }
        );
    };

    $scope.showAlert = function (text) {
        $mdDialog.show(
            $mdDialog.alert()
                .clickOutsideToClose(true)
                .textContent(text)
                .ok('Verstanden')
        );
    }
});