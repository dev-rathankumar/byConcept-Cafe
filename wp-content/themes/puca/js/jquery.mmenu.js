'use strict';

/*!
 * jQuery mmenu v7.0.5
 * @requires jQuery 1.7.0 or later
 *
 * mmenu.frebsite.nl
 *
 * Copyright (c) Fred Heusschen
 * www.frebsite.nl
 *
 * License: CC-BY-NC-4.0
 * http://creativecommons.org/licenses/by-nc/4.0/
 */
!function (e) {
  function t() {
    e[n].glbl || (l = {
      $wndw: e(window),
      $docu: e(document),
      $html: e("html"),
      $body: e("body")
    }, s = {}, a = {}, r = {}, e.each([s, a, r], function (e, t) {
      t.add = function (e) {
        e = e.split(" ");

        for (var n = 0, i = e.length; n < i; n++) t[e[n]] = t.mm(e[n]);
      };
    }), s.mm = function (e) {
      return "mm-" + e;
    }, s.add("wrapper menu panels panel nopanel navbar listview nolistview listitem btn hidden"), s.umm = function (e) {
      return "mm-" == e.slice(0, 3) && (e = e.slice(3)), e;
    }, a.mm = function (e) {
      return "mm-" + e;
    }, a.add("parent child title"), r.mm = function (e) {
      return e + ".mm";
    }, r.add("transitionend webkitTransitionEnd click scroll resize keydown mousedown mouseup touchstart touchmove touchend orientationchange"), e[n]._c = s, e[n]._d = a, e[n]._e = r, e[n].glbl = l);
  }

  var n = "mmenu",
      i = "7.0.5";

  if (!(e[n] && e[n].version > i)) {
    e[n] = function (e, t, n) {
      return this.$menu = e, this._api = ["bind", "getInstance", "initPanels", "openPanel", "closePanel", "closeAllPanels", "setSelected"], this.opts = t, this.conf = n, this.vars = {}, this.cbck = {}, this.mtch = {}, "function" == typeof this.___deprecated && this.___deprecated(), this._initHooks(), this._initWrappers(), this._initAddons(), this._initExtensions(), this._initMenu(), this._initPanels(), this._initOpened(), this._initAnchors(), this._initMatchMedia(), "function" == typeof this.___debug && this.___debug(), this;
    }, e[n].version = i, e[n].uniqueId = 0, e[n].wrappers = {}, e[n].addons = {}, e[n].defaults = {
      hooks: {},
      extensions: [],
      wrappers: [],
      navbar: {
        add: !0,
        title: "Menu",
        titleLink: "parent"
      },
      onClick: {
        setSelected: !0
      },
      slidingSubmenus: !0
    }, e[n].configuration = {
      classNames: {
        divider: "Divider",
        inset: "Inset",
        nolistview: "NoListview",
        nopanel: "NoPanel",
        panel: "Panel",
        selected: "Selected",
        spacer: "Spacer",
        vertical: "Vertical"
      },
      clone: !1,
      openingInterval: 25,
      panelNodetype: "ul, ol, div",
      transitionDuration: 400
    }, e[n].prototype = {
      getInstance: function () {
        return this;
      },
      initPanels: function (e) {
        this._initPanels(e);
      },
      openPanel: function (t, i) {
        if (this.trigger("openPanel:before", t), t && t.length && (t.is("." + s.panel) || (t = t.closest("." + s.panel)), t.is("." + s.panel))) {
          var r = this;
          if ("boolean" != typeof i && (i = !0), t.parent("." + s.listitem + "_vertical").length) t.parents("." + s.listitem + "_vertical").addClass(s.listitem + "_opened").children("." + s.panel).removeClass(s.hidden), this.openPanel(t.parents("." + s.panel).not(function () {
            return e(this).parent("." + s.listitem + "_vertical").length;
          }).first()), this.trigger("openPanel:start", t), this.trigger("openPanel:finish", t);else {
            if (t.hasClass(s.panel + "_opened")) return;
            var l = this.$pnls.children("." + s.panel),
                o = this.$pnls.children("." + s.panel + "_opened");
            if (!e[n].support.csstransitions) return o.addClass(s.hidden).removeClass(s.panel + "_opened"), t.removeClass(s.hidden).addClass(s.panel + "_opened"), this.trigger("openPanel:start", t), void this.trigger("openPanel:finish", t);
            l.not(t).removeClass(s.panel + "_opened-parent");

            for (var d = t.data(a.parent); d;) d = d.closest("." + s.panel), d.parent("." + s.listitem + "_vertical").length || d.addClass(s.panel + "_opened-parent"), d = d.data(a.parent);

            l.removeClass(s.panel + "_highest").not(o).not(t).addClass(s.hidden), t.removeClass(s.hidden);

            var c = function () {
              o.removeClass(s.panel + "_opened"), t.addClass(s.panel + "_opened"), t.hasClass(s.panel + "_opened-parent") ? (o.addClass(s.panel + "_highest"), t.removeClass(s.panel + "_opened-parent")) : (o.addClass(s.panel + "_opened-parent"), t.addClass(s.panel + "_highest")), r.trigger("openPanel:start", t);
            },
                h = function () {
              o.removeClass(s.panel + "_highest").addClass(s.hidden), t.removeClass(s.panel + "_highest"), r.trigger("openPanel:finish", t);
            };

            i && !t.hasClass(s.panel + "_noanimation") ? setTimeout(function () {
              r.__transitionend(t, function () {
                h();
              }, r.conf.transitionDuration), c();
            }, r.conf.openingInterval) : (c(), h());
          }
          this.trigger("openPanel:after", t);
        }
      },
      closePanel: function (e) {
        this.trigger("closePanel:before", e);
        var t = e.parent();
        t.hasClass(s.listitem + "_vertical") && (t.removeClass(s.listitem + "_opened"), e.addClass(s.hidden), this.trigger("closePanel", e)), this.trigger("closePanel:after", e);
      },
      closeAllPanels: function (e) {
        this.trigger("closeAllPanels:before"), this.$pnls.find("." + s.listview).children().removeClass(s.listitem + "_selected").filter("." + s.listitem + "_vertical").removeClass(s.listitem + "_opened");
        var t = this.$pnls.children("." + s.panel),
            n = e && e.length ? e : t.first();
        this.$pnls.children("." + s.panel).not(n).removeClass(s.panel + "_opened").removeClass(s.panel + "_opened-parent").removeClass(s.panel + "_highest").addClass(s.hidden), this.openPanel(n, !1), this.trigger("closeAllPanels:after");
      },
      togglePanel: function (e) {
        var t = e.parent();
        t.hasClass(s.listitem + "_vertical") && this[t.hasClass(s.listitem + "_opened") ? "closePanel" : "openPanel"](e);
      },
      setSelected: function (e) {
        this.trigger("setSelected:before", e), this.$menu.find("." + s.listitem + "_selected").removeClass(s.listitem + "_selected"), e.addClass(s.listitem + "_selected"), this.trigger("setSelected:after", e);
      },
      bind: function (e, t) {
        this.cbck[e] = this.cbck[e] || [], this.cbck[e].push(t);
      },
      trigger: function () {
        var e = this,
            t = Array.prototype.slice.call(arguments),
            n = t.shift();
        if (this.cbck[n]) for (var i = 0, s = this.cbck[n].length; i < s; i++) this.cbck[n][i].apply(e, t);
      },
      matchMedia: function (e, t, n) {
        var i = {
          yes: t,
          no: n
        };
        this.mtch[e] = this.mtch[e] || [], this.mtch[e].push(i);
      },
      _initHooks: function () {
        for (var e in this.opts.hooks) this.bind(e, this.opts.hooks[e]);
      },
      _initWrappers: function () {
        this.trigger("initWrappers:before");

        for (var t = 0; t < this.opts.wrappers.length; t++) {
          var i = e[n].wrappers[this.opts.wrappers[t]];
          "function" == typeof i && i.call(this);
        }

        this.trigger("initWrappers:after");
      },
      _initAddons: function () {
        this.trigger("initAddons:before");
        var t;

        for (t in e[n].addons) e[n].addons[t].add.call(this), e[n].addons[t].add = function () {};

        for (t in e[n].addons) e[n].addons[t].setup.call(this);

        this.trigger("initAddons:after");
      },
      _initExtensions: function () {
        this.trigger("initExtensions:before");
        var e = this;
        this.opts.extensions.constructor === Array && (this.opts.extensions = {
          all: this.opts.extensions
        });

        for (var t in this.opts.extensions) this.opts.extensions[t] = this.opts.extensions[t].length ? s.menu + "_" + this.opts.extensions[t].join(" " + s.menu + "_") : "", this.opts.extensions[t] && !function (t) {
          e.matchMedia(t, function () {
            this.$menu.addClass(this.opts.extensions[t]);
          }, function () {
            this.$menu.removeClass(this.opts.extensions[t]);
          });
        }(t);

        this.trigger("initExtensions:after");
      },
      _initMenu: function () {
        this.trigger("initMenu:before");
        this.conf.clone && (this.$orig = this.$menu, this.$menu = this.$orig.clone(), this.$menu.add(this.$menu.find("[id]")).filter("[id]").each(function () {
          e(this).attr("id", s.mm(e(this).attr("id")));
        })), this.$menu.attr("id", this.$menu.attr("id") || this.__getUniqueId()), this.$pnls = e('<div class="' + s.panels + '" />').append(this.$menu.children(this.conf.panelNodetype)).prependTo(this.$menu), this.$menu.addClass(s.menu).parent().addClass(s.wrapper), this.trigger("initMenu:after");
      },
      _initPanels: function (t) {
        this.trigger("initPanels:before", t), t = t || this.$pnls.children(this.conf.panelNodetype);

        var n = e(),
            i = this,
            a = function (t) {
          t.filter(i.conf.panelNodetype).each(function (t) {
            var r = i._initPanel(e(this));

            if (r) {
              i._initNavbar(r), i._initListview(r), n = n.add(r);
              var l = r.children("." + s.listview).children("li").children(i.conf.panelNodetype).add(r.children("." + i.conf.classNames.panel));
              l.length && a(l);
            }
          });
        };

        a(t), this.trigger("initPanels:after", n);
      },
      _initPanel: function (e) {
        this.trigger("initPanel:before", e);
        if (e.hasClass(s.panel)) return e;
        if (this.__refactorClass(e, this.conf.classNames.panel, s.panel), this.__refactorClass(e, this.conf.classNames.nopanel, s.nopanel), this.__refactorClass(e, this.conf.classNames.inset, s.listview + "_inset"), e.filter("." + s.listview + "_inset").addClass(s.nopanel), e.hasClass(s.nopanel)) return !1;
        var t = e.hasClass(this.conf.classNames.vertical) || !this.opts.slidingSubmenus;
        e.removeClass(this.conf.classNames.vertical);

        var n = e.attr("id") || this.__getUniqueId();

        e.is("ul, ol") && (e.removeAttr("id"), e.wrap("<div />"), e = e.parent()), e.attr("id", n), e.addClass(s.panel + " " + s.hidden);
        var i = e.parent("li");
        return t ? i.addClass(s.listitem + "_vertical") : e.appendTo(this.$pnls), i.length && (i.data(a.child, e), e.data(a.parent, i)), this.trigger("initPanel:after", e), e;
      },
      _initNavbar: function (t) {
        if (this.trigger("initNavbar:before", t), !t.children("." + s.navbar).length) {
          var n = t.data(a.parent),
              i = e('<div class="' + s.navbar + '" />'),
              r = this.__getPanelTitle(t, this.opts.navbar.title),
              l = "";

          if (n && n.length) {
            if (n.hasClass(s.listitem + "_vertical")) return;
            if (n.parent().is("." + s.listview)) var o = n.children("a, span").not("." + s.btn + "_next");else var o = n.closest("." + s.panel).find('a[href="#' + t.attr("id") + '"]');
            o = o.first(), n = o.closest("." + s.panel);
            var d = n.attr("id");

            switch (r = this.__getPanelTitle(t, e("<span>" + o.text() + "</span>").text()), this.opts.navbar.titleLink) {
              case "anchor":
                l = o.attr("href");
                break;

              case "parent":
                l = "#" + d;
            }

            i.append('<a class="' + s.btn + " " + s.btn + "_prev " + s.navbar + '__btn" href="#' + d + '" />');
          } else if (!this.opts.navbar.title) return;

          this.opts.navbar.add && t.addClass(s.panel + "_has-navbar"), i.append('<a class="' + s.navbar + '__title"' + (l.length ? ' href="' + l + '"' : "") + ">" + r + "</a>").prependTo(t), this.trigger("initNavbar:after", t);
        }
      },
      _initListview: function (t) {
        this.trigger("initListview:before", t);

        var n = this.__childAddBack(t, "ul, ol");

        this.__refactorClass(n, this.conf.classNames.nolistview, s.nolistview);

        var i = n.not("." + s.nolistview).addClass(s.listview).children().addClass(s.listitem);
        this.__refactorClass(i, this.conf.classNames.selected, s.listitem + "_selected"), this.__refactorClass(i, this.conf.classNames.divider, s.listitem + "_divider"), this.__refactorClass(i, this.conf.classNames.spacer, s.listitem + "_spacer");
        var r = t.data(a.parent);

        if (r && r.is("." + s.listitem) && !r.children("." + s.btn + "_next").length) {
          var l = r.children("a, span").first(),
              o = e('<a class="' + s.btn + '_next" href="#' + t.attr("id") + '" />').insertBefore(l);
          l.is("span") && o.addClass(s.btn + "_fullwidth");
        }

        this.trigger("initListview:after", t);
      },
      _initOpened: function () {
        this.trigger("initOpened:before");
        var e = this.$pnls.find("." + s.listitem + "_selected").removeClass(s.listitem + "_selected").last().addClass(s.listitem + "_selected"),
            t = e.length ? e.closest("." + s.panel) : this.$pnls.children("." + s.panel).first();
        this.openPanel(t, !1), this.trigger("initOpened:after");
      },
      _initAnchors: function () {
        this.trigger("initAnchors:before");
        var t = this;
        l.$body.on(r.click + "-oncanvas", "a[href]", function (i) {
          var a = e(this),
              r = a.attr("href"),
              l = t.$menu.find(a).length,
              o = a.is("." + s.listitem + " > a"),
              d = a.is('[rel="external"]') || a.is('[target="_blank"]');
          if (l && r.length > 1 && "#" == r.slice(0, 1)) try {
            var c = t.$menu.find(r);
            if (c.is("." + s.panel)) return t[a.parent().hasClass(s.listitem + "_vertical") ? "togglePanel" : "openPanel"](c), void i.preventDefault();
          } catch (h) {}
          var f = {
            close: null,
            setSelected: null,
            preventDefault: "#" == r.slice(0, 1)
          };

          for (var p in e[n].addons) {
            var u = e[n].addons[p].clickAnchor.call(t, a, l, o, d);

            if (u) {
              if ("boolean" == typeof u) return void i.preventDefault();
              "object" == typeof u && (f = e.extend({}, f, u));
            }
          }

          l && o && !d && (t.__valueOrFn(a, t.opts.onClick.setSelected, f.setSelected) && t.setSelected(e(i.target).parent()), t.__valueOrFn(a, t.opts.onClick.preventDefault, f.preventDefault) && i.preventDefault(), t.__valueOrFn(a, t.opts.onClick.close, f.close) && t.opts.offCanvas && "function" == typeof t.close && t.close());
        }), this.trigger("initAnchors:after");
      },
      _initMatchMedia: function () {
        var e = this;

        for (var t in this.mtch) !function () {
          var n = t,
              i = window.matchMedia(n);
          e._fireMatchMedia(n, i), i.addListener(function (t) {
            e._fireMatchMedia(n, t);
          });
        }();
      },
      _fireMatchMedia: function (e, t) {
        for (var n = t.matches ? "yes" : "no", i = 0; i < this.mtch[e].length; i++) this.mtch[e][i][n].call(this);
      },
      _getOriginalMenuId: function () {
        var e = this.$menu.attr("id");
        return this.conf.clone && e && e.length && (e = s.umm(e)), e;
      },
      __api: function () {
        var t = this,
            n = {};
        return e.each(this._api, function (e) {
          var i = this;

          n[i] = function () {
            var e = t[i].apply(t, arguments);
            return "undefined" == typeof e ? n : e;
          };
        }), n;
      },
      __valueOrFn: function (e, t, n) {
        if ("function" == typeof t) {
          var i = t.call(e[0]);
          if ("undefined" != typeof i) return i;
        }

        return "function" != typeof t && "undefined" != typeof t || "undefined" == typeof n ? t : n;
      },
      __getPanelTitle: function (t, i) {
        var s;
        return "function" == typeof this.opts.navbar.title && (s = this.opts.navbar.title.call(t[0])), "undefined" == typeof s && (s = t.data(a.title)), "undefined" != typeof s ? s : "string" == typeof i ? e[n].i18n(i) : e[n].i18n(e[n].defaults.navbar.title);
      },
      __refactorClass: function (e, t, n) {
        return e.filter("." + t).removeClass(t).addClass(n);
      },
      __findAddBack: function (e, t) {
        return e.find(t).add(e.filter(t));
      },
      __childAddBack: function (e, t) {
        return e.children(t).add(e.filter(t));
      },
      __filterListItems: function (e) {
        return e.not("." + s.listitem + "_divider").not("." + s.hidden);
      },
      __filterListItemAnchors: function (e) {
        return this.__filterListItems(e).children("a").not("." + s.btn + "_next");
      },
      __openPanelWoAnimation: function (e) {
        e.hasClass(s.panel + "_noanimation") || (e.addClass(s.panel + "_noanimation"), this.__transitionend(e, function () {
          e.removeClass(s.panel + "_noanimation");
        }, this.conf.openingInterval), this.openPanel(e));
      },
      __transitionend: function (e, t, n) {
        var i = !1,
            s = function (n) {
          "undefined" != typeof n && n.target != e[0] || (i || (e.off(r.transitionend), e.off(r.webkitTransitionEnd), t.call(e[0])), i = !0);
        };

        e.on(r.transitionend, s), e.on(r.webkitTransitionEnd, s), setTimeout(s, 1.1 * n);
      },
      __getUniqueId: function () {
        return s.mm(e[n].uniqueId++);
      }
    }, e.fn[n] = function (i, s) {
      t(), i = e.extend(!0, {}, e[n].defaults, i), s = e.extend(!0, {}, e[n].configuration, s);
      var a = e();
      return this.each(function () {
        var t = e(this);

        if (!t.data(n)) {
          var r = new e[n](t, i, s);
          r.$menu.data(n, r.__api()), a = a.add(r.$menu);
        }
      }), a;
    }, e[n].i18n = function () {
      var t = {};
      return function (n) {
        switch (typeof n) {
          case "object":
            return e.extend(t, n), t;

          case "string":
            return t[n] || n;

          case "undefined":
          default:
            return t;
        }
      };
    }(), e[n].support = {
      touch: "ontouchstart" in window || navigator.msMaxTouchPoints || !1,
      csstransitions: function () {
        return "undefined" == typeof Modernizr || "undefined" == typeof Modernizr.csstransitions || Modernizr.csstransitions;
      }(),
      csstransforms: function () {
        return "undefined" == typeof Modernizr || "undefined" == typeof Modernizr.csstransforms || Modernizr.csstransforms;
      }(),
      csstransforms3d: function () {
        return "undefined" == typeof Modernizr || "undefined" == typeof Modernizr.csstransforms3d || Modernizr.csstransforms3d;
      }()
    };
    var s, a, r, l;
  }
}(jQuery);
!function (e) {
  var t = "mmenu",
      n = "offCanvas";
  e[t].addons[n] = {
    setup: function () {
      if (this.opts[n]) {
        var i = this.opts[n],
            s = this.conf[n];
        r = e[t].glbl, this._api = e.merge(this._api, ["open", "close", "setPage"]), "object" != typeof i && (i = {}), i = this.opts[n] = e.extend(!0, {}, e[t].defaults[n], i), "string" != typeof s.pageSelector && (s.pageSelector = "> " + s.pageNodetype), this.vars.opened = !1;
        var a = [o.menu + "_offcanvas"];
        e[t].support.csstransforms || a.push(o["no-csstransforms"]), e[t].support.csstransforms3d || a.push(o["no-csstransforms3d"]), this.bind("initMenu:after", function () {
          var e = this;
          this.setPage(r.$page), this._initBlocker(), this["_initWindow_" + n](), this.$menu.addClass(a.join(" ")).parent("." + o.wrapper).removeClass(o.wrapper), this.$menu[s.menuInsertMethod](s.menuInsertSelector);
          var t = window.location.hash;

          if (t) {
            var i = this._getOriginalMenuId();

            i && i == t.slice(1) && setTimeout(function () {
              e.open();
            }, 1e3);
          }
        }), this.bind("open:start:sr-aria", function () {
          this.__sr_aria(this.$menu, "hidden", !1);
        }), this.bind("close:finish:sr-aria", function () {
          this.__sr_aria(this.$menu, "hidden", !0);
        }), this.bind("initMenu:after:sr-aria", function () {
          this.__sr_aria(this.$menu, "hidden", !0);
        });
      }
    },
    add: function () {
      o = e[t]._c, i = e[t]._d, s = e[t]._e, o.add("slideout page no-csstransforms3d"), i.add("style");
    },
    clickAnchor: function (e, t) {
      var i = this;

      if (this.opts[n]) {
        var s = this._getOriginalMenuId();

        if (s && e.is('[href="#' + s + '"]')) {
          if (t) return this.open(), !0;
          var a = e.closest("." + o.menu);

          if (a.length) {
            var p = a.data("mmenu");
            if (p && p.close) return p.close(), i.__transitionend(a, function () {
              i.open();
            }, i.conf.transitionDuration), !0;
          }

          return this.open(), !0;
        }

        if (r.$page) return s = r.$page.first().attr("id"), s && e.is('[href="#' + s + '"]') ? (this.close(), !0) : void 0;
      }
    }
  }, e[t].defaults[n] = {
    blockUI: !0,
    moveBackground: !0
  }, e[t].configuration[n] = {
    pageNodetype: "div",
    pageSelector: null,
    noPageSelector: [],
    wrapPageIfNeeded: !0,
    menuInsertMethod: "prependTo",
    menuInsertSelector: "body"
  }, e[t].prototype.open = function () {
    if (this.trigger("open:before"), !this.vars.opened) {
      var e = this;
      this._openSetup(), setTimeout(function () {
        e._openFinish();
      }, this.conf.openingInterval), this.trigger("open:after");
    }
  }, e[t].prototype._openSetup = function () {
    var t = this,
        a = this.opts[n];
    this.closeAllOthers(), r.$page.each(function () {
      e(this).data(i.style, e(this).attr("style") || "");
    }), r.$wndw.trigger(s.resize + "-" + n, [!0]);
    var p = [o.wrapper + "_opened"];
    a.blockUI && p.push(o.wrapper + "_blocking"), "modal" == a.blockUI && p.push(o.wrapper + "_modal"), a.moveBackground && p.push(o.wrapper + "_background"), r.$html.addClass(p.join(" ")), setTimeout(function () {
      t.vars.opened = !0;
    }, this.conf.openingInterval), this.$menu.addClass(o.menu + "_opened");
  }, e[t].prototype._openFinish = function () {
    var e = this;
    this.__transitionend(r.$page.first(), function () {
      e.trigger("open:finish");
    }, this.conf.transitionDuration), this.trigger("open:start"), r.$html.addClass(o.wrapper + "_opening");
  }, e[t].prototype.close = function () {
    if (this.trigger("close:before"), this.vars.opened) {
      var t = this;
      this.__transitionend(r.$page.first(), function () {
        t.$menu.removeClass(o.menu + "_opened");
        var n = [o.wrapper + "_opened", o.wrapper + "_blocking", o.wrapper + "_modal", o.wrapper + "_background"];
        r.$html.removeClass(n.join(" ")), r.$page.each(function () {
          e(this).attr("style", e(this).data(i.style));
        }), t.vars.opened = !1, t.trigger("close:finish");
      }, this.conf.transitionDuration), this.trigger("close:start"), r.$html.removeClass(o.wrapper + "_opening"), this.trigger("close:after");
    }
  }, e[t].prototype.closeAllOthers = function () {
    r.$body.find("." + o.menu + "_offcanvas").not(this.$menu).each(function () {
      var n = e(this).data(t);
      n && n.close && n.close();
    });
  }, e[t].prototype.setPage = function (t) {
    this.trigger("setPage:before", t);
    var i = this,
        s = this.conf[n];
    t && t.length || (t = r.$body.find(s.pageSelector), s.noPageSelector.length && (t = t.not(s.noPageSelector.join(", "))), t.length > 1 && s.wrapPageIfNeeded && (t = t.wrapAll("<" + this.conf[n].pageNodetype + " />").parent())), t.each(function () {
      e(this).attr("id", e(this).attr("id") || i.__getUniqueId());
    }), t.addClass(o.page + " " + o.slideout), r.$page = t, this.trigger("setPage:after", t);
  }, e[t].prototype["_initWindow_" + n] = function () {
    r.$wndw.off(s.keydown + "-" + n).on(s.keydown + "-" + n, function (e) {
      if (r.$html.hasClass(o.wrapper + "_opened") && 9 == e.keyCode) return e.preventDefault(), !1;
    });
    var e = 0;
    r.$wndw.off(s.resize + "-" + n).on(s.resize + "-" + n, function (t, n) {
      if (1 == r.$page.length && (n || r.$html.hasClass(o.wrapper + "_opened"))) {
        var i = r.$wndw.height();
        (n || i != e) && (e = i, r.$page.css("minHeight", i));
      }
    });
  }, e[t].prototype._initBlocker = function () {
    var t = this;
    this.opts[n].blockUI && (r.$blck || (r.$blck = e('<div class="' + o.page + "__blocker " + o.slideout + '" />')), r.$blck.appendTo(r.$body).off(s.touchstart + "-" + n + " " + s.touchmove + "-" + n).on(s.touchstart + "-" + n + " " + s.touchmove + "-" + n, function (e) {
      e.preventDefault(), e.stopPropagation(), r.$blck.trigger(s.mousedown + "-" + n);
    }).off(s.mousedown + "-" + n).on(s.mousedown + "-" + n, function (e) {
      e.preventDefault(), r.$html.hasClass(o.wrapper + "_modal") || (t.closeAllOthers(), t.close());
    }));
  };
  var o, i, s, r;
}(jQuery);
!function (t) {
  var i = "mmenu",
      n = "screenReader";
  t[i].addons[n] = {
    setup: function () {
      var a = this,
          o = this.opts[n],
          h = this.conf[n];
      s = t[i].glbl, "boolean" == typeof o && (o = {
        aria: o,
        text: o
      }), "object" != typeof o && (o = {}), o = this.opts[n] = t.extend(!0, {}, t[i].defaults[n], o), o.aria && (this.bind("initAddons:after", function () {
        this.bind("initMenu:after", function () {
          this.trigger("initMenu:after:sr-aria");
        }), this.bind("initNavbar:after", function () {
          this.trigger("initNavbar:after:sr-aria", arguments[0]);
        }), this.bind("openPanel:start", function () {
          this.trigger("openPanel:start:sr-aria", arguments[0]);
        }), this.bind("close:start", function () {
          this.trigger("close:start:sr-aria");
        }), this.bind("close:finish", function () {
          this.trigger("close:finish:sr-aria");
        }), this.bind("open:start", function () {
          this.trigger("open:start:sr-aria");
        }), this.bind("initOpened:after", function () {
          this.trigger("initOpened:after:sr-aria");
        });
      }), this.bind("updateListview", function () {
        this.$pnls.find("." + e.listview).children().each(function () {
          a.__sr_aria(t(this), "hidden", t(this).is("." + e.hidden));
        });
      }), this.bind("openPanel:start", function (t) {
        var i = this.$menu.find("." + e.panel).not(t).not(t.parents("." + e.panel)),
            n = t.add(t.find("." + e.listitem + "_vertical ." + e.listitem + "_opened").children("." + e.panel));
        this.__sr_aria(i, "hidden", !0), this.__sr_aria(n, "hidden", !1);
      }), this.bind("closePanel", function (t) {
        this.__sr_aria(t, "hidden", !0);
      }), this.bind("initPanels:after", function (i) {
        var n = i.find("." + e.btn).each(function () {
          a.__sr_aria(t(this), "owns", t(this).attr("href").replace("#", ""));
        });

        this.__sr_aria(n, "haspopup", !0);
      }), this.bind("initNavbar:after", function (t) {
        var i = t.children("." + e.navbar);

        this.__sr_aria(i, "hidden", !t.hasClass(e.panel + "_has-navbar"));
      }), o.text && (this.bind("initlistview:after", function (t) {
        var i = t.find("." + e.listview).find("." + e.btn + "_fullwidth").parent().children("span");

        this.__sr_aria(i, "hidden", !0);
      }), "parent" == this.opts.navbar.titleLink && this.bind("initNavbar:after", function (t) {
        var i = t.children("." + e.navbar),
            n = !!i.children("." + e.btn + "_prev").length;

        this.__sr_aria(i.children("." + e.title), "hidden", n);
      }))), o.text && (this.bind("initAddons:after", function () {
        this.bind("setPage:after", function () {
          this.trigger("setPage:after:sr-text", arguments[0]);
        });
      }), this.bind("initNavbar:after", function (n) {
        var r = n.children("." + e.navbar),
            a = r.children("." + e.title).text(),
            s = t[i].i18n(h.text.closeSubmenu);
        a && (s += " (" + a + ")"), r.children("." + e.btn + "_prev").html(this.__sr_text(s));
      }), this.bind("initListview:after", function (n) {
        var s = n.data(r.parent);

        if (s && s.length) {
          var o = s.children("." + e.btn + "_next"),
              d = o.nextAll("span, a").first().text(),
              l = t[i].i18n(h.text[o.parent().is("." + e.listitem + "_vertical") ? "toggleSubmenu" : "openSubmenu"]);
          d && (l += " (" + d + ")"), o.html(a.__sr_text(l));
        }
      }));
    },
    add: function () {
      e = t[i]._c, r = t[i]._d, a = t[i]._e, e.add("sronly");
    },
    clickAnchor: function (t, i) {}
  }, t[i].defaults[n] = {
    aria: !0,
    text: !0
  }, t[i].configuration[n] = {
    text: {
      closeMenu: "Close menu",
      closeSubmenu: "Close submenu",
      openSubmenu: "Open submenu",
      toggleSubmenu: "Toggle submenu"
    }
  }, t[i].prototype.__sr_aria = function (t, i, n) {
    t.prop("aria-" + i, n)[n ? "attr" : "removeAttr"]("aria-" + i, n);
  }, t[i].prototype.__sr_role = function (t, i) {
    t.prop("role", i)[i ? "attr" : "removeAttr"]("role", i);
  }, t[i].prototype.__sr_text = function (t) {
    return '<span class="' + e.sronly + '">' + t + "</span>";
  };
  var e, r, a, s;
}(jQuery);
!function (t) {
  var e = "mmenu",
      n = "counters";
  t[e].addons[n] = {
    setup: function () {
      var s = this,
          d = this.opts[n];
      this.conf[n];

      if (c = t[e].glbl, "boolean" == typeof d && (d = {
        add: d,
        update: d
      }), "object" != typeof d && (d = {}), d = this.opts[n] = t.extend(!0, {}, t[e].defaults[n], d), this.bind("initListview:after", function (t) {
        var e = this.conf.classNames[n].counter;

        this.__refactorClass(t.find("." + e), e, i.counter);
      }), d.add && this.bind("initListview:after", function (e) {
        var n;

        switch (d.addTo) {
          case "panels":
            n = e;
            break;

          default:
            n = e.filter(d.addTo);
        }

        n.each(function () {
          var e = t(this).data(a.parent);
          e && (e.children("." + i.counter).length || e.prepend(t('<em class="' + i.counter + '" />')));
        });
      }), d.update) {
        var r = function (e) {
          e = e || this.$pnls.children("." + i.panel), e.each(function () {
            var e = t(this),
                n = e.data(a.parent);

            if (n) {
              var c = n.children("em." + i.counter);
              c.length && (e = e.children("." + i.listview), e.length && c.html(s.__filterListItems(e.children()).length));
            }
          });
        };

        this.bind("initListview:after", r), this.bind("updateListview", r);
      }
    },
    add: function () {
      i = t[e]._c, a = t[e]._d, s = t[e]._e, i.add("counter");
    },
    clickAnchor: function (t, e) {}
  }, t[e].defaults[n] = {
    add: !1,
    addTo: "panels",
    count: !1
  }, t[e].configuration.classNames[n] = {
    counter: "Counter"
  };
  var i, a, s, c;
}(jQuery);
!function (e) {
  function n(e, n, t) {
    return e < n && (e = n), e > t && (e = t), e;
  }

  function t(t, o, i) {
    var r,
        p,
        d,
        f = this,
        u = {
      events: "panleft panright",
      typeLower: "x",
      typeUpper: "X",
      open_dir: "right",
      close_dir: "left",
      negative: !1
    },
        c = "width",
        l = u.open_dir,
        m = function (e) {
      e <= t.maxStartPos && (g = 1);
    },
        h = function () {
      return e("." + s.slideout);
    },
        g = 0,
        _ = 0,
        v = 0,
        b = this.opts.extensions.all,
        w = "undefined" == typeof b ? "left" : b.indexOf(s.menu + "_position-right") > -1 ? "right" : b.indexOf(s.menu + "_position-top") > -1 ? "top" : b.indexOf(s.menu + "_position-bottom") > -1 ? "bottom" : "left",
        y = "undefined" == typeof b ? "back" : b.indexOf(s.menu + "_position-top") > -1 || b.indexOf(s.menu + "_position-bottom") > -1 || b.indexOf(s.menu + "_position-front") > -1 ? "front" : "back";

    switch (w) {
      case "top":
      case "bottom":
        u.events = "panup pandown", u.typeLower = "y", u.typeUpper = "Y", c = "height";
    }

    switch (w) {
      case "right":
      case "bottom":
        u.negative = !0, m = function (e) {
          e >= i.$wndw[c]() - t.maxStartPos && (g = 1);
        };
    }

    switch (w) {
      case "right":
        u.open_dir = "left", u.close_dir = "right";
        break;

      case "top":
        u.open_dir = "down", u.close_dir = "up";
        break;

      case "bottom":
        u.open_dir = "up", u.close_dir = "down";
    }

    switch (y) {
      case "front":
        h = function () {
          return f.$menu;
        };

    }

    var x,
        O = this.__valueOrFn(this.$menu, t.node, i.$page);

    "string" == typeof O && (O = e(O));
    var $ = new Hammer(O[0], this.opts[a].vendors.hammer);
    $.on("panstart", function (e) {
      m(e.center[u.typeLower]), x = h(), l = u.open_dir;
    }), $.on(u.events + " panend", function (e) {
      g > 0 && e.preventDefault();
    }), $.on(u.events, function (e) {
      if (r = e["delta" + u.typeUpper], u.negative && (r = -r), r != _ && (l = r >= _ ? u.open_dir : u.close_dir), _ = r, _ > t.threshold && 1 == g) {
        if (i.$html.hasClass(s.wrapper + "_opened")) return;
        g = 2, f._openSetup(), f.trigger("open:start"), i.$html.addClass(s.dragging), v = n(i.$wndw[c]() * o[c].perc, o[c].min, o[c].max);
      }

      2 == g && (p = n(_, 10, v) - ("front" == y ? v : 0), u.negative && (p = -p), d = "translate" + u.typeUpper + "(" + p + "px )", x.css({
        "-webkit-transform": "-webkit-" + d,
        transform: d
      }));
    }), $.on("panend", function (e) {
      2 == g && (i.$html.removeClass(s.dragging), x.css("transform", ""), f[l == u.open_dir ? "_openFinish" : "close"]()), g = 0;
    });
  }

  function o(e, n, t, o) {
    var i = this,
        p = e.data(r.parent);

    if (p) {
      p = p.closest("." + s.panel);
      var d = new Hammer(e[0], i.opts[a].vendors.hammer),
          f = null;
      d.on("panright", function (e) {
        f || (i.openPanel(p), f = setTimeout(function () {
          clearTimeout(f), f = null;
        }, i.conf.openingInterval + i.conf.transitionDuration));
      });
    }
  }

  var i = "mmenu",
      a = "drag";
  e[i].addons[a] = {
    setup: function () {
      if (this.opts.offCanvas) {
        var n = this.opts[a],
            s = this.conf[a];
        d = e[i].glbl, "boolean" == typeof n && (n = {
          menu: n,
          panels: n
        }), "object" != typeof n && (n = {}), "boolean" == typeof n.menu && (n.menu = {
          open: n.menu
        }), "object" != typeof n.menu && (n.menu = {}), "boolean" == typeof n.panels && (n.panels = {
          close: n.panels
        }), "object" != typeof n.panels && (n.panels = {}), n = this.opts[a] = e.extend(!0, {}, e[i].defaults[a], n), n.menu.open && this.bind("setPage:after", function () {
          t.call(this, n.menu, s.menu, d);
        }), n.panels.close && this.bind("initPanel:after", function (e) {
          o.call(this, e, n.panels, s.panels, d);
        });
      }
    },
    add: function () {
      return "function" != typeof Hammer || Hammer.VERSION < 2 ? (e[i].addons[a].add = function () {}, void (e[i].addons[a].setup = function () {})) : (s = e[i]._c, r = e[i]._d, p = e[i]._e, void s.add("dragging"));
    },
    clickAnchor: function (e, n) {}
  }, e[i].defaults[a] = {
    menu: {
      open: !1,
      maxStartPos: 100,
      threshold: 50
    },
    panels: {
      close: !1
    },
    vendors: {
      hammer: {}
    }
  }, e[i].configuration[a] = {
    menu: {
      width: {
        perc: .8,
        min: 140,
        max: 440
      },
      height: {
        perc: .8,
        min: 140,
        max: 880
      }
    },
    panels: {}
  };
  var s, r, p, d;
}(jQuery);
!function (a) {
  var t = "mmenu",
      n = "iconbar";
  a[t].addons[n] = {
    setup: function () {
      function s(a) {
        f.removeClass(e.iconbar + "__tab_selected");
        var t = f.filter('[href="#' + a.attr("id") + '"]');
        if (t.length) t.addClass(e.iconbar + "__tab_selected");else {
          var n = a.data(i.parent);
          n && n.length && s(n.closest("." + e.panel));
        }
      }

      var d = this,
          c = this.opts[n];
      this.conf[n];

      if (r = a[t].glbl, c instanceof Array && (c = {
        add: !0,
        top: c
      }), c.add) {
        var l = null;

        if (a.each(["top", "bottom"], function (t, n) {
          var i = c[n];
          i instanceof Array || (i = [i]);

          for (var o = a('<div class="' + e.iconbar + "__" + n + '" />'), r = 0, s = i.length; r < s; r++) o.append(i[r]);

          o.children().length && (l || (l = a('<div class="' + e.iconbar + '" />')), l.append(o));
        }), l && (this.bind("initMenu:after", function () {
          this.$menu.addClass(e.menu + "_iconbar-" + c.size).prepend(l);
        }), "tabs" == c.type)) {
          l.addClass(e.iconbar + "_tabs");
          var f = l.find("a");
          f.on(o.click + "-" + n, function (t) {
            var n = a(this);
            if (n.hasClass(e.iconbar + "__tab_selected")) return void t.stopImmediatePropagation();

            try {
              var i = a(n.attr("href"));
              i.hasClass(e.panel) && (t.preventDefault(), t.stopImmediatePropagation(), d.__openPanelWoAnimation(i));
            } catch (o) {}
          }), this.bind("openPanel:start", s);
        }
      }
    },
    add: function () {
      e = a[t]._c, i = a[t]._d, o = a[t]._e, e.add(n);
    },
    clickAnchor: function (a, t) {}
  }, a[t].defaults[n] = {
    add: !1,
    size: 40,
    top: [],
    bottom: []
  }, a[t].configuration[n] = {};
  var e, i, o, r;
}(jQuery);
!function (e) {
  var i = "mmenu",
      n = "iconPanels";
  e[i].addons[n] = {
    setup: function () {
      var a = this,
          l = this.opts[n],
          d = (this.conf[n], !1);

      if (s = e[i].glbl, "boolean" == typeof l && (l = {
        add: l
      }), "number" != typeof l && "string" != typeof l || (l = {
        add: !0,
        visible: l
      }), "object" != typeof l && (l = {}), "first" == l.visible && (d = !0, l.visible = 1), l = this.opts[n] = e.extend(!0, {}, e[i].defaults[n], l), l.visible = Math.min(3, Math.max(1, l.visible)), l.visible++, l.add) {
        for (var r = "", o = 0; o <= l.visible; o++) r += " " + t.panel + "_iconpanel-" + o;

        r.length && (r = r.slice(1));

        var c = function (i) {
          if (!i.parent("." + t.listitem + "_vertical").length) {
            var n = a.$pnls.children("." + t.panel).removeClass(r);
            d && n.removeClass(t.panel + "_iconpanel-first").first().addClass(t.panel + "_iconpanel-first"), n.filter("." + t.panel + "_opened-parent").removeClass(t.hidden).not(function () {
              return e(this).parent("." + t.listitem + "_vertical").length;
            }).add(i).slice(-l.visible).each(function (i) {
              e(this).addClass(t.panel + "_iconpanel-" + i);
            });
          }
        };

        this.bind("initMenu:after", function () {
          var e = [t.menu + "_iconpanel-" + l.size];
          l.hideNavbar && e.push(t.menu + "_hidenavbar"), l.hideDivider && e.push(t.menu + "_hidedivider"), this.$menu.addClass(e.join(" "));
        }), this.bind("openPanel:start", c), this.bind("initPanels:after", function (e) {
          c.call(a, a.$pnls.children("." + t.panel + "_opened"));
        }), this.bind("initListview:after", function (e) {
          !l.blockPanel || e.parent("." + t.listitem + "_vertical").length || e.children("." + t.panel + "__blocker").length || e.prepend('<a href="#' + e.closest("." + t.panel).attr("id") + '" class="' + t.panel + '__blocker" />');
        });
      }
    },
    add: function () {
      t = e[i]._c, a = e[i]._d, l = e[i]._e;
    },
    clickAnchor: function (e, i) {}
  }, e[i].defaults[n] = {
    add: !1,
    blockPanel: !0,
    hideDivider: !1,
    hideNavbar: !0,
    size: 40,
    visible: 3
  };
  var t, a, l, s;
}(jQuery);
!function (n) {
  var t = "mmenu",
      a = "navbars";
  n[t].addons[a] = {
    setup: function () {
      var o = this,
          r = this.opts[a],
          i = this.conf[a];

      if (s = n[t].glbl, "undefined" != typeof r) {
        r instanceof Array || (r = [r]);
        var c = {},
            d = {};
        r.length && (n.each(r, function (s) {
          var f = r[s];
          "boolean" == typeof f && f && (f = {}), "object" != typeof f && (f = {}), "undefined" == typeof f.content && (f.content = ["prev", "title"]), f.content instanceof Array || (f.content = [f.content]), f = n.extend(!0, {}, o.opts.navbar, f);
          var l = n('<div class="' + e.navbar + '" />'),
              u = f.height;
          "number" != typeof u ? u = 1 : (u = Math.min(4, Math.max(1, u)), u > 1 && l.addClass(e.navbar + "_size-" + u));
          var v = f.position;

          switch (v) {
            case "bottom":
              break;

            default:
              v = "top";
          }

          c[v] || (c[v] = 0), c[v] += u, d[v] || (d[v] = n('<div class="' + e.navbars + "_" + v + '" />')), d[v].append(l);

          for (var p = 0, b = f.content.length; p < b; p++) {
            var h = n[t].addons[a][f.content[p]] || null;
            h ? h.call(o, l, f, i) : (h = f.content[p], h instanceof n || (h = n(f.content[p])), l.append(h));
          }

          var m = n[t].addons[a][f.type] || null;
          m && m.call(o, l, f, i), l.children("." + e.btn).length && l.addClass(e.navbar + "_has-btns");
        }), this.bind("initMenu:after", function () {
          for (var n in c) this.$menu.addClass(e.menu + "_navbar_" + n + "-" + c[n]), this.$menu["bottom" == n ? "append" : "prepend"](d[n]);
        }));
      }
    },
    add: function () {
      e = n[t]._c, o = n[t]._d, r = n[t]._e, e.add(a);
    },
    clickAnchor: function (n, t) {}
  }, n[t].configuration[a] = {
    breadcrumbs: {
      separator: "/",
      removeFirst: !1
    }
  }, n[t].configuration.classNames[a] = {};
  var e, o, r, s;
}(jQuery);
!function (e) {
  function n(e, n) {
    if (n) for (var s in n) e.attr(s, n[s]);
  }

  function s(e) {
    switch (e) {
      case 9:
      case 16:
      case 17:
      case 18:
      case 37:
      case 38:
      case 39:
      case 40:
        return !0;
    }

    return !1;
  }

  var a = "mmenu",
      t = "searchfield";
  e[a].addons[t] = {
    setup: function () {
      var n = this,
          s = this.opts[t],
          i = this.conf[t];
      r = e[a].glbl, "boolean" == typeof s && (s = {
        add: s
      }), "object" != typeof s && (s = {}), "boolean" == typeof s.panel && (s.panel = {
        add: s.panel
      }), "object" != typeof s.panel && (s.panel = {}), s.add && ("panel" == s.addTo && (s.panel.add = !0), s.panel.add && (s.showSubPanels = !1, s.panel.splash && (s.cancel = !0)), s = this.opts[t] = e.extend(!0, {}, e[a].defaults[t], s), i = this.conf[t] = e.extend(!0, {}, e[a].configuration[t], i), this.bind("close:start", function () {
        this.$menu.find("." + l.searchfield).children("input").blur();
      }), this.bind("initPanels:after", function (a) {
        var t = e();
        s.panel.add && (t = this._initSearchPanel(a));
        var l;

        switch (s.addTo) {
          case "panels":
            l = a;
            break;

          case "panel":
            l = t;
            break;

          default:
            l = this.$menu.find(s.addTo);
        }

        if (l.each(function () {
          var a = n._initSearchfield(e(this));

          s.search && a.length && n._initSearching(a);
        }), s.noResults) {
          var i = s.panel.add ? t : a;
          i.each(function () {
            n._initNoResultsMsg(e(this));
          });
        }
      }));
    },
    add: function () {
      l = e[a]._c, i = e[a]._d, d = e[a]._e, l.add("searchfield"), i.add("searchfield"), d.add("input focus blur");
    },
    clickAnchor: function (e, n) {
      if (e.hasClass(l.searchfield + "__btn")) {
        if (e.hasClass(l.btn + "_clear")) {
          var s = e.closest("." + l.searchfield).find("input");
          return s.val(""), this.search(s), !0;
        }

        if (e.hasClass(l.btn + "_next")) return e.closest("." + l.searchfield).submit(), !0;
      }
    }
  }, e[a].defaults[t] = {
    add: !1,
    addTo: "panels",
    noResults: "No results found.",
    placeholder: "Search",
    panel: {
      add: !1,
      dividers: !0,
      fx: "none",
      id: null,
      splash: null,
      title: "Search"
    },
    search: !0,
    showTextItems: !1,
    showSubPanels: !0
  }, e[a].configuration[t] = {
    clear: !1,
    form: !1,
    input: !1,
    submit: !1
  };
  var l, i, d, r;
  e[a].prototype._initSearchPanel = function (n) {
    var s = this.opts[t];
    this.conf[t];
    if (this.$pnls.children("." + l.panel + "_search").length) return e();
    var a = e('<div class="' + l.panel + '_search " />').append("<ul />").appendTo(this.$pnls);

    switch (s.panel.id && a.attr("id", s.panel.id), s.panel.title && a.attr("data-mm-title", s.panel.title), s.panel.fx) {
      case !1:
        break;

      case "none":
        a.addClass(l.panel + "_noanimation");
        break;

      default:
        a.addClass(l.panel + "_fx-" + s.panel.fx);
    }

    return s.panel.splash && a.append('<div class="' + l.panel + '__searchsplash">' + s.panel.splash + "</div>"), this._initPanels(a), a;
  }, e[a].prototype._initSearchfield = function (s) {
    var i = this.opts[t],
        d = this.conf[t];
    if (s.parent("." + l.listitem + "_vertical").length) return e();
    if (s.find("." + l.searchfield).length) return e();
    var r = e("<" + (d.form ? "form" : "div") + ' class="' + l.searchfield + '" />'),
        h = e('<div class="' + l.searchfield + '__input" />'),
        c = e('<input placeholder="' + e[a].i18n(i.placeholder) + '" type="text" autocomplete="off" />');
    return h.append(c).appendTo(r), s.hasClass(l.searchfield) ? s.replaceWith(r) : (s.prepend(r), s.hasClass(l.panel) && s.addClass(l.panel + "_has-searchfield")), n(c, d.input), d.clear && e('<a class="' + l.btn + " " + l.btn + "_clear " + l.searchfield + '__btn" href="#" />').appendTo(h), n(r, d.form), d.form && d.submit && !d.clear && e('<a class="' + l.btn + " " + l.btn + "_next " + l.searchfield + '__btn" href="#" />').appendTo(h), i.cancel && e('<a href="#" class="' + l.searchfield + '__cancel">' + e[a].i18n("cancel") + "</a>").appendTo(r), r;
  }, e[a].prototype._initSearching = function (n) {
    var a = this,
        r = this.opts[t],
        h = (this.conf[t], {});
    n.closest("." + l.panel + "_search").length ? (h.$pnls = this.$pnls.find("." + l.panel), h.$nrsp = n.closest("." + l.panel)) : n.closest("." + l.panel).length ? (h.$pnls = n.closest("." + l.panel), h.$nrsp = h.$pnls) : (h.$pnls = this.$pnls.find("." + l.panel), h.$nrsp = this.$menu), r.panel.add && (h.$pnls = h.$pnls.not("." + l.panel + "_search"));
    var c = n.find("input"),
        p = n.find("." + l.searchfield + "__cancel"),
        o = this.$pnls.children("." + l.panel + "_search"),
        f = h.$pnls.find("." + l.listitem);
    h.$itms = f.not("." + l.listitem + "_divider"), h.$dvdr = f.filter("." + l.listitem + "_divider"), r.panel.add && r.panel.splash && c.off(d.focus + "-" + t + "-splash").on(d.focus + "-" + t + "-splash", function (e) {
      a.openPanel(o);
    }), r.cancel && (c.off(d.focus + "-" + t + "-cancel").on(d.focus + "-" + t + "-cancel", function (e) {
      p.addClass(l.searchfield + "__cancel-active");
    }), p.off(d.click + "-" + t + "-splash").on(d.click + "-" + t + "-splash", function (n) {
      n.preventDefault(), e(this).removeClass(l.searchfield + "__cancel-active"), o.hasClass(l.panel + "_opened") && a.openPanel(a.$pnls.children("." + l.panel + "_opened-parent").last());
    })), r.panel.add && "panel" == r.addTo && this.bind("openPanel:finish", function (e) {
      e[0] === o[0] && c.focus();
    }), c.data(i.searchfield, h).off(d.input + "-" + t).on(d.input + "-" + t, function (e) {
      s(e.keyCode) || a.search(c);
    }), this.search(c);
  }, e[a].prototype._initNoResultsMsg = function (n) {
    var s = this.opts[t];
    this.conf[t];

    if (n.closest("." + l.panel).length || (n = this.$pnls.children("." + l.panel).first()), !n.children("." + l.panel + "__noresultsmsg").length) {
      var i = n.children("." + l.listview).first(),
          d = e('<div class="' + l.panel + "__noresultsmsg " + l.hidden + '" />').append(e[a].i18n(s.noResults));
      i.length ? d.insertAfter(i) : d.prependTo(n);
    }
  }, e[a].prototype.search = function (n, s) {
    var a = this,
        d = this.opts[t];
    this.conf[t];
    n = n || this.$menu.find("." + l.searchfield).chidren("input").first(), s = s || n.val(), s = s.toLowerCase().trim();
    var r = "a",
        h = "a, span",
        c = n.data(i.searchfield),
        p = n.closest("." + l.searchfield),
        o = p.find("." + l.btn),
        f = this.$pnls.children("." + l.panel + "_search"),
        u = c.$pnls,
        _ = c.$itms,
        v = c.$dvdr,
        m = c.$nrsp;

    if (_.removeClass(l.listitem + "_nosubitems").find("." + l.btn + "_fullwidth-search").removeClass(l.btn + "_fullwidth-search " + l.btn + "_fullwidth"), f.children("." + l.listview).empty(), u.scrollTop(0), s.length) {
      if (_.add(v).addClass(l.hidden), _.each(function () {
        var n = e(this),
            a = r;
        (d.showTextItems || d.showSubPanels && n.find("." + l.btn + "_next")) && (a = h), n.children(a).not("." + l.btn + "_next").text().toLowerCase().indexOf(s) > -1 && n.removeClass(l.hidden);
      }), d.panel.add) {
        var b = e();
        u.each(function () {
          var n = a.__filterListItems(e(this).find("." + l.listitem)).clone(!0);

          n.length && (d.panel.dividers && (b = b.add('<li class="' + l.listitem + " " + l.listitem + '_divider">' + e(this).find("." + l.navbar + "__title").text() + "</li>")), b = b.add(n));
        }), b.find("." + l.mm("toggle")).remove().end().find("." + l.mm("check")).remove().end().find("." + l.btn).remove(), f.children("." + l.listview).append(b), this.openPanel(f);
      } else d.showSubPanels && u.each(function (n) {
        var s = e(this);

        a.__filterListItems(s.find("." + l.listitem)).each(function () {
          var n = e(this),
              s = n.data(i.child);
          s && s.find("." + l.listview).children().removeClass(l.hidden);
        });
      }), e(u.get().reverse()).each(function (s) {
        var t = e(this),
            d = t.data(i.parent);
        d && (a.__filterListItems(t.find("." + l.listitem)).length ? d.hasClass(l.hidden) && d.removeClass(l.hidden).children("." + l.btn + "_next").not("." + l.btn + "_fullwidth").addClass(l.btn + "_fullwidth").addClass(l.btn + "_fullwidth-search") : n.closest("." + l.panel).length || ((t.hasClass(l.panel + "_opened") || t.hasClass(l.panel + "_opened-parent")) && setTimeout(function () {
          a.openPanel(d.closest("." + l.panel));
        }, (s + 1) * (1.5 * a.conf.openingInterval)), d.addClass(l.listitem + "_nosubitems")));
      }), this.__filterListItems(u.find("." + l.listitem)).each(function () {
        e(this).prevAll("." + l.listitem + "_divider").first().removeClass(l.hidden);
      });

      o.removeClass(l.hidden), m.find("." + l.panel + "__noresultsmsg")[_.not("." + l.hidden).length ? "addClass" : "removeClass"](l.hidden), d.panel.add && (d.panel.splash && f.find("." + l.panel + "__searchsplash").addClass(l.hidden), _.add(v).removeClass(l.hidden));
    } else _.add(v).removeClass(l.hidden), o.addClass(l.hidden), m.find("." + l.panel + "__noresultsmsg").addClass(l.hidden), d.panel.add && (d.panel.splash ? f.find("." + l.panel + "__searchsplash").removeClass(l.hidden) : n.closest("." + l.panel + "_search").length || this.openPanel(this.$pnls.children("." + l.panel + "_opened-parent").last()));

    this.trigger("updateListview");
  };
}(jQuery);
!function (e) {
  var t = "mmenu",
      n = "setSelected";
  e[t].addons[n] = {
    setup: function () {
      var a = this,
          r = this.opts[n];
      this.conf[n];

      if (l = e[t].glbl, "boolean" == typeof r && (r = {
        hover: r,
        parent: r
      }), "object" != typeof r && (r = {}), r = this.opts[n] = e.extend(!0, {}, e[t].defaults[n], r), "detect" == r.current) {
        var d = function (e) {
          e = e.split("?")[0].split("#")[0];
          var t = a.$menu.find('a[href="' + e + '"], a[href="' + e + '/"]');
          t.length ? a.setSelected(t.parent(), !0) : (e = e.split("/").slice(0, -1), e.length && d(e.join("/")));
        };

        this.bind("initMenu:after", function () {
          d(window.location.href);
        });
      } else r.current || this.bind("initListview:after", function (e) {
        e.find("." + i.listview).children("." + i.listitem + "_selected").removeClass(i.listitem + "_selected");
      });

      r.hover && this.bind("initMenu:after", function () {
        this.$menu.addClass(i.menu + "_selected-hover");
      }), r.parent && (this.bind("openPanel:finish", function (e) {
        this.$pnls.find("." + i.listview).find("." + i.listitem + "_selected-parent").removeClass(i.listitem + "_selected-parent");

        for (var t = e.data(s.parent); t;) t.not("." + i.listitem + "_vertical").addClass(i.listitem + "_selected-parent"), t = t.closest("." + i.panel).data(s.parent);
      }), this.bind("initMenu:after", function () {
        this.$menu.addClass(i.menu + "_selected-parent");
      }));
    },
    add: function () {
      i = e[t]._c, s = e[t]._d, a = e[t]._e;
    },
    clickAnchor: function (e, t) {}
  }, e[t].defaults[n] = {
    current: !0,
    hover: !1,
    parent: !1
  };
  var i, s, a, l;
}(jQuery);
!function (a) {
  var n = "mmenu",
      r = "navbars",
      e = "prev";
  a[n].addons[r][e] = function (e, t) {
    var i = a[n]._c,
        s = a('<a class="' + i.btn + " " + i.btn + "_prev " + i.navbar + '__btn" href="#" />').appendTo(e);
    this.bind("initNavbar:after", function (a) {
      a.removeClass(i.panel + "_has-navbar");
    });
    var h, l, d;
    this.bind("openPanel:start", function (a) {
      a.parent("." + i.listitem + "_vertical").length || (h = a.find("." + this.conf.classNames[r].panelPrev), h.length || (h = a.children("." + i.navbar).children("." + i.btn + "_prev")), l = h.attr("href"), d = h.html(), l ? s.attr("href", l) : s.removeAttr("href"), s[l || d ? "removeClass" : "addClass"](i.hidden), s.html(d));
    }), this.bind("initNavbar:after:sr-aria", function (a) {
      var n = a.children("." + i.navbar);

      this.__sr_aria(n, "hidden", !0);
    }), this.bind("openPanel:start:sr-aria", function (a) {
      this.__sr_aria(s, "hidden", s.hasClass(i.hidden)), this.__sr_aria(s, "owns", (s.attr("href") || "").slice(1));
    });
  }, a[n].configuration.classNames[r].panelPrev = "Prev";
}(jQuery);
!function (s) {
  var e = "mmenu",
      a = "navbars",
      d = "searchfield";

  s[e].addons[a][d] = function (a, d) {
    var i = s[e]._c,
        t = s('<div class="' + i.searchfield + '" />').appendTo(a);
    "object" != typeof this.opts.searchfield && (this.opts.searchfield = {}), this.opts.searchfield.add = !0, this.opts.searchfield.addTo = t;
  };
}(jQuery);
!function (a) {
  var t = "mmenu",
      e = "navbars",
      n = "tabs";

  a[t].addons[e][n] = function (n, s, r) {
    function i(a) {
      c.removeClass(d.navbar + "__tab_selected");
      var t = c.filter('[href="#' + a.attr("id") + '"]');
      if (t.length) t.addClass(d.navbar + "__tab_selected");else {
        var e = a.data(l.parent);
        e && e.length && i(e.closest("." + d.panel));
      }
    }

    var d = a[t]._c,
        l = a[t]._d,
        o = a[t]._e,
        _ = this,
        c = n.children("a");

    n.addClass(d.navbar + "_tabs").parent().addClass(d.navbars + "_has-tabs"), c.on(o.click + "-" + e, function (t) {
      t.preventDefault();
      var e = a(this);
      if (e.hasClass(d.navbar + "__tab_selected")) return void t.stopImmediatePropagation();

      try {
        _.__openPanelWoAnimation(a(e.attr("href"))), t.stopImmediatePropagation();
      } catch (n) {}
    }), this.bind("openPanel:start", i);
  };
}(jQuery);

