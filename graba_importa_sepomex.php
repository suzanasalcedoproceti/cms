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

	//ini_set ('error_reporting', E_ALL);
	//ini_set ("display_errors","0" );

	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');
	
	$nombrearchivo = './imp_pre/cp_sepomex.txt';
	
	if (!file_exists($nombrearchivo)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor...<br>'.$nombrearchivo;
		
    } else {
		$subido=TRUE; 
	} // si hay archivo a subir
	
	if ($subido) {  // si se subió el archivo
		
		include("../conexion.php");
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_pre/cp_sepomex.txt";
		

		$resultado = mysql_query("REPAIR TABLE temp_sepomex",$conexion);
		$resultado = mysql_query("DELETE FROM temp_sepomex",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_sepomex",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_sepomex FIELDS TERMINATED BY '|' LINES TERMINATED BY '\r\n' IGNORE 2 LINES ";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $resultado = mysql_query("SELECT 1 FROM temp_sepomex",$conexion);
		  $enc = mysql_num_rows($resultado);
		  unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros..<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de CPs Sepomex';

			
			// eliminar tabla CPs Sepomex
			$resultado = mysql_query("DELETE FROM cp_sepomex",$conexion);

			// Insertar desde temporal
			$resultadoT = mysql_query("INSERT INTO cp_sepomex (SELECT codigo, asenta, tipo_asenta, mnpio, estado, ciudad, cp, estado2, mnpio2, asentacpcons, cve_ciudad FROM temp_sepomex)",$conexion);
			$afe = mysql_affected_rows();

			$resultadoU = mysql_query("UPDATE cp_sepomex SET asenta=UPPER(asenta) where codigo<>''",$conexion);

			
			$mensaje .= '<br>Se insertaron '.$afe.' en tabla de C.P. SEPOMEX';

		  
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
	<? $tit='Importar y Actualizar CPs SEPOMEX'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" >
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
