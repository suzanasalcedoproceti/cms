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
	
	$nombrearchivo = './imp_pre/archivo_clas.txt';
	
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
		
		//$direc = $rowCFG['wwwroot']."admin/imp_pre/archivo_clas.txt";
		$direc = $rowCFG['wwwroot']."adminMERGE/imp_pre/archivo_clas.txt";
		
		$resultado = mysql_query("DELETE FROM temp_clas",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_clas",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA INFILE '$direc' INTO TABLE temp_clas FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 0 LINES";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $resultado = mysql_query("SELECT 1 FROM temp_clas",$conexion);
		  $enc = mysql_num_rows($resultado);
		  unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros..<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de clasificación';

			
			// eliminar contenido de tabla de clasificacion
			
			$resultado = mysql_query("DELETE FROM producto_clasificacion",$conexion);
			
			// recorrer temporal
			$resultadoT = mysql_query("SELECT * FROM temp_clas");
			$total_rep = 0;
			$total_act = 0;
			$bitacora_repetidos = '';
			while ($rowT = mysql_fetch_array($resultadoT)) {
			
				// buscar si ya viene repetido para informar.
				$producto = $rowT['producto'];
				$tipo = $rowT['tipo'];
				$costo = $rowT['costo']+0;
				$resultadoB = mysql_query("SELECT 1 FROM producto_clasificacion WHERE producto = '$producto'");
				$encB = mysql_num_rows($resultadoB);
				
				if ($encB>0) {
					$total_rep ++;
					$bitacora_repetidos .= $producto."<br>";
				} else {
					$query = "INSERT INTO producto_clasificacion (producto, tipo, costo) VALUES ('$producto', '$tipo', $costo)";
					$resultado = mysql_query($query,$conexion);
					$ins = mysql_affected_rows();
					if ($ins>0) $total_act ++;
				}
			} 				
					
			
			// insertar de temporal a tabla de produccion
/*			$query = 'INSERT INTO producto_clasificacion (producto, tipo, costo) SELECT producto, tipo, costo FROM temp_clas';
			$resultado = mysql_query($query,$conexion);
			$total_act = mysql_affected_rows();
*/
			$mensaje .= '<br>Se actualizaron '.$total_act.' registros en tabla de clasificacion (LOW / LTL); Se encontraron '.$total_rep.' registros repetidos';
			if ($bitacora_repetidos)
				$mensaje .= '<br><br><strong>Repetidos:</strong><br>'.$bitacora_repetidos;

		  
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
	<? $tit='Importar y Actualizar Clasificación (LOW/LTL)'; include('top.php'); ?>
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
            <td><input name="descartar" type="button" class="boton" onclick="descarta();" value="SALIR" />            </td>
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
