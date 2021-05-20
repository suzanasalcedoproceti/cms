<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=19;
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
	
	$nombrearchivo = './imp_puntos_convenio/archivo_puntos_convenio.txt';
	
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
		
		$direc = $rowCFG['wwwroot']."admin/imp_puntos_convenio/archivo_puntos_convenio.txt";
		//$direc = "x:/imp_puntos/archivo_puntos.txt";
		
		
		$resultado = mysql_query("DELETE FROM temp_puntos_convenio",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_puntos_convenio",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_puntos_convenio FIELDS TERMINATED BY ',' IGNORE 0 LINES";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $resultado = mysql_query("SELECT 1 FROM temp_puntos_convenio",$conexion);
		  $enc = mysql_num_rows($resultado);
		  unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros..<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de puntos';

			
			// eliminar puntos anteriores de todos los empleados 
			// NO ELIMINAR; por si suben actualización parcial por empresa
//			$resultado = mysql_query("UPDATE cliente SET puntos = 0");
			
			$query = 'SELECT * FROM temp_puntos_convenio ORDER BY empresa';
			$resultado = mysql_query($query,$conexion);
			$total_act =0;
			$total_err =0;
			$total_puntos=0;
			$total_puntos_aplicados=0;
			$clave_empresa = 0;
			$genera_puntos = 0;
			$total_emp = 0;
			$log_correcto = '';
			$log_incorrecto = '';
			
			while ($row = mysql_fetch_array($resultado)) {
				
				$empresa = $row['empresa'];
				$empleado = $row['empleado'];
				$nombre = $row['nombre'];
				$saldo = $row['saldo'];
				$total_puntos += $saldo;
				
				if ($clave_empresa != $empresa) { // buscar una sola vez cada empresa
					// buscar clave de empresa
					$resultadoEMPR = mysql_query("SELECT clave, puntos FROM empresa WHERE cliente_sap = '$empresa' and empresa_whirlpool=1");
					$rowEMPR = mysql_fetch_array($resultadoEMPR);
					$clave_empresa = $empresa;
					$clave_web = $rowEMPR['clave'];
					$genera_puntos = $rowEMPR['puntos'];
					
					if ($genera_puntos) { 
						$total_emp++;
					}
				}
				
				if ($clave_empresa > 0) {
					$query_ins = "UPDATE cliente SET 
									puntos_convenio = $saldo,
									act = 1-act
								 WHERE empresa = $clave_web AND numero_empleado = '$empleado' LIMIT 1";
					$resultado_ins = mysql_query($query_ins,$conexion);
					
					$act = mysql_affected_rows();
					if ($act >0) {
						$total_act ++;
						$total_puntos_aplicados += $saldo;
						// $log_correcto .= $nombre." (".$empleado.") Puntos: ".$saldo."<br>";
					} else {
						$total_err ++;
						// $log_incorrecto .= $nombre." (".$empleado.") Puntos: ".$saldo."<br>";
					}
					
				} // if genera puntos
				
				
			} // while
			$mensaje .= '<br>Se actualizaron los puntos de '.$total_act.' empleados de '.$total_emp.' empresas diferentes';
			$mensaje .= '<br>Total de puntos en el archivo de importación: '.number_format($total_puntos,0);
			$mensaje .= '<br>Total de puntos aplicados: '.number_format($total_puntos_aplicados,0);

		  
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
	<? $tit='Importar y Actualizar Puntos Convenio'; include('top.php'); ?>
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
