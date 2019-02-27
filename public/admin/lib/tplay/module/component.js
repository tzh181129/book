layui.define(["layer"],
function(i) {
    layui.layer;
    var n = layui.jquery,
    e = "component",
    t = {
        NAV: ".tplay-nav",
        ITEM: ".tplay-item",
        SHOW: "layui-show",
        THIS: "layui-this"
    },
    a = function() {
        this.version = "1.0.0"
    };
    a.prototype.render = function(i, n) {
        if (void 0 === i) l.renderNav(n);
        else switch (i) {
        case "nav":
            l.renderNav(n)
        }
        return this
    },
    a.prototype.init = function() {
        return this.render(),
        this
    },
    a.prototype.on = function(i, n) {
        return layui.onevent.call(this, e, i, n)
    };
    var l = {
        renderNav: function(i) {
            n(void 0 === i ? t.NAV: ".tplay-nav[lay-filter=" + i + "]").find(t.ITEM).each(function() {
                var i = n(this),
                a = i.find("ul.tplay-nav-child"),
                l = a.length > 0;
                l && (i.children("a").addClass("child"), a.addClass("layui-anim").addClass("layui-anim-upbit")),
                i.off("click").on("click",
                function(a) {
                    if (layui.stope(a), l) i.addClass(t.SHOW),
                    n(document).on("click",
                    function() {
                        i.removeClass(t.SHOW),
                        n(this).off("click")
                    });
                    else {
                        i.parents(t.NAV).find(t.ITEM).removeClass("layui-this"),
                        i.addClass(t.THIS),
                        i.parent(".tplay-nav-child").parent(".layui-show").removeClass(t.SHOW);
                        var r = i.parents(t.NAV).attr("lay-filter");
                        layui.event.call(this, e, "nav(" + r + ")", {
                            elem: i
                        })
                    }
                })
            })
        }
    },
    r = new a;
    r.init(),
    i("component", r)
});
layui.use(['layer', 'apiconfig'],
function() {
    var layer = layui.layer,
    $ = layui.$,
    apiconfig = layui.apiconfig;
    var remember = '';
    $('#tag').click(function() {
        var tag = localStorage.getItem("tag");
        layer.prompt({
            formType: 2,
            anim: 1,
            offset: ['52px', 'calc(100% - 290px)'],
            value: tag,
            title: '备忘便签',
            skin: 'tplay-tag-class',
            area: ['280px', '150px'],
            id: 'remember',
            btn: ['save', 'del'],
            shade: 0,
            moveType: 1,
            btn2: function(index, layero) {
                localStorage.removeItem("tag");
                $('#remember textarea').val('');
                return false;
            }
        },
        function(value, index, elem) {
            localStorage.setItem("tag", value);
        })
    });
    $('[tplay-target=logout]').click(function() {
        layer.confirm('真的要退出?',
        function(index) {
            $.ajax({
                url: "/admin/common/logout",
                success: function(res) {
                    layer.msg(res.msg);
                    if (res.code == apiconfig.tplayCode.logout) {
                        setTimeout(function() {
                            location.href = apiconfig.user.login;
                        },
                        1000)
                    }
                }
            })
        })
    })
});