<?
	// SCRIPT PARA IMPORTAR DATOS PARA COMPLETAR NO_RETURN (devoluciones) EN DASHBOARD DE ARCHIVO TXT  /imp_dashboard/tawret.txt separado por |
	// Lo ejecuta un servicio cron, pero antes el BAT que invoca este script copia el archivo del servidor web al servidor Mysql

	$error = '';
	$size=20000;  // tamaño máximo en Kb
	$CR = chr(10);
	$verbose = 1;

//	require_once("lib_dashboard.php");

	ini_set ('error_reporting', 'E_ALL ~E_NOTICE');
	ini_set ("display_errors","1" );


	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');

	if ($_SERVER['HTTP_HOST']=='localhost' ) {
		$nombrearchivo = './imp_dashboard/tawret.txt';
		$nombrearchivofull = 'd:/www/whirlpool/admin/imp_dashboard/tawret.txt';

		$base="whirlpool";
		$conexion=mysql_connect("localhost","root","okap");
		mysql_select_db($base,$conexion);

	} else {
		$nombrearchivo = './imp_dashboard/tawret.txt';
		$nombrearchivofull = 'd:/inetpub/wwwroot/admin/imp_dashboard/tawret.txt';

//   	$base="wp_test";
		$base="whirlpool";
		$conexion=mysql_connect("mty-mysqlq01","root","Whr.Web.Soluciones@1");
		mysql_select_db($base,$conexion);
	  

	}
    mysql_set_charset('latin1', $conexion);

	
	if (!file_exists($nombrearchivofull)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor web.'.$CR;
		echo $error;
		return;
		exit;
		
    } else {
		$subido=TRUE;
	} // si hay archivo a subir


	// guardar log de errores y mensajes
	$mensaje = 'Se inició proceso de importación de datos (SAP) returns'.$CR;
	$fecha_hora = date("Y-m-d H:i:s");
	$fecha = date("Y-m-d");
	$hora = date("H:i");
	$resultadoLOG = mysql_query("INSERT log_dashboard (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
	$mensaje = '';

	
	if ($subido) {  // si se subió el archivo
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_dashboard/tawret.txt";
		
		$resultado = mysql_query("DELETE FROM temp_devol",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_devol",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_devol FIELDS TERMINATED BY '|' IGNORE 0 LINES";
		  
		 // echo "<br>".$query."<br>";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $direc = $rowCFG['wwwroot']."admin/imp_dashboard/tawret.txt";
		  $direc_bk = $rowCFG['wwwroot']."admin/imp_dashboard/backup/tawret.txt";

		  //unlink($direc);
		  //$mensaje .= 'Se eliminó el archivo TXT'.$CR;
		  
		  $resultado = mysql_query("SELECT 1 FROM temp_devol",$conexion);
		  $enc = mysql_num_rows($resultado);

		  if ($enc<=0) 
			$error .= 'No se insertaron registros..'.$CR.$error_my.$CR;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = $CR.$CR.'Se subieron '.$enc.' registros a tabla temporal de devoluciones ';

//			$resultado = mysql_query("SET AUTOCOMMIT = 0");
//			$resultado = mysql_query("START TRANSACTION");


			$mensaje .= $CR."Inicio: ".date("H:i:s");
		  	$mensaje .= $CR."---------------------------------".$CR;

			// recorrer temporal
			
			$query = 'SELECT * FROM temp_devol ';  // ref_doc  material
			$resultado = mysql_query($query,$conexion);
			$encontrados = 0;
			$no_encontrados = 0;
			$actualizados = 0;
			
			while ($row = mysql_fetch_array($resultado)) {
				
				$ref_doc = $row['ref_doc'];
				$material = $row['material'];
				$folio_sap = trim($row['folio_sap']);

				// buscar en dashboard con esa factura y material, para grabarle en el no_return el folio_Sap
				$query = "SELECT 1 FROM dashboard WHERE billing_doc = '$ref_doc' AND material = '$material' AND no_return = '' ";	

				$resultadoDASH = mysql_query($query,$conexion);
				$enc = mysql_num_rows($resultadoDASH);
	
				if ($enc>0) {
					$encontrados++;
					$query = "UPDATE dashboard SET no_return = '$folio_sap' WHERE billing_doc = '$ref_doc' AND material = '$material' AND no_return = '' LIMIT 1 ";	
					$resultadoDASH = mysql_query($query,$conexion);
					$afe = mysql_affected_rows();
					if ($afe>0) {
						$actualizados++;
					}
				} else {
					echo ' ---_no_update';
					$no_encontrados++;
				}

			} // while
	
	  	
			$mensaje .= " ".date("H:i:s");
		  	$mensaje .= $CR."---------------------------------".$CR;
			$mensaje .= "Se encontraron ".$encontrados." registros que coinciden en dashboard<br>";
			$mensaje .= "Se encontraron ".$no_encontrados." registros que NO coinciden en dashboard<br>";
			$mensaje .= "Se actualizaron ".$actualizados." registros de dashboard<br>";

		  
  			if ($error) { 
			//	mysql_query("ROLLBACK"); 
			//	$mensaje .= 'Error: No se actualizaron todos los datos para returns.'. date("H:i:s");
			}
			else {
			//	mysql_query("COMMIT");
			//	$mensaje .= 'Se actualizaron todos los datos para returns.'. date("H:i:s");
			//	mysql_query("ROLLBACK");
			
			}
			

		  } // si hay registros en temporal
		} /// si se elimino temporal anterior
		
	} // if subido
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_dashboard (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
//	$resultadoDU = mysql_query("UPDATE dashboard_updates SET log = '$mensaje' WHERE folio = $folio_update",$conexion);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>
<body>
  <? 
    if ($mensaje) echo str_replace(chr(10),'<br>',$mensaje)."<br>";
	if ($error) echo 'ERRORES: <br>'.str_replace(chr(10),'<br>',$error)."<br>";
  ?>
</body>
</html>