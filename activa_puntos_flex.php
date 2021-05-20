<?php
	// SCRIPT PARA ACTIVAR PUNTOS FLEX Y PEP DE TABLA DE PUNTOS
	// Se ejecutará una sola vez para activar todos los puntos que no han sido activados
	// Creado Agosto 2020 BITMORE


	$error = '';

	ini_set ('error_reporting', E_ALL);
	ini_set ("display_errors","1" );

	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');


   // $base="wp_test";
	$base="whirlpool";
	$conexion=mysql_connect("mty-mysqlq01","root","Whr.Web.Soluciones@1");
//	$conexion=mysql_connect("localhost","root","pwd"); // BITMORE HOST DESARROLLO
	mysql_select_db($base,$conexion);
	mysql_set_charset('latin1', $conexion);


	$mensaje = '';

		
	// obtener configuracion
	$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
	$rowCFG = mysql_fetch_array($resultadoCFG);
		

	$fecha_aplicacion = date("Y-m-d");
	$reg_pep = 0;
	$reg_flex = 0;
	$reg_err = 0;

  	$resultado = mysql_query("SELECT * FROM puntos_flex WHERE estatus = 0",$conexion);
			
	while ($row = mysql_fetch_array($resultado)) {
		
		$folio = $row['folio'];
		$tipo = $row['tipo'];
		$monto = $row['monto']+0;
		$numero_empleado = $row['empleado'];

		// 
		$query = "UPDATE puntos_flex SET estatus = 1, canal='CMS', fecha_aplicacion = '$fecha_aplicacion', act = 1-act
					WHERE folio = '$folio' LIMIT 1";

		$resultadoUPD = mysql_query($query);
		$afe = mysql_affected_rows();
		if ($afe>0) {
			// Procesar puntos PEP/FLEX
			if ($tipo=='P') {
				$resultadoUC = mysql_query("UPDATE cliente SET puntos_pep = puntos_pep + $monto, act = 1-act WHERE numero_empleado = '$numero_empleado' AND tipo = 'E' LIMIT 1");
				$afe = mysql_affected_rows();
				if ($afe>0) $reg_pep++;
			}
			if ($tipo=='F') {
				$resultadoUC = mysql_query("UPDATE cliente SET puntos_flex = puntos_flex + $monto, act = 1-act WHERE numero_empleado = '$numero_empleado'  AND tipo = 'E' LIMIT 1");
				$afe = mysql_affected_rows();
				if ($afe>0) $reg_flex++;
			}
		} else {
			$reg_err ++;
		}
			
	} // while


	$mensaje .= '<br>Se activaron puntos Flex/PEP.';
	$mensaje .= '<br>Total de registros (claves) PEP: '.$reg_pep;
	$mensaje .= '<br>Total de registros (claves) FLEX: '.$reg_flex;
	$mensaje .= '<br>Total de registros con error: '.$reg_err;

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
