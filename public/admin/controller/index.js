layui.define(['element', 'sidebar', 'menu', 'route', 'utils', 'component', 'tplay', 'apiconfig', 'tabs'], function(exports) 
{
    var element = layui.element,
    utils = layui.utils,
    $ = layui.jquery,
    route = layui.route,
    layer = layui.layer,
    menu = layui.menu,
    tabs = layui.tabs,
    component = layui.component,
    apiconfig = layui.apiconfig,
    tplay = layui.tplay;


    var Admin = function() {
        this.config = {
            elem: '#app',
            loadType: 'SPA'
        };
        this.version = '1.0.0';
    };


    Admin.prototype.ready = function(callback) {
        var that = this,
        config = that.config;

        // 初始化加载方式
        const { getItem } = utils.localStorage;
        const setting = getItem("TPLAY_SETTING");
        if (setting !== null && setting.loadType !== undefined) {
            config.loadType = setting.loadType;
        }

        tplay.set({
            type: config.loadType
        }).init();

        // 初始化路由
        _private.routeInit(config);
        // 初始化左侧菜单
        _private.menuInit(config);
        // 初始化选项卡
        if (config.loadType === 'TABS') {
            _private.tabsInit();
        }
        // 跳转至首页
        if (location.hash === '') {
            utils.setUrlState('主页', '#/');
        }

        // 监听头部右侧 nav
        component.on('nav(header_right)', function(_that) {
            var target = _that.elem.attr('tplay-target');
            if (target === 'setting') {
                // 绑定sidebar
                layui.sidebar.render({
                    elem: _that.elem,
                    //content:'', 
                    title: '设置',
                    shade: true,
                    // shadeClose:false,
                    // direction: 'left'
                    dynamicRender: true,
                    url: 'views/setting.html',
                    // width: '50%', //可以设置百分比和px
                });
            }
        });

        // 初始化渲染
        if (config.loadType === 'SPA') {
            route.render();
        }
        that.render();

        // 执行回调函数
        typeof callback === 'function' && callback();
    }
    Admin.prototype.render = function() {
        var that = this;
        return that;
    }

    var _private = {
        routeInit: function(config) {
            var that = this;
              // route.set({
              //   beforeRender: function (route) {
              //     // 此配置可以限制页面访问
              //     if (!utils.oneOf(route.path, ['/user/table', '/user/table2', '/'])) {
              //       return {
              //         id: new Date().getTime(),
              //         name: 'unauthorized',
              //         path: '/error/unauthorized',
              //         component: 'views/error/unauthorized.html'
              //       };
              //     }
              //     return route;
              //   }
              // });
            // 配置路由
            var routes;
            $.ajax({url:apiconfig.routes,type:'get',async:false,success:function(res)
            {
                console.log(res, '-----------')
                routes = res.data
                console.log(apiconfig.routes)
                console.log(routes)

                if (routes == '') 
                {
                    location.href = apiconfig.user.login
                }
            }});

            var routeOpts = {
                routes: routes
            };
            
            if (config.loadType === 'TABS') 
            {
                routeOpts.onChanged = function() 
                {
                    // 如果当前hash不存在选项卡列表中
                    if (!tabs.existsByPath(location.hash)) 
                    {
                        // 新增一个选项卡
                        that.addTab(location.hash, new Date().getTime());
                    } 
                    else 
                    {
                        // 切换到已存在的选项卡
                        tabs.switchByPath(location.hash);
                    }
                }
            }
            route.setRoutes(routeOpts);
            return this;
        },
        addTab: function(href, layid) 
        {
            var r = route.getRoute(href);
            if (r) {
                tabs.add({
                    id: layid,
                    title: r.name,
                    path: href,
                    component: r.component,
                    rendered: false,
                    icon: '&#xe62e;'
                });
            }
        },
        menuInit: function(config) 
        {
            var that = this;

            const { user } = apiconfig;
            const { getMenus } = user;
            // 配置menu
            menu.set({
                dynamicRender: true,
                elem: '#menu-box',
                isJump: config.loadType === 'SPA',
                onClicked: function(obj) 
                {
                    if (config.loadType === 'TABS') 
                    {
                        if (!obj.hasChild) 
                        {
                            var data = obj.data;
                            var href = data.href;
                            var layid = data.layid;
                            that.addTab(href, layid);
                        }
                    }
                },
                remote: {
                    url: apiconfig.user.getMenus,
                    method: 'get',
                    transformResponse: [function (data) 
                    {
                        // 对 data 进行任意转换处理
                        var js = JSON.parse(data);
                    
                        console.log(js.data);
                        return js.data;
                    }],
                },
                cached: false
            }).render();
            return this;
        },
        tabsInit: function() 
        {
            tabs.set({
                onChanged: function(layid) {
                  // var tab = tabs.get(layid);
                  // if (tab !== null) {
                  //   utils.setUrlState(tab.title, tab.path);
                  // }
                }
            }).render(function(obj) 
            {
                // 如果只有首页的选项卡
                if (obj.isIndex) 
                {
                    route.render('#/');
                }
            });
        }
    }

    var admin = new Admin();
    admin.ready(function() {
        console.log('Init successed.');
    });

    //输出admin接口
    exports('index', {});
});
