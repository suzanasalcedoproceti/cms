<?php 
session_start();

require ('xajax/xajax_core/xajax.inc.php');
$xajax = new xajax(); 

// $xajax->setFlag('debug', true); 

function grafica($eje_x, $eje_y, $serie_x) {

	include('../conexion.php');
	$eje_x = mysql_real_escape_string($eje_x);
	$eje_y = mysql_real_escape_string($eje_y);
	$serie_x = mysql_real_escape_string($serie_x);
    $respuesta = new xajaxResponse(); 
    $respuesta->setCharacterEncoding('ISO-8859-1');

	$condicion = mysql_real_escape_string($_SESSION['ss_condicion_dashboard']);
	$condicion = $_SESSION['ss_condicion_dashboard'];
//	$condicion = mysql_real_escape_string($condicion);
//	$condicion = $_SESSION['ss_condicion_dashboard'];



	// determinar qué dato calcular: Pedidos: count(*) |  Montos: sum(total) | Unidades: sum(cantidad_pedido)
	switch ($eje_y) {
		case 'Pedidos'  : $valorquery = ' COUNT(*) '; break;
		case 'Montos'   : $valorquery = ' sum(total) '; break;
		case 'Unidades' : $valorquery = ' sum(cantidad_pedido) '; break;
	}	

	// determinar qué dato buscar para ejex: tienda | vendedor | tiendavend
	switch ($eje_x) {
		case 'tienda'     : $ejexquery = " AND nombre_tienda = '##valor_ejex##' "; 
							$distinct_x = "nombre_tienda";
							break;
		case 'vendedor'   : $ejexquery = " AND login_vendedor = '##valor_ejex##' "; 
							$distinct_x = "login_vendedor";
							break;
/*		case 'tiendavend' : $ejexquery = " AND nombre_tienda = '##valor_ejex##' "; 
							$distinct_x = "nombre_tienda, login_vendedor";
							break;
*/
	}

	// obtener valores de eje x

    $query = "SELECT DISTINCT $distinct_x AS ejex FROM dashboard $condicion ORDER BY $distinct_x";
    $resultado = mysql_query($query);
	$arr_ejex = array();
	$i = 0;
    while ($row = mysql_fetch_array($resultado)) {
		$i++;
		$arr_ejex[$i] = strtoupper($row['ejex']);
	} // while 

	// determinar qué dato buscar para ejex: tienda | vendedor | tiendavend
	switch ($serie_x) {
		case 'vendedor'   : $sxquery = " AND login_vendedor = '##valor_serie##' "; 
							$distinct_sx = "login_vendedor";
							break;
		case 'tienda'     : $sxquery = " AND nombre_tienda = '##valor_serie##' "; 
							$distinct_sx = "nombre_tienda";
							break;
		case 'estatus'    : $sxquery = " AND overall_status = '##valor_serie##' "; 
							$distinct_sx = "overall_status";
							break;
		case 'forma_pago' : $sxquery = " AND ##valor_serie## > 0 "; 
							$distinct_sx = "";
							break;
		case 'categoria'  : $sxquery = " AND nombre_categoria = '##valor_serie##' "; 
							$distinct_sx = "nombre_categoria";
							break;
		case 'lista_precios' : $sxquery = " AND lista_precios = '##valor_serie##' "; 
							$distinct_sx = "lista_precios";
							break;
		case 'backorder'  : $sxquery = " AND delivery_block = '##valor_serie##' "; 
							$distinct_sx = "delivery_block";
							break;
		case 'facturado'  : $sxquery = " AND ##valor_serie## "; 
							$distinct_sx = "";
							break;
	}


	// armar arreglo
	$varr = " data_graf = google.visualization.arrayToDataTable([";
	
	$varr .= "['".$eje_x."', ";
	$vtab = '<table class="fancyTable tabla_grafica">';



	// obtener series x
	$arr_series = array();
	
	// primero escepciones, para tener "todas" las posibles opciones
		  
	if ($serie_x=='estatus') {
		$arr_series[1] = 'NOT BLOCKED';
		$arr_series[2] = 'BLOCKED';
		$varr .= " 'NOT BLOCKED', 'BLOCKED' ],";
		$vtab .= "<th><td>NOT BLOCKED</td><td>BLOCKED</td></th>";

	} elseif ($serie_x=='facturado') {

		$arr_series[1] = " billing_doc ";
		$arr_series[2] = " NOT billing_doc ";
		$varr .= " 'FACTURADO', 'NO FACTURADO' ],";
		$vtab .= "<th><td>FACTURADO</td><td>NO FACTURADO</td></th>";
		
	} elseif ($serie_x=='forma_pago') {
		$arr_series[1] = 'fdp_tdd';
		$arr_series[2] = 'fdp_tdc';
		$arr_series[3] = 'fdp_efectivo';
		$arr_series[4] = 'fdp_odc';
		$arr_series[5] = 'fdp_cheque';
		$arr_series[6] = 'fdp_cep';
		$arr_series[7] = 'fdp_dep';
		$arr_series[8] = 'fdp_puntos';
		$arr_series[9] = 'fdp_puntos_flex';
		$arr_series[10] = 'fdp_puntos_pep';
		$arr_series[11] = 'fdp_gc';
		$arr_series[12] = 'fdp_sustitucion';
		$arr_series[13] = 'fdp_refacturacion';
		$varr .= " 'DEBITO', 'CREDITO', 'EFECTIVO', 'OC', 'CHEQUE', 'CEP', 'DEPOSITO', 'PUNTOS', 'FLEX', 'PEP', 'GIFTCARD', 'SUSTITUTO', 'REFACTURACION' ],";
		$vtab .= "<th><td>DEBITO</td><td>CREDITO</td><td>EFECTIVO</td><td>OC</td><td>CHEQUE</td><td>CEP</td><td>DEPOSITO</td><td>PUNTOS</td><td>FLEX</td><td>PEP</td><td>GIFTCARD</td><td>SUSTITUTO</td><td>REFACTURACION</td></th>";
	
	
	} else {
		
		$query = "SELECT DISTINCT $distinct_sx AS seriex FROM dashboard $condicion ORDER BY $distinct_sx";
		$resultado = mysql_query($query);
		
		$i = 0;
		$vtab.="<th>";
		while ($row = mysql_fetch_array($resultado)) {
			$i++;
			$arr_series[$i] = $row['seriex'];
			$varr .= " '".strtoupper($row['seriex'])."',	";
			$vtab .= "<td>".strtoupper($row['seriex'])."</td>";
			
		} // while 
		$varr = substr($varr,0,-2); // eliminar coma final
		$varr .= "],";
		$vtab .= "</th>";
	}
	
		



	// llenar matriz de ejex x series x para el eje y
	// recorrer serie x
	
	foreach ($arr_ejex as $valor_ejex) {

		$varr .= "
			['".$valor_ejex."', ";	

		$vtab .= "<tr><td>".$valor_ejex."</td>";
				
				
		foreach ($arr_series as $valor_serie) {
		
			$vqx = str_replace('##valor_ejex##',$valor_ejex,$ejexquery);
			$vsx = str_replace('##valor_serie##',$valor_serie,$sxquery);
			$query2 = "SELECT $valorquery AS valor FROM dashboard $condicion ".$vqx.$vsx;
			$query = "SELECT $valorquery AS valor FROM dashboard $condicion ".$vqx.$vsx;
//			$query = "SELECT $valorquery AS valor FROM dashboard $condicion AND nombre_tienda = '$valor_ejex' AND login_vendedor = '$valor_serie' ";
			$resultado = mysql_query($query);
			$row = mysql_fetch_array($resultado);
			$valor = $row['valor']+0;
			$varr .= $valor.", ";
			$vtab .= "<td>".$valor."</td>";
			
		} // foreach series
		$varr = substr($varr,0,-2); // quitar coma final
		$varr .= "],".chr(10);
		$vtab .="</tr>";

		
		
	} // foreach ejex
	$varr .= "]);
	
	
	 options_graf = {
          title: document.forma_dashboard.eje_y.value+' por '+document.forma_dashboard.eje_x.value + ' / ' + document.forma_dashboard.serie_x.value
        };		
		
		chart.draw(data_graf, options_graf);
	
	";
	$vtab .= "</table>";
	

//	$respuesta->script("alert('".$condicion."');");

	$xx = "
        data_graf = google.visualization.arrayToDataTable([
          ['Tienda', 	'Garzad',	  'Maltod', 'Otro'],
          ['Celaya',	  114, 20, 16],
          ['Guadalajara', 13, 13, 17],
          ['Corp Mty', 	  14, 20, 35],
          ['Corp GDL', 	  80, 112, 22],
          ['Apodaca',    1, 10, 19],
          ['Padre Mier', 12, 18, 22],
          ['Ramos Arizpe', 18, 7, 31],
        ]);
						
        options_graf = {
          title: document.forma_dashboard.eje_y.value+' por '+document.forma_dashboard.eje_x.value + ' / ' + document.forma_dashboard.serie_x.value + ' ".mysql_real_escape_string($condicion)." ' ,
        };		
		
		chart.draw(data_graf, options_graf); ";

		$respuesta->script($varr);
		$respuesta->Assign('tabla_grafica','innerHTML',$vtab);
//		$respuesta->Assign('debug','innerHTML',$varr."<br>".$query2);



/*
		$respuesta->script("destinos = [];");
		$respuesta->script("var destinos = new Array();");
		$cont=-1;

//		$query "SELECT * FROM destino WHERE destino LIKE '%$destino%' OR pais LIKE '%$destino%' ORDER BY destino LIMIT 20";


	}
//	else
//		$respuesta->script("jQuery('#destino').autocomplete({source: ''});");


//	$respuesta->script('document.buscador.id_destino.value="";');
	*/	
    return $respuesta;
} 



$xajax->register(XAJAX_FUNCTION, 'grafica');

$xajax->processRequest(); 

?> 