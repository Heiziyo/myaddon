$(function () {
    $(".right-icon").on("mouseover", function () {
        $(".tongji-submenu").css("display", "block");
    })
    $(".right-icon").on("mouseout", function () {
        $(".tongji-submenu").css("display", "none");
    })
    $(".tongji-submenu").on("mouseenter", function () {
        $(this).css("display", "block");
    })
    $(".tongji-submenu").on("mouseleave", function () {
        $(this).css("display", "none");
    })

    $(".setting-more").on("click", function () {
        if ($("#more_setting:hidden")) {
            $("#more_setting").show();
            $(this).hide();
        }
    })

    // var n = $(".nav-bar").find("li").length;
    // for (var i = 0; i < n; i++) {
    //     $(".nav-bar li a").on("mouseover", function () {
    //         $(this).addClass("actived");
    //         $(this).parents("li").siblings("li").find("a").removeClass("actived")
    //     })
    //
    // }


        $(".nav-bar li a").each(function (index) {
            $(this).on("click",function () {
                $(".nav-bar li a").removeClass("actived");
                $(".nav-bar li a").eq(index).addClass("actived");
            });
        });



})
