app.component("inhalt", {
    templateUrl: "components/inhalt.html",
    controller: "InhaltController"
});

app.controller("InhaltController", function($log, $window) {

    this.openDashboard = () => {
        $window.location.href = "#!/dashboard";
    };

});