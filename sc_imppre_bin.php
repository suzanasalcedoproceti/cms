<?
	// SCRIPT PARA IMPORTAR PRECIOS DE ARCHIVO TXT  archivo_precios.txt desde el bin de php

	if ($_SERVER['HTTP_HOST']=='localhost' ) {
		//$nombrearchivofull = 'd:/www/whirlpool/admin/imp_pre/archivo_precios.txt';
		$nombrearchivofull = 'd:/inetpub/twpos/admin/imp_pre/archivo_precios.txt';

		$base="whirlpool";
		$conexion=mysql_connect("localhost","root","okap");
		mysql_select_db($base,$conexion);

	} else {
		$nombrearchivofull = 'd:/inetpub/wwwroot/admin/imp_pre/archivo_precios.txt';
		$base="whirlpool";
		$conexion=mysql_connect("mty-mysqlq01","root","Whr.Web.Soluciones@1");
		mysql_select_db($base,$conexion);
	    mysql_set_charset('latin1', $conexion);

	}
 

	$nombrearchivo = 'archivo_precios.txt';
	if (!file_exists($nombrearchivofull)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor web.<br>';
		return;
		exit;
		
    } else {
		$subido=TRUE;
	} // si hay archivo a subir

	$size=20000;  // tamaño máximo en Kb
	ini_set ('error_reporting', E_ALL);
	ini_set ("display_errors","1" );

	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');
	
	$error = '';
	$mensaje = '';
	$existe=FALSE;

	if (file_exists($nombrearchivofull))
		$existe=TRUE;

	$mensaje = 'Se inició proceso de importación de precios';
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_precios (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
	$mensaje = '';
	
/*    $base="whirlpool";
    $conexion=mysql_connect("localhost","root","wh1rlp00L",FALSE,128);
//    $conexion=mysql_connect("localhost","root","okap",FALSE,128);
    mysql_select_db($base,$conexion);
*/
	
	if ($existe) {  // si se subió el archivo
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_pre/".$nombrearchivo;
		$direc_bk = $rowCFG['wwwroot']."admin/imp_pre/backup/".$nombrearchivo;

		//$direc =  "d:/inetpub/twpos/admin/admin/imp_pre/".$nombrearchivo;
		//$direc_bk ="d:/inetpub/twpos/admin/admin/imp_pre/backup/".$nombrearchivo;
		
		$resultado = mysql_query("DELETE FROM temp_precios",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_precios",$conexion);
		$enc = mysql_num_rows($resultado);
		if ($enc>0) 
			$error .= 'No se pudo eliminar contenido de tabla temporal anterior<br>';
		else {

		  // OJO, poner ruta absoluta del servidor
//		  $query = "LOAD DATA INFILE '$direc' INTO TABLE temp_precios FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 0 LINES";
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_precios FIELDS TERMINATED BY ',' IGNORE 0 LINES";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $resultado = mysql_query("SELECT 1 FROM temp_precios",$conexion); 
		  $enc = mysql_num_rows($resultado);

		  if ($enc<=0) 
			$error .= 'No se insertaron registros en tabla temporal<br>'.$query.'<br>'.$error_my;
		  else {
			
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal';
			
			// pasar a tabla de productos
			
			$query = 'SELECT * FROM temp_precios ORDER BY modelo';
			$resultado = mysql_query($query,$conexion);
			$total_act =0;
			$no_coinciden = '';
			while ($row = mysql_fetch_array($resultado)) {
				
				extract ($row, EXTR_OVERWRITE);
				
				$query_ins = "UPDATE producto SET 
								precio_lista = $precio_w7,
								precio_web = $precio_web,
								precio_w1 = $precio_w1,
								precio_w2 = $precio_w2,
								precio_w3 = $precio_w3,
								precio_w4 = $precio_w4,
								precio_w5 = $precio_w5,
								precio_w6 = $precio_w6,
								precio_w7 = $precio_w7,
								precio_w8 = $precio_w8,
								precio_w9 = $precio_w9,
								precio_x0 = $precio_x0,
								precio_x1 = $precio_x1,
								precio_x2 = $precio_x2,
								precio_x3 = $precio_x3,
								precio_x4 = $precio_x4,
								precio_x5 = $precio_x5,
								precio_x6 = $precio_x6,
								precio_x7 = $precio_x7,
								precio_x8 = $precio_x8, ";
					if($precio_x9>0) $query_ins .= "precio_x9 = $precio_x9, ";
					if($precio_LH>0) $query_ins .= "precio_LH = $precio_LH, ";
					if($precio_LG>0) $query_ins .= "precio_LG = $precio_LG, ";
					if($precio_LF>0) $query_ins .= "precio_LF = $precio_LF, ";
					if($precio_T2>0) $query_ins .= "precio_T2 = $precio_T2, ";
					if($precio_T5>0) $query_ins .= "precio_T5 = $precio_T5, ";
					$query_ins .= "	act = 1-act
							 WHERE trim(modelo) = '$modelo' LIMIT 1";
//							 WHERE trim(modelo) = '$modelo' AND solo_para_marcas = 0 LIMIT 1";
				$resultado_ins = mysql_query($query_ins,$conexion);
				$act = mysql_affected_rows();
				if ($act >0)
					$total_act ++;
				else
					$no_coinciden .= $modelo.', ';
			
			} // while
			$mensaje .= '<br>Se procesaron '.$total_act.' productos en tabla de productos (solo los que coinciden con el modelo)<br>No coinciden:<br>'.$no_coinciden;

			
			// antes de borrar el archivo, copiarlo a ./backup
			@copy($direc,$direc_bk);
			$mensaje .= '<br>Se respaldó el archivo TXT';
			
			@unlink($direc);
			$mensaje .= '<br>Se eliminó el archivo TXT';

		  
		  } // si hay registros en temporal
		} /// si se elimino temporal anterior
	} else $error .= 'No existe archivo: '.$nombrearchivo."<br>";
	
	// enviar mail con evento
	// obtener configuracion
	/*

	$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
	$rowCFG = mysql_fetch_array($resultadoCFG);
	$resMAIL= mysql_query("SELECT * FROM mail WHERE clave=1",$conexion);
	$rowMAIL= mysql_fetch_array($resMAIL);

	$CR='\r\n';
	$BR='<br>';
	$texto_mail  ='Log de importación de precios a la Tienda Whirlpool<br><br>';
	
	$texto_mail .= 'Mensajes:<br>';
	$texto_mail .= $mensaje."<br><br>";
	$texto_mail .= 'Errores:<br>';
	$texto_mail .= $error;
	
	require('../phpmailer/class.phpmailer.php');
	$mail = new phpmailer();
	$mail->SetLanguage('es','../phpmailer/language/');
	
	$mail->From     = $rowMAIL['from'];
	$mail->FromName = $rowMAIL['fromname'];
	$mail->AddReplyTo($rowMAIL['replyto'],$rowMAIL['replytoname']);
	$mail->Sender   = $rowMAIL['sender'];
	$mail->Host     = $rowMAIL['host'];
	$mail->Mailer   = 'smtp';
	$mail->SMTPAuth = true;
	$mail->Username = $rowMAIL['username'];
	$mail->Password = $rowMAIL['password'];
	
	$mail->isHTML(true);
	$mail->ClearAddresses();
	$mail->Subject = 'Log de importacion de precios de la Tienda Whirlpool';
	$mail->MsgHTML($texto_mail);
	$mail->AddAddress($rowMAIL['email_logs'],$rowMAIL['nombre_logs']); // Alejandro Renau
	
	$mail->Send();
	
	$error_mail = $mail->ErrorInfo;
	if ($error_mail) $error .= 'No se pudo enviar correo electrónico con Log de importación de precios: '.$error_mail."<br>";
	*/
	
	// guardar log de errores y mensajes
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_precios (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>
<body>
  <? 
    if ($mensaje) echo $mensaje."<br>";
	if ($error) echo $error."<br>";
  ?>
</body>
</html>