class MMenu {
  constructor() {
    this._initMmenu();
  }

  _initMmenu() {
    if ($('body').hasClass('admin-bar')) {
      $('html').addClass('html-mmenu');
    }

    var _PLUGIN_ = 'mmenu';

    $[_PLUGIN_].i18n({
      'cancel': puca_settings.cancel
    });

    var mmenu = $("#tbay-mobile-smartmenu");
    if ($(mmenu).length === 0) return;
    var themes = mmenu.data('themes');
    var enablesearch = Boolean(mmenu.data("enablesearch"));
    var textsearch = enablesearch ? mmenu.data('textsearch') : '';
    var searchnoresults = enablesearch ? mmenu.data('searchnoresults') : '';
    var searchsplash = enablesearch ? mmenu.data('searchsplash') : '';
    var menu_title = mmenu.data('title');
    var searchcounters = Boolean(mmenu.data('counters'));
    var enabletabs = Boolean(mmenu.data("enabletabs"));
    var tabone = enabletabs ? mmenu.data('tabone') : '';
    var taboneicon = enabletabs ? mmenu.data('taboneicon') : '';
    var tabsecond = enabletabs ? mmenu.data('tabsecond') : '';
    var tabsecondicon = enabletabs ? mmenu.data('tabsecondicon') : '';
    var enablesocial = Boolean(mmenu.data("enablesocial"));
    var socialjsons = '';
    var enableeffects = Boolean(mmenu.data("enableeffects"));
    var effectspanels = enableeffects ? mmenu.data('effectspanels') : '';
    var effectslistitems = enableeffects ? mmenu.data('effectslistitems') : '';
    var mmenuOptions = {
      offCanvas: true,
      counters: searchcounters,
      extensions: [themes, effectspanels, effectslistitems]
    };
    var mmenuOptionsAddition = {
      navbars: [],
      searchfield: {},
      navbar: {}
    };

    if (menu_title.length === 0) {
      mmenuOptionsAddition.navbar = {
        add: false
      };
    } else {
      mmenuOptionsAddition.navbar = {
        title: menu_title
      };
    }

    if (enablesearch) {
      mmenuOptionsAddition.navbars.push({
        position: ['top'],
        content: ['searchfield']
      });
      mmenuOptionsAddition.searchfield = {
        placeholder: textsearch,
        noResults: searchnoresults,
        panel: {
          add: true,
          splash: searchsplash,
          title: puca_settings.search
        },
        showTextItems: true,
        clear: true
      };
    }

    if (enabletabs) {
      mmenuOptionsAddition.navbars.push({
        type: 'tabs',
        content: ['<a href="#main-mobile-menu-mmenu"><i class="' + taboneicon + '"></i> <span>' + tabone + '</span></a>', '<a href="#mobile-menu-second-mmenu"><i class="' + tabsecondicon + '"></i> <span>' + tabsecond + '</span></a>']
      });
    }

    if (enablesocial) {
      socialjsons = JSON.parse(mmenu.data("socialjsons").replace(/'/g, '"'));
      var content = $.map(socialjsons, function (value) {
        return `<a class="mmenu-icon" href="${value.url}" target="_blank"><i class="${value.icon}"></i></a>`;
      });
      mmenuOptionsAddition.navbars.push({
        position: 'bottom',
        content: content
      });
    }

    mmenuOptions = _.extend(mmenuOptionsAddition, mmenuOptions);
    var mmenuConfigurations = {
      offCanvas: {
        pageSelector: "#tbay-main-content"
      },
      searchfield: {
        clear: true
      }
    };
    $("#tbay-mobile-menu-navbar").mmenu(mmenuOptions, mmenuConfigurations);
  }

}

jQuery(document).ready(function ($) {
  new MMenu();
});
