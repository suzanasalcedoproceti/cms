<?
	// SCRIPT PARA IMPORTAR DATOS PARA DASHBOARD (SAP) DE ARCHIVO TXT  /imp_dashboard/tawotd.txt separado por |
	// Lo ejecuta un servicio cron, pero antes el BAT que invoca este script copia el archivo del servidor web al servidor Mysql

	$error = '';
	$size=20000;  // tamaño máximo en Kb
	$CR = chr(10);
	$verbose = 1;

	require_once("lib_dashboard.php");

	ini_set ('error_reporting', 'E_ALL ~E_NOTICE');
	ini_set ("display_errors","1" );


	//ini_set('max_execution_time','10000');
	//ini_set('max_input_time','10000');
	ini_set('post_max_size','200M'); ini_set('upload_max_filesize','200M'); ini_set('max_execution_time','200M'); ini_set('max_input_time','200M'); ini_set('memory_limit','200M'); set_time_limit(65536);

	if ($_SERVER['HTTP_HOST']=='localhost' ) {
		$nombrearchivo = './imp_dashboard/tawotd.txt';
		$nombrearchivofull = 'd:/www/whirlpool/admin/imp_dashboard/tawotd.txt';

		$base="whirlpool";
		$conexion=mysql_connect("localhost","root","okap");
		mysql_select_db($base,$conexion);

	} else {
		$nombrearchivo = './imp_dashboard/tawotd.txt';
		$nombrearchivofull = 'd:/inetpub/wwwroot/admin/imp_dashboard/tawotd.txt';

//   	$base="wp_test";
		$base="whirlpool";
		$conexion=mysql_connect("mty-mysqlq01","root","Whr.Web.Soluciones@1");
		mysql_select_db($base,$conexion);
	    mysql_set_charset('latin1', $conexion);

	}
    mysql_set_charset('latin1', $conexion);

	
	if (!file_exists($nombrearchivofull)) {
		$subido=FALSE;
		$error.='No se encontró el archivo en el servidor web.'.$CR;
		echo $error;
		return;
		exit;
		
    } else {
		$subido=TRUE;
	} // si hay archivo a subir


	// guardar log de errores y mensajes
	$mensaje = 'Se inició proceso de importación de datos (SAP) dashboard'.$CR;
	$fecha_hora = date("Y-m-d H:i:s");
	$fecha = date("Y-m-d");
	$hora = date("H:i");
	$resultadoLOG = mysql_query("INSERT log_dashboard (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
	$mensaje = '';

	
	if ($subido) {  // si se subió el archivo
		
		// obtener configuracion
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);
		
		$direc = $rowCFG['wwwroot']."admin/imp_dashboard/tawotd.txt";
		
		$resultado = mysql_query("DELETE FROM temp_dashboard",$conexion);
		$resultado = mysql_query("SELECT 1 FROM temp_dashboard",$conexion);
		$enc = mysql_num_rows($resultado);

		if ($enc>0) 
			$error .= 'No se pudo eliminar temporal anterior';
		else {


		  // OJO, poner ruta absoluta del servidor
		  $query = "LOAD DATA LOCAL INFILE '$direc' INTO TABLE temp_dashboard FIELDS TERMINATED BY '|' IGNORE 0 LINES";
		  
		 // echo "<br>".$query."<br>";

		  $resultado = mysql_query($query,$conexion);
		  $error_my = mysql_errno().": ".mysql_error();

		  $direc = $rowCFG['wwwroot']."admin/imp_dashboard/tawotd.txt";
		  $direc_bk = $rowCFG['wwwroot']."admin/imp_dashboard/backup/tawotd.txt";

		  //unlink($direc);
		  //$mensaje .= 'Se eliminó el archivo TXT'.$CR;
		  
		  $resultado = mysql_query("SELECT 1 FROM temp_dashboard",$conexion);
		  $enc = mysql_num_rows($resultado);

		  if ($enc<=0) 
			$error .= 'No se insertaron registros..'.$CR.$error_my.$CR;
		  else {
		  	$total_importados1 = $enc;
		  	$mensaje = $CR.$CR.'Se subieron '.$enc.' registros a tabla temporal de datos dashboard ';

			$resultado = mysql_query("SET AUTOCOMMIT = 0");
			$resultado = mysql_query("START TRANSACTION");


			$mensaje .= $CR."Inicio: ".date("H:i:s");
		  	$mensaje .= $CR."---------------------------------".$CR;
			$mensaje .= "Pasada 1: Carga Inicial:";

			// crear folio de actualización dashboard, para referencia y actualizar únicamente registros de este folio
			$resultadoDU = mysql_query("INSERT INTO dashboard_updates (fecha, hora, log) VALUES ('$fecha', '$hora', '')");
			$folio_update = mysql_insert_id();


			// recorrer temporal, agrupando por folio_sap
			
			$query = 'SELECT * FROM temp_dashboard GROUP BY folio_sap ORDER BY folio_sap';
			$resultado = mysql_query($query,$conexion);
			$mensaje .= $CR."Se eliminan registros de los folios importados, y se vuelven a agregar".$CR;
			
			while ($row = mysql_fetch_array($resultado)) {
				
				$folio_sap = (int) $row['folio_sap']+0;

				// detectar cambio de estatus delivery_block | credit_status | reason_rejection
				// si cualquiera de esos datos cambia, guardar en fecha_cambio_estatus la fecha actual
				// revisar proceso: obtener valor actual de las 3 variables antes de eliminar, luego eliminar, insertar del temporal y volver a consultar, comparando contra las 3 variables
				// almacenadas; si hay cambio, regsitrar la fecha del update.. --> problema: no hay concordancia de partidas (pedido, material, partida)
				// se queda pendiente.
	
				// eliminar de dashboard registros con ese folio_sap, (en caso que en SAP se hayan hecho cambios o eliminado items de pedido)
				$resultadoD = mysql_query("DELETE FROM dashboard WHERE folio_sap = $folio_sap");
				$del = mysql_affected_rows();

				$query = "INSERT INTO dashboard ( folio_update, folio_pos, folio_sap, order_date, saty, delivery_block, delivery_block_txt, reason_rejection, reason_rejection2, credit_status, overall_status, 
												  delivery, delivery_date,
												  shipment, good_issue, billing_doc, billing_date, ref_doc, material, cantidad_delivery, cantidad_pedido, proveedor, organizacion, canal, planta, store_loc, division, 
												  numero_cliente, p_l, pot, sc, guia,
												  sufijo,
												  procesado, 
												  lista_precios, motivo_descuento, folio_odc, sku_garantia, folio_garantia, tipo_pedido, origen, nombre_tienda, nombre_vendedor, login_vendedor, po_number,
												  tipo_cliente, nombre_cliente, numero_empleado, nombre_empresa, numero_empresa, entrega, forma_pago, estatus_pago, confirmo_pago, estatus_material,
												    no_delivery, comentarios, folio_devolucion, adicionales, tipo_inconformidad, estatus_inconformidad, semaforo, estatus_cliente, feedback,
												   proveedor_logistica,fecha_entrega_promesa,fecha_entrega_final,estatus_entrega
												   ) 
												   SELECT 
													  $folio_update, folio_pos, folio_sap, order_date, saty, delivery_block, delivery_block_txt, reason_rejection, reason_rejection2, credit_status, overall_status, 
													  delivery, delivery_date,
													  shipment, good_issue, billing_doc, billing_date, ref_doc, material, cantidad_delivery, cantidad_pedido, proveedor, organizacion, canal, planta, store_loc, division, 
													  numero_cliente, p_l, pot, sc,
													  CASE
													  WHEN tracking_number like '%CARERA%' THEN folio_sap
													  ELSE tracking_number
													  END as tracking_number,
													  '',
													  0 ,
													  '','','','','','','','','','','',
													  '','','','','','','','','',
													  '','','','','','','','','','',
													  logistic_provider,IF(fechaentp='','0000-00-00',DATE_FORMAT(STR_TO_DATE(fechaentp, '%m/%d/%Y'), '%Y-%m-%d')),IF(fechaentf='', '0000-00-00',DATE_FORMAT(STR_TO_DATE(fechaentf, '%m/%d/%Y'), '%Y-%m-%d')),estatus
												   FROM temp_dashboard
												   WHERE folio_sap = $folio_sap";
				
				$resID = mysql_query($query);
				if (mysql_error()) echo '<br>'.$query."<br>".mysql_error();

			} // while
	
	  	
			$mensaje .= " ".date("H:i:s");
		  	$mensaje .= $CR."---------------------------------".$CR;
			$mensaje .= "Pasada 2: Completar datos".$CR;

			// recorrer los registros de dashboard recien insertados de acuerdo al folio del update y completar datos de POS, y de logística.
			$resultadoDASH = mysql_query("SELECT * FROM dashboard WHERE folio_update = $folio_update ORDER BY folio_pos "); 
			while ($rowDASH = mysql_fetch_array($resultadoDASH)){
			
				$id = $rowDASH['id'];
				
				actualiza_reg_dash($id,$verbose); // parámetros: id del dashboard, verbose
				actualiza_reg_dash_logistica($id,$verbose); // parámetros: id del dashboard, verbose
				
			
			} // while dashboard 1 x 1 
			// Revisa los numeros de guias que fueron actualizados
			
			$resultadoDaLo = mysql_query("SELECT d.folio_pos, d.folio_sap,d.material,d.guia FROM dashboard d 
			inner join temp_log_guias tlg on  d.folio_sap=tlg.folio_sap and d.material=tlg.material and d.guia=tlg.guia group by d.folio_pos,d.folio_sap,d.material,d.guia"); 
			if(mysql_num_rows($resultadoDaLo)>0)
			{
				$total_act=0;
				$total_not=0;
				$coinciden = '';
				$no_coinciden = '';
				$error_act = '';
				while ($rowDaLo = mysql_fetch_array($resultadoDaLo)) {
					$folio_pos =  $rowDaLo['folio_pos'];
					$folio_sap =  $rowDaLo['folio_sap'];
					$material =  $rowDaLo['material'];
					$guia =  $rowDaLo['guia'];
					$total_act ++;
					$coinciden .= $folio_pos.', '.$folio_sap.', '.$material.', '.$guia.' <br>';
					mysql_query("delete from temp_log_guias where folio_sap='$folio_sap' and material='$material' and guia='$guia'",$conexion);
				}
				$resQryNot=mysql_query("select * from temp_log_guias",$conexion);
				if(mysql_num_rows($resQryNot)>0)
				{
					while ($rowQryNot = mysql_fetch_array($resQryNot)) {
					$folio_sap =  $rowQryNot['folio_sap'];
					$material =  $rowQryNot['material'];
					$guia =  $rowQryNot['guia'];
					$total_not ++;
					$no_coinciden .= $folio_sap.', '.$material.', '.$guia.' <br>';
					}
				}
				$mensaje_act = 'Se actualizaron '.$total_act.' registros <br>'.$coinciden;
				if($total_not>0)
				{
					$error_act .= 'No se encontraron '.$total_not.' registros <br>'.$no_coinciden;
				}
				$fecha_hora = date("Y-m-d H:i:s");
				$resultadoLOG = mysql_query("INSERT log_guias (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje_act', '$error_act')",$conexion);
			}
			
			// ahora calcular avance promedio de los diferentes materiales, agrupar por folio 
			

			// ahora sacar promedio de avance %  por pedido (POS o SAP?) y grabarlo   
			// OJOOOO   (si es por folio_POS, hacerlo primero para folio_pos con valor, agrupado, y después para folio_pos = '' agrupado por folio_sap; )
			//          (si es por folio_SAP, agruparlo por folio sap y ya)
			// Ignorar fletes y garantías (sufijo G) y Cancelados, para sacar promedio real de entregas.
			$mensaje .= date("H:i:s");
		  	$mensaje .= $CR."---------------------------------".$CR;
			$mensaje .= "Pasada 3: Calcular % avances".$CR;

			$query = "SELECT DISTINCT folio_pedido FROM dashboard 
						WHERE folio_update = $folio_update AND folio_pedido > 0 
						  AND sufijo != 'G' AND sufijo !='MA' AND sufijo != 'MP' 
						  AND estatus_material != 'Cancelado' ORDER BY folio_pedido";  // ignorar pedidos que no estan en POS
					   
			$resultadoGD = mysql_query($query,$conexion);
			while ($rowGD = mysql_fetch_array($resultadoGD)) {
			
				$folio_pedido = $rowGD['folio_pedido'];
				
				actualiza_avg_avance($folio_pedido,$verbose); // parámetros: folio pedido POS, verbose
			
			}
			
		  	$mensaje .= "---------------------------------".$CR;
			$mensaje .= date("H:i:s");
			$mensaje .= $CR;
		  
		  
  			if ($error) { 
				mysql_query("ROLLBACK"); 
				$mensaje .= 'Error: No se actualizaron los datos para dashboard. [Rollback] '. date("H:i:s");
			}
			else {
				mysql_query("COMMIT");
				$mensaje .= 'Se actualizaron los datos para dashboard. [Commit] '. date("H:i:s");
			//	mysql_query("ROLLBACK");
			
			}
			

		  } // si hay registros en temporal
		} /// si se elimino temporal anterior
		
	} // if subido
	$fecha_hora = date("Y-m-d H:i:s");
	$resultadoLOG = mysql_query("INSERT log_dashboard (fecha_hora, mensaje, error) VALUES ('$fecha_hora', '$mensaje', '$error')",$conexion);
	$resultadoDU = mysql_query("UPDATE dashboard_updates SET log = '$mensaje' WHERE folio = $folio_update",$conexion);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>
<body>
  <? 
    if ($mensaje) echo str_replace(chr(10),'<br>',$mensaje)."<br>";
	if ($error) echo 'ERRORES: <br>'.str_replace(chr(10),'<br>',$error)."<br>";
  ?>
</body>
</html>
