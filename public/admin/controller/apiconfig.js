layui.define(function (exports) { 
    const baseUrl = 'api/';
    const apiconfig = {
        domain: '',
        user: {
            login: '/admin/views/user/login.html',//login页面地址,要相对于根目录的完整路径
            // getMenus: 'api/menu.json',//菜单接口
            getMenus: '/admin/index/menu.html',
        },
        // routes:'api/routes.json',//路由配置接口
        routes: '/admin/index/getRoutes.html',
        isLogin : 1, //是否验证登录状态1为验证0不验证
        indexPage : '/admin',//主页路径
      	tplayCode:{
      		ok: 1, //成功状态码
      		login: 402, //已登录状态码
      		logout: 401, //未登录状态码
      		error: 0, //错误状态码
      	},
    };
    exports('apiconfig', apiconfig);
});