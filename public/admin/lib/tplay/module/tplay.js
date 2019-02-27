layui.define(["layer", "apiconfig", "route"],
function(d) {
    var f = layui.layer,
    c = layui.$,
    b = layui.apiconfig,
    k = layui.route;
    var h = layui.data("tplay").access_token;
    var l = layui.jquery,
    j = l(window),
    m = function() {
        this.config = {
            type: "TABS"
        },
        this.version = "1.0.0"
    };
    m.prototype.ajax = function(n) {
        if (b.isLogin == 1 && h === "") {
            location.href = b.user.login;
            return false
        } else {
            n.access_token = h
        }
        var i = n.type || "post";
        var a = n.dataType || "json";
        var e = n.async == undefined ? true: false;
        c.ajax({
            type: i,
            url: n.url,
            data: n.data,
            async: e,
            dataType: a,
            success: function(o) {
                if (o.code == b.tplayCode.logout) {
                    location.href = b.user.login
                }
                if (o.code == b.tplayCode.login) {
                    location.hash = "#/"
                }
                if (n.success) {
                    n.success(o)
                }
            },
            error: function(o) {
                if (n.error) {
                    n.error(o)
                }
            }
        })
    };
    m.prototype.set = function(a) {
        return l.extend(!0, this.config, a),
        this
    },
    m.prototype.init = function() {
        g.tabsInit(this),
        g.toolsInit()
    };
    var g = {
        toolsInit: function() {
            var a = l('[tplay-toggle="side"]');
            a.on("click",
            function() {
                var n = l("div[tplay-side]"),
                o = l("div[tplay-body]"),
                p = l("div[tplay-tabs-t]"),
                i = l("div[tplay-footer]");
                switch (a.attr("data-toggle")) {
                case "on":
                    n.animate({
                        width:
                        "50px"
                    }),
                    o.animate({
                        left: "50px"
                    }),
                    p.animate({
                        "margin-left": "50px"
                    }),
                    i.animate({
                        left: "50px"
                    }),
                    l(this).attr("data-toggle", "off"),
                    a.find("i.layui-icon").removeClass("layui-icon-shrink-right").addClass("layui-icon-spread-left");
                    n.addClass('sidebar-mini');
                    // c.(".sidebar-mini .tplay-menu-item span").hide();
                    break;
                case "off":
                    n.animate({
                        width:
                        "180px"
                    }),
                    o.animate({
                        left: "180px"
                    }),
                    i.animate({
                        left: "180px"
                    }),
                    p.animate({
                        "margin-left": "180px"
                    }),
                    l(this).attr("data-toggle", "on"),
                    a.find("i.layui-icon").removeClass("layui-icon-spread-left").addClass("layui-icon-shrink-right");
                    n.removeClass('sidebar-mini');
                }
            }),
            j.on("resize",
            function() {
                var e = l('[tplay-toggle="side"]').attr("data-toggle"),
                i = this.innerWidth;
                i < 1024 && "on" === e && a.click(),
                i > 1024 && "off" === e && a.click()
            }),
            j.resize()
        },
        tabsInit: function(p) {
            var o = (new Date).getTime(),
            q = ['<div class="tplay-tabs" tplay-target="tabs" tplay-tabs-t="true">', '  <div class="tplay-tabs-prev">', '    <i class="layui-icon">&#xe65a;</i>', "  </div>", '  <div class="tplay-tab">', '    <ul class="tplay-tab-title" style="left: 0;">', '      <li lay-id="' + o + '" class="layui-this" data-path="#/">', '        <span title="主页"><i class="layui-icon">&#xe68e;</i> 主页</span>', "      </li>", "    </ul>", "  </div>", '  <div class="tplay-tabs-next">', '    <i class="layui-icon">&#xe65b;</i>', "  </div>", '  <div class="tplay-tabs-tools">', '    <i class="layui-icon">&#xe61a;</i>', "  </div>", '  <div class="tplay-tabs-toolsbox layui-anim layui-anim-upbit">', "    <ul>", '      <li class="tplay-item" data-action="closeOther">', '        <a href="javascript:;">', "          <span>关闭其他标签页</span>", "        </a>", "      </li>", '      <li class="tplay-item" data-action="closeAll">', '        <a href="javascript:;">', "          <span>关闭所有标签页</span>", "        </a>", "      </li>", '      <li class="tplay-item-line"></li>', '      <li class="tplay-item" lay-id="1">', '        <a href="#/">', "          <span>首页</span>", "        </a>", "      </li>", "    </ul>", "  </div>", "</div>"],
            r = ['<div class="tplay-tabs-content" tplay-tabs="tabs">', '  <div class="tplay-tabs-item layui-show" data-rendered="false" data-path="#/" lay-tab-id="' + o + '">', "    <router-view></router-view>", "  </div>", "</div>"];
            if ("TABS" === p.config.type.toUpperCase()) {
                var n = l(".layui-layout-admin");
                n.append(q.join("")),
                n.find(".layui-body").html(r.join("")).css("top", "90px")
            }
        }
    };
    d("tplay", new m)
});