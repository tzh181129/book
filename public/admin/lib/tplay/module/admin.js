var mods = ["element", "sidebar", "select", "tabs", "menu", "route", "utils", "component", "tplay", "echarts"];
layui.define(mods,
function(e) {
    layui.element;
    var n = layui.utils,
    t = (layui.jquery, layui.route),
    o = layui.tabs,
    a = layui.layer,
    m = layui.menu,
    l = layui.component,
    p = layui.tplay,
    c = function() {
        this.config = {
            elem: "#app",
            loadType: "SPA"
        },
        this.version = "1.0.0"
    };
    c.prototype.ready = function(e) {
        var o = this.config,
        m = (0, n.localStorage.getItem)("TPLAY_SETTING");
        null !== m && void 0 !== m.loadType && (o.loadType = m.loadType),
        p.set({
            type: o.loadType
        }).init(),
        i.routeInit(o),
        i.menuInit(o),
        "TABS" === o.loadType && i.tabsInit(),
        "" === location.hash && n.setUrlState("主页", "#/"),
        l.on("nav(header_right)",
        function(e) {
            var n = e.elem.attr("tplay-target");
            "setting" === n && layui.sidebar.render({
                elem: e.elem,
                title: "设置",
                shade: !0,
                dynamicRender: !0,
                url: "components/setting.html"
            }),
            "help" === n && a.alert("QQ群：248049395，616153456")
        }),
        "SPA" === o.loadType && t.render(),
        this.render(),
        "function" == typeof e && e()
    },
    c.prototype.render = function() {
        return this
    };
    var i = {
        routeInit: function(e) {
            var n = this,
            a = {
                r: [{
                    path: "/user/index",
                    component: "/components/user/index.html",
                    name: "用户列表",
                    children: [{
                        path: "/user/create",
                        component: "components/user/create.html",
                        name: "新增用户"
                    },
                    {
                        path: "/user/edit",
                        component: "components/user/edit.html",
                        name: "编辑用户"
                    }]
                }],
                routes: [{
                    path: "/user/index",
                    component: "/components/user/index.html",
                    name: "用户列表"
                },
                {
                    path: "/user/create",
                    component: "components/user/create.html",
                    name: "新增用户"
                },
                {
                    path: "/user/edit",
                    component: "components/user/edit.html",
                    name: "编辑用户"
                },
                {
                    path: "/cascader",
                    component: "components/cascader/index.html",
                    name: "Cascader"
                },
                {
                    path: "/",
                    component: "components/app.html",
                    name: "主页"
                },
                {
                    path: "/user/my",
                    component: "components/profile.html",
                    name: "用户中心"
                },
                {
                    path: "/inputnumber",
                    component: "components/inputnumber.html",
                    name: "InputNumber"
                },
                {
                    path: "/layui/grid",
                    component: "components/layui/grid.html",
                    name: "Grid"
                },
                {
                    path: "/layui/button",
                    component: "components/layui/button.html",
                    name: "按钮"
                },
                {
                    path: "/layui/form",
                    component: "components/layui/form.html",
                    name: "表单"
                },
                {
                    path: "/layui/nav",
                    component: "components/layui/nav.html",
                    name: "导航/面包屑"
                },
                {
                    path: "/layui/tab",
                    component: "components/layui/tab.html",
                    name: "选项卡"
                },
                {
                    path: "/layui/progress",
                    component: "components/layui/progress.html",
                    name: "progress"
                },
                {
                    path: "/layui/panel",
                    component: "components/layui/panel.html",
                    name: "panel"
                },
                {
                    path: "/layui/badge",
                    component: "components/layui/badge.html",
                    name: "badge"
                },
                {
                    path: "/layui/timeline",
                    component: "components/layui/timeline.html",
                    name: "timeline"
                },
                {
                    path: "/layui/table-element",
                    component: "components/layui/table-element.html",
                    name: "table-element"
                },
                {
                    path: "/layui/anim",
                    component: "components/layui/anim.html",
                    name: "anim"
                },
                {
                    path: "/layui/layer",
                    component: "components/layui/layer.html",
                    name: "layer"
                },
                {
                    path: "/layui/laydate",
                    component: "components/layui/laydate.html",
                    name: "laydate"
                },
                {
                    path: "/layui/table",
                    component: "components/layui/table.html",
                    name: "table"
                },
                {
                    path: "/layui/laypage",
                    component: "components/layui/laypage.html",
                    name: "laypage"
                },
                {
                    path: "/layui/upload",
                    component: "components/layui/upload.html",
                    name: "upload"
                },
                {
                    path: "/layui/carousel",
                    component: "components/layui/carousel.html",
                    name: "carousel"
                },
                {
                    path: "/layui/laytpl",
                    component: "components/layui/laytpl.html",
                    name: "laytpl"
                },
                {
                    path: "/layui/flow",
                    component: "components/layui/flow.html",
                    name: "flow"
                },
                {
                    path: "/layui/util",
                    component: "components/layui/util.html",
                    name: "util"
                },
                {
                    path: "/layui/code",
                    component: "components/layui/code.html",
                    name: "code"
                },
                {
                    path: "/user/table",
                    component: "/components/table/teble.html",
                    name: "Table"
                },
                {
                    path: "/user/table2",
                    component: "/components/table/teble2.html",
                    name: "数据表格2"
                },
                {
                    path: "/user/table3",
                    component: "/components/table/teble3.html",
                    name: "数据表格3"
                },
                {
                    path: "/user/form",
                    component: "/components/form/index.html",
                    name: "Form"
                },
                {
                    path: "/docs/menu",
                    component: "/docs/menu.html",
                    name: "左侧菜单(Menu)"
                },
                {
                    path: "/docs/route",
                    component: "/docs/route.html",
                    name: "路由配置(Route)"
                },
                {
                    path: "/docs/tabs",
                    component: "/docs/tabs.html",
                    name: "选项卡(Tabs)"
                },
                {
                    path: "/docs/utils",
                    component: "/docs/utils.html",
                    name: "工具包(Utils)"
                },
                {
                    path: "/components/select",
                    component: "components/select/index.html",
                    name: "Select"
                },
                {
                    path: "/exception/403",
                    component: "components/exception/403.html",
                    name: "403"
                },
                {
                    path: "/exception/404",
                    component: "components/exception/404.html",
                    name: "404"
                },
                {
                    path: "/exception/500",
                    component: "components/exception/500.html",
                    name: "500"
                }]
            };
            return "TABS" === e.loadType && (a.onChanged = function() {
                o.existsByPath(location.hash) ? o.switchByPath(location.hash) : n.addTab(location.hash, (new Date).getTime())
            }),
            t.setRoutes(a),
            this
        },
        addTab: function(e, n) {
            var a = t.getRoute(e);
            a && o.add({
                id: n,
                title: a.name,
                path: e,
                component: a.component,
                rendered: !1,
                icon: "&#xe62e;"
            })
        },
        menuInit: function(e) {
            var n = this;
            return m.set({
                dynamicRender: !1,
                isJump: !0,
                onClicked: function(t) {
                    if ("TABS" === e.loadType && !t.hasChild) {
                        var o = t.data,
                        a = o.href,
                        m = o.layid;
                        n.addTab(a, m)
                    }
                },
                elem: "#menu-box",
                remote: {
                    url: "/api/getmenus",
                    method: "post"
                },
                cached: !1
            }).render(),
            this
        },
        tabsInit: function() {
            o.set({
                onChanged: function(e) {}
            }).render(function(e) {
                e.isIndex && t.render("#/")
            })
        }
    }; (new c).ready(function() {
        console.log("Init successed.")
    }),
    e("admin", {})
});