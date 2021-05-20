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
	$size=20000;  // tamaño máximo en Kb


	ini_set ('error_reporting', E_ALL);
	ini_set ("display_errors","1" );

	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');
	
	$nombrearchivo = './imp_pre/archivo_precio_entrega.txt';
	
	if (!file_exists($nombrearchivo)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor.<br>';
		
    } else {
		$subido=TRUE; 
	} // si hay archivo a subir
	
	if ($subido) {  // si se subió el archivo
		
		include("../conexion.php");
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_pre/archivo_precio_entrega.txt";
		
		$resultado = mysql_query("DELETE FROM temp_precio_entrega",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_precio_entrega",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA INFILE '$direc' INTO TABLE temp_precio_entrega FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 0 LINES";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $resultado = mysql_query("SELECT 1 FROM temp_precio_entrega",$conexion);
		  $enc = mysql_num_rows($resultado);
		  unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros..<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de precios de entrega LTL';

			
			// eliminar contenido de tabla precios de entrega
			$resultado = mysql_query("DELETE FROM precio_entrega",$conexion);

			// recorrer temporal
			$resultadoT = mysql_query("SELECT * FROM temp_precio_entrega",$conexion);
			$total_rep = 0;
			$total_act = 0;
			$bitacora_repetidos = '';
		
			while ($rowT = mysql_fetch_array($resultadoT)) {
			
				// buscar si ya viene repetido para informar.
				$estado = $rowT['estado'];
				$trans_zone = $rowT['trans_zone'];
				$RM01 = $rowT['RM01'];
				$RM02 = $rowT['RM02'];
				$RM06 = $rowT['RM06'];
				$origen = $rowT['origen'];
				$disponible_solo_origen = $rowT['disponible_solo_origen'];
				
				$resultadoB = mysql_query("SELECT 1 FROM precio_entrega WHERE estado = '$estado' AND trans_zone = '$trans_zone'");
				$encB = mysql_num_rows($resultadoB);
				
				if ($encB>0) {
					$total_rep ++;
					$bitacora_repetidos .= $cp." - ".$colonia."<br>";
				} else {
					$query = "INSERT INTO cp (cp, estado, ciudad, trans_zone, colonia) VALUES ('$cp', '$estado', '$ciudad', '$trans_zone', '$colonia')";
					$resultado = mysql_query($query,$conexion);
					$ins = mysql_affected_rows();
					if ($ins>0) $total_act ++;
				}
			} 				


			// insertar de temporal a tabla de produccion
						
/*			$query = 'INSERT INTO precio_entrega (estado, trans_zone, RM01, RM02, RM06, origen, disponible_solo_origen) SELECT estado, trans_zone, RM01, RM02, RM06, origen, disponible_solo_origen FROM temp_precio_entrega';
			$resultado = mysql_query($query,$conexion);
			$total_act = mysql_affected_rows();
*/
			$mensaje .= '<br>Se actualizaron '.$total_act.' registros en tabla de precios de entrega LTL';

		  
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
	<? $tit='Importar y Actualizar Precios de Entrega (LTL)'; include('top.php'); ?>
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
