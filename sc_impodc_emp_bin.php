<?
	// SCRIPT PARA IMPORTAR DATOS DE EMPLEADO DE RH ARCHIVO TXT, sirve para actualizar Ordenes de Compra, y actualizar # de badge.  
	// ASí como para agregar clientes (empleados) nuevos.
	// Lo ejecuta un servicio cron, pero antes el BAT que invoca este script copia el archivo del servidor web al servidor Mysql


	function convierte_fecha($vfecha) {
		if (empty($vfecha) || $vfecha=="  /  /    " || $vfecha=="--" || $vfecha=="- -" || $vfecha=="//") {
			return "0000-00-00";
		} else {
			return substr($vfecha,6,4).'-'.substr($vfecha,3,2).'-'.substr($vfecha,0,2);
		}
	}
	

	$error = '';
	$size=20000;  // tamaño máximo en Kb


	ini_set ('error_reporting', E_ALL);
	ini_set ("display_errors","1" );
	


	ini_set('max_execution_time','40000');
	ini_set('max_input_time','40000');

	$arch = "EMPLOYEES.TXT";
	
	if ($_SERVER['HTTP_HOST']=='localhost' ) {
		$nombrearchivo = './imp_odc/'.$arch;
		$nombrearchivofull = 'd:/www/whirlpool/admin/imp_odc/'.$arch;

		$base="whirlpool";
		$conexion=mysql_connect("localhost","root","okap");
		mysql_select_db($base,$conexion);

	} else {
		$nombrearchivo = './imp_odc/'.$arch;
		$nombrearchivofull = 'd:/inetpub/wwwroot/admin/imp_odc/'.$arch;

	//   	$base="wp_test";
		$base="whirlpool";		  
		$conexion=mysql_connect("mty-mysqlq01","root","Whr.Web.Soluciones@1");
		mysql_select_db($base,$conexion);
	 

	}
    mysql_set_charset('latin1', $conexion);

	if (!file_exists($nombrearchivofull)) {
		$subido=FALSE;
		$error.='No se encontró el archivo '.$nombrearchivofull.' en el servidor.<br>';
		echo $error;
		return;
		exit;
		
    } else {
		$subido=TRUE;

	} // si hay archivo a subir


	// guardar log de errores y mensajes
	$mensaje = 'Se inició proceso de importación de empleados (RH)';
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_odc (fecha_hora, dato, mensaje, error) VALUES ('$fecha_hora', 'e', '$mensaje', '$error')",$conexion);
	$mensaje = '';


	
	if ($subido) {  // si se subió el archivo
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_odc/".$arch;
		
		$resultado = mysql_query("DELETE FROM temp_odc_emp",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_odc_emp",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_odc_emp FIELDS TERMINATED BY '|' IGNORE 0 LINES";
		  
		 // echo "<br>".$query."<br>";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();


		  $direc = $rowCFG['wwwroot']."admin/imp_odc/".$arch;
		  $direc_bk = $rowCFG['wwwroot']."admin/imp_odc/backup/".$arch;

		  //@copy($direc,$direc_bk);
		  //$mensaje .= '<br>Se respaldó el archivo TXT';
		  
		//  unlink($direc);
		//  $mensaje .= '<br>Se eliminó el archivo TXT';
		  
		  $resultado = mysql_query("SELECT 1 FROM temp_odc_emp",$conexion);
		  $enc = mysql_num_rows($resultado);

		  if ($enc<=0) 
			$error .= 'No se insertaron registros.. .<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de Empleados (RH)';

			
//			$query = "SELECT * FROM temp_odc_emp WHERE numero_empleado > '00084970' ORDER BY numero_empleado";
//			$query = "SELECT * FROM temp_odc_emp WHERE estatus_sap = 1 ORDER BY numero_empleado";  //LIMIT 5000, 5000
			$query = "SELECT * FROM temp_odc_emp ORDER BY numero_empleado";  //LIMIT 5000, 5000
			$resultado = mysql_query($query,$conexion);
			$total_act =0;
			$total_err =0;
			$total_noenc =0;
			$total_ins = 0;
			$rfc = '';
			$log_correcto = '';
			$log_incorrecto = '';
			
			$ir = 0;
			echo "<br>Recorriendo temporal<br>";
			if (1)
			while ($row = mysql_fetch_array($resultado)) {
				
				 $ir++; 
//				if ($ir>=101) break;
				
				// obtener datos de RH del empleado del temporal 
				$numero_empleado_alfa = trim($row['numero_empleado']);
				$numero_empleado = (int) trim($row['numero_empleado']); 
				$nombre = trim($row['nombre']);
				$apellido_paterno = trim($row['apellido_paterno']);
				$apellido_materno = trim($row['apellido_materno']);
				$fecha_nacimiento = substr($row['fecha_nacimiento'],0,4).'-'.substr($row['fecha_nacimiento'],4,2).'-'.substr($row['fecha_nacimiento'],6,2);
				$user_id = trim($row['user_id']);
				$correo_e = strtolower(trim($row['correo_e']));
				$rfc = trim($row['rfc']);
				$numero_empresa = trim($row['numero_empresa']);
				$nombre_empresa = trim($row['nombre_empresa']);
				$division = trim($row['division']);
				$area_nomina = trim($row['area_nomina']);
				$falta = trim($row['falta']);
				$tope_deduccion = $row['tope_deduccion']+0;
				$estatus_sap = trim($row['estatus_sap'])+0;
				// tres campos nuevos:
				$badge = substr(trim($row['badge']),0,10)+0;
				$banda = trim($row['banda']);
				$fecha_ingreso = substr($row['fecha_ingreso'],0,4).'-'.substr($row['fecha_ingreso'],4,2).'-'.substr($row['fecha_ingreso'],6,2);
				
				// buscar qué empleado / empresa es en la BD de TW
				
				$resultadoDE = mysql_query("SELECT empresa FROM empresa_division WHERE division = '$division'",$conexion);
				$rowDE = mysql_fetch_array($resultadoDE);
				$empresa = $rowDE['empresa'];
				
		//		echo "<br>Div: ".$division." Empre: ".$empresa;
//				continue;

				if ($empresa && $numero_empleado>0) {
				
					// encontrar el empleado (s) y evaluar si ya tienen o no ciertos datos.
					// algunos se reemplazan (o todos?)
					//echo "SELECT 1 FROM cliente WHERE empresa = $empresa AND numero_empleado = '$numero_empleado'";
					
					// se compara numero_empleado convertido a numérico, ya que en algunos casos no le capturan ceros a la izquierda.
					$query = "SELECT nombre, apellido_paterno, apellido_materno, clave, email, pers_rfc, fecha_nacimiento 
								FROM cliente 
							   WHERE empresa IN (4,5,6,7,8,132)  AND CAST(numero_empleado AS UNSIGNED) = $numero_empleado";
					$resultadoC = mysql_query($query,$conexion);
					$enc = mysql_num_rows($resultadoC);
					if ($enc>0) {
					
						$rowC = mysql_fetch_array($resultadoC);
						$clave = $rowC['clave'];
						
/*						echo '<br> Datos existentes';
						echo '<br> CVECTE: '.$rowC['clave'];
						echo '<br> Nombre: '.$rowC['nombre'].' '.$rowC['apellido_paterno'].' '.$rowC['apellido_materno'];
						echo '<br> Email: '.$rowC['email'];
						echo '<br> RFC: '.$rowC['pers_rfc'];
						echo '<br> Fecha_Nac: '.$rowC['fecha_nacimiento']."<br>";
				
						// validar si ya tenía mail, no actualiza ese dato
						if ($correo_e) $act_email = " email = '".$correo_e."', ";
						else 	*/		
						$act_email = "";
					
						$query = " UPDATE cliente SET $act_email
													  numero_empleado = '$numero_empleado_alfa',
													  nombre = '$nombre', apellido_paterno = '$apellido_paterno', apellido_materno = '$apellido_materno', fecha_nacimiento = '$fecha_nacimiento', 
													  pers_rfc = '$rfc', user_id='$user_id', division='$division', area_nomina='$area_nomina', falta='$falta', tope_deduccion=$tope_deduccion, 
													  estatus_sap=$estatus_sap, 
													  numero_badge = '$badge', banda = '$banda', fecha_ingreso = '$fecha_ingreso', empresa=$empresa,
													  act=1-act
									WHERE clave = $clave";
						
					//	echo " --> ".$query;

						$resultadoUPD = mysql_query($query);
						$afe = mysql_affected_rows();
						if ($afe>0) 
							$total_act++;
						else {
							$total_err++;
							$error .= mysql_error().'<br>';
							echo "<br>Err: ".mysql_error()."<br>".$query;
							// echo "<br>".$query;
						}
						

//$total_act++;
					} else {
						$total_noenc++;
					//	echo "..insertar..";
						$password = "";
						if($correo_e)
						{
							$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    						$randstring = '';
						    for ($i = 0; $i < 10; $i++) {
						        $randstring .= $characters[rand(0, strlen($characters))];
						    }
						    $password = $randstring;
						}
						$query = " INSERT INTO cliente (empresa, tipo, invitado, numero_empleado, origen, acceso_web, activo,
														email, nombre, apellido_paterno, apellido_materno, fecha_nacimiento, pers_rfc, 
														user_id, division, area_nomina, falta, tope_deduccion, estatus_sap, 
														numero_badge, banda, fecha_ingreso, password)
												VALUES
														($empresa, 'E', 0, '$numero_empleado_alfa', 'pos', 0, 1,
														'$correo_e', '$nombre', '$apellido_paterno', '$apellido_materno', '$fecha_nacimiento', '$rfc', 
														'$user_id', '$division', '$area_nomina', '$falta', $tope_deduccion, $estatus_sap, 
													  	'$badge', '$banda', '$fecha_ingreso', '$password')";
						
					//	echo " --> ".$query;

						$resultadoINS = mysql_query($query);
						$afe = mysql_affected_rows();
						if ($afe>0) 
							$total_ins++;
							//mail bienvenida
							if($correo_e)
							{
								echo "string";
								$texto_mail = file_get_contents("../mail/mail_bienvenida.html");

								require('../phpmailer/class.phpmailer.php');
								$mail = new phpmailer();
								$mail->SetLanguage('es','../phpmailer/language/');
								$mes=array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
								$fecha_bienvenida = date("d")." de ".$mes[date("n")]." de ".date("Y");
								$texto_mail = str_replace('##fecha_bienvenida##',$fecha_bienvenida,$texto_mail);
								$texto_mail = str_replace('##usuario##',$correo_e,$texto_mail);
								$texto_mail = str_replace('##password##',$password,$texto_mail);

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
							    $mail->Username = $rowMAIL['username'];
							    $mail->Password = $rowMAIL['password'];    
							    $mail->Port   = $rowMAIL['port'];
							    $mail->isHTML(true);
							    $mail->ClearAddresses();
    							$mail->MsgHTML($texto_mail);
							    $mail->AddAddress($correo_e);
								$mail->AddBCC('keren_lozano_movit@whirlpool.com');
							    $mail->Subject = "Bienvenido a Tienda Whirlpool";
							    if ($mail->Host != 'localhost') $mail->Send();
								$error_mail = $mail->ErrorInfo;
								
							    if ($error_mail) $error .= 
							      '<br />No se pudo enviar correo con detalles del pedido, favor de imprimir esta pantalla con datos del pedido.<br>'.$error_mail;
							}
						else {
							$total_err++;
							$error .= mysql_error().'<br>';
							echo "<br>Err: ".mysql_error()."<br>".$query;
							// echo "<br>".$query;
						}
						
					}
					
				} // if empresa encontrada
				
			} // while
			$mensaje .= '<br>Se actualizaron los datos de Empleados (RH).';
			$mensaje .= '<br>Total de clientes actualizados: '.$total_act;
			$mensaje .= '<br>Total de clientes no encontrados: '.$total_noenc;
			$mensaje .= '<br>Total de clientes agregados: '.$total_ins;
			$mensaje .= '<br>Total de errores: '.$total_err;

		  
		  } // si hay registros en temporal
		} /// si se elimino temporal anterior
	}
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_odc (fecha_hora, dato, mensaje, error) VALUES ('$fecha_hora', 'e', '$mensaje', '$error')",$conexion);
	

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
	if ($error) echo $error.".<br>";
  ?>
</body>
</html>