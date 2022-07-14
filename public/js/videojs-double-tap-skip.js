const defaultOptions = {
    skipTime: 10,
    customElementsclass: "",
};
function doubleTap(player, options = {}) {
    /* Merge defaults and options, without modifying defaults */
    var settings = $.extend({}, defaultOptions, options);

    var MainDiv, BackwordSkip, ForwardSkip, MiddleDiv;

    MainDiv = document.createElement("div");
    MainDiv.classList.add("vjs-double-tap");
    if (settings.customElementsclass) {
        MainDiv.classList.add(settings.customElementsclass);
    }
    MainDiv.style.cssText = `
        position: relative;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-gap: 10%;
        height: 88% !important;
        `;

    BackwordSkip = document.createElement("div");
    BackwordSkip.classList.add("vjs-double-click-BackwordSkip");
    MainDiv.appendChild(BackwordSkip);

    MiddleDiv = document.createElement("div");
    MiddleDiv.classList.add("vjs-double-click-MiddleDiv");
    MainDiv.appendChild(MiddleDiv);

    ForwardSkip = document.createElement("div");
    ForwardSkip.classList.add("vjs-double-click-ForwardSkip");
    MainDiv.appendChild(ForwardSkip);

    var cntrollBar = document.querySelector(".vjs-control-bar");
    insertElementAfter(MainDiv, cntrollBar);

    function insertElementAfter(newEl, element) {
        element.parentNode.insertBefore(newEl, element.nextSibling);
    }

    player.on("play", function () {
        console.log("options == ", settings);
        BackwordSkip.addEventListener("dblclick", function handleClick(event) {
            console.log("element BackwordSkip clicked 🎉🎉🎉", event);
            // player.currentTime(
            //     player.currentTime() - settings.skipTime < 0
            //         ? 0
            //         : player.currentTime() - settings.skipTime
            // );
            document.querySelector(".skip-back").click();
        });
        ForwardSkip.addEventListener("dblclick", function handleClick(event) {
            console.log("element ForwardSkip clicked 🎉🎉🎉", event);
            // player.currentTime(player.currentTime() + settings.skipTime);
            document.querySelector(".skip-forward").click();
        });
    });
}

videojs.registerPlugin("doubleTap", doubleTap);
