layui.use(['element','util', 'laydate', 'layer'], function(){
  var util = layui.util
  ,laydate = layui.laydate
  ,layer = layui.layer;
  //固定块
  util.fixbar({
    bar1: false
    ,bar2: false
    ,showHeight: 100
    ,css: {right: 30, bottom: 20}
    ,bgcolor: '#23262E;width:36px;height:36px;font-size:36px;line-height:36px;'
    ,click: function(type){
      if(type === 'bar1'){
        layer.msg('Layui和ThinkPHP提供强力驱动');
      } else if(type === 'bar2') {
        layer.msg('听雨漫漫，江湖路远。');
      }
    }
  });

  //提示登录
  $('#tplay-notlogin').click(function(){
    layer.confirm('你尚未登录，现在登录?',{title:'登录提醒'}, function(index) {
      var url = window.location.href;
      window.location.href = '/index/user/login.html?url='+url;
    })
  })

  //msg提醒位置提示
  $('#tplay-nomsg').click(function(){
    layer.open({
      type:4,
      shade:0,
      time: 5000,
      tips:[3,'#000'],
      content: ['如果有人@你，这里会有提示哦','#tplay-nomsg'],
    });
  })
  
});