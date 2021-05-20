<?
	// SCRIPT PARA IMPORTAR CP DE temporal temp_cp 
	
	$error = '';

	ini_set ('error_reporting', E_ALL);
	ini_set ("display_errors","1" );

	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');
	
	$base="whirlpool";
	$conexion=mysql_connect("mty-mysqlq01","root","Whr.Web.Soluciones@1");
	mysql_select_db($base,$conexion);
	mysql_set_charset('latin1', $conexion);



	// guardar log de errores y mensajes
	$mensaje = 'Se inició proceso de importación de CP';
	$subido = true;
	if ($subido) {  // si se subió el archivo
		
		if (1) {

		  $resultado = mysql_query("SELECT 1 FROM temp_cp",$conexion);
		  $enc = mysql_num_rows($resultado);

		  if ($enc<=0) 
			$error .= 'No se encontraron registros en temporal<br>';
		  else {
		  	$total_importados = $enc;
		  	$mensaje = '<br><br>Se encontraron '.$enc.' registros en tabla temporal de CP';

			$query = 'SELECT * FROM temp_cp ORDER BY cp ';  //WHERE cp > 79000 
			echo $query;
			$resultado = mysql_query($query,$conexion);
			$total_ins =0;
			$total_act =0;
			$total_err =0;
			
			while ($row = @mysql_fetch_assoc($resultado)) {
				
				$cp = $row['cp'];
				$estado = $row['estado'];
				$ciudad = $row['ciudad'];
				$trans_zone = $row['trans_zone'];
				$colonia = $row['colonia'];
				$low_dom = $row['low_dom']+0;
				$low_ocu = $row['low_ocu']+0;
				$ltl_dom = $row['ltl_dom']+0;
				$ltl_ocu = $row['ltl_ocu']+0;
				$sku_low = $row['sku_low'];
				$sku_ltl = $row['sku_ltl'];
				$cedis_origen_ltl = $row['cedis_origen_ltl'];
				$sucursal_ocurre = $row['sucursal_ocurre']+0;
				
				$resultadoCP = mysql_query("SELECT 1 FROM cp_nueva WHERE cp = '$cp'",$conexion);
//				$rowCP = mysql_fetch_array($resultadoCP);
				$encCP = mysql_num_rows($resultadoCP);

				echo "<br>CP: ".$cp." - ".$encCP."<br>";
				
				if ($encCP>0) {
					// actualizar
					$query = "UPDATE cp_nueva SET estado = '$estado', ciudad = '$ciudad', trans_zone = '$trans_zone', colonia = '$colonia', 
											low_dom = $low_dom, low_ocu = $low_ocu, ltl_dom = $ltl_dom, ltl_ocu = $ltl_ocu, sku_low = '$sku_low', sku_ltl = '$sku_ltl',
											cedis_origen_ltl = '$cedis_origen_ltl', sucursal_ocurre = $sucursal_ocurre, act = 1-act WHERE cp = $cp LIMIT 1";
					$resultadoUPD = mysql_query($query,$conexion);
					if (mysql_affected_rows()>0) $total_act++;
					else $total_err ++;
				} else {
				
					// insertar	
					$query = "INSERT INTO cp_nueva (cp, estado, ciudad, trans_zone, colonia, low_dom, low_ocu, ltl_dom, ltl_ocu, sku_low, sku_ltl,
											  cedis_origen_ltl, sucursal_ocurre) VALUES (
											  $cp, '$estado', '$ciudad', '$trans_zone', '$colonia', $low_dom, $low_ocu, $ltl_dom, $ltl_ocu, '$sku_low', '$sku_ltl',
											  '$cedis_origen_ltl', $sucursal_ocurre)";
					$resultadoINS = mysql_query($query,$conexion);
					if (mysql_affected_rows()>0) $total_ins++;
					else $total_err ++;
					
				}
				echo $query;
			} // while
			$mensaje .= '<br>Total de CP en el archivo de importación: '.number_format($total_importados,0);
			$mensaje .= '<br>Se actualizaron '.$total_act.' CPs';
			$mensaje .= '<br>Se insertaron '.$total_ins.' CPs';
			$mensaje .= '<br>Total de registros con error: '.number_format($total_err,0);

		  
		  } // si hay registros en temporal
		} /// si se elimino temporal anterior
	}

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
