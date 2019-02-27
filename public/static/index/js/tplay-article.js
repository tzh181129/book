layui.use(['util', 'laydate','code','layer', 'form'], function(){
    var layer = layui.layer,
    		  util = layui.util,
    		  laydate = layui.laydate,
              $ = layui.jquery,
              form = layui.form;
    //开启layui代码修饰器
    layui.code({
        about: false
        ,elem: 'pre' //默认值为.layui-code
        //,skin: 'notepad' //如果要默认风格，不用设定该key。
    });

    //提交留言
    $(window).on('load', function() {
	      form.on('submit(messages)', function(data) {
	          $.ajax({
	              url:"/index/user/messages",
	              data:$('#messages').serialize(),
	              type:'post',
	              async: false,
	              success:function(res) {
	                   
	                   if(res.code == 0){
	                   		layer.msg(res.msg);
	                   } else {
	                   		var data = $.parseJSON(res);
	                   		var msg = "<li><div><a href='javascript:;' class='tplay-msg-reply tplay-msg-tag' data-id="+data.id+">回复ta</a><img src="+data.thumb+" width='40' height='40' class='tplay-msg-thumb' /><span class='msg_user_name'>"+data.name+"</span><span class='msg_create_time'>"+"刚刚"+"</span></div><p>"+data.message+"</p></li>";
	                   		$('.tplay-msg-lst').prepend(msg);
	                   		$('#messages textarea').val("");
	                   		
	                   		if($('.tplay-msg-null')) {
	                   			$('.tplay-msg-null').css('display','none');
	                   		}
	                   }
	              }
	          })
	          return false;
	      });
	  });

    //登录
    $('#user_login').click(function(){
    	var url = window.location.href;
    	window.location.href = '/index/user/login.html?url='+url;
    })

    //多少时间之前
	$(document).ready(function(){
	    var list = $('.msg_create_time');
	    list.each(function(){
	      var date = $(this).html();
	      var str = util.timeAgo(date,30);
	      $(this).html(str);
	    })
	})

	//分享
	var href = window.location.href;
		href+=$('#tplay-title').text();
    var clipboard = new Clipboard('#share', {
        text: function() {
            return href;
        }
    });
    clipboard.on('success', function(e) {
        layer.open({
	      type:4,
	      shade:0,
	      time: 5000,
	      tips:[3,'#000'],
	      content: ['复制本页url成功，快去粘贴分享吧','#share'],
	    });
    });
    clipboard.on('error', function(e) {
        layer.open({
	      type:4,
	      shade:0,
	      time: 5000,
	      tips:[3,'#000'],
	      content: ['复制本页url失败，请手动复制网址进行分享','#share'],
	    });
    });
});

$(function(){
	//表情
  $('.tplay-msg-emotion').qqFace({

    id : 'facebox', 

    assign:'message', 

    path:'/static/public/qqFace/arclist/' //表情存放的路径

  });
});

//回复ta及滚动到指定位置
$('.tplay-msg-reply').on('click', function(){
	var msg_name = $(this).data('name');
	$('#message').val('@'+msg_name+' ');
})
$('.tplay-msg-tag').on('click',function(){
	$("html, body").animate({
        scrollTop: $("#tplay-msg").offset().top-230 },
        {duration: 200,easing: "swing"}
    );
})