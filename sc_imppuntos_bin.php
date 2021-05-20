<?php
	// SCRIPT PARA IMPORTAR PUNTOS DE ARCHIVO TXT  archivo_puntos.txt
	// Lo ejecuta un servicio cron, pero antes el BAT que invoca este script copia el archivo del servidor web al servidor Mysql
	
	// Abril 2015
	// Se quitó condicion de empresa, solo se valida el número de empleado
	
//	$nombrearchivo = 'archivo_puntos.txt';


	$error = '';
	$size=20000;  // tamaño máximo en Kb

	ini_set ('error_reporting', E_ALL);
	ini_set ("display_errors","1" );

	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');
	
	$nombrearchivo = './imp_puntos/archivo_puntos.txt';
	$nombrearchivofull = 'd:/inetpub/wwwroot/admin/imp_puntos/archivo_puntos.txt';

	if (!file_exists($nombrearchivofull)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor web.<br>';
		echo $error;
		return;
		exit;
		
    } else {
		$subido=TRUE;
	} // si hay archivo a subir


    $base="whirlpool";
	$conexion=mysql_connect("mty-mysqlq01","root","Whr.Web.Soluciones@1");
	mysql_select_db($base,$conexion);
	mysql_set_charset('latin1', $conexion);


	// guardar log de errores y mensajes
	$mensaje = 'Se inició proceso de importación de puntos';
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_puntos (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
	$mensaje = '';

	
	if ($subido) {  // si se subió el archivo
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_puntos/archivo_puntos.txt";
		
		$resultado = mysql_query("DELETE FROM temp_puntos",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_puntos",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_puntos FIELDS TERMINATED BY ',' IGNORE 0 LINES";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();


		  $direc = $rowCFG['wwwroot']."admin/imp_puntos/archivo_puntos.txt";
		  $direc_bk = $rowCFG['wwwroot']."admin/imp_puntos/backup/archivo_puntos.txt";

		  @copy($direc,$direc_bk);
		  $mensaje .= '<br>Se respaldó el archivo TXT';
		  
//		  unlink($direc);
		  $mensaje .= '<br>Se eliminó el archivo TXT';
		  
		  $resultado = mysql_query("SELECT 1 FROM temp_puntos",$conexion);
		  $enc = mysql_num_rows($resultado);

		  if ($enc<=0) 
			$error .= 'No se insertaron registros..<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de puntos';

			
			// eliminar puntos anteriores de todos los empleados 
			// NO ELIMINAR; por si suben actualización parcial por empresa
//			$resultado = mysql_query("UPDATE cliente SET puntos = 0");
			
			$query = 'SELECT * FROM temp_puntos ORDER BY empresa';
			$resultado = mysql_query($query,$conexion);
			$total_act =0;
			$total_err =0;
			$total_puntos=0;
			$total_puntos_aplicados=0;
			$clave_empresa = 0;
			$genera_puntos = 0;
			$total_emp = 0;
			$log_correcto = '';
			$log_incorrecto = '';
			
			while ($row = mysql_fetch_array($resultado)) {
				
				$empresa = $row['empresa'];
				$empleado = trim($row['empleado'])+0;
				$nombre = $row['nombre'];
				$saldo = $row['saldo'];
				$total_puntos += $saldo;
				
				if ($clave_empresa != $empresa) { // buscar una sola vez cada empresa
					// buscar clave de empresa
					$resultadoEMPR = mysql_query("SELECT clave, puntos FROM empresa WHERE cliente_sap = '$empresa'");
					$rowEMPR = mysql_fetch_array($resultadoEMPR);
					$clave_empresa = $empresa;
					$clave_web = $rowEMPR['clave'];
					$genera_puntos = $rowEMPR['puntos'];

					echo "<br>empr: ".$clave_empresa;
					
					if ($genera_puntos) { 
						$total_emp++;
						echo " Sí genera puntos";
					} else echo " NO genera puntos";
					
					
				}
				
				if ($clave_empresa > 0 && $genera_puntos) {
				
					// obtener registro para update
					// no validar empresa, pues puede venir incorrecta en txt, solo se valida que sea tipo Empleado (WP), y el número de empleado
					$query_cte = "SELECT clave FROM cliente WHERE tipo = 'E' AND CAST(numero_empleado AS UNSIGNED) = $empleado AND empresa != 178 LIMIT 1";
					$res = mysql_query($query_cte,$conexion);
					$rowCTE = mysql_fetch_assoc($res);
					$clave_cte = $rowCTE['clave'];
					
					if ($clave_cte) {

						$query_ins = "UPDATE cliente SET 
										puntos = $saldo,
										act = 1-act
									 WHERE clave = $clave_cte LIMIT 1";
						$resultado_ins = mysql_query($query_ins,$conexion);
						
						$act = mysql_affected_rows();
						if ($act >0) {
							$total_act ++;
							$total_puntos_aplicados += $saldo;
	//						if ($clave_web=='0004001311')
							// $log_correcto .= $nombre." (".$empleado.") Puntos: ".$saldo."<br>";
							  echo "<br>Actualizado: ".$nombre." (".$empleado.") Puntos: ".$saldo;
						} else {
							$total_err ++;
							// $log_incorrecto .= $nombre." (".$empleado.") Puntos: ".$saldo." [".$query."]<br>";
							  echo "<br>ERROR update: ".$nombre." (".$empleado.") Puntos: ".$saldo." [".$query_ins."]<br>";
						}
					} else {
						  echo "<br>Cliente no encontrado ".$empleado."<br>";
					}
					
				} // if genera puntos
				
				
			} // while
			$mensaje .= '<br>Se actualizaron los puntos de '.$total_act.' empleados de '.$total_emp.' empresas diferentes';
			$mensaje .= '<br>Total de puntos en el archivo de importación: '.number_format($total_puntos,0);
			$mensaje .= '<br>Total de puntos aplicados: '.number_format($total_puntos_aplicados,0);

		  
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
