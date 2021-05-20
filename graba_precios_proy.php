<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=31;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	$error = '';

	ini_set ('error_reporting', E_ALL);
	ini_set ("display_errors","0" );

	ini_set('max_execution_time','10000');
	ini_set('max_input_time','10000');
	
	$nombrearchivo = './imp_pre/precios_proy.xlsx';
	
	if (!file_exists($nombrearchivo)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor...<br>'.$nombrearchivo;
		
    } else {
		$subido=TRUE; 
	} // si hay archivo a subir
	
	if ($subido) {  // si se subió el archivo
		
		include("../conexion.php");
		
		


		// leer archivo xlsx y recorrer sus registros
		

		/** Clases necesarias */
	
		require_once('phpExcel/Classes/PHPExcel.php');
	
		require_once('phpExcel/Classes/PHPExcel/Reader/Excel2007.php');
	
		// Cargando la hoja de cálculo
		$objReader = new PHPExcel_Reader_Excel2007();
	
		$objPHPExcel = $objReader->load($nombrearchivo);
	
		$objFecha = new PHPExcel_Shared_Date();
	
		// Asignar hoja de excel activa
	
		$objPHPExcel->setActiveSheetIndex(0);
	
		// Llenamos el arreglo con los datos  del archivo xlsx
	
		$log = '';
		$i=1;
		while (true) {
			
			$i++; // comenzar en el 2
	
			$sku = trim($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue());
			if (!$sku) {
				break;
			}
			$precio_LH = (float) trim($objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue());
			$precio_LG = (float) trim($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue());
			$precio_LF = (float) trim($objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue());
			$precio_T2 = (float) trim($objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue());
			$precio_T5 = (float) trim($objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue());

			$log .= "<br>SKU: <strong> ".$sku."</strong> --> " ;
			$log .=  " <strong>LH:</strong> $".$precio_LH;
			$log .=  " <strong>LG:</strong> $".$precio_LG;
			$log .=  " <strong>LF:</strong> $".$precio_LF;
			$log .=  " <strong>T2:</strong> $".$precio_T2;
			$log .=  " <strong>T5:</strong> $".$precio_T5;
			
			$resultado = mysql_query("SELECT 1 FROM producto WHERE modelo = '$sku' LIMIT 1",$conexion);
			$enc = mysql_num_rows($resultado);
			if ($enc>0) {
				$query = "UPDATE producto SET precio_LH = $precio_LH, precio_LG = $precio_LG, precio_LF = $precio_LF, precio_T2 = $precio_T2, precio_T5 = $precio_T5, act = 1-act WHERE modelo = '$sku' LIMIT 1";
				$resultado= mysql_query($query,$conexion);
				$act = mysql_affected_rows();
				if ($act>0) {
					$log.='.... <span style="color:BLUE">Actualizado</span>';
				} else {
					$log .='... <span style="color:RED">no se pudo actualizar </span> '.$query;
				}
			} else {
				$log.= '.... <span style="color:RED">Error: SKU no encontrado en TW </span>';
			}
	
		}


	} // archivo subido
	file_put_contents("./imp_pre/backup/".date("Y-m-d h:i:s").".txt",$log);

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
	<? $tit='Importar y Actualizar lista de precios de PROYECTOS'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" >
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><? if ($error) echo $error; else echo 'Archivo Procesado.<br>'.$log; ?></td>
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
