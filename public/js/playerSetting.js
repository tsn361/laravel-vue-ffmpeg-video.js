function settings(player, settings) {
    console.log("setrtings == ", settings);

    if (settings.stg_autoplay == "1") {
        player.play(true);
    }
    if (settings.stg_muted == "1") {
        player.muted(true);
    }

    if (settings.stg_autopause == "1") {
        $(window).scroll(function () {
            var scroll = $(this).scrollTop();
            scroll > 600 ? player.pause() : player.play();
        });
    }
    if (settings.stg_loop == "1") {
        player.on("ended", function () {
            player.play();
        });
    }

    if (player.options_.controlBar.children.includes("CaptionsButton")) {
        $("body .video-js .vjs-captions-button  .vjs-menu-content")
            .children()
            .each(function () {
                $(this).html(
                    $(this)
                        .html()
                        .replace("captions settings", "subtitles settings")
                );
                $(this).html($(this).html().replace("captions off", "off"));
            });
    }

    player.on(
        [
            "adserror",
            "ended",
            "adplay",
            "adplaying",
            "adfirstplay",
            "adpause",
            "adended",
            "contentplay",
            "contentplaying",
            "contentfirstplay",
            "contentpause",
            "contentended",
        ],
        function (e) {
            if (e.type == "adplaying") {
                console.log("*************adplaying************* ");
                setTimeout(function () {
                    player.pause();
                }, 500);
            }
            if (e.type == "adended") {
                console.log("*************adended************* ");
                setTimeout(function () {
                    player.play();
                }, 500);
            }
            if (e.type == "adserror") {
                console.log("*************adserror************* ");
                setTimeout(function () {
                    player.play();
                }, 500);
            }
        }
    );
    player.on("adend", function (event) {
        console.log("********************AD ended***************************");
        setTimeout(function () {
            // player.removeClass('vjs-ad-playing');
            player.play();
        }, 500);
    });
}

(function () {
    settings();
});
