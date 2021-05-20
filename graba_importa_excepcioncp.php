<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=9;
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

	ini_set('post_max_size','20M');
	ini_set('upload_max_filesize','20M');
	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');
	
	// Errores devueltos por $_FILES
	$errores = array();
	$errores[1]='*El archivo excede '.ini_get('upload_max_filesize').'.';  // excede el parámetro upload_max_filesize en php.ini
	$errores[2]='*El archivo excede '.$_POST['MAX_FILE_SIZE'].'.';  // excede el parámetro MAX_FILE_SIZE en la form
	$errores[3]='*El archivo se subi&oacute; parcialmente.';
	$errores[4]='*No se subi&oacute; el archivo.';
	
	$subido=FALSE;
	
	$nombrearchivo = './imp_excepcioncp/archivo_excepcioncp.csv';
	
	if (!file_exists($nombrearchivo)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor.<br>';
		
    } else {
		$subido=TRUE; 
		//copy('./imp_puntos/archivo_puntos.txt','x:/imp_puntos/archivo_puntos.txt');
	} // si hay archivo a subir
	
	if ($subido) {  // si se subió el archivo
		
		include("../conexion.php");
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_excepcioncp/archivo_excepcioncp.csv";
		
		$resultado = mysql_query("DELETE FROM temp_excepcioncp",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_excepcioncp",$conexion);
		$enc = mysql_num_rows($resultado);
		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {

		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_excepcioncp FIELDS TERMINATED BY ',' IGNORE 0 LINES";

		  $resultado = mysql_query($query,$conexion);

		  $resultado = mysql_query("SELECT 1 FROM temp_excepcioncp",$conexion);
		  $enc = mysql_num_rows($resultado);
		  @unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros';
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal';
			
			// pasar a tabla de productos
			
			$query = 'SELECT * FROM temp_excepcioncp ';
			$resultado = mysql_query($query,$conexion);
			$validos = array();
			while ($row = mysql_fetch_assoc($resultado)) {
				$validos[] = $row;
				
			} // while
			
			$total_act =0;
			$total_ins =0;
			foreach ($validos as $row) {
				$cp = $row['cp'];
				$tipo_producto = $row['tipo_producto'];
				$idservicio = $row['idservicio'];

				$query_val = "SELECT idexcepciones_cp FROM excepciones_cp where cp='$cp' and tipo_producto='$tipo_producto' and idservicio='$idservicio'";
					$resultadoval = mysql_query($query_val,$conexion);
		  			$encval = mysql_num_rows($resultadoval);

					if ($encval<=0){
						 
						// INSERT$error .= 'No se insertaron registros..<br>'.$error_my;
						$query_ins = "INSERT INTO excepciones_cp(cp,tipo_producto,idservicio)
						VALUES ($cp,'$tipo_producto',$idservicio)";
						$resultado_ins = mysql_query($query_ins,$conexion);
						$total_ins++;
						 
					}
					else {
						// UPDATE$total_importados1 = $enc;
						// 
						$row = mysql_fetch_row($resultadoval);
						$idexcepcioncp = $row[0];
						$query_upd = "UPDATE excepciones_cp SET cp=$cp, tipo_producto='$tipo_producto', idservicio=$idservicio 
						WHERE idexcepciones_cp=$idexcepcioncp";
						$resultado_upd = mysql_query($query_upd,$conexion);
						$total_act++;

						 
					}
			}

			$mensaje .= '<br>Se actualizaron '.$total_act.' CPs en tabla de excepciones_cp';
			$mensaje .= '<br>Se insertaron '.$total_ins.' CPs en tabla de excepciones_cp';
		  
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
	<? $tit='Importar Precios'; include('top.php'); ?>
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