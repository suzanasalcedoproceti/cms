<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=20;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	$error = '';

	ini_set ('error_reporting', E_ALL);
	ini_set ("display_errors","1" );

	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');
	
	$nombrearchivo = './imp_pre/archivo_cp.txt';
	
	if (!file_exists($nombrearchivo)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor...<br>';
		
    } else {
		$subido=TRUE; 
	} // si hay archivo a subir
	
	if ($subido) {  // si se subió el archivo
		
		include("../conexion.php");
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		//$direc = $rowCFG['wwwroot']."admin/imp_pre/archivo_clas.txt";
		$direc = $rowCFG['wwwroot']."admin/imp_pre/archivo_cp.txt";
		

		$resultado = mysql_query("REPAIR TABLE temp_cp",$conexion);
		$resultado = mysql_query("DELETE FROM temp_cp",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_cp",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_cp FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 0 LINES ";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $resultado = mysql_query("SELECT 1 FROM temp_cp",$conexion);
		  $enc = mysql_num_rows($resultado);
		  unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros..<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de CPs';

			
			// eliminar tabla CPs
//			$resultado = mysql_query("DELETE FROM cp",$conexion);

			// recorrer temporal
			$resultadoT = mysql_query("SELECT * FROM temp_cp",$conexion);
			$total_rep = 0;
			$total_act = 0;
			$bitacora_repetidos = '';
			$bitacora_insertados = '';
		
			while ($rowT = mysql_fetch_array($resultadoT)) {
			
				// buscar si ya viene repetido para informar.
				$cp = $rowT['cp'];
				$estado = $rowT['estado'];
				$ciudad = $rowT['ciudad'];
				$trans_zone = $rowT['trans_zone'];
				$colonia = $rowT['colonia'];
				$low_dom = $rowT['low_dom']+0;
				$low_ocu = $rowT['low_ocu']+0;
				$ltl_dom = $rowT['ltl_dom']+0;
				$ltl_ocu = $rowT['ltl_ocu']+0;
				$sku_low = $rowT['sku_low'];
				$sku_ltl = $rowT['sku_ltl'];
				$cedis_origen_ltl = $rowT['cedis_origen_ltl'];
				$sucursal_ocurre = $rowT['sucursal_ocurre']+0;
				
				$resultadoB = mysql_query("SELECT 1 FROM cp WHERE cp = '$cp'");
				$encB = mysql_num_rows($resultadoB);
				if ($encB>0) {
					// actualizar
					$query = "UPDATE cp SET low_dom = $low_dom, low_ocu = $low_ocu, ltl_dom = $ltl_dom, ltl_ocu = $ltl_ocu,
											sku_low = '$sku_low', sku_ltl = '$sku_ltl', trans_zone = '$trans_zone', 
											cedis_origen_ltl = '$cedis_origen_ltl', sucursal_ocurre = $sucursal_ocurre, act=1-act
								 WHERE cp = '$cp' LIMIT 1";
					$resultado = mysql_query($query,$conexion);
					$afe = mysql_affected_rows();
					if ($afe>0) {
						$total_rep ++;
						$bitacora_repetidos .= $cp." - ".$colonia."<br>";
					}
					
				} else {
					$query = "INSERT INTO cp (cp, estado, ciudad, trans_zone, colonia, 
											  low_dom, low_ocu, ltl_dom, ltl_ocu, sku_low, sku_ltl, cedis_origen_ltl, sucursal_ocurre) 
									VALUES ('$cp', '$estado', '$ciudad', '$trans_zone', '$colonia',
											  $low_dom, $low_ocu, $ltl_dom, $ltl_ocu, '$sku_low', '$sku_ltl', '$cedis_origen_ltl', $sucursal_ocurre)";
					$resultado = mysql_query($query,$conexion);
					$ins = mysql_affected_rows();
					if ($ins>0) { 
						$total_act ++;
						$bitacora_insertados .= $cp." - ".$colonia."<br>";
					}						
				}
			} 				

			$mensaje .= '<br>Se insertaron '.$total_act.' nuevos registros en tabla de C.P.; Se encontraron '.$total_rep.' registros repetidos y se actualizaron.';
			if ($bitacora_insertados)
				$mensaje .= '<br><br><strong>Agregados:</strong><br>'.$bitacora_insertados;
			if ($bitacora_repetidos)
				$mensaje .= '<br><strong>Actualizados:</strong><br>'.$bitacora_repetidos;

		  
		  } // si hay registros en temporal
		} /// si se elimino temporal anterior
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script language="JavaScript">
  function descarta() {
   document.forma.action='principal.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Importar y Actualizar CPs'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><? if ($error) echo $error; else echo 'Archivo Subido.<br>'.$mensaje; ?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="descartar" type="button" class="boton" onclick="descarta();" value="SALIR" />
          </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
