/**
* @version   $Id: gantry-morearticles.js 1644 2012-07-13 00:49:41Z kevin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

var GantryMoreArticles = new Class({
    Implements: [Options],
    options: {
        leadings: 2,
        moreText: "more articles",
        url: ""
    },
    initialize: function (a) {
        this.setOptions(a);
        this.wrapper = document.getElements('.blog-featured .items-row') || document.getElements('.blog .items-row') || document.getElement('.items-leading');
        if (!this.wrapper || !this.wrapper.length) {
            return;
        }
        if (this.wrapper.length) this.wrapper = this.wrapper.getLast();
        this.start = this.options.leadings;
        this.buildButton();
        this.ajax = new Request({
            url: this.options.url,
            method: "get",
            onRequest: function () {
                this.button.addClass("spinner");
            }.bind(this),
            onSuccess: this.handle.bind(this)
        });
    },
    buildButton: function () {
        this.button = new Element("a", {
            id: "more-articles",
            href: "#"
        }).adopt(new Element("span").set("text", this.options.moreText));
        var a = new Element("div", {
            "class": "rt-more-articles"
        }).inject(this.wrapper, "after");
        this.button.inject(a).addEvent("click", function (b) {
            b.stop();
            if (this.button.hasClass("disabled")) {
                return;
            }
            this.ajax.get({
                limitstart: this.start
            });
        }.bind(this));
    },
    handle: function (b) {
        this.start += this.options.leadings;
        this.button.removeClass("spinner");
        var d = new Element("div").set("html", b);
        var a = d.getElements(".items-leading > div, .item");
        if (!a.length) {
            this.button.removeEvent("click");
            this.button.addClass("disabled");
        } else {
            if (a.length < this.options.leadings) {
                this.button.removeEvent("click");
                this.button.addClass("disabled");
            }
            a.inject(this.wrapper, 'after');
            if (typeof GantryBuildSpans == "function") {
                var c = ["rt-block"];
                var e = ["h3", "h2", "h1"];
                GantryBuildSpans(c, e);
            }
            if (typeof GantryArticleDetails != "undefined") {
                GantryArticleDetails.init();
            }
        }
        if (typeof rokbox != "undefined" && rokbox.refresh) {
            rokbox.refresh();
        }
    }
});