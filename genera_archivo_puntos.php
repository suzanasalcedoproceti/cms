<?php
	// SCRIPT PARA GENERAR ARCHIVO DE TEXTO CON PUNTOS DISPONIBLES DE CLIENTES
	// Será ejecutado por un chron
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
	
	$ruta = $rowCFG['wwwroot'].'/admin/puntos_vtex/';
	$archivo = $ruta.'middleware_D2CWhirlpoolPoints.txt';
	echo $archivo;
  	$resultado = mysql_query("SELECT clave, numero_empleado, puntos, puntos_flex, puntos_pep, puntos_convenio FROM cliente WHERE tipo = 'E' ORDER BY numero_empleado ",$conexion);
	$txt = '';
	$SEP = '|';
	$CR = chr(10);
			
	while ($row = mysql_fetch_array($resultado)) {
		
		$clave = $row['clave'];
		$numero_empleado = trim($row['numero_empleado']);
		$puntos = $row['puntos']+0;
		$puntos_flex = $row['puntos_flex']+0;
		$puntos_pep = $row['puntos_pep']+0;
		$puntos_convenio = $row['puntos_convenio']+0;

		$txt .= $numero_empleado.$SEP.$puntos.$SEP.$puntos_flex.$SEP.$puntos_pep.$SEP.$puntos_convenio.$CR;
		
	} // while

	file_put_contents($archivo,$txt);

	$mensaje .= '<br>Se exportó archivo de puntos<br><br><br><hr>';
	
//	$mensaje .= str_replace($CR,'<br>',$txt);
	
	

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
