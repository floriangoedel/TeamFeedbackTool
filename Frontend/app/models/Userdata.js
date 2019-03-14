app.factory("Userdata", function() {
    function Userdata(data) {
        Object.assign(this, data);
    }
    return Userdata;
});