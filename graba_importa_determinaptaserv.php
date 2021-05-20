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


	//ini_set ('error_reporting', E_ALL);
	//ini_set ("display_errors","1" );

	ini_set('max_execution_time','1000000');
	ini_set('max_input_time','1000000');
	
	$nombrearchivo = './imp_dptaserv/archivo_determinaptaserv.csv';
	
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
		
	   //$direc = $rowCFG['wwwroot']."admin/imp_pre/archivo_clas.txt";
		$direc = $rowCFG['wwwroot']."admin/imp_dptaserv/archivo_determinaptaserv.csv";
			//$direc = "c:xampp/htdocs/twpos/admin/imp_dptaserv/archivo_determinaptaserv.csv";
		
		//$direc = "x:/imp_puntos/archivo_puntos.txt";
		//VALIDAR ARCHIVO COBERTURA
		
		//estado,municipio,servicio,tipo producto,cobertura
		
		$fn = fopen($direc,"r");
  		$linea_error = array();
  		$le=0;
		while(! feof($fn))  {
			$linea = fgets($fn);
			$lin = explode(",",$linea);
			if(count($lin)!=4)
			$linea_error[$le]=["linea"=>$linea,"error"=>"Tamaño incorrecto. "];
			
			if(!isset($linea_error[$le])){
				//Validar Estado
				$error_data='';
				if(!preg_match('/^[\d]+$/', trim($lin[0])))
				{
					$error_data.='Columna Cluster no valido. ';
				}
				//Validar Municipio
				if(!preg_match('/^[A-Za-záéíóúÁÉÍÓÚ\s]+$/', trim($lin[1])))
				{
					$error_data.='Columna Tipo Producto no valido. ';
				}
				
				//Validar Tipo producto
				 /*	 if(!preg_match('/^[A-Za-záéíóúÁÉÍÓÚ\s]+$/', trim($lin[2])))
				{
					$error_data.='Columna Servicio no valido. ';
				}


				//Validar cobertura
               	
					if(!preg_match('/^(?:\d+|\d*\.\d+)$/', trim($lin[4])))
				{
					$error_data.='Columna Precio no valido. ';
				}*/
				if($error_data!='')
				{
					$linea_error[$le]=["linea"=>$linea,"error"=>$error_data];
				}
			}
			$le++;
		}

		fclose($fn);
		//print_r($linea_error);
		//exit();
	if(count($linea_error)==0)
		{
		$resultado = mysql_query("DELETE FROM temp_determina_plantaservicio",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_determina_plantaservicio",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_determina_plantaservicio FIELDS TERMINATED BY ','";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $resultado = mysql_query("SELECT 1 FROM temp_determina_plantaservicio",$conexion);
		  $enc = mysql_num_rows($resultado);
		  unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros..<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de determina planta servicio';

			
			$query = 'SELECT tdpt.*,IF(tdpt.cluster >= 1 and tdpt.cluster <= 32, tdpt.cluster,0) as clusterv ,serv.idservicio as idserviciov, tp.nombre as tipov  FROM temp_determina_plantaservicio tdpt
			left join servicios serv on tdpt.servicio=serv.descripcion
			left join tipo_producto tp on tdpt.tipo_producto=tp.clave;';
			//echo $query;
			$resultado = mysql_query($query,$conexion);
			$validos = array();
			$le=0;
			while ($row = mysql_fetch_array($resultado)) {
				$cluster = $row['clusterv'];
				$idservicio = $row['idserviciov'];
				$tipo_producto = $row['tipov'];
				$cedis = $row['cedis'];
				$error_data='';
				//Validar Estado
				if($cluster==0)
				{
					$error_data.='Columna Cluster no valido. ';
				}

				//Validar Municipio
				if(!$idservicio)
				{
					$error_data.='Columna Servicio no valido. ';
				}
				//Validar Servicio
				if(!$tipo_producto)
				{
					$error_data.='Columna Tipo Producto no valido. ';
				} 

				if($error_data!='')
				{
					$linea=$row['cluster'].",".$row['tipo_producto'].",".$row['servicio'].",".$row['cedis'];
					$linea_error[$le]=["linea"=>$linea,"error"=>$error_data];
				}
				else
				{
					$validos[] = $row;
				}
				$le++;	
 
				
			} // while
			if(count($linea_error)==0)
			{
				$total_act =0;
				$total_ins =0;
				$total_err =0;
				foreach ($validos as $row) {
					$cluster = $row['clusterv'];
					$idservicio = $row['idserviciov'];
					$tipo_producto = $row['tipov'];
					$cedis = $row['cedis'];

					$query_val = "SELECT idplantaservicio FROM determina_plantaservicio where cluster=$cluster and idServicio=$idservicio and tipo_producto='$tipo_producto' and Cedis='$cedis'";
					$resultadoval = mysql_query($query_val,$conexion);
		  			$encval = mysql_num_rows($resultadoval);

					if ($encval<=0){ 
						 
						// INSERT$error .= 'No se insertaron registros..<br>'.$error_my;
						$query_ins = "INSERT INTO determina_plantaservicio(cluster,tipo_producto,idservicio,Cedis)
						VALUES ($cluster,'$tipo_producto',$idservicio,'$cedis')";
						$resultado_ins = mysql_query($query_ins,$conexion);
						$total_ins++;
						 
					}
					else {
						// UPDATE$total_importados1 = $enc;
						// 
						$row = mysql_fetch_row($resultadoval);
						$idplantaservicio = $row[0];
						$query_upd = "UPDATE determina_plantaservicio SET cluster=$cluster, idservicio=$idservicio, tipo_producto='$tipo_producto', cedis='$cedis' 
						WHERE idplantaservicio=$idplantaservicio";
						$resultado_upd = mysql_query($query_upd,$conexion);
						$total_act++;

						 
					}
				}
			}
			$mensaje .= '<br>Se actualizaron '.$total_act.' registros';
			$mensaje .= '<br>Se insertaron '.$total_ins.' registros';
		  
		  } // si hay registros en temporal
		} // No encontro errores validacion
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
   document.forma.action='lista_determinaptaserv.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Importar y Actualizar Precios Servicios'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma" enctype="multipart/form-data">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><? if (count($linea_error)>0){
            	echo "Errores en el archivo:<br><br>";
             foreach ($linea_error as $key => $value) {
             	echo 'Linea '.($key+1).': '.$value['error'].'Datos: '.$value['linea'].' <br>';
             }
             } elseif($error!='') 
            { 
            	echo 'Error.<br>'.$error;
            } else 
            { 
            	echo 'Archivo Subido.<br>'.$mensaje;
         	} ?></td>
          </tr>
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
