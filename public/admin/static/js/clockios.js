!(function(win,doc){win.requestAnimationFrame=win.requestAnimationFrame||win.webkitRequestAnimationFrame||win.mozRequestAnimationFrame||window.msRequestAnimationFrame;var hour=doc.getElementById("div-hour"),minute=doc.getElementById("div-minute"),second=doc.getElementById("div-second");var start=function(){var now=new Date(),midnight=new Date(now.getFullYear(),now.getMonth(),now.getDate(),0,0,0),ms=now.getTime()-midnight.getTime(),hh=ms/(1000*60*60),mm=hh*60,ss=mm*60;hour.style.transform="rotate("+(hh*30+(hh/2))+"deg)";minute.style.transform="rotate("+(mm*6)+"deg)";second.style.transform="rotate("+(ss*6)+"deg)";win.requestAnimationFrame(start);}
win.requestAnimationFrame(start);})(window,document);