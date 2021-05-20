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
	
	$nombrearchivo = './imp_oc/archivo_subcluster.csv';
	
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
		$direc = $rowCFG['wwwroot']."admin/imp_oc/archivo_subcluster.csv";
		//$direc = "x:/imp_puntos/archivo_puntos.txt";
		
		//$direc = "x:/imp_puntos/archivo_puntos.txt";
		//VALIDAR ARCHIVO COBERTURA
		
		//estado,municipio,servicio,tipo producto,cobertura
  
		 
		}

	 
		print_r($linea_error);
		//exit();
		if(count($linea_error)==0)
		{
		$resultado1 = mysql_query("DELETE FROM temp_subcluster",$conexion);
		$enc3 = mysql_num_rows($resultado1);
		$resultado2 = mysql_query("SELECT 1 FROM temp_subcluster",$conexion);
		$enc = mysql_num_rows($resultado2);
	 
 
		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_subcluster FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 0 LINES";

		  $resultado3 = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();
	 
		  $resultado4 = mysql_query("SELECT 1 FROM temp_subcluster",$conexion);
		  $enc = mysql_num_rows($resultado4);
		  unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros..<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de precios servicios';
             }

			// eliminar puntos anteriores de todos los empleados 
			// NO ELIMINAR; por si suben actualización parcial por empresa
//			$resultado = mysql_query("UPDATE cliente SET puntos = 0");
			 

			$query = 'SELECT DISTINCT tps.*,IF(tps.cluster >= 1 and tps.cluster <= 32, tps.cluster,0) as clusterv,tps.subcluster as subclusterv,edos.cve_estado,edos.cve_mnpio
				FROM temp_subcluster tps left join cp_sepomex edos on tps.estado=edos.estado and tps.municipio=edos.mnpio';
			 
			$resultado = mysql_query($query,$conexion);
			$validos = array();
			$le=0;
			while ($row = mysql_fetch_array($resultado)) {
				$cluster = $row['clusterv'];
				$subcluster = $row['subclusterv'];
				$cve_estado = $row['cve_estado'];
				$cve_mnpio = $row['cve_mnpio'];
				$error_data='';
				//Validar Estado
				if($cluster==0)
				{
					$error_data.='Columna Cluster no valido. ';
				}
				//Validar Municipio
				if(!$subcluster)
				{
					$error_data.='Columna Servicio no valido. ';
				}
				//Validar Servicio
				if(!$cve_estado)
				{
					$error_data.='Columna Tipo Producto no valido. ';
				}
				//Validar Tipo producto
				if(!$cve_mnpio)
				{
					$error_data.='Columna Subtipo Producto no valido. ';
				}
				if($error_data!='')
				{
					$linea=$row['cluster'].",".$row['subcluster'].",".$row['subtipo_producto'].",".$row['cve_estado'].",".$row['cve_mnpio'];
					$linea_error[$le]=["linea"=>$linea,"error"=>$error_data];
				}
				else
				{
					$validos[] = $row;
				}
				$le++;				
				
			}


			if(count($linea_error)==0)
			{
				$total_act =0;
				$total_ins =0;
				$total_err =0;
				foreach ($validos as $row) {
					$cluster = $row['clusterv'];					
					$subcluster = $row['subclusterv'];
					$cve_estado = $row['cve_estado'];
					$cve_mnpio = $row['cve_mnpio']; 

					$query_val = "SELECT idsubcluster FROM subcluster where cluster=$cluster and subcluster=$subcluster and cve_estado=$cve_estado
					and cve_mnpio=$cve_mnpio";
					$resultadoval = mysql_query($query_val,$conexion);
		  			$encval = mysql_num_rows($resultadoval);
					if ($encval<=0){ 
						// INSERT$error .= 'No se insertaron registros..<br>'.$error_my;
						$query_ins = "INSERT INTO subcluster(cve_estado,cve_mnpio,cluster,subcluster) VALUES ($cve_estado,$cve_mnpio,$cluster,$subcluster)";
						$resultado_ins = mysql_query($query_ins,$conexion);
						$total_ins++;
					}
					else {
						// UPDATE$total_importados1 = $enc;
						// 
						$row = mysql_fetch_row($resultadoval);
						$idsubcluster = $row[0];
						$query_upd = "UPDATE subcluster SET cve_estado,=$cve_estado,cve_mnpio=$cve_mnpio,cluster=$cluster, 
							subcluster=$subcluster WHERE idsubcluster=$idsubcluster";
						$resultado_upd = mysql_query($query_upd,$conexion);
						$total_act++;
					}
				 
				}
			}
        $resultado1 = mysql_query("DELETE FROM temp_precioservicios",$conexion);
		$enc3 = mysql_num_rows($resultado1);
			}
		}

	  $mensaje .= '<br>Se actualizaron '.$total_act.' registros';
	  $mensaje .= '<br>Se insertaron '.$total_ins.' registros';

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
   document.forma.action='lista_subcluster.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Importar y Actualizar Subcluster'; include('top.php'); ?>
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
