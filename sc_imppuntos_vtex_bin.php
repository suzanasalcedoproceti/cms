<?
// SCRIPT PARA IMPORTAR PUNTOS FLEX DE ARCHIVO TXT DESDE VTEX
// Creado Agosto 2020 BITMORE


$error = '';
$size=20000;  // tamaño máximo en Kb


ini_set ('error_reporting', E_ALL);
ini_set ("display_errors","1" );


ini_set('max_execution_time','10000');
ini_set('max_input_time','10000');

$fecha = date("Y-m-d");

// $arch = 'puntos_vtex.txt'; 
$arch = 'middleware_D2CPointsUsedInVtex.txt'; 

$nombrearchivo = './imp_puntos_/'.$arch;
$nombrearchivofull = 'd:/inetpub/wwwroot/admin/imp_puntos/'.$arch;
//$nombrearchivofull = 'd:/www/whirlpool/admin/imp_puntos/'.$arch; // BITMORE URL DESARROLLO


if (!file_exists($nombrearchivofull)) {
	$subido=FALSE;
	$error.="No se encontró el archivo $nombrearchivofull en el servidor web.<br>";
	echo $error;
	return;
	exit;
	
} else {
	$subido=TRUE;

} // si hay archivo a subir



// $base="wp_test";
$base="whirlpool";
$conexion=mysql_connect("mty-mysqlq01","root","Whr.Web.Soluciones@1");
//$conexion=mysql_connect("localhost","root","pwd"); // BITMORE HOST DESARROLLO
mysql_select_db($base,$conexion);
mysql_set_charset('latin1', $conexion);


// guardar log de errores y mensajes
$mensaje = 'Se inició proceso de importación de consumo de puntos en VTEX';
$fecha_hora = date("Y-m-d H:i:s");
$resultadoLOG = mysql_query("INSERT log_puntos (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
$mensaje = '';

if ($subido) {  // hay archivo a procesar
	
	// obtener configuracion
	$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
	$rowCFG = mysql_fetch_array($resultadoCFG);
	
	$direc = $rowCFG['wwwroot']."admin/imp_puntos/".$arch;
	
	$resultado = mysql_query("DELETE FROM temp_puntos_vtex",$conexion);
	$resultado = mysql_query("SELECT 1 FROM temp_puntos_vtex",$conexion);
	$enc = mysql_num_rows($resultado);

	if ($enc>0) {
		$error .= 'No se pudo eliminar temporal anterior';
	} else {


	  // OJO, poner ruta absoluta del servidor
	  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_puntos_vtex FIELDS TERMINATED BY '|' IGNORE 0 LINES";
	  
	  
	  
	  
	 // echo "<br>".$query."<br>";

	  $resultado = mysql_query($query,$conexion);
	  $error_my = mysql_errno().": ".mysql_error();

	//  unlink($direc);
	  
	  $resultado = mysql_query("SELECT 1 FROM temp_puntos_vtex",$conexion);
	  $enc = mysql_num_rows($resultado);

	  if ($enc<=0) {
		$error .= 'No se insertaron registros.. .<br>'.$error_my;
	  } else {
		$total_importados1 = $enc;
		$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de puntos desde VTEX';

		
		$query = 'SELECT * FROM temp_puntos_vtex ORDER BY pedido';
		$resultado = mysql_query($query,$conexion);
		$total_act =0;
		$total_cte =0;
		$total_cteerr =0;
		$total_err =0;
		$total_rep =0;
		
		while ($row = mysql_fetch_assoc($resultado)) {
			
			$pedido = $row['pedido'];
			$numero_empleado = $row['numero_empleado'];
			$puntos = $row['puntos']+0;
			$puntos_flex = $row['puntos_flex']+0;
			$puntos_pep = $row['puntos_pep']+0;
			$puntos_convenio = $row['puntos_convenio']+0;
			
			// insertar la clave si no existe
			$resultadoBP = mysql_query("SELECT 1 FROM puntos_vtex WHERE pedido = '$pedido'");
			$enc = mysql_num_rows($resultadoBP);
			if ($enc<=0) {
			
				$query = "INSERT puntos_vtex (pedido, fecha, numero_empleado, puntos, puntos_flex, puntos_pep, puntos_convenio) VALUES (
							'$pedido', '$fecha', '$numero_empleado', $puntos, $puntos_flex, $puntos_pep, $puntos_convenio)";


				$resultadoINS = mysql_query($query);
				$afe = mysql_affected_rows();
				if ($afe>0) {
					$total_act++;

					$update = '';
					// Descontar puntos en clientes
					if ($puntos>0) {
						$update .= " puntos = puntos - $puntos, ";
					}
					if ($puntos_flex>0) {
						$update .= " puntos_flex = puntos_flex - $puntos_flex, ";
					}
					if ($puntos_pep>0) {
						$update .= " puntos_pep = puntos_pep - $puntos_pep, ";
					}
					if ($puntos_convenio>0) {
						$update .= " puntos_convenio = puntos_convenio - $puntos_convenio, ";
					}
					
					$query = "UPDATE cliente SET $update act = 1-act WHERE numero_empleado = '$numero_empleado' and tipo='E' LIMIT 1";
					echo "<hr>".$query;

					$resultadoUC = mysql_query($query,$conexion);
					$afe = mysql_affected_rows();
					
					if ($afe>0) 
						$total_cte ++;
					else
						$total_cteerr ++;
					
					
					
					
				} else {
					$total_err++;
//						echo "<br>".$query;
				}
			} else {
				$total_rep++;
			}
			
		} // while
		$mensaje .= '<br>Se actualizaron los puntos desde VTEX.';
		$mensaje .= '<br>Total de registros (pedidos) importados: '.$total_act;
		$mensaje .= '<br>Total de registros (pedidos) repetidos: '.$total_rep;
		$mensaje .= '<br>Total de registros (pedidos) no agregados: '.$total_err;
		$mensaje .= '<br>Total de registros (clientes) actualizados: '.$total_cte;
		$mensaje .= '<br>Total de registros (clientes) no encontrados: '.$total_cteerr;

	  
	  } // si hay registros en temporal
	} /// si se elimino temporal anterior
}
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
