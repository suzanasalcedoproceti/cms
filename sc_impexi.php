<?
	// SCRIPT PARA IMPORTAR EXISTENCIAS DE ARCHIVO TXT  archivo_exist.txt
	
	// CONTROL DE CAMBIOS
	//
	// 30 Junio 2011: FARN: 
	//    Cambio de criterio de disponibilidad. No importa el estatus (30,40, etc), 
	// 	  los productos de todas formas se muestra, pero si no hay existencia, 
	//	  no se puede comprar y se pone leyenda de que compren en tienda física
	//
	// 23 Nov 2011: FARN:
	//    Criterio de disponibilidad en web (producto.mostrar) considerando solo CEDIS RM01 RM02 y RM06

	$nombrearchivofull = 'd:/inetpub/wwwroot/admin/imp_exi/archivo_exist.txt';
	$nombrearchivo = 'archivo_exist.txt';

	$size=20000;  // tamaño máximo en Kb
	ini_set ('error_reporting', E_ALL);
	ini_set ("display_errors","1" );

	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');
	
	$error = '';
	$existe=FALSE;
	if (file_exists($nombrearchivofull))
		$existe=TRUE;

	include("../conexion.php");
	// guardar log de errores y mensajes
	$mensaje = 'Se inició proceso de importación de existencias';
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_exist (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
	$mensaje = '';

  	// obtener datos de configuración
  	$resultadoCFG = mysql_query("SELECT disponibilidad_venta FROM config WHERE reg = 1");
  	$rowCFG = mysql_fetch_array($resultadoCFG);
  	$disponibilidad_venta = $rowCFG['disponibilidad_venta']+0;
	
	if ($existe) {  // si se subió el archivo
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_exi/".$nombrearchivo;
		
		$resultado = mysql_query("DELETE FROM temp_exist",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_exist",$conexion);
		$enc = mysql_num_rows($resultado);
		if ($enc>0) 
			$error .= 'No se pudo eliminar contenido de tabla temporal anterior<br>';
		else {

		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_exist FIELDS TERMINATED BY ',' IGNORE 0 LINES";

//echo $query;

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $resultado = mysql_query("SELECT 1 FROM temp_exist",$conexion);
		  $enc = mysql_num_rows($resultado);

		  if ($enc<=0) 
			$error .= 'No se insertaron registros en la tabla temporal<br>'.$error_my;
		  else {

		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal';
			
			// eliminar tabla existencias
			$resultado = mysql_query("DELETE FROM existencia",$conexion);

			// recorrer temporal para ir agregando a tabla existencia
						
			$query = 'INSERT INTO existencia (producto, cedis, loc, existencia, estatus, vol_reb, act) SELECT producto, cedis, loc, existencia, estatus, vol_reb, 0 FROM temp_exist';
			$resultado = mysql_query($query,$conexion);
			$total_act = mysql_affected_rows();

			$mensaje .= '<br>Se procesaron '.$total_act.' registros en tabla de existencias';

			@unlink($direc);
			$mensaje .= '<br>Se eliminó el archivo TXT';

			// actualizar total de productos y estatus en tabla producto  (resurtible, mostrar)
			
			$query = 'SELECT * FROM producto ORDER BY modelo';
			$resultado = mysql_query($query,$conexion);
			$total_upd = 0;
			while ($row = mysql_fetch_array($resultado)) {
			
				$producto = $row['modelo'];
				$query = "SELECT SUM(existencia) AS total_ex, estatus, vol_reb FROM existencia WHERE producto = '$producto' 
								AND (cedis = 'RM01' OR cedis = 'RM02' OR cedis = 'RM06')
								GROUP BY producto ";
				$resultado_acum = mysql_query($query,$conexion);
				$row_acum = mysql_fetch_array($resultado_acum);
				$total_ex = $row_acum['total_ex'];
				$estatus = $row_acum['estatus'];
				$vol_reb = $row_acum['vol_reb'];
				if ($total_ex <= $disponibilidad_venta) { // && $estatus != 30) {
					$mostrar = 0;
				} else $mostrar = 1;
				
				// if ($estatus == 30) $resurtible = 1; else $resurtible = 0;
				// M1 = Linea      M4 = Promocionado     M3 = Obsoleto
				if ($vol_reb == 'M3') $resurtible = 0; else $resurtible = 1; 
				
				// actualizar producto
				$query_upd = mysql_query("UPDATE producto SET mostrar = $mostrar, resurtible = $resurtible, vol_reb = '$vol_reb', act=1-act WHERE trim(modelo) = '$producto' LIMIT 1",$conexion);
				$upd = mysql_affected_rows();
				if ($upd >0)
					$total_upd ++;
					
				
			} // while
			$mensaje .= '<br>Se actualizó el estatus y global de existencias de '.$total_upd.' productos en tabla de productos';

		  
		  } // si hay registros en temporal
		} /// si se elimino temporal anterior
	} else $error .= 'No existe archivo: '.$nombrearchivo."<br>";

	// enviar mail con evento
	// obtener configuracion
	$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
	$rowCFG = mysql_fetch_array($resultadoCFG);
	$resMAIL= mysql_query("SELECT * FROM mail WHERE clave=4",$conexion);
	$rowMAIL= mysql_fetch_array($resMAIL);

	$CR='\r\n';
	$BR='<br>';
	$texto_mail  ='Log de importación de existencias a la Tienda Whirlpool<br><br>';
	
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
		    $mail->SMTPAuth = ($rowMAIL['host']=='mailhost.whirlpool.com') ? false : true;
			if($rowMAIL['host']=='mailhost.whirlpool.com') $mail->SMTPSecure = 'tls';   
		    $mail->Username = $rowMAIL['username'];
		    $mail->Password = $rowMAIL['password'];    
		    $mail->Port   = $rowMAIL['port'];
		    $mail->isHTML(true);
		    $mail->ClearAddresses();
	$mail->Subject = 'Log de importacion de existencias de la Tienda Whirlpool';
	$mail->MsgHTML($texto_mail);
	$mail->AddAddress($rowMAIL['email_logs'],$rowMAIL['nombre_logs']); // Alejandro Renau
	
	$mail->Send();
	
	$error_mail = $mail->ErrorInfo;
	if ($error_mail) $error .= 'No se pudo enviar correo electrónico con Log de importación de existencias: '.$error_mail."<br>";
	
	// guardar log de errores y mensajes
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_exist (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);


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
