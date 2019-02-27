var _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ?
function(i) {
    return typeof i
}: function(i) {
    return i && "function" == typeof Symbol && i.constructor === Symbol && i !== Symbol.prototype ? "symbol": typeof i
};
function _defineProperty(i, e, t) {
    return e in i ? Object.defineProperty(i, e, {
        value: t,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : i[e] = t,
    i
}
layui.define(["layer", "laytpl", "utils", "lodash"],
function(i) {
    var e = layui.jquery,
    t = (layui.layer, layui.laytpl),
    n = layui.lodash,
    a = layui.utils,
    d = e("body"),
    o = function() {
        this.version = "1.0.0"
    },
    r = ['<div class="tplay-sidebar" style="{{d.direction}}:-{{d.width}};width:{{d.width}};" tplay-sidebar="{{d.id}}">', '<div class="tplay-sidebar-body">', '  <div class="layui-card">', '    <div class="layui-card-header">', '      <span class="nowrap" title="{{d.title}}">{{d.title}}</span>', '      <div class="tplay-sidebar-reload" title="刷新">', '        <i class="layui-icon">&#x1002;</i>', "      </div>", '      <div class="tplay-sidebar-close" title="关闭">', '        <i class="layui-icon">&#x1006;</i>', "      </div>", "    </div>", '    <div class="layui-card-body">', "      {{d.content}}", "    </div>", "  </div>", "</div>", "</div>"].join(""),
    l = ['<div class="tplay-sidebar-loading layui-anim layui-anim-fadein">', "    <div>", '        <i class="layui-icon layui-anim layui-anim-rotate layui-anim-loop">&#xe63e;</i>', "    </div>", "</div>"];
    o.prototype.defaults = {
        elem: void 0,
        content: "",
        shade: !1,
        shadeClose: !0,
        title: "未命名",
        direction: "right",
        dynamicRender: !1,
        url: void 0,
        width: "280px",
        done: void 0
    },
    o.prototype.render = function(i) {
        var t = this,
        d = n.cloneDeep(t.defaults);
        e.extend(!0, d, i);
        var o = d;
        if (!a.oneOf(o.direction, ["left", "right"])) return a.error('Sidebar error: [direction] property error,Only "left" or "right" .'),
        t;
        var r = {
            title: o.title,
            id: a.randomCode(),
            content: o.content,
            direction: o.direction,
            width: o.width
        };
        if (o.dynamicRender) {
            var l = o.url + "?version=" + (new Date).getTime();
            a.tplLoader(l,
            function(i) {
                r.content = i,
                c.renderHTML(o, r)
            },
            function(i) {
                r.content = i,
                c.renderHTML(o, r)
            })
        } else c.renderHTML(o, r);
        return t
    };
    var c = {
        renderHTML: function(i, n) {
            var o = e(i.elem);
            void 0 === o.attr("tplay-sidebar-target") && t(r).render(n,
            function(t) {
                i.shade && (t = t + '<div class="tplay-shade" tplay-shade="' + n.id + '"></div>'),
                d.append(t),
                "function" == typeof i.done && i.done();
                var r = e('div[tplay-sidebar="' + n.id + '"]'),
                l = e('div[tplay-shade="' + n.id + '"]');
                o.attr("data-toggle", "off"),
                o.attr("tplay-sidebar-target", "true"),
                o.on("click",
                function() {
                    switch (e(this).data("toggle")) {
                    case "on":
                        r.animate(_defineProperty({},
                        i.direction, "-" + i.width)),
                        l.hide(),
                        e(this).data("toggle", "off");
                        break;
                    case "off":
                        r.animate(_defineProperty({},
                        i.direction, "0px")),
                        l.show(),
                        e(this).data("toggle", "on")
                    }
                }),
                "object" === _typeof(i.elem) && o.click(),
                i.shadeClose && l.on("click",
                function() {
                    o.click()
                }),
                r.find(".tplay-sidebar-reload").on("click",
                function() {
                    var t = this;
                    if (i.dynamicRender) {
                        c.showLoading(r);
                        var n = i.url + "?version=" + (new Date).getTime();
                        a.tplLoader(n,
                        function(i) {
                            e(t).parent().next(".layui-card-body").html(i),
                            c.hideLoading(r)
                        },
                        function(i) {
                            e(t).parent().next(".layui-card-body").html("Loading error:" + i),
                            c.hideLoading(r)
                        })
                    }
                }),
                r.find(".tplay-sidebar-close").on("click",
                function() {
                    o.click()
                })
            })
        },
        showLoading: function(i) {
            i.append(l.join(""))
        },
        hideLoading: function(i) {
            setTimeout(function() {
                var e = i.find(".tplay-sidebar-loading");
                e.addClass("layui-anim-fadeout"),
                setTimeout(function() {
                    e.remove()
                },
                300)
            },
            500)
        }
    };
    i("sidebar", new o)
});