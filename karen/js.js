$(document).ready(function(){
    $.urlParam = function(name){
        var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results != null)
            return results[1] || 0;
    }
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {criterion: 'season'},
        success: function(msg){
            $("#season").empty();
            $("#season").append(msg);
            $("#season2").empty();
            $("#season2").append(msg);
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
    // Жесткость
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {criterion: 'stiffness'},
        success: function(msg){
            $("#stiffness").empty();
            $("#stiffness").append(msg);
            $("#stiffness2").empty();
            $("#stiffness2").append(msg);

        }
    });
    $.ajax({
        type: "POST",
        url: "server.php",
        data: {criterion: 'dia'},
        success: function(msg){
            $("#dia").empty();
            $("#dia").append(msg);
            $("#dia2").empty();
            $("#dia2").append(msg);
        }
    });

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
                dia: $("#dia :selected").val(),
                minPrice: $("#min1").val(),
                maxPrice: $("#max1").val(),
                presence: $("#presence1").prop("checked")
            },
            success: function(msg){
                $("#searchResult").html(msg);
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
                    });
                });
            }
        });
    });

    $('#searchAuto').click(function() {
        $.ajax({
            type: "POST",
            url: "server.php",
            data: {
                criterion: 'searchAuto',
                season: $("#season2 :selected").val(),
                firm: $("#firm2 :selected").val(),
                model: $("#model :selected").val(),
                modification: $("#modification :selected").val(),
                stiffness: $("#stiffness2 :selected").val(),
                dia: $("#dia2 :selected").val(),
                minPrice: $("#min1").val(),
                maxPrice: $("#max1").val(),
                presence: $("#presence2").prop("checked")
            },
            success: function(msg){
                $("#searchResult").html(msg);
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
                    });
                });
            }
        });
    });

    if (parseInt($.urlParam('offset')) > 0) {
        $.ajax({
            type: "POST",
            url: "server.php",
            data: {
                criterion: 'nextPage',
                offset: $.urlParam('offset'),
                tbl: $.urlParam('tbl')},
            success: function(msg){
                $("#searchResult").empty();
                $("#searchResult").html(msg);
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
                    });
                });
            }
        });
    }
});