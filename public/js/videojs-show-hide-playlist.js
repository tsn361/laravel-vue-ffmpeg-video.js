/*! @name @misterben/videojs-skip-intro @version 0.1.1 @license MIT */
(function (global, factory) {
    typeof exports === "object" && typeof module !== "undefined"
        ? (module.exports = factory(
              require("video.js"),
              require("global/window")
          ))
        : typeof define === "function" && define.amd
        ? define(["video.js", "global/window"], factory)
        : (global.ShowHidePlaylist = factory(global.videojs, global.window));
})(this, function (videojs, window) {
    ("use strict");

    videojs =
        videojs && videojs.hasOwnProperty("default")
            ? videojs["default"]
            : videojs;
    window =
        window && window.hasOwnProperty("default") ? window["default"] : window;

    var version = "1.0.0";

    function _inheritsLoose(subClass, superClass) {
        subClass.prototype = Object.create(superClass.prototype);
        subClass.prototype.constructor = subClass;
        subClass.__proto__ = superClass;
    }

    /*! @name @misterben/videojs-float-button @version 0.1.5 @license MIT */

    function _inheritsLoose$1(subClass, superClass) {
        subClass.prototype = Object.create(superClass.prototype);
        subClass.prototype.constructor = subClass;
        subClass.__proto__ = superClass;
    }

    var version$1 = "1.0.0";

    var Button = videojs.getComponent("Button"); // Default options for the plugin.

    var defaults = {
        forceTimeout: 4000,
        text: "",
        position: "top right",
    };
    /**
     * A floating button that appears over the video, outside the controls
     *
     * @class FloatButton
     * @extends {Button}
     */

    var FloatButton =
        /*#__PURE__*/
        (function (_Button) {
            _inheritsLoose$1(FloatButton, _Button);

            /**
             * Constructor for FloatButton
             *
             * @method constructor
             * @param  {Player} player @link videojs#Player
             * @param  {Object} [options] Options object
             * @param  {?number} [options.forceTimeout] ms button should display when shown
             */
            function FloatButton(player, options) {
                var _this;

                options = videojs.mergeOptions(defaults, options);
                _this = _Button.call(this, player, options) || this;

                _this.controlText(options.text);

                _this.hide();

                if (player.bcinfo && player.bcinfo.css) {
                    _this
                        .el()
                        .style.setProperty(
                            "--vjs-accent-colour",
                            player.bcinfo.css.progressColor
                        );
                } // When shown, the float button will be visible even if the user is inactive.
                // Clear this if there is any interaction.

                player.on(["useractive", "userinactive"], function () {
                    _this.removeClass("force-display");
                });
                return _this;
            }
            /**
             * Builds the default DOM `className`.
             *
             * @return {string}
             *         The DOM `className` for this object.
             */

            var _proto = FloatButton.prototype;

            _proto.buildCSSClass = function buildCSSClass() {
                var positions = {
                    left: "vjs-fb-left",
                    right: "vjs-fb-right",
                    top: "vjs-fb-top",
                    bottom: "vjs-fb-bottom",
                    flush: "vjs-fb-flush",
                };
                var rc = this.options_.position
                    .split(" ")
                    .reduce(function (result, p) {
                        if (positions[p]) {
                            result.push(positions[p]);
                        }

                        return result;
                    }, [])
                    .join(" ");
                return "vjs-float-button " + rc;
            };
            /**
             * Create the button el
             *
             * @method createEl
             * @return {Element}
             *          The button's element
             */

            _proto.createEl = function createEl() {
                var el = videojs.dom.createEl(
                    "button",
                    {},
                    {
                        class: this.buildCSSClass(),
                    },
                    videojs.dom.createEl("span")
                );
                this.controlTextEl_ = el.querySelector("span");
                return el;
            };
            /**
             * Show button
             *
             * When shown, the button is made visible irrespective of the user activity state
             * After a timeout or interaction, the button appears with the regular controls
             *
             * @method show
             */

            _proto.show = function show(playList) {
                var _this2 = this;

                _Button.prototype.show.call(this);

                this.addClass("force-display");
                window.setTimeout(function () {
                    _this2.removeClass("force-display");
                }, this.options_.forceTimeout);
                console.log("Got playlist data: ", playList);
                if (playList.length > 0) {
                    this.player_.playlist(playList);
                    // Initialize the playlist-ui plugin with the horizontal option
                    this.player_.playlistUi();
                    // Play through the playlist automatically.
                    this.player_.playlist.autoadvance(0);
                }
            };
            /**
             * Hide button
             *
             * @method show
             */

            _proto.hide = function hide() {
                this.removeClass("force-display");

                _Button.prototype.hide.call(this);
            };

            return FloatButton;
        })(Button); // Include the version number.

    FloatButton.VERSION = version$1;
    videojs.registerComponent("FloatButton", FloatButton);

    /**
     * @class ShowHidePlaylistButton
     * @extends {FloatButton}
     */

    var ShowHidePlaylistButton =
        /*#__PURE__*/
        (function (_FloatButton) {
            _inheritsLoose(ShowHidePlaylistButton, _FloatButton);

            function ShowHidePlaylistButton() {
                return _FloatButton.apply(this, arguments) || this;
            }

            var _proto = ShowHidePlaylistButton.prototype;

            /**
             * Update button
             *
             * @method update
             * @param {Object} data New data for button
             * @param {number} data.seekTo Position to seek to if clicked
             * @param {string} data.label Text for button
             */
            _proto.update = function update(data) {
                this.seekTo = data.seekTo;
                this.controlText(data.label);
            };

            _proto.buildCSSClass = function buildCSSClass() {
                return (
                    "vjs-show-hide-playlist-button " +
                    _FloatButton.prototype.buildCSSClass.call(this)
                );
            };

            _proto.handleClick = function handleClick() {
                $(".card-header").toggle();
            };

            return ShowHidePlaylistButton;
        })(FloatButton);

    videojs.registerComponent("ShowHidePlaylistButton", ShowHidePlaylistButton);

    var defaults$1 = {
        label: "Show playlist",
        playList: [],
    };
    /**
     * Function to invoke when the player is ready.
     *
     * This is a great place for your plugin to initialize itself. When this
     * function is called, the player will have its DOM and child components
     * in place.
     *
     * @function onPlayerReady
     * @param    {Player} player
     *           A Video.js player object.
     *
     * @param    {Object} [options={}]
     *           A plain object containing options for the plugin.
     *
     * @param    {Array.<{name: String, label: string}>} [options.ranges]
     *           An array of data used to find ranges to skip
     *
     */

    var onPlayerReady = function onPlayerReady(player, options) {
        player.showHidePlaylistButton = player.addChild(
            "ShowHidePlaylistButton"
        );
        player.addClass("vjs-show-hide-playlist");
        player.showHidePlaylistButton.show(options.playList);
        player.showHidePlaylistButton.update({
            seekTo: options.skipTime,
            label: options.label,
        });
        // if (options.skipTime > 0) {
        //     player.on("loadedmetadata", function () {
        //         player.showHidePlaylistButton.update({
        //             seekTo: options.skipTime,
        //             label: options.label,
        //         });
        //         player.showHidePlaylistButton.show();
        //         player.on("timeupdate", function () {
        //             if (Math.round(this.currentTime()) > options.skipTime) {
        //                 player.showHidePlaylistButton.hide();
        //             }
        //         });
        //     });
        // }
    };
    /**
     * A video.js plugin.
     *
     *
     * @function showHidePlaylist
     * @param    {Object} [options={}]
     *           An object of options left to the plugin author to define.
     */

    var showHidePlaylist = function showHidePlaylist(options) {
        var _this = this;

        this.ready(function () {
            onPlayerReady(_this, videojs.mergeOptions(defaults$1, options));
        });
    }; // Register the plugin with video.js.

    videojs.registerPlugin("showHidePlaylist", showHidePlaylist); // Include the version number.

    showHidePlaylist.VERSION = version;

    return showHidePlaylist;
});
