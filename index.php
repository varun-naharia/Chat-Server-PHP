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
<?php 
$colours = array('007AFF','FF7000','FF7000','15E25F','CFC700','CFC700','CF1100','CF00BE','F00');
$user_colour = array_rand($colours);
?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>

<script language="javascript" type="text/javascript">  
$(document).ready(function(){
    var pid = localStorage.getItem("pid");
    if(pid != "null")
    {
        $("#server").html("Stop");
    }
    else
    {
        $("#server").html("Start");
    }
    $("#server").click(function(event) {
          var url = "function.php?page=server";
         var command = "";
         var type = "";
         if($('#server').html() == "Start")
            {
                command = 'php -q server.php > /dev/null 2>&1 & echo $!';
                type = "start";
            }
            else 
            {
                
                command = 'kill '+pid;
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
               //alert(data.command);
               if(data.status)
                {
                    $("#server").html("Stop");
                    $("#pid").val(data.message);
                    localStorage.setItem("pid", data.message);
                     //create a new WebSocket object.
	               var wsUri = "ws://<?php echo $_SERVER['SERVER_NAME']; ?>:9000/chatserver/server.php"; 	
	               websocket = new WebSocket(wsUri);
                   websocket.onopen = function(ev) { // connection is open 
                        $('#message_box').append("<div class=\"system_msg\">Connected!</div>"); //notify user
	               }
                }
               else
                {
                    localStorage.setItem("pid", null);
                    $("#server").html("Start");
                }
                location.reload();
             //$("#message").html(data.message);
           }
         }); 
     });
	//create a new WebSocket object.
	var wsUri = "ws://<?php echo $_SERVER['SERVER_NAME']; ?>:9000/chatserver/server.php"; 	
	websocket = new WebSocket(wsUri); 
	
	websocket.onopen = function(ev) { // connection is open 
		$('#message_box').append("<div class=\"system_msg\">Connected!</div>"); //notify user
	}

	$('#send-btn').click(function(){ //use clicks message send button	
		var mymessage = $('#message').val(); //get message text
		var myname = $('#name').val(); //get user name
		
		if(myname == ""){ //empty name?
			alert("Enter your Name please!");
			return;
		}
		if(mymessage == ""){ //emtpy message?
			alert("Enter Some message Please!");
			return;
		}
		
		//prepare json data
		var msg = {
		message: mymessage,
		name: myname,
		color : '<?php echo $colours[$user_colour]; ?>'
		};
		//convert and send data to server
		websocket.send(JSON.stringify(msg));
	});
	
	//#### Message received from server?
	websocket.onmessage = function(ev) {
		var msg = JSON.parse(ev.data); //PHP sends Json data
		var type = msg.type; //message type
		var umsg = msg.message; //message text
		var uname = msg.name; //user name
		var ucolor = msg.color; //color

		if(type == 'usermsg') 
		{
			$('#message_box').append("<div><span class=\"user_name\" style=\"color:#"+ucolor+"\">"+uname+"</span> : <span class=\"user_message\">"+umsg+"</span></div>");
		}
		if(type == 'system')
		{
			$('#message_box').append("<div class=\"system_msg\">"+umsg+"</div>");
		}
		
		$('#message').val(''); //reset text
	};
	
	websocket.onerror	= function(ev){$('#message_box').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");}; 
	websocket.onclose 	= function(ev){$('#message_box').append("<div class=\"system_msg\">Connection Closed</div>");}; 
});
    
    
</script>
<button id="server">Start</button>    
<input type="hidden" id="pid">
<div class="chat_wrapper">
<div class="message_box" id="message_box"></div>
<div class="panel">
<input type="text" name="name" id="name" placeholder="Your Name" maxlength="10" style="width:20%"  />
<input type="text" name="message" id="message" placeholder="Message" maxlength="80" style="width:60%" />
<button id="send-btn">Send</button>
</div>
</div>

</body>
</html>