<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$link='principal.php';
	include("lib.php");

	include('../conexion.php');

	$error=FALSE;
	
	// extrae variables del formulario
	$aplicacion = 'CMS';
	$modulo = $_POST['modulo'];
	$tienda = 0;
	$tipo = $_POST['tipo']+0;
	$asunto = mysql_real_escape_string($_POST['asunto']);
	$detalle = mysql_real_escape_string($_POST['descripcion']);
	$imagen_seleccionada=$_POST['imagen_seleccionada'];
	$imagen_tmp = date("Ymd")."_".session_id();


	$fecha = date("Y-m-d");
	$hora = date("H:i");
	$usuario = $_SESSION['usr_valido'];
	

	$query = "INSERT bug (fecha, actualizado, aplicacion, modulo, tipo, usuario, tienda, asunto, estatus) VALUES (
	                      '$fecha', '$fecha', '$aplicacion', '$modulo', $tipo, $usuario, $tienda, '$asunto', 'A')";

	$resultado= mysql_query($query,$conexion);
	$new_id= mysql_insert_id();
	$reg = mysql_affected_rows();

	if ($reg>0) {
		$query = "INSERT bug_detalle (bug, fecha, hora, usuario, detalle) VALUES (
									  $new_id, '$fecha', '$hora', $usuario, '$detalle')";
	
		$resultado= mysql_query($query,$conexion);
		$reg = mysql_affected_rows();
		if ($reg>0) {
			$mensaje='Se registró el evento...<br>En breve recibirás respuesta.';

			// enviar correo
			$resMAIL= mysql_query("SELECT * FROM mail WHERE clave=4",$conexion);
			$rowMAIL= mysql_fetch_array($resMAIL);
			
			// obtener correo de usuario
			$resultadoUSR = mysql_query("SELECT nombre, email FROM usuario WHERE clave = $usuario");
			$rowUSR = mysql_fetch_array($resultadoUSR);
			
			// obtener correos de WP para seguimiento
			$resultadoCFG = mysql_query("SELECT correo_seguimiento_bugs FROM config WHERE reg = 1");
			$rowCFG = mysql_fetch_array($resultadoCFG);
			$arr_correo = explode(chr(10),$rowCFG['correo_seguimiento_bugs']);
			
			
			// obtener formato a la variable texto_mail
			$texto_mail = file_get_contents("../mail/mail_reporte_bug.html");

			require('../phpmailer/class.phpmailer.php');
			$mail = new phpmailer();
			$mail->SetLanguage('es','../phpmailer/language/');

			$CR='\r\n';
			$BR='<br>';
			
			$texto_mail = str_replace('##titulo##',"Notificación de falla en CMS",$texto_mail);
			$texto_mail = str_replace('##fecha##',fecha($fecha),$texto_mail);
			$texto_mail = str_replace('##asunto##',$asunto,$texto_mail);
			$texto_mail = str_replace('##detalle##',str_replace(chr(10),'<br>',$detalle),$texto_mail);		
			$texto_mail = str_replace('##notas##',"Gracias por tus comentarios, en breve daremos seguimiento",$texto_mail);

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
			$mail->Subject = 'Notificación de falla en CMS';
			$mail->MsgHTML($texto_mail);
			$mail->AddAddress($rowUSR['email'],$rowUSR['nombre']);
			
			for ($ic = 0; $ic<=count($arr_correo); $ic++) {
				if (trim($arr_correo[$ic])!='') {
					$mail->AddCC($arr_correo[$ic],"Whirpool");
				}
			}
			
			// echo "enviando mail a: ".$rowCLI['email']." -> ".$rowCLI['nombre'];
			if ($mail->Host != 'localhost') $mail->Send();
			
			$error_mail = $mail->ErrorInfo;
			// if ($error_mail) 
				//$error .= '<br />No se pudo enviar correo con detalles del pedido, favor de imprimir esta pantalla con datos del pedido.<br>'.$error_mail;			
		
			if ($imagen_seleccionada) {
			
					// mover imagen de carpeta uploads 
					$archivo_original = './uploads/'.$imagen_tmp.'.jpg';
					if (file_exists($archivo_original)) {
						  $archivo_destino = 'images/cms/bugs/'.$new_id.'.jpg';
						  copy($archivo_original,$archivo_destino);
						  @unlink($archivo_original);
						  $mensaje .= "<br>Se subió imagen del reporte";
					} // si existe el archivo
		  	}
			
			// enviar correo al usuario y a WP
			

		} else { 
			$error=TRUE; $mensaje='ERROR<br>No se registró el evento...'.mysql_error(); $link='javascript:history.go(-1);'; 
		}

		
	} else { 
		$error=TRUE; $mensaje='ERROR<br>No se registró el evento...'; $link='javascript:history.go(-1);'; 
	}
	mysql_close();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
</head>

<body>
<div id="container">
	<? $tit='Registro de Bug o Recomendación'; include('top.php'); ?>
	<div class="main">
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>
