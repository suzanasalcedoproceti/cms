<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=4;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este m?dulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	$error = '';
	$size=20000;  // tama?o m?ximo en Kb


ini_set ('error_reporting', E_ALL);
ini_set ("display_errors","1" );

	ini_set('post_max_size','20M');
	ini_set('upload_max_filesize','20M');
	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');
	
	// Errores devueltos por $_FILES
	$errores = array();
	$errores[1]='*El archivo excede '.ini_get('upload_max_filesize').'.';  // excede el par?metro upload_max_filesize en php.ini
	$errores[2]='*El archivo excede '.$_POST['MAX_FILE_SIZE'].'.';  // excede el par?metro MAX_FILE_SIZE en la form
	$errores[3]='*El archivo se subi&oacute; parcialmente.';
	$errores[4]='*No se subi&oacute; el archivo.';
	
	$subido=FALSE;
	
	if ($_FILES['archivo']['name']!="") {  // si hay archivo a subir

	  if ($_FILES['archivo']['error']>0) { $error.=$errores[$_FILES['archivo']['error']].'<br>'; }
	  else {
		if ($_FILES['archivo']['size']>($size*1024)) { $error.='La foto excede '.$size.' Kb.<br>'; }
		if ($_FILES['archivo']['type']!="text/plain") { $error.='S&oacute;lo se pueden subir archivos TXT.<br>'; } 
	  }
	  
	  if (!$error) {
		$nombrearchivo = './imp_pre/archivo_exist.txt';
		if (file_exists($nombrearchivo)) unlink($nombrearchivo);
		
		if (is_uploaded_file($_FILES['archivo']['tmp_name'])) {
			copy($_FILES['archivo']['tmp_name'], $nombrearchivo);
			$subido=TRUE; 
		}
		else $error.='Problema al subir el archivo.<br>';
	  }
	  
	} // si hay archivo a subir
	
	if ($subido) {  // si se subi? el archivo
		
		include("../conexion.php");
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_pre/archivo_exist.txt";
		
		$resultado = mysql_query("DELETE FROM temp_exist",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_exist",$conexion);
		$enc = mysql_num_rows($resultado);
		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {

		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_exist FIELDS TERMINATED BY ',' IGNORE 0 LINES";

		  $resultado = mysql_query($query,$conexion);

		  $resultado = mysql_query("SELECT 1 FROM temp_exist",$conexion);
		  $enc = mysql_num_rows($resultado);
		  unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros';
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal';
			
			// eliminar tabla existencias
			$resultado = mysql_query("DELETE FROM existencia",$conexion);

			// recorrer temporal para ir agregando a tabla existencia
						
			$query = 'INSERT INTO existencia (producto, cedis, loc, existencia, estatus, act) SELECT producto, cedis, loc, existencia, estatus, act FROM temp_exist';
			$resultado = mysql_query($query,$conexion);
			$total_act = mysql_affected_rows();

			$mensaje .= '<br>Se procesaron '.$total_act.' registros en tabla de existencias';

			// actualizar total de productos y estatus en tabla producto  (resurtible, mostrar)
			
			$query = 'SELECT * FROM producto ORDER BY modelo';
			$resultado = mysql_query($query,$conexion);
			$total_upd = 0;
			while ($row = mysql_fetch_array($resultado)) {
			
				$producto = $row['modelo'];
				$query = "SELECT SUM(existencia) AS total_ex, estatus FROM existencia WHERE producto = '$producto' GROUP BY producto ";
				$resultado_acum = mysql_query($query,$conexion);
				$row_acum = mysql_fetch_array($resultado_acum);
				$total_ex = $row_acum['total_ex'];
				$estatus = $row_acum['estatus'];
				if ($total_ex <= 0 && $estatus != 30) {
					$mostrar = 0;
				} else $mostrar = 1;
				if ($estatus == 30) $resurtible = 1; else $resurtible = 0;
				
				// actualizar producto
				$query_upd = mysql_query("UPDATE producto SET mostrar = $mostrar, resurtible = $resurtible, act=1-act WHERE modelo = '$producto' LIMIT 1",$conexion);
				$upd = mysql_affected_rows();
				if ($upd >0)
					$total_upd ++;
					
				
			} // while
			$mensaje .= '<br>Se actualiz? el estatus y global de existencias de '.$total_upd.' productos en tabla de productos';

		  
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
	<? $tit='Importar Existencias'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><? if ($error) echo $error; else echo 'Archivo Subido.<br>'.$mensaje; ?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="descartar" type="button" class="boton" onclick="descarta();" value="REGRESAR" />            </td>
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
