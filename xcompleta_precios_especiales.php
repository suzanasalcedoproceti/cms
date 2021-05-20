<?
	// SCRIPT PARA completar precios especiales disponibles en cliente.pe_disponibles

	$error = '';
	$CR = chr(10);
	$verbose = 1;

	ini_set ('error_reporting', 'E_ALL');
	ini_set ("display_errors","1" );

	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');

	include("../conexion.php");
	echo "inicio<br>";

	// obtener datos de configuracion
	$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
	$rowCFG = mysql_fetch_array($resultadoCFG);
	$limite_precios_especiales = $rowCFG['limite_precios_especiales']+0;
	$ano = date("Y");


	$resultado = mysql_query("SELECT * FROM cliente WHERE tipo = 'E' AND empresa IN (SELECT clave FROM empresa WHERE empresa_whirlpool = 1)");

	while ($row = mysql_fetch_array($resultado)) {
		
		$clave_cliente = $row['clave'];
		$empresa = $row['empresa'];
		echo "<br>[".$empresa."] ".$rowEMP['nombre']." - ".$row['nombre'].' '.$row['apellido_paterno']." ----> ";
		
		// consultar PE consumidos en el año
		$resultadoPRS = mysql_query("SELECT cantidad FROM precios_especiales WHERE ano = '$ano' AND cliente = $clave_cliente");
		$rowPRS = mysql_fetch_array($resultadoPRS);
		$pe_disponibles = $limite_precios_especiales - $rowPRS['cantidad'];
		if ($pe_disponibles < 0) $pe_disponibles = 0;

		$resultadoU = mysql_query("UPDATE cliente SET pe_disponibles = $pe_disponibles, act = 1-act WHERE clave = $clave_cliente LIMIT 1");
		$afe = mysql_affected_rows();
		if ($afe>0) echo '[ok] ->'.$pe_disponibles; else echo '[error]'.mysql_error();
	
	}
	
	echo "<br><br>fin";
	
	
?>
