const defaultOptions = {
    skipTime: 10,
    customElementsclass: "",
};
function doubleTap(player, options = {}) {
    /* Merge defaults and options, without modifying defaults */
    var settings = $.extend({}, defaultOptions, options);

    var MainDiv, BackwordSkip, ForwardSkip, MiddleDiv;

    MainDiv = document.createElement("div");
    MainDiv.setAttribute("id", "vjs-double-tap");
    MainDiv.classList.add("vjs-double-tap");
    if (settings.customElementsclass) {
        MainDiv.classList.add(settings.customElementsclass);
    }
    MainDiv.style.cssText = `
        position: relative;
        display: grid;
        grid-template-columns: 1fr 2fr 1fr;
        grid-gap: 2%;
        height: 88% !important;
        `;

    BackwordSkip = document.createElement("div");
    BackwordSkip.setAttribute("id", "vjs-double-click-BackwordSkip");
    BackwordSkip.classList.add("vjs-double-click-BackwordSkip");
    MainDiv.appendChild(BackwordSkip);

    MiddleDiv = document.createElement("div");
    MiddleDiv.setAttribute("id", "vjs-double-click-MiddleDiv");
    MiddleDiv.classList.add("vjs-double-click-MiddleDiv");
    MainDiv.appendChild(MiddleDiv);

    ForwardSkip = document.createElement("div");
    ForwardSkip.setAttribute("id", "vjs-double-click-ForwardSkip");
    ForwardSkip.classList.add("vjs-double-click-ForwardSkip");
    MainDiv.appendChild(ForwardSkip);

    var cntrollBar = document.querySelector(".vjs-control-bar");
    insertElementAfter(MainDiv, cntrollBar);

    function insertElementAfter(newEl, element) {
        element.parentNode.insertBefore(newEl, element.nextSibling);
    }
    function singleClick() {
        console.log("singleClick");
        if (player.paused()) {
            player.play();
        } else {
            player.pause();
        }
    }

    function doubleClick(whichSide) {
        console.log("doubleClick");
        if (whichSide == "vjs-double-click-BackwordSkip") {
            console.log("element BackwordSkip clicked 🎉🎉🎉", event);
            document.querySelector(".skip-back").click();
        } else if (whichSide == "vjs-double-click-ForwardSkip") {
            console.log("element ForwardSkip clicked 🎉🎉🎉", event);
            document.querySelector(".skip-forward").click();
        }
    }
    var clickCount = 0;

    MainDiv.addEventListener(
        "click",
        function (e) {
            clickCount++;
            if (clickCount === 1) {
                singleClickTimer = setTimeout(function () {
                    clickCount = 0;
                    singleClick();
                }, 400);
            } else if (clickCount === 2) {
                clearTimeout(singleClickTimer);
                clickCount = 0;
                // console.log("e.target.id single click = ", e.target.id);
                if (
                    /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
                        navigator.userAgent
                    )
                ) {
                    // true for mobile device
                    doubleClick(e.target.id);
                } else {
                    // false for not mobile device
                    document.querySelector(".vjs-fullscreen-control").click();
                }
            }
        },
        false
    );
}

videojs.registerPlugin("doubleTap", doubleTap);
