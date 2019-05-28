<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>橘子HR—简单好用的HR Sass</title>
    <style type="text/css">
       .error{
        position: fixed;
        width: 100%;
        height: 100%;
        overflow: hidden;
        display: table;
        text-align: center;
       }
       .error_cont{
        display: table-cell;
        vertical-align: middle;
       }
       .error_cont img{
        width: 45%;
       }
       .error_cont p{
        color: #333;
        margin: 50px auto 70px;
       }
       .error_cont span{
        display: block;
        margin-bottom: 20px;
       }
       .error_cont a{
        display: block;
        margin: 0 auto;
        width: 150px;
        height: 40px;
        line-height: 40px;
        border-radius: 20px;
        border: 1px solid #c3c3c3;
        color: #333;
        text-decoration: none;
       }
    </style>
  </head>
  <body>
      <div class="error">
        <div class="error_cont">
           <img src="/static/bootstrap/images/404.jpg" alt=""/>
           <h1><?php echo(strip_tags($msg));?></h1>
           <input type="hidden" id="href" value="<?php echo($url);?>">
           <p class="jump">
            页面自动跳转中，等待时间： <b id="wait"><?php echo($wait);?></b>
          </p>
          <a href="javascript:history.back(-1)">返回</a>
        </div>
      </div>
      <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').value;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
  </body>
</html>