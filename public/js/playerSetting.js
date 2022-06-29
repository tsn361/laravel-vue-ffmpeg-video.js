function settings(player, settings) {
    console.log("setrtings == ", settings);
    volumeLevel = 0.7;

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
}

(function () {
    settings();
});
