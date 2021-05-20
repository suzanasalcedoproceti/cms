<?

	include(dirname(__FILE__)."/../conexion.php");

	$rs = mysql_query("SELECT COUNT(*) AS encolados FROM jobs WHERE created_at < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 10 MINUTE))",$conexion);
	$row = mysql_fetch_assoc($rs);
	if($row['encolados'] > 15){
		
		include_once(dirname(__FILE__).'/../phpmailer/class.phpmailer.php');
		$mail = new phpmailer();
		$mail->SetLanguage('es',dirname(__FILE__).'/../phpmailer/language/');
	
		$resMAIL= mysql_query("SELECT * FROM mail WHERE clave=4",$conexion);
		$rowMAIL= mysql_fetch_array($resMAIL);
		$mail->CharSet = "iso-8859-1";
		$mail->From     = $rowMAIL['from'];
		$mail->FromName = $rowMAIL['fromname'];
		$mail->AddReplyTo($rowMAIL['replyto'],$rowMAIL['replytoname']);
		$mail->Sender   = $rowMAIL['sender'];
		$mail->Host     = $rowMAIL['host'];
		$mail->Mailer   = 'smtp';
		$mail->SMTPAuth = ($rowMAIL['host']=='mailhost.whirlpool.com') ? false : true;
		if($rowMAIL['host']=='mailhost.whirlpool.com') $mail->SMTPSecure = 'tls';   
  
		$mail->Port   = $rowMAIL['port'];
		
		$mail->ClearAddresses();
		$texto = "Hola,<br><br> Hay ".$row['encolados']." registros de correos de pedidos por enviar en la tabla 'jobs'.<br>
		Favor de revisar que se encuentre corriendo la pantalla de command line abierta en el servidor mty-twhrp1. <br>
		En caso de no estar ejecutándose, correr los siguiente comandos en una ventana nueva:<br><p>
		d:<br>
		cd POS<br>		
		cd D:\POS<br>
		\"C:\Program Files (x86)\PHP\\v5.6\php.exe\" artisan queue:restart<br>
		\"C:\Program Files (x86)\PHP\\v5.6\php.exe\" artisan queue:work --daemon
		</p>
		";
		$mail->MsgHTML($texto);
	
		$mail->AddAddress('suzana_salcedo_proceti@whirlpool.com');
		$mail->AddAddress('fernando_valero_openservice@whirlpool.com');	
		$mail->AddAddress('ernesto_valero_proceti@whirlpool.com');	
		
		$mail->Subject = "Aviso de Job de correos encolados en POS3 ";
		$mail->Send();
		$error_mail = $mail->ErrorInfo;
		echo $error_mail."<br>".$texto;
	}	
		