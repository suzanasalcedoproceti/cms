<?php
session_cache_limiter('private, must-revalidate');
session_start();
if (empty($_SESSION['usr_valido'])) {
 include('logout.php');
	 exit;
}

function test($valor) {
	$objResponse = new xajaxResponse();
    $objResponse->setCharacterEncoding('ISO-8859-1');
//	$xvalor = $valor;
	$objResponse->script("alert(".$valor.");");
	return $objResponse;   
}

function autocomplete($texto) {
	$respuesta = new xajaxResponse();
    $respuesta->setCharacterEncoding('ISO-8859-1');
//	$respuesta->Assign('mensaje','innerHTML',$valor);
	include('../conexion.php');

	$error=0;
	// arma el data
	
	$respuesta->script("data = [];");
	$respuesta->script("var data = new Array();"); 
	$cont=-1;
	// productos que coinciden con la búsqueda
	$query = "SELECT nombre, modelo, clave FROM producto 
						   WHERE (producto.nombre LIKE '%$texto%' OR producto.modelo LIKE '$texto%' ) 
							 AND (estatus=1 OR estatus=2) 
							  LIMIT 10";
    $resPRO= mysql_query($query,$conexion);
							
    while ($rowPRO = mysql_fetch_array($resPRO)) {
		$cont++;
		$respuesta->script("data[".$cont."] = new Array(4);");
		
		if (stripos($rowPRO['modelo'],$texto)===false) {   // stripos es strpos insensitive a may min
			$respuesta->script("data[".$cont."]['label']='".$rowPRO['nombre']."';");
			$respuesta->script("data[".$cont."]['clave']='".$rowPRO['clave']."';");
			$respuesta->script("data[".$cont."]['modelo']='".$rowPRO['modelo']."';");
			$respuesta->script("data[".$cont."]['nombre']='".$rowPRO['nombre']."';");
			
		} else {
			$respuesta->script("data[".$cont."]['label']='".$rowPRO['modelo']."';");
			$respuesta->script("data[".$cont."]['clave']='".$rowPRO['clave']."';");
			$respuesta->script("data[".$cont."]['modelo']='".$rowPRO['modelo']."';");
			$respuesta->script("data[".$cont."]['nombre']='".$rowPRO['nombre']."';");
		}
			
		$respuesta->script("data[".$cont."]['category']='';");
		
	}

	$respuesta->script("$(document).ready(function() { $('#busqueda').catcomplete({ source: data }); });");
    return $respuesta;
} 

function comparar_orden($a, $b) {
  if ($a["orden"] < $b["orden"]) return -1;
  if ($a["orden"] > $b["orden"]) return 1;
  if ($a["orden"] == $b["orden"]) return 0;
  return;
}


function xcambia_orden($producto,$orden) {
	$objResponse = new xajaxResponse();
	$objResponse->setCharacterEncoding('ISO-8859-1');
	include('../conexion.php');
	$query = "UPDATE m_producto SET orden = $orden, act = 1-act WHERE id = $producto LIMIT 1";
	$resultadoI = mysql_query($query,$conexion);
	return $objResponse;   
}

