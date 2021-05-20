<?
	// SCRIPT PARA IMPORTAR PUNTOS FLEX DE ARCHIVO TXT  
	// Lo ejecuta un servicio cron, pero antes el BAT que invoca este script copia el archivo del servidor web al servidor Mysql
	// Modificado Agosto 2020 BITMORE: se procesan los puntos (Se activan) al momento de ser insertados.


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


	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');

	$arch = 'eFlex'.date("Ymd").'.txt'; // formato: eFlexAAAAMMDD

	$nombrearchivo = './imp_puntos_eflex/'.$arch;
	$nombrearchivofull = 'd:/inetpub/wwwroot/admin/imp_puntos_eflex/'.$arch;
	//$nombrearchivofull = 'd:/www/whirlpool/admin/imp_puntos_eflex/'.$arch; // BITMORE URL DESARROLLO

	
	if (!file_exists($nombrearchivofull)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor web.<br>';
		return;
		exit;
		
    } else {
		$subido=TRUE;

	} // si hay archivo a subir



   // $base="wp_test";
	$base="whirlpool";
	$conexion=mysql_connect("mty-mysqlq01","root","Whr.Web.Soluciones@1");
//	$conexion=mysql_connect("localhost","root","pwd"); // BITMORE HOST DESARROLLO
	mysql_select_db($base,$conexion);
	mysql_set_charset('latin1', $conexion);


	// guardar log de errores y mensajes
	$mensaje = 'Se inició proceso de importación de puntos flex';
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_puntos (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
	$mensaje = '';


	
	if ($subido) {  // si se subió el archivo
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_puntos_eflex/".$arch;
		
		$resultado = mysql_query("DELETE FROM temp_puntos_flex",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_puntos_flex",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_puntos_flex FIELDS TERMINATED BY '|' IGNORE 0 LINES";
		  
		 // echo "<br>".$query."<br>";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();


		  $direc = $rowCFG['wwwroot']."admin/imp_puntos_eflex/".$arch;
		  $direc_bk = $rowCFG['wwwroot']."admin/imp_puntos_eflex/backup/".$arch;

		  @copy($direc,$direc_bk);
		  $mensaje .= '<br>Se respaldó el archivo TXT';
		  
		//  unlink($direc);
		//  $mensaje .= '<br>Se eliminó el archivo TXT';
		  
		  $resultado = mysql_query("SELECT 1 FROM temp_puntos_flex",$conexion);
		  $enc = mysql_num_rows($resultado);

		  if ($enc<=0) 
			$error .= 'No se insertaron registros.. .<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de puntos flex';

			
			$query = 'SELECT * FROM temp_puntos_flex ORDER BY clave';
			$resultado = mysql_query($query,$conexion);
			$total_act =0;
			$total_err =0;
			$total_rep =0;
			$rfc = '';
			$log_correcto = '';
			$log_incorrecto = '';
			
			while ($row = mysql_fetch_array($resultado)) {
				
				$clave = $row['clave'];
				$tipo = substr($row['tipo'],0,1);
				$monto = $row['monto']+0;
				$fecha = convierte_fecha($row['fecha']);
				$usuario = $row['usuario'];
				$numero_empleado = $row['numero_empleado'];
				$rfc = $row['rfc'];
				
				// insertar la clave si no existe
				$resultadoBP = mysql_query("SELECT 1 FROM puntos_flex WHERE folio = '$clave'");
				$enc = mysql_num_rows($resultadoBP);
				if ($enc<=0) {
				
					////////////////////////////////////
					// Cambios Agosto 2020 - BITMORE
					// Procesar puntos PEP/FLEX al momento de ser cargados: 
					// El registro de puntos_flex se inserta con estatus de procesado=1, fecha_aplicacion, y canal=AUT

					$fecha_aplicacion = date("Y-m-d");
					$query = "INSERT puntos_flex (folio, tipo, fecha, usuario, rfc, empleado, monto, estatus, canal, fecha_aplicacion, vendedor) VALUES (
								'$clave', '$tipo', '$fecha', '$usuario', '$rfc', '$numero_empleado', $monto, 1, 'AUT', '$fecha_aplicacion', 0)";

					// FIN CAMBIOS
					///////////////////////////////////					

					$resultadoINS = mysql_query($query);
					$afe = mysql_affected_rows();
					if ($afe>0) 
						$total_act++;
						/////////////////////////////////
						// Cambios Agosto 2020 - BITMORE
						// Procesar puntos PEP/FLEX al momento de ser cargados
						if ($tipo=='P') {
							$resultadoUC = mysql_query("UPDATE cliente SET puntos_pep = puntos_pep + $monto, act = 1-act WHERE numero_empleado = '$numero_empleado' and tipo='E' LIMIT 1"); 
							echo "UPDATE cliente SET puntos_pep = puntos_pep + $monto, act = 1-act WHERE numero_empleado = '$numero_empleado' and tipo='E' LIMIT 1";
							$afe = mysql_affected_rows();
						}
						if ($tipo=='F') {
							$resultadoUC = mysql_query("UPDATE cliente SET puntos_flex = puntos_flex + $monto, act = 1-act WHERE numero_empleado = '$numero_empleado' and tipo='E' LIMIT 1");
							echo "UPDATE cliente SET puntos_flex = puntos_flex + $monto, act = 1-act WHERE numero_empleado = '$numero_empleado' and tipo='E' LIMIT 1";
							$afe = mysql_affected_rows();
						}
						//// FIN CAMBIOS Agosto 2020
						//////////////////////////////
						
						
						
					else {
						$total_err++;
//						echo "<br>".$query;
					}
				} else {
					$total_rep++;
				}
				
			} // while
			$mensaje .= '<br>Se actualizaron los puntos Flex/PEP.';
			$mensaje .= '<br>Total de registros (claves) importados: '.$total_act;
			$mensaje .= '<br>Total de registros (claves) repetidos: '.$total_rep;
			$mensaje .= '<br>Total de registros (claves) no agregados: '.$total_err;

		  
		  } // si hay registros en temporal
		} /// si se elimino temporal anterior
	}
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_puntos (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
	

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
