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
	
	$nombrearchivo = './imp_oc/archivo_cobertura.csv';
	
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
		$direc = $rowCFG['wwwroot']."admin/imp_oc/archivo_cobertura.csv";
		//$direc = "x:/imp_puntos/archivo_puntos.txt";
		//VALIDAR ARCHIVO COBERTURA
		
		//estado,municipio,servicio,tipo producto,cobertura
		
		$fn = fopen($direc,"r");
  		$linea_error = array();
  		$le=0;
		while(! feof($fn))  {
			$linea = fgets($fn);
			$lin = explode(",",$linea);
			if(count($lin)!=5)
			$linea_error[$le]=["linea"=>$linea,"error"=>"Tamaño incorrecto. "];
			
			if(!isset($linea_error[$le])){
				//Validar Estado
				$error_data='';
				 
				//Validar cobertura
				if(!in_array(strtolower(trim($lin[4])), ['si','no']))
				{
					$error_data.='Columna Cobertura no valido. ';
				}
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
		$resultado = mysql_query("DELETE FROM temp_cobertura",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_cobertura",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_cobertura FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 0 LINES";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $resultado = mysql_query("SELECT 1 FROM temp_cobertura",$conexion);
		  $enc = mysql_num_rows($resultado);
		  unlink($direc);
		  if ($enc<=0) 
			$error .= 'No se insertaron registros..<br>'.$error_my;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = 'Se subieron '.$enc.' registros a tabla temporal de cobertura';

			
			// eliminar puntos anteriores de todos los empleados 
			// NO ELIMINAR; por si suben actualización parcial por empresa
           //	SELECT tc.*,edos.cve_estado,mpos.cve_mnpio,serv.idservicio, tp.nombre FROM temp_cobertura tc
			//left join estados edos on tc.estado=edos.estado
			//left join municipios mpos on tc.municipio=mpos.mnpio
			//left join servicios serv on tc.servicio=serv.descripcion
			//left join tipo_producto tp on tc.tipo_producto=tp.clave;
			
			$query = 'SELECT DISTINCT tc.*,edos.cve_estado,edos.cve_mnpio,serv.idservicio, tp.nombre FROM temp_cobertura tc 
					  left join cp_sepomex edos on (tc.estado=edos.estado and  tc.municipio=edos.mnpio)
					  left join servicios serv on tc.servicio=serv.descripcion 
					  left join tipo_producto tp on tc.tipo_producto=tp.clave;';
			$resultado = mysql_query($query,$conexion);
			$validos = array();
			$le=0;
			while ($row = mysql_fetch_array($resultado)) {
				$cve_estado = $row['cve_estado'];
				$cve_mnpio = $row['cve_mnpio'];
				$idservicio = $row['idservicio'];
				$tipo_producto = $row['nombre'];
				$error_data='';
				//Validar Estado
				$error_data='';
				if(!$cve_estado)
				{
					$error_data.='Columna Estado no valido. ';
				}
				//Validar Municipio
				if(!$cve_mnpio)
				{
					$error_data.='Columna Municipio no valido. ';
				}
				//Validar Servicio
				if(!$idservicio)
				{
					$error_data.='Columna Servicio no valido. ';
				}
				//Validar Tipo producto
				if(!$tipo_producto)
				{
					$error_data.='Columna Tipo Producto no valido. ';
				}
				if($error_data!='')
				{
					$linea=$row['estado'].",".$row['municipio'].",".$row['servicio'].",".$row['tipo_producto'].",".$row['cobertura'];
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
					$cve_estado = $row['cve_estado'];
					$cve_mnpio = $row['cve_mnpio'];
					$idservicio = $row['idservicio'];
					$tipo_producto = $row['nombre'];
					$cobertura = strtoupper($row['cobertura']);
					$query_val = "SELECT idcobertura FROM cobertura where cve_estado=$cve_estado and cve_mnpio=$cve_mnpio
					and idServicio=$idservicio and tipo_producto='$tipo_producto'";
					$resultadoval = mysql_query($query_val,$conexion);
		  			$encval = mysql_num_rows($resultadoval);
					if ($encval<=0){ 
						// INSERT$error .= 'No se insertaron registros..<br>'.$error_my;
						$query_ins = "INSERT INTO cobertura(cve_estado,cve_mnpio,idServicio,tipo_producto,cobertura) VALUES ($cve_estado,$cve_mnpio
					,$idservicio,'$tipo_producto','$cobertura')";
						$resultado_ins = mysql_query($query_ins,$conexion);
						$total_ins++;
					}
					else {
						// UPDATE$total_importados1 = $enc;
						// 
						$row = mysql_fetch_row($resultadoval);
						$idcobertura = $row[0];
						$query_upd = "UPDATE cobertura SET cve_estado=$cve_estado,cve_mnpio=$cve_mnpio,
					idServicio=$idservicio,tipo_producto='$tipo_producto',cobertura='$cobertura' WHERE idcobertura=$idcobertura";
						$resultado_upd = mysql_query($query_upd,$conexion);
						$total_act++;
					}
				}
			}
			$mensaje .= '<br>Se actualizaron '.$total_act.' registros';
			$mensaje .= '<br>Se insertaron '.$total_ins . ' registros';
		  
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
   document.forma.action='lista_cobertura.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Importar y Actualizar Cobertura'; include('top.php'); ?>
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
