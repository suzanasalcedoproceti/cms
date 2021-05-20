<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
	$link='lista_bugs.php';

	include('../conexion.php');

	$error=FALSE;
	
	// extrae variables del formulario
	$detalle = mysql_real_escape_string($_POST['descripcion']);
	$permiso_bugs = op(28);

	$folio = $_POST['folio'];
	$fecha = date("Y-m-d");
	$hora = date("H:i");
	$usuario = $_SESSION['usr_valido'];
	$estatus = $_POST['estatus'];
	$asunto = $_POST['asunto'];
	$tipo_usuario = ($permiso_bugs) ? ('W') : ('C');
	$nombre_usuario = $_SESSION['ss_nombre'];

	$txt_detalle = str_replace(chr(10),'<br>',$detalle);
	$txt_detalle = str_replace("\\r\\n",'<br>',$txt_detalle);

	$query = "INSERT bug_detalle (bug, fecha, hora, usuario, tipo_usuario, nombre_usuario, detalle) VALUES (
								  $folio, '$fecha', '$hora', $usuario,  '$tipo_usuario', '$nombre_usuario', '$detalle')";

	$resultado= mysql_query($query,$conexion);
	$reg = mysql_affected_rows();
	if ($reg>0) {
		$query = "UPDATE bug SET actualizado = '$fecha', estatus = '$estatus', act = 1-act WHERE folio = $folio";
		$resultado= mysql_query($query,$conexion);
		$reg = mysql_affected_rows();
		if ($reg>0) {
			$mensaje='Se registró el evento...<br>';
			
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

			$texto_mail = str_replace('##titulo##',"Seguimiento de falla",$texto_mail);
			$texto_mail = str_replace('##fecha##',fecha($fecha),$texto_mail);
			$texto_mail = str_replace('##asunto##',$asunto,$texto_mail);
			$texto_mail = str_replace('##detalle##',$txt_detalle,$texto_mail);		
			$texto_mail = str_replace('##notas##',"Se ha dado seguimiento a tus comentarios",$texto_mail);

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
			$mail->Subject = 'Seguimiento de falla';
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
		
		} else
			$mensaje='Se registró el evento pero no se actualizó estatus ';
		
	} else { 
		$error=TRUE; $mensaje='ERROR<br>No se registró el evento...'.$query; $link='javascript:history.go(-1);'; 
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
