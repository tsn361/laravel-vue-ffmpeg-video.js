function doubleTap(player) {
    var MainDiv, BackwordSkip, ForwardSkip, MiddleDiv;

    MainDiv = document.createElement("div");
    MainDiv.classList.add("vjs-double-tap");

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
}

videojs.registerPlugin("doubleTap", doubleTap);
