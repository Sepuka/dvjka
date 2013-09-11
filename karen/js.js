$(document).ready(function(){
    $.urlParam = function(name){
        var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results != null)
            return results[1] || 0;
    }

    $.ajax({
        type: "POST",
        url: "server.php",
        data: {criterion: 'firm2'},
        success: function(msg){
            $("#firm2").empty();
            $("#firm2").append(msg);
        }
    });

    $("#firm2").change(function() {
        $.ajax({
            type: "POST",
            url: "server.php",
            data: {
                criterion: 'model',
                model: $("#firm2 :selected").val()
            },
            success: function(msg){
                $("#model").empty();
                $("#model").append(msg);
            }
        });
    });

    $("#model").change(function() {
        $.ajax({
            type: "POST",
            url: "server.php",
            data: {
                criterion: 'modification',
                modification: $("#model :selected").val()
            },
            success: function(msg){
                $("#modification").empty();
                $("#modification").append(msg);
            }
        });
    });

    $(".zoomimg").click(function() {
        $(this).children('img').stop(true,true).animate({
           height: "500",
           width: "500",
           left: "-200",
           top: "-200"
        }, "fast");
        $(this).addClass('isBig');
        $(".isBig").click(function() {
            $(this).children('img').stop(true,true).animate({
                height: "50",
                width: "50",
                left: "0",
                top: "0"
            }, "fast");
            $(this).removeClass('isBig');
        });
    });
});