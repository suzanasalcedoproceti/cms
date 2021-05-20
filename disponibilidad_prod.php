<?

// script para obtener disponiblidad de producto (si es visible y vendible o no para tienda) en base a la categoría a la que perteneces
// requiere las variables $categoria y $producto

// Nov 2015
// Se limita criterio de existencias en CEDIS rm01,02, 06 únicamente con store location 1
// y el cedis RM12 de cualquier store location
// se entiende que el RM08 es únicamente para POS

// Feb 2016
// Cambian condiciones de venta de acuerdo al estatus del producto (30 40 50 60) y la existencia

/*	$resultadoCAT = mysql_query("SELECT minimo, tipo_inventario FROM categoria WHERE clave = $categoria");
	$rowCAT = mysql_fetch_array($resultadoCAT);
	$disponibilidad_venta = $rowCAT['minimo'];
	$tipo_inventario = $rowCAT['tipo_inventario'];
*/
    $query = "SELECT SUM(existencia) AS total_ex, estatus, vol_reb FROM existencia WHERE producto = '$producto' 
					 AND  (cedis IN('RM01', 'RM06', 'RM07') AND loc = 1)  
					 AND existencia > 0 
					GROUP BY producto ";
    $resultado_acum = mysql_query($query,$conexion);
    $row_acum = mysql_fetch_array($resultado_acum);
    $total_ex = $row_acum['total_ex'];
//	$estatus = $row_acum['estatus'];
//	$vol_reb = $row_acum['vol_reb'];
	
	// buscar vol_reb sin importar el cedis, (si no trae existencias en esos cedis, no lo encuentra y no se lleva el vol_reb 
	// y por lo tanto no podemos saber si es resurtible / no resurtible
    $query = "SELECT estatus, vol_reb FROM existencia WHERE producto = '$producto' AND vol_reb != '' LIMIT 1";
    $resultado_vr = mysql_query($query,$conexion);
    $row_vr = mysql_fetch_array($resultado_vr);
	$estatus = $row_vr['estatus'];
	$vol_reb = $row_vr['vol_reb'];

	if ($estatus == '30') { // Active
		if ($total_ex>0) {
			$mostrar = 1;  // si vender
			$ocultar = 0;  // si mostrar
		} else {
			$mostrar = 1;  // si vender
			$ocultar = 0;  // si mostrar
		}
	}
	if ($estatus == '40') { // Discontinued
		if ($total_ex>0) {
			$mostrar = 1;  // si vender
			$ocultar = 0;  // si mostrar
		} else {
			$mostrar = 0;  // no vender
			$ocultar = 1;  // no mostrar
		}
	}
	if ($estatus == '50') { // Obsolete
		if ($total_ex>0) {
			$mostrar = 1;
			$ocultar = 0;
		} else {
			$mostrar = 0;
			$ocultar = 1;
		}
	}
	if ($estatus == '60') { // Inactive
		$mostrar = 0;
		$ocultar = 1;
	}
	if ($estatus == '') { // desconocido
		$mostrar = 0;
		$ocultar = 1;
	}	
	

?>