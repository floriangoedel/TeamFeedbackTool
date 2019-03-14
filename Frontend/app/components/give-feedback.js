app.component("giveFeedback", {
    templateUrl: "components/give-feedback.html",
    controller: "giveFeedbackController"
});

app.controller("giveFeedbackController", function ($http, $scope, $mdDialog, $rootScope) {

    $scope.stress = 0;

    $scope.motivation = 0;

    $scope.data = {
        group1: 'Ja',
        group2: 'Ja'
    };

    $scope.antwort = "Danke für das Beantworten der Fragen! Kommen Sie in einer Woche wieder!";

    this.submit = () => {

        let satisfied = 0;

        let technicalSkills = 0;

        if ($scope.data.group1 === "Ja") {
            satisfied = 1;
        }

        if ($scope.data.group2 === "Ja") {
            technicalSkills = 1;
        }

        let parameter = JSON.stringify({
            projectId: $rootScope.projectId,
            sliderValue_stress: $scope.stress,
            sliderValue_motivation: $scope.motivation,
            work_performance_satisfied: satisfied,
            technicalSkills: technicalSkills
        });

        let url = "../../Backend/GiveFeedback.php";

        $http({
            method: 'POST',
            url: url,
            data: parameter
        }).then(
            (response) => {
                this.info = response.data.infotext;
                console.log(response);

                if (this.info === "You gotta wait a week buddy") {
                    $scope.showAlert("Sie habe diese Woche bereits abgestimmt!")
                } else if (this.info === "Feedback Submitted") {
                    $scope.showAlert("Danke für das Abstimmen! Kommen Sie nächste Woche wieder!")
                } else {

                }
            }, function (error) {
                console.log(error);
            });
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