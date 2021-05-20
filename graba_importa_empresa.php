<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=6;
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
	
	$nombrearchivo = './imp_odc/formas_pago.csv';
	
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
		
		//$direc = $rowCFG['wwwroot']."admin/imp_odc/formas_pago.csv";
		//$direc = "x:/imp_puntos/archivo_puntos.txt";
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . "/twpos/admin/imp_odc/formas_pago.csv";
	    $direc =  str_replace('//','/',$targetPath);
		
		
		$resultado = mysql_query("DELETE FROM temp_empresa_formas_pago",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_empresa_formas_pago",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA  INFILE '$direc' INTO TABLE temp_empresa_formas_pago FIELDS TERMINATED BY ',' IGNORE 2 LINES";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $resultado = mysql_query("SELECT 1 FROM temp_empresa_formas_pago",$conexion);
		  $enc = mysql_num_rows($resultado);
		  unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros..<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de formas de pago';

			
			// eliminar puntos anteriores de todos los empleados 
			// NO ELIMINAR; por si suben actualización parcial por empresa
//			$resultado = mysql_query("UPDATE cliente SET puntos = 0");
			
			$query = 'SELECT * FROM temp_empresa_formas_pago';
			$resultado = mysql_query($query,$conexion);
			$total_act =0;
			$total_err =0;
			$log_correcto = '';
			$log_incorrecto = '';
			
			while ($row = mysql_fetch_array($resultado)) {
				$empresa = $row['dominios'];

				$clabe = (strtoupper($row['clabe'])=="SI")?1:0;
				$cheque = (strtoupper($row['cheque'])=="SI")?1:0;
				$cep = (strtoupper($row['cep'])=="SI")?1:0;
				$odc = (strtoupper($row['odc'])=="SI")?1:0;
				$puntos = (strtoupper($row['puntos'])=="SI")?1:0;
				$msi03 = (strtoupper($row['msi03'])=="SI")?1:0;
				$msi06 = (strtoupper($row['msi06'])=="SI")?1:0;
				$msi09 = (strtoupper($row['msi09'])=="SI")?1:0;
				$msi10 = (strtoupper($row['msi10'])=="SI")?1:0;
				$msi12 = (strtoupper($row['msi12'])=="SI")?1:0;
				$msi18 = (strtoupper($row['msi18'])=="SI")?1:0;
				$msi24 = (strtoupper($row['msi24'])=="SI")?1:0;

				$query_ins = "UPDATE `empresa`
				SET
				`pago_debito` = $clabe,
				`pago_cheque` = $cheque,
				`pago_cep` = $cep,
				`pago_odc` = $odc,
				`puntos` = $puntos,
				`msi03` = $msi03,
				`msi06` = $msi06,
				`msi09` = $msi09,
				`msi10` = $msi10,
				`msi12` = $msi12,
				`msi18` = $msi18,
				`msi24` = $msi24
				WHERE clave=$empresa" ;
				//echo $query_ins;
					$resultado_ins = mysql_query($query_ins,$conexion);
					
					$act = mysql_affected_rows();
					if ($act >0) {
						$total_act ++;
						// $log_correcto .= $nombre." (".$empleado.") Puntos: ".$saldo."<br>";
					} else {
						$total_err ++;
						// $log_incorrecto .= $nombre." (".$empleado.") Puntos: ".$saldo."<br>";
					}
					


				
				
				
			} // while
			$mensaje .= '<br>Se actualizaron las formas de pago de '.$total_act.' empresas';
			$mensaje .= '<br>No se actualizaron las formas de pago de '.$total_err.' empresas';

		  
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
   document.forma.action='lista_empresa.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Importar y Actualizar Formas de Pago'; include('top.php'); ?>
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
