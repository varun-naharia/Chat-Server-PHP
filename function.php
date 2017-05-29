<?php 
if(isset($_GET['page']))
{
    $type = $_GET['page'];
    global $con;
    switch($type)
    {
        case 'execute':
        {
            $command = $_POST["command"];
            $ret = '<pre>'.shell_exec($command).'</pre>';
            echo json_encode($arr = array('message' => $ret));
            break;
        }
            
            case 'server':
        {
            $command = $_POST["command"];
            $type = $_POST["type"];
            $ret = shell_exec($command);
            if($type == "start")
            {
                $type = "Stop";
            }
            else
            {
                $type = "Start";
            }
            echo json_encode($arr = array('message' => $ret, 'status' => $type, 'command'=> $command));
            break;
        }
    }
}


?>