function autocomplete_rel($texto,$tipoinput) {
	$respuesta = new xajaxResponse();
    $respuesta->setCharacterEncoding('ISO-8859-1');
//	$respuesta->Assign('mensaje','innerHTML',$valor);
	include('../conexion.php');

	$error=0;
	// arma el data
	
	$respuesta->script("data = [];");
	$respuesta->script("var data = new Array();"); 
	$cont=-1;
	// productos que coinciden con la búsqueda
	$query = "SELECT producto.nombre, modelo, producto.clave, color 
						    FROM producto 
							LEFT JOIN categoria ON producto.categoria = categoria.clave
						   WHERE (producto.nombre LIKE '%$texto%' OR producto.modelo LIKE '$texto%' ) 
						     AND NOT categoria.accesorios AND NOT categoria.garantias 
							 AND (producto.estatus=1 OR producto.estatus=2) AND solo_para_servicio=0 
						   LIMIT 15";
							  
//	$respuesta->Assign('debug','innerHTML',$query);

//	return $respuesta;
	
    $resPRO= mysql_query($query,$conexion);
							
    while ($rowPRO = mysql_fetch_array($resPRO)) {
		$cont++;
		$respuesta->script("data[".$cont."] = new Array(4);");
		
		if (stripos($rowPRO['modelo'],$texto)===false) {   // stripos es strpos insensitive a may min
			$respuesta->script("data[".$cont."]['label']='".mysql_real_escape_string($rowPRO['nombre'])."';");
			$respuesta->script("data[".$cont."]['clave']='".$rowPRO['clave']."';");
			$respuesta->script("data[".$cont."]['modelo']='".$rowPRO['modelo']."';");
			$respuesta->script("data[".$cont."]['color']='".mysql_real_escape_string($rowPRO['color'])."';");
			
		} else {
			$respuesta->script("data[".$cont."]['label']='".$rowPRO['modelo']."';");
			$respuesta->script("data[".$cont."]['clave']='".$rowPRO['clave']."';");
			$respuesta->script("data[".$cont."]['modelo']='".$rowPRO['modelo']."';");
			$respuesta->script("data[".$cont."]['color']='".mysql_real_escape_string($rowPRO['color'])."';");
		}
			
//		$respuesta->script("data[".$cont."]['category']='';");
//		$respuesta->Assign('debug','innerHTML',$rowPRO['modelo']);
	}
	
	if ($tipoinput=='prod_mas') {
		$respuesta->script("
						$('#pro_mas').autocomplete({ 
							source: data, 
							select: function( event, ui ) {	
								$('#pro_mas').val(ui.item.clave);
								$('#pro_mas').dblclick();
						}
					}); ");
	
	}

	
    return $respuesta;
} 

function cambia_estado($estado) {
	$objResponse = new xajaxResponse();
    $objResponse->setCharacterEncoding('ISO-8859-1');
//	$objResponse->script("alert('$estado');");
	
	include_once("../conexion.php");
	$resultadoCD = mysql_query("SELECT * FROM ciudad WHERE estado = '$estado' ORDER BY nombre ",$conexion);

	$sel = '<select name="ciudad" id="ciudad"  class="campo" onchange="xajax_actualiza_dato_dir(\'ciudad\',this.value);">
                <option value="" selected>Selecciona la ciudad</option>';
	while ($rowCD = mysql_fetch_array($resultadoCD)) {	
        $sel .= '<option value="'.$rowCD['clave'].'">'.$rowCD['nombre'].'</option>';
	}
	$sel .= '</select>';
//	$objResponse->Assign('debug','innerHTML',$sel);
	$objResponse->Assign('div_ciudad','innerHTML',$sel);
	return $objResponse;


}	

function cambia_estado_proy($estado) {
	$objResponse = new xajaxResponse();
    $objResponse->setCharacterEncoding('ISO-8859-1');
//	$objResponse->script("alert('$estado');");
	
	include_once("../conexion.php");
	$resultadoCD = mysql_query("SELECT * FROM ciudad WHERE estado = '$estado' ORDER BY nombre ",$conexion);

	$sel = '<select name="ciudad" id="ciudad"  class="campo">
                <option value="" selected>Selecciona la ciudad</option>';
	while ($rowCD = mysql_fetch_array($resultadoCD)) {	
        $sel .= '<option value="'.$rowCD['clave'].'">'.$rowCD['nombre'].'</option>';
	}
	$sel .= '</select>';
//	$objResponse->Assign('debug','innerHTML',$sel);
	$objResponse->Assign('div_ciudad','innerHTML',$sel);
	return $objResponse;


}	

function cambia_categoria2($categoria,$campox,$valor) {
	$objResponse = new xajaxResponse();
    $objResponse->setCharacterEncoding('ISO-8859-1');
//	$xvalor = $valor;
	$objResponse->script("alert(".$campox.");");
	return $objResponse;   
}

function cambia_categoria_uni($categoria,$valor) {
	$objResponse = new xajaxResponse();
	$objResponse->setCharacterEncoding('ISO-8859-1');
//	$objResponse->script("alert(".$valor.");");	return $objResponse;   
	include('../conexion.php');
	$query = "UPDATE categoria SET unidades_disponibles_flex_pep = $valor, act = 1-act WHERE clave = $categoria LIMIT 1";
	$resultadoI = mysql_query($query,$conexion);
	return $objResponse;   
}
function cambia_categoria_rep($categoria,$valor) {
	$objResponse = new xajaxResponse();
	$objResponse->setCharacterEncoding('ISO-8859-1');
//	$objResponse->script("alert(".$valor.");");	return $objResponse;   
	include('../conexion.php');
	$query = "UPDATE categoria SET repetir_sku_flex_pep = $valor, act = 1-act WHERE clave = $categoria LIMIT 1";
	$resultadoI = mysql_query($query,$conexion);
	return $objResponse;   
}

?>