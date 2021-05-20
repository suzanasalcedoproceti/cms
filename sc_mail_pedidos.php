<?
	ini_set ('error_reporting', 'E_ALL ~E_NOTICE');
	ini_set ("display_errors","1" );
	ini_set('post_max_size','200M'); ini_set('upload_max_filesize','200M'); ini_set('max_execution_time','200M'); ini_set('max_input_time','200M'); ini_set('memory_limit','200M'); set_time_limit(65536);
//echo 'paso 1';



function dirList ($directory, $sortOrder){

    //Get each file and add its details to two arrays
    $results = array();
    $handler = opendir($directory);
    while ($file = readdir($handler)) {  
        if ($file != '.' && $file != '..' && $file != "robots.txt" && $file != ".htaccess" && is_dir($directory."/".$file)==false){
            $currentModified = filectime($directory."/".$file);
            $file_names[] = $file;
            $file_dates[] = $currentModified;
        }    
    }
       closedir($handler);

    //Sort the date array by preferred order
    if ($sortOrder == "newestFirst"){
        arsort($file_dates);
    }else{
        asort($file_dates);
    }
    
    //Match file_names array to file_dates array
    $file_names_Array = array_keys($file_dates);
    foreach ($file_names_Array as $idx => $name) $name=$file_names[$name];
    $file_dates = array_merge($file_dates);
    
    $i = 0;
    $mensaje = "";
    //Loop through dates array and then echo the list
    foreach ($file_dates as $file_dates){
        $date = $file_dates;
        if ($date+300<=time()) {
          # code...

        $j = $file_names_Array[$i];
        $file = $file_names[$j];
        $i++;
            
        $mensaje .= "Archivo: $file <br/>";  
        }      
    }
    if($i>0){
      enviaNotif($mensaje);
    }

}

function enviaNotif ($mensaje){
include('d:/inetpub/wwwroot/conexion.php');
include('d:/inetpub/wwwroot/phpmailer/class.phpmailer.php');
$mail = new phpmailer();
$mail->SetLanguage('es','d:/inetpub/wwwroot/phpmailer/language/');
    $resMAIL= mysql_query("SELECT * FROM mail WHERE clave=4",$conexion);
    $rowMAIL= mysql_fetch_array($resMAIL);
    //print_r($rowMAIL);
  
   $mail->CharSet = "UTF-8";
        $mail->From     = $rowMAIL['from'];
        $mail->FromName = $rowMAIL['fromname'];
        $mail->AddReplyTo($rowMAIL['replyto'],$rowMAIL['replytoname']);
        $mail->Sender   = $rowMAIL['sender'];
        $mail->Host     = $rowMAIL['host'];
        $mail->Mailer   = 'smtp';
        $mail->SMTPAuth = ($rowMAIL['host']=='mailhost.whirlpool.com') ? false : true;
      if($rowMAIL['host']=='mailhost.whirlpool.com') $mail->SMTPSecure = 'tls';   
        $mail->Username = $rowMAIL['username'];
        $mail->Password = $rowMAIL['password'];    
        $mail->Port   = $rowMAIL['port'];
        $mail->isHTML(true);
        $mail->ClearAddresses();

     //echo $texto_mail;
    $mail->Subject = "Notificacion de TXT";

    $mail->MsgHTML($mensaje);
   
    //$mail->AddAddress('victor_lopez_openservice@whirlpool.com','Victor Lopez');
    $mail->AddAddress('victor_lopez_openservice@whirlpool.com','Victor Lopez');
    $mail->AddAddress('suzana_salcedo_proceti@whirlpool.com','Alejandro Renau');
    $mail->AddAddress('sarah_aguilar_openservice@whirlpool.com','Sarah Aguilar');
    $mail->AddAddress('fernando_valero_openservice@whirlpool.com','Sarah Aguilar');
    $mail->AddAddress('keren_lozano_movit@whirlpool.com','Sarah Aguilar');
    //$mail->AddBCC('hector_martin_montemayor@whirlpool.com','Hector Montemayor');
      //echo "enviando mail a: ".$rowCLI['email']." -> ".$rowCLI['nombre'];
    if ($mail->Host != 'localhost') $mail->Send();
  $error_mail = $mail->ErrorInfo;

}

dirList("d:/inetpub/wwwroot/admin/exp_ped","");

?>	
