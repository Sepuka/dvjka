$(document).ready(function(){
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {criterion: 'season'},
        success: function(msg){
            $("#season").empty();
            $("#season").append(msg);
        }
    });
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {criterion: 'brand'},
        success: function(msg){
            $("#firm").empty();
            $("#firm").append(msg);
        }
    });
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {criterion: 'width'},
        success: function(msg){
            $("#width").empty();
            $("#width").append(msg);
        }
    });
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {criterion: 'profile'},
        success: function(msg){
            $("#profile").empty();
            $("#profile").append(msg);
        }
    });
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {criterion: 'stiffness'},
        success: function(msg){
            $("#stiffness").empty();
            $("#stiffness").append(msg);
        }
    });
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {criterion: 'dia'},
        success: function(msg){
            $("#dia").empty();
            $("#dia").append(msg);
        }
    });

    $('#searchTire').click(function() {
        $.ajax({
            type: "POST",
            url: "server.php",
            data: {
                criterion: 'searchTire',
                season: $("#season :selected").val(),
                firm: $("#firm :selected").val(),
                width: $("#width :selected").val(),
                profile: $("#profile :selected").val(),
                stiffness: $("#stiffness :selected").val(),
                dia: $("#dia :selected").val()
            },
            success: function(msg){
                $("#searchResult").html(msg);
            }
        });
    });
});