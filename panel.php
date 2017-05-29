<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8' />
<style type="text/css">
<!--
.chat_wrapper {
	width: 500px;
	margin-right: auto;
	margin-left: auto;
	background: #CCCCCC;
	border: 1px solid #999999;
	padding: 10px;
	font: 12px 'lucida grande',tahoma,verdana,arial,sans-serif;
}
.chat_wrapper .message_box {
	background: #FFFFFF;
	height: 150px;
	overflow: auto;
	padding: 10px;
	border: 1px solid #999999;
}
.chat_wrapper .panel input{
	padding: 2px 2px 2px 5px;
}
.system_msg{color: #BDBDBD;font-style: italic;}
.user_name{font-weight:bold;}
.user_message{color: #88B6E0;}
-->
</style>
</head>
<body>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>

<input type="text" id="command">
<button id="sendcommand">Send Command</button> 
<button id="server">Start</button>
<input type="hidden" id="pid">
    <div id="message"> </div>
    
<script language="javascript" type="text/javascript">  
$(document).ready(function(){
    
     $("#server").click(function(event) {
          var url = "function.php?page=server";
         var command = $('#command').val();
         var type = "";
         if($('#server').html() == "Start")
            {
                command = 'php -q server.php > /dev/null 2>&1 & echo $!';
                type = "start";
            }
            else 
            {
                command = 'kill '+$("#pid").val();
                type = "stop";
            }
          
         //alert(command);
          $.ajax({
           type: "POST",
           url: url,
           dataType:"json",
           data : {'command':command, 'type':type},
           success: function(data)
           {
               alert(data.command);
               if(data.status)
                {
                    $("#server").html("Stop");
                    $("#pid").val(data.message);
                }
               else
                {
                    $("#server").html("Start");
                }
                
             //$("#message").html(data.message);
           }
         }); 
     });
    
    $("#sendcommand").click(function(event) {
          var url = "function.php?page=execute"; 
          var command = $('#command').val();
         //alert(command);
          $.ajax({
           type: "POST",
           url: url,
           dataType:"json",
           data : {'command':command},
           success: function(data)
           {
             //alert(data);
             $("#message").html(data.message);
           }
         }); 
     });
});
    
</script>
</body>
</html>