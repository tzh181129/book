layui.define(["jquery", "utils", "axios", "apiconfig"],
function(i) {
    var e = layui.jquery,
    n = layui.utils,
    t = layui.lodash,
    a = layui.axios,
    r = n.localStorage,
    config = layui.apiconfig,
    o = function() {
        this.config = {
            elem: void 0,
            onClicked: void 0,
            dynamicRender: !1,
            data: [],
            remote: {
                url: void 0,
                method: "get"
            },
            cached: !1,
            cacheKey: "TPLAYMENU",
            isJump: !0
        },
        this.version = "1.0.0"
    },
    u = ".tplay-menu",
    s = ".tplay-menu-item";
    o.prototype.set = function(i) {
        return e.extend(!0, this.config, i),
        this
    },
    o.prototype.render = function() {
        var i = this,
        e = i.config;
        if (e.dynamicRender) if (e.data.length > 0) r.setItem(e.cacheKey, t),
        l.renderHTML(e.elem, e.data,
        function() {
            i.bind()
        });
        else {
            var n = !1;
            if (e.cached) {
                var t = r.getItem(e.cacheKey);
                null !== t && void 0 !== t && (n = !0, l.renderHTML(e.elem, t,
                function() {
                    i.bind()
                }))
            }
            n || l.loadData(e.remote,
            function(n) {
                r.setItem(e.cacheKey, n),
                l.renderHTML(e.elem, n,
                function() {
                    i.bind()
                })
            })
        } else i.bind();
        return i
    },
    o.prototype.bind = function() {
        var i = this.config;
        return e(u).find(s).each(function() {
            var t = e(this),
            a = t.children("a"),
            r = t.find("ul.tplay-menu-child").length > 0;
            r && a.addClass("child");
            var o = t.attr("lay-id");
            "" !== o && void 0 !== o || (o = n.randomCode(), t.attr("lay-id", o)),
            a.off("click").on("click",
            function(l) {
                e.post(config.domain+"/admin/index/currentMenuId", {data:t.attr("mid")}, function(res){});
                console.log(t.attr("mid"))
                if (
                    layui.stope(l), 
                    r ? t.hasClass("layui-show") ? t.removeClass("layui-show") : (e(u).find(s).removeClass("layui-show"),t.addClass("layui-show")) : (e(u).find(s).removeClass("layui-this"), 
                        t.addClass("layui-this")), 
                    n.isFunction(i.onClicked) && i.onClicked({
                    elem: t,
                    hasChild: r,
                    data: {
                        href: a.attr("href"),
                        layid: o
                    }
                }), !i.isJump) return ! 1
            })
        }),
        this
    },
    o.prototype.removeCache = function(i) {
        var e = this.config;
        i = i || e.cacheKey,
        n.localStorage.removeItem(i)
    };
    var l = {
        renderHTML: function(i, t, a) {
            var r = ['<ul class="tplay-menu">'];
            if (this.recursion(r, t, 0), r.length > 0) {
                r.push("</ul>");
                var o = e(i);
                if (0 === o.length) return void n.error("Menu config error:请配置elem参数.");
                o.html(r.join("")),
                n.isFunction(a) && a()
            }
        },
        recursion: function(i, e, n) {
            var a = this,
            r = [];
            t.forEach(e,
            function(i, e) {
                i.pid === n && r.push(i)
            }),
            r.length > 0 && t.forEach(r,
            function(e) {
                if (!e.pid) 
                {
                    var n = e.open ? "layui-show": "";
                    if (!e.children) {var n = e.open ? "layui-show layui-this": "";}
                }
                else
                {
                    var n = e.open ? "layui-this": "";
                }
                i.push('<li class="tplay-menu-item ' + n + '" mid="' + e.id + '">');
                var r = t.isEmpty(e.path) ? "javascript:;": e.path;
                // var mid = e.id;
                e.blank ? i.push('<a href="' + r + '" target="_blank">') : i.push('<a href="' + r + '">'),
                // i.push('<i class="layui-icon">' + e.icon + "</i> "),
                i.push('<i class="layui-icon ' + e.icon +  '"></i>&nbsp; '),
                i.push("<span>" + e.title + "</span>"),
                i.push("</a>");
                var o = e.children;
                void 0 !== o && null !== o && o.length > 0 && (i.push('<ul class="tplay-menu-child layui-anim layui-anim-upbit">'), a.recursion(i, o, e.id), i.push("</ul>")),
                i.push("</li>")
            })
        },
        loadData: function(i, e) {
            a(i).then(function(i) {
                if (500 === i.status) throw new Error(i.statusText);
                return i.data
            }).then(function(i) {
                e(i)
            }).
            catch(function(i) {
                n.error(i)
            })
        }
    };
    i("menu", new o)
});