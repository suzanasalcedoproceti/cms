<?
	// SCRIPT PARA IMPORTAR DATOS PARA DASHBOARD (LOGISTICA) DE ARCHIVO CSV  /imp_dashboard/dashboard_log.csv
	// Lo ejecuta un servicio cron, pero antes el BAT que invoca este script copia el archivo del servidor web al servidor Mysql

	$error = '';
	$size=20000;  // tamaño máximo en Kb
	$CR = chr(10);

	ini_set ('error_reporting', 'E_ALL ~E_NOTICE');
	ini_set ("display_errors","1" );


	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');

	

	if ($_SERVER['HTTP_HOST']=='localhost' ) {
		$nombrearchivo = './imp_dashboard/dashboard_logistica.csv';
		$nombrearchivofull = 'd:/www/whirlpool/admin/imp_dashboard/dashboard_logistica.csv';

		$base="whirlpool";
		$conexion=mysql_connect("localhost","root","okap");
		mysql_select_db($base,$conexion);

	} else {
		$nombrearchivo = './imp_dashboard/dashboard_logistica.csv';
		$nombrearchivofull = 'd:/inetpub/wwwroot/admin/imp_dashboard/dashboard_logistica.csv';

//   	$base="wp_test";
		$base="whirlpool";
		$conexion=mysql_connect("mty-mysqlq01","root","Whr.Web.Soluciones@1");
		mysql_select_db($base,$conexion);
	    mysql_set_charset('latin1', $conexion);


	}
    mysql_set_charset('latin1', $conexion);


	if (!file_exists($nombrearchivofull)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor web.'.$CR;
		return;
		exit;
		
    } else {
		$subido=TRUE;
	} // si hay archivo a subir



	// guardar log de errores y mensajes
	$mensaje = 'Se inició proceso de importación de datos (LOGISTICA) dashboard'.$CR;
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_guias (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
	$mensaje = '';

	
	if ($subido) {  // si se subió el archivo
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_dashboard/dashboard_logistica.csv";
		
		$resultado = mysql_query("DELETE FROM temp_dashboard_log",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_dashboard_log",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
//		  $query = "LOAD DATA  INFILE '$direc' INTO TABLE temp_dashboard_log FIELDS TERMINATED BY ',' IGNORE 0 LINES";
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_dashboard_log FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'";
//		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_dashboard_log FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'";
		  
//		  echo "<br>".$query."<br>";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();
		  if (mysql_error()) echo $error_my;

		  $direc = $rowCFG['wwwroot']."admin/imp_dashboard/dashboard_logistica.csv";
		  $direc_bk = $rowCFG['wwwroot']."admin/imp_dashboard/backup/dashboard_logistica.csv";

		  //copy($direc,$direc_bk);
		  //$mensaje .= 'Se respaldó el archivo CSV'.$CR;
		  
		  //unlink($direc);
		  //$mensaje .= 'Se eliminó el archivo CSV'.$CR;
		  
		  $resultado = mysql_query("SELECT 1 FROM temp_dashboard_log",$conexion);
		  $enc = mysql_num_rows($resultado);

		  if ($enc<=0) 
			$error .= 'No se insertaron registros..'.$CR.$error_my.$CR;
		  else 
			{
				$total_importados1 = $enc;
				$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de datos dashboard logística';
				$fecha_hora = date("Y-m-d H:i:s");
				$resultadoLOG = mysql_query("INSERT log_guias (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
//			$resultado = mysql_query("SET AUTOCOMMIT = 0");
//			$resultado = mysql_query("START TRANSACTION");

				echo "Inicio: ".date("H:i:s");
				echo "<br>---------------------------------<br>";
				echo "Pasada 1: Carga Inicial<br>";

				// recorrer temporal, agrupando por folio_sap
				
				$query = 'SELECT * FROM temp_dashboard_log GROUP BY folio_sap ORDER BY folio_sap';
				$resultado = mysql_query($query,$conexion);
				echo "<br>Se eliminan registros de los folios importados, y se vuelven a agregar";
				
				while ($row = mysql_fetch_array($resultado)) {
					
					$folio_sap = (int) $row['folio_sap']+0;

					// eliminar de dashboard registros con ese folio_sap, (en caso que en SAP se hayan hecho cambios o eliminado items de pedido)
					$resultadoD = mysql_query("DELETE FROM dashboard_logistica WHERE folio_sap = $folio_sap");
					$del = mysql_affected_rows();

					$query = "INSERT INTO dashboard_logistica ( proveedor, folio_sap, no_delivery, material, unidades, guia, fecha_entrega_promesa, fecha_entrega_final, estatus_entrega, comentarios, no_return, folio_devolucion ) 
													   SELECT 
														   proveedor, folio_sap, no_delivery, material, unidades, guia, fecha_entrega_promesa, fecha_entrega_final, estatus_entrega, comentarios, no_return, folio_devolucion 
													   FROM temp_dashboard_log
													   WHERE folio_sap = '$folio_sap'";
													   
					$resID = mysql_query($query);
					if (mysql_error()) echo '<br>'.$query."<br>".mysql_error();

				} // while
		
				
				echo " ".date("H:i:s");
				echo "<br>---------------------------------<br>";
				echo "Pasada 2: Actualizar numeros de guias<br>";

				$query = 'SELECT * FROM temp_dashboard_log ORDER BY folio_sap';
				$resultado = mysql_query($query,$conexion);
				echo "<br>Se actualizan los numeros de guias";
				$total_act=0;
				$total_not=0;
				$coinciden = '';
				$no_coinciden = '';
				$error_act = '';
				while ($row = mysql_fetch_array($resultado)) {
					$folio_sap = (int) $row['folio_sap']+0;
					$material =  $row['material'];
					$guia =  $row['guia'];
					
					$query = "SELECT * FROM dashboard WHERE folio_sap = '$folio_sap' AND material = '$material'";
						//echo $query.'<br>';
					$resultadoDL = mysql_query($query);
					if (mysql_num_rows($resultadoDL)>0) {
						$rowDL = mysql_fetch_array($resultadoDL);
						$folio_pos = $rowDL['folio_pos'];

						// eliminar de dashboard registros con ese folio_sap, (en caso que en SAP se hayan hecho cambios o eliminado items de pedido)
						$query = "UPDATE dashboard SET 
									guia = '$guia'
								  WHERE folio_sap='$folio_sap' and material='$material'			
						";
						//echo $query.'<br>';
						mysql_query($query);
						$total_act ++;
						$coinciden .= $folio_pos.', '.$folio_sap.', '.$material.', '.$guia.' <br>';
					}
					else 
					{
						$total_not ++;
						$no_coinciden .= $folio_sap.', '.$material.', '.$guia.' <br>';
						mysql_query("Insert temp_log_guias (folio_sap, material, guia) VALUES ('$folio_sap', '$material', '$guia')",$conexion);
					}

				} // while
				$mensaje_act = 'Se actualizaron '.$total_act.' registros <br>'.$coinciden;
				if($total_not>0)
				{
					$error_act .= 'No se encontraron '.$total_not.' registros <br>'.$no_coinciden;
				}
				$fecha_hora = date("Y-m-d H:i:s");
				$resultadoLOG = mysql_query("INSERT log_guias (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje_act', '$error_act')",$conexion);

			}
		}

		

	} // if subido
	

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
	if ($error) echo str_replace(chr(10),'<br>',$error)."<br>";
  ?>
</body>
</html>
