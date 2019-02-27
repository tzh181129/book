if (layui === undefined) {
  console.error('请先引用layui.js文件.');
} else {
  var modules = {
    admin: '../../tplay/module/admin',
    axios: '../../tplay/module/axios',
    lodash: '../../tplay/module/lodash',
    menu: '../../tplay/module/menu',
    route: '../../tplay/module/route',
    tabs: '../../tplay/module/tabs',
    utils: '../../tplay/module/utils',
    component:'../../tplay/module/component',
    nprogress:'../../tplay/module/nprogress',
    sidebar:'../../tplay/module/sidebar',
    echarts:'../../tplay/module/echarts',
    tplay:'../../tplay/module/tplay',
  };
  layui.injectModules(modules);
}