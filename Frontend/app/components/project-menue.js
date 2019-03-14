"use strict";

app.component("projectMenue", {
    templateUrl: "components/project-menue.html",
    controller: "ProjectMenueController",
    bindings: {}
});


app.controller("ProjectMenueController", function ($log, $rootScope, $scope, $http, UserdataService) {
    $log.debug("ProjectMenueController()");

    this.$onInit = function () {
        $scope.plview = false;
        $scope.giveFeedback = false;
        $scope.pressedIndex = NaN;
        loadProjects();
    };

    $rootScope.$watch('projectMenueVisibility', () => {
        if ($rootScope.projectMenueVisibility) {
            let element = angular.element(document.querySelector('.project-menue-closed'));
            element.addClass('project-menue-opened');
            element.removeClass('project-menue-closed');
        } else {
            let element = angular.element(document.querySelector('.project-menue-opened'));
            element.addClass('project-menue-closed');
            element.removeClass('project-menue-opened');
        }
    });

    this.clickedOnProjectTitle = ($event, index) => {
        $rootScope.projectId = angular.element($event.currentTarget).attr('project-id');
        let parameter = JSON.stringify({
            projectId: $rootScope.projectId
        });

        let url = "../../Backend/SendProjectInformation.php";

        let userId;

        UserdataService.getUserdata().then((responseUserdata) => {
            userId = responseUserdata.userId;
            $http({
                method: 'POST',
                url: url,
                data: parameter
            }).then(
                (responseLeaderId) => {
                    console.log(responseLeaderId);
                    $scope.leaderId = responseLeaderId.data.fk_leaderId;
                    if ($scope.leaderId === userId) {
                        $scope.giveFeedback = false;
                        $scope.plview = true;
                    } else {
                        $scope.giveFeedback = true;
                        $scope.plview = false;
                    }
                });
        });

        $scope.pressedIndex = index;
    };

    let loadProjects = () => {
        let recievingUrlProjectInformation = "../../Backend/SendProjects.php";

        $http({
            method: 'POST',
            url: recievingUrlProjectInformation
        }).then(
            (response) => {
                this.projects = [];
                this.projectTitles = response.data.projectNames.split(";");
                this.projectIds = response.data.projectIds.split(";");
            }, function (error) {
                console.log(error);
            }).then(() => {
            for (let i = 0; i < this.projectIds.length; i++) {
                if (this.projectTitles[i].length > 23) {
                    this.projects.push({
                        'id': this.projectIds[i],
                        'name': this.projectTitles[i].slice(0, 23) + '...'
                    });
                } else {
                    this.projects.push({
                        'id': this.projectIds[i],
                        'name': this.projectTitles[i]
                    });
                }
            }
        }).then(() => {
            // what project is shown in the dashboard
            $rootScope.currentProject = this.projects[this.projects.length];
            angular.element(document.getElementById());
        });

    };
});