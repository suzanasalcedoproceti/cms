<?
// CONTROL DE CAMBIOS
//
// 20 Agosto 2012:  
//    Se agregó registro de nuevas formas de pago (banorte: debito, cheque, cep)
//
// 25 Marzo 2013: 
//	  Entregas nacionales v2
//
// 8 Nov 2013
//	 Cedis se toma de la categoría siempre
// 16 Marzo 2015
//    Precios especiales
// Feb 2016
//	  Logística B2C y disponibilidad

include_once('ajax_ini.php'); 
include("libprod.php");
include("libprod_combo.php");

include("admin/lib.php");
/* arreglo carrito
['producto']
['cantidad']
['rel_garantia']
*/

// iniciar transacción
include("conexion.php");
$resultado = mysql_query("SET AUTOCOMMIT = 0");
$resultado = mysql_query("START TRANSACTION");

// generar código de serguridad para cotejar numero de pedido + codigo de seguridad de ida y vuelta del banco

$codigo_seguridad = $token;
srand((double)microtime()*1000000);
$codigo_seguridad ='';
for ($i=1; $i<=20; $i++) {
 if (rand(0,1)==0) // letra
   $codigo_seguridad .= chr(rand(97,102));
 else // numero
   $codigo_seguridad .= chr(rand(48,57));
}

$cliente = $_SESSION['cliente_valido'];

$resCLI= mysql_query("SELECT *, CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) AS nombre  FROM cliente WHERE clave = $cliente",$conexion);
$rowCLI = mysql_fetch_array($resCLI);

$empresa = $rowCLI['empresa'];
$nombre_cliente = $rowCLI['nombre'];

// detectar lista de precios especiales
$resEMP= mysql_query("SELECT empresa_whirlpool, lista_precios_especiales_tw FROM empresa WHERE clave='$empresa'",$conexion);
$rowEMP= mysql_fetch_array($resEMP);
$lista_precios_especiales = $rowEMP['lista_precios_especiales_tw'];
$precios_especiales = $rowEMP['empresa_whirlpool'];
$empresa_whirlpool = $rowEMP['empresa_whirlpool'];
if (!$precios_especiales && $empresa == 178) $precios_especiales = 1;

$fecha = date("Y-m-d");
$hora = date("H:i");

$envio_alias = $_SESSION['datos_envio']['alias'];
$envio_nombre = $_SESSION['datos_envio']['nombre'];
$envio_estado = $_SESSION['datos_envio']['estado'];
$envio_ciudad = $_SESSION['datos_envio']['ciudad'];

$resCD = mysql_query("SELECT * FROM ciudad WHERE clave = $envio_ciudad",$conexion);
$rowCD = mysql_fetch_array($resCD);
$envio_ciudad_nombre = $rowCD['nombre'];

$envio_colonia = $_SESSION['datos_envio']['colonia'];
$envio_calle = $_SESSION['datos_envio']['calle'];
$envio_exterior = $_SESSION['datos_envio']['exterior'];
$envio_interior = $_SESSION['datos_envio']['interior'];
$envio_cp = $_SESSION['datos_envio']['cp'];
$envio_referencias = $_SESSION['datos_envio']['referencias'];
$envio_observaciones = $_SESSION['datos_envio']['observaciones'];
$envio_telefono_casa = $_SESSION['datos_envio']['telefono_casa'];
$envio_telefono_oficina = $_SESSION['datos_envio']['telefono_oficina'];
$envio_telefono_celular = $_SESSION['datos_envio']['telefono_celular'];
$envio_contacto = $_SESSION['datos_envio']['contacto'];
$envio_email = $rowCLI['email'];

$nombre = $rowCLI['nombre'];
$pers_calle = $rowCLI['pers_calle'];
$pers_exterior = $rowCLI['pers_exterior'];
$pers_interior = $rowCLI['pers_interior'];
$pers_cp = $rowCLI['pers_cp'];
$pers_ciudad = $rowCLI['pers_ciudad'];
$pers_colonia = $rowCLI['pers_colonia'];
$pers_estado = $rowCLI['pers_estado'];
$pers_telefono = $rowCLI['pers_telefono'];

$requiere_factura = $_SESSION['requiere_factura']+0;

if ($requiere_factura) {
	$fact_calle = $rowCLI['fact_calle'];
	$fact_exterior = $rowCLI['fact_exterior'];
	$fact_interior = $rowCLI['fact_interior'];
	$fact_cp = $rowCLI['fact_cp'];
	$fact_ciudad = $rowCLI['fact_ciudad'];
	$fact_colonia = $rowCLI['fact_colonia'];
	$fact_estado = $rowCLI['fact_estado'];
	$fact_telefono = $rowCLI['fact_telefono'];
	$fact_razon_social = $rowCLI['razon_social'];
	$fact_rfc = $rowCLI['rfc'];
	$fact_email = $rowCLI['fact_email'];
} else {
	$fact_calle = "";
	$fact_exterior = "";
	$fact_interior = "";
	$fact_cp = "";
	$fact_ciudad = "";
	$fact_colonia = "";
	$fact_estado = "";
	$fact_telefono = "";
	$fact_razon_social = "";
	$fact_rfc = "";
	$fact_email = "";
}



// obtener datos de entrega B2C para combos (en los items normales ya se trae en el carrito)
$arr_entrega = get_entrega($envio_ciudad);
$trans_zone = $rowCD['trans_zone'];
$cve_sucursal_ocurre_rm = $arr_entrega['sucursal_rm_ocu']+0;
$cve_sucursal_ocurre_rs = $arr_entrega['sucursal_rs_ocu']+0;
$sucursal_ocurre_nombre_rm = $arr_entrega['sucursal_rm_ocu_nombre'];
$sucursal_ocurre_nombre_rs = $arr_entrega['sucursal_rs_ocu_nombre'];

$entrega_rm_dom = $arr_entrega['entrega_rm_dom'];
$entrega_rm_ocu = $arr_entrega['entrega_rm_ocu'];
$entrega_rs_dom = $arr_entrega['entrega_rs_dom'];
$entrega_rs_ocu = $arr_entrega['entrega_rs_ocu'];

$sku_rm_dom = $arr_entrega['sku_rm_dom'];
$sku_rm_ocu = $arr_entrega['sku_rm_ocu'];
$sku_rs_dom = $arr_entrega['sku_rs_dom'];
$sku_rs_ocu = $arr_entrega['sku_rs_ocu'];

$rm_origen_rm = $arr_entrega['rm_origen'];
$rm_origen_rs = $arr_entrega['rs_origen'];

$costo_rm_dom = $arr_entrega['costo_rm_dom'];
$costo_rm_ocu = $arr_entrega['costo_rm_ocu'];
$costo_rs_dom = $arr_entrega['costo_rs_dom'];
$costo_rs_ocu = $arr_entrega['costo_rs_ocu'];

////////////////////////////////////////////////////////

$estatus = '0';
// insertar registro de encabezado

$query = "INSERT pedido (
			cliente,
			empresa,
			codigo_seguridad,
			fecha,
			hora,
			
			nombre,
			pers_calle,
			pers_exterior,
			pers_interior,
			pers_cp,
			pers_ciudad,
			pers_colonia,
			pers_estado,
			pers_telefono,
			
			requiere_factura, 
			
			fact_calle,
			fact_exterior,
			fact_interior,
			fact_cp,
			fact_ciudad,
			fact_colonia,
			fact_estado,
			fact_telefono,
			fact_razon_social,
			fact_rfc,
			fact_email,
			
			envio_alias,
			envio_nombre,
			envio_estado,
			envio_ciudad,
			envio_ciudad_nombre,
			envio_colonia,
			envio_calle,
			envio_exterior,
			envio_interior,
			envio_cp,
			envio_referencias,
			envio_observaciones,
			envio_telefono_casa,
			envio_telefono_oficina,
			envio_telefono_celular,
			envio_contacto,
			envio_email,
			estatus,
			pago_msi,
			fdp_tdc,
			fdp_tdd,
			fdp_cheque,
			fdp_cep,
			fdp_cep_folio,
			fdp_puntos,
			fdp_puntos_flex,
			fdp_puntos_pep,
			fdp_puntos_convenio
			
		) VALUES (
			$cliente,
			$empresa,
			'$codigo_seguridad',
			'$fecha',
			'$hora',

			'$nombre',
			'$pers_calle',
			'$pers_exterior',
			'$pers_interior',
			'$pers_cp',
			'$pers_ciudad',
			'$pers_colonia',
			'$pers_estado',
			'$pers_telefono',
			
			$requiere_factura,
			
			'$fact_calle',
			'$fact_exterior',
			'$fact_interior',
			'$fact_cp',
			'$fact_ciudad',
			'$fact_colonia',
			'$fact_estado',
			'$fact_telefono',
			'$fact_razon_social',
			'$fact_rfc',
			'$fact_email',
			
			'$envio_alias',
			'$envio_nombre',
			'$envio_estado',
			$envio_ciudad,
			'$envio_ciudad_nombre',
			'$envio_colonia',
			'$envio_calle',
			'$envio_exterior',
			'$envio_interior',
			'$envio_cp',
			'$envio_referencias',
			'$envio_observaciones',
			'$envio_telefono_casa',
			'$envio_telefono_oficina',
			'$envio_telefono_celular',
			'$envio_contacto',
			'$envio_email',
			'$estatus',
			$pago_msi,
			$fdp_tdc,
			$fdp_tdd,
			$fdp_cheque,
			$fdp_cep,
			'$fdp_cep_folio',
			$fdp_puntos,
			$fdp_puntos_flex,
			$fdp_puntos_pep,
			$fdp_puntos_convenio
		)";

$resultado = mysql_query($query,$conexion);
$folio_pedido = mysql_insert_id();

if ($folio_pedido <= 0) {
	$error .= "No se pudo insertar encabezado de pedido ";
	
} else {

	// recorrer productos del carrito
	$total = 0;
	$total_productos = 0;
	$total_flete = 0;
	$partida = 0;
	$fecha_maxima = 0;

	foreach ($_SESSION['articulosEnCarrito'] as $key => $item) {

		if ($item['disponible']) {


			///////////// PRODUCTO NORMAL
			
			if (!$item['es_combo']) {
			
				$partida++;
				$clave_prod =  $item['producto'];
				$cantidad = $item['cantidad'];
				$rel_garantia = $item['rel_garantia'];
				$cedis = $item['cedis']; 
				$loc = str_pad($item['loc']+0,4,'0',STR_PAD_LEFT);
				$entrega = $item['entrega']+0;
				
				$fecha_entrega = date ( "Y-m-d", mktime(0,0,0,date("m"),date("d")+$entrega,date("Y") ));
	
				// obtener fecha máxima del pedido
				if ($fecha_entrega > $fecha_maxima) $fecha_maxima = $fecha_entrega;
				
				$tiempo_entrega = $entrega.' días';
		
				
				$resultadoPRO = mysql_query("SELECT producto.clave, producto.nombre, producto.precio_web, producto.modelo, producto.color, producto.marca,
											producto.resurtible, producto.es_garantia, 
											categoria.garantias, categoria.planta, marca.nombre AS nombre_marca 
										FROM producto 
										LEFT JOIN marca ON producto.marca = marca.clave
										LEFT JOIN categoria ON producto.categoria = categoria.clave
										WHERE producto.clave = $clave_prod",$conexion);
				$rowPRO = mysql_fetch_array($resultadoPRO);
				
				$arr_precios = get_precio($clave_prod);
				
				$marca = $rowPRO['marca'];
				$marca_nombre = $rowPRO['nombre_marca'];
				$modelo = $rowPRO['modelo'];
				$nombre = $rowPRO['nombre'];
				$color = $rowPRO['color'];
				$es_resurtible = $rowPRO['resurtible'];
				$ar_lp = explode('_',get_lista());

				$lista_precios = $ar_lp[1];
				$precio_lista = $arr_precios['precio_lista'];
				$tipo_precio = $item['tipo_precio'];
				if ($precios_especiales && $tipo_precio=='especial') {
					$precio_empleado = $arr_precios['precio_especial'];
					$lista_precios = $arr_precios['lista_especial'];
				} else {
					$precio_empleado = $arr_precios['precio_empleado'];
				}
				$costo_entrega = $item['costo_entrega']+0;
				$tipo_entrega = $item['tipo_entrega'];
				$sucursal_ocurre = $item['sucursal_ocurre']+0;
				$sku_entrega = $item['sku_entrega'];
				//$cedis = $rowPRO['planta']; // de la categoria ya no se toma, se toma de la ciudad de acuerdo al tipo de entrega

				
				// detectar si es garantía
				if ($rowPRO['es_garantia']) {
					$es_garantia = $rowPRO['es_garantia']; // 1 2 o 3

					// calcular inicio y fin de garantia. En POS se captura, en TW es la de la compra
					// la garantía es extendida, así que comienza dentro de 1 año
					$prox_ano = date("Y")+1;
					
					
					$inicio_garantia = $prox_ano."-".date("m-d");
					$fi = explode('-',$inicio_garantia);
					$fin_garantia = $fi[0]+$es_garantia;  //aumentarle los años de garantia
					$fin_garantia .= '-'.$fi[1].'-'.$fi[2];
					
					
					//if (!$es_garantia) $es_garantia = 1;
					
					// generar token para validar garantias
					srand((double)microtime()*1000000);
					$token_garantia = '';
					for ($i=1; $i<=8; $i++) {
					 if (rand(0,1)==0) // letra
					   $token_garantia .= chr(rand(97,102));
					 else // numero
					   $token_garantia .= chr(rand(48,57));
					}
					// insertar registro en tabla de garantías
					$query = "INSERT garantia (fecha, pedido, modelo, aplica_para, token, origen, inicio_garantia, fin_garantia)
								VALUES ('$fecha',$folio_pedido,'$modelo', '$rel_garantia', '$token_garantia', 'web', '$inicio_garantia', '$fin_garantia')";
					$resultadoG = mysql_query($query,$conexion);
					$folio_garantia = mysql_insert_id();	
					// $cedis = 'RM11';  // ya se toma de la categoría
					
					
				} else {
					$es_garantia = 0;
					$folio_garantia = 0;
				}
				
				// validación adicional, si no trae precio
				if ($precio_empleado <=0)
					continue;
				
				$subtotal = ($precio_empleado + $costo_entrega) * $cantidad;
				$total_productos += ($precio_empleado  * $cantidad);
				$total_flete += $costo_entrega;

				$total += $subtotal;
				
				$puntos_a_generar = 0;
				// calcular los puntos a generar (solo empresas whirlpool o invitados)
				if ($empresa_whirlpool || $empresa == 178) { // o invitados whirlpool
					$puntos_a_generar = calcula_puntos($modelo,$precio_empleado);
				}
				
		
				// insertar partida
				
				$query = "INSERT detalle_pedido (
							pedido,
							partida,
							marca,
							marca_nombre, 
							modelo,
							nombre,
							color,
							combo,
							lista_precios,
							precio_lista,
							tipo_precio,
							precio_empleado,
							cantidad,
							subtotal,
							es_garantia,
							folio_garantia,
							rel_garantia,
							tiempo_entrega,
							fecha_entrega,
							costo_entrega,
							tipo_entrega,
							sucursal_ocurre,
							sku_entrega,
							cedis,
							loc,
							entrega,
							puntos_a_generar
						) VALUES (
							$folio_pedido,
							$partida,
							$marca,
							'$marca_nombre',
							'$modelo',
							'$nombre',
							'$color',
							0,
							'$lista_precios',
							$precio_lista,
							'$tipo_precio',
							$precio_empleado,
							$cantidad,
							$subtotal,
							$es_garantia,
							$folio_garantia,
							'$rel_garantia',
							'$tiempo_entrega',
							'$fecha_entrega',
							$costo_entrega,
							'$tipo_entrega',
							$sucursal_ocurre,
							'$sku_entrega',
							'$cedis',
							$loc,
							$entrega,
							$puntos_a_generar
						)";
				$resultado = mysql_query($query,$conexion);
				$ins_det = mysql_affected_rows();
		
				if ($ins_det <= 0) {
					$error .= "<br>No se pudo insertar detalle de pedido<br>".mysql_error(); //.$query."<br>Error mysql: ".mysql_error();
				} else { 
				  // disminuir existencias en verifica_pago			
				  
				  // registrar si se tomó precio especial
				  if ($precios_especiales && $tipo_precio=='especial') {
					  
					  $queryPE = "UPDATE cliente SET pe_disponibles = pe_disponibles - $cantidad, act = 1-act WHERE clave = $cliente";
					  $resultadoCTE = mysql_query($queryPE,$conexion);
					  $actPE = mysql_affected_rows();
					  if ($actPE <= 0) {
						$query_err = "INSERT INTO log_error (fecha, usuario, error) VALUES ('".date("Y-m-d H:i")."', '".$cliente."', '/graba_pedido.php".chr(10).mysql_real_escape_string(mysql_error()).chr(10).mysql_real_escape_string($queryPE)."')";
						mysql_query($query_err,$conexion);						  
						//$error .= "<br>No se pudo descontar precios especiales. El pedido no fue colcado<br>".mysql_error(); //.$query."<br>Error mysql: ".mysql_error();
					  }
				  }
	
				} // se insertó detalle de carrito
			
			///////////////////////////////////////////////////////
			} else {  	/// COMBO
			///////////////////////////////////////////////////////

				$clave_combo =  $item['producto'];
				$tipo_entrega = $item['tipo_entrega'];
				$sucursal_ocurre = $item['sucursal_ocurre']+0;

				// recorrer productos del combo
				$query = "SELECT * FROM combo_detalle WHERE combo = $clave_combo ORDER by orden";
				$resultadoDC = mysql_query($query);
				while ($rowDC = mysql_fetch_array($resultadoDC)) {

					$arr_precios = get_precio_combo($rowDC['producto'],$rowDC['lista_precios']);
					$precio_empleado = $arr_precios['precio_empleado'];
					$precio_lista = $arr_precios['precio_lista'];
					$lista_precios = $rowDC['lista_precios'];
					
					$partida++;
					$clave_prod =  $rowDC['producto'];
					$clave_combo = $rowDC['combo'];
					$cantidad = 1;
					$rel_garantia = '';
					// $cedis = $item['cedis']; // ya se toma de la categoría
					$loc = str_pad($item['loc']+0,4,'0',STR_PAD_LEFT);
					$entrega = $item['entrega']+0;
	
					
					$fecha_entrega = date ( "Y-m-d", mktime(0,0,0,date("m"),date("d")+$entrega,date("Y") ));
		
					// obtener fecha máxima del pedido
					if ($fecha_entrega > $fecha_maxima) $fecha_maxima = $fecha_entrega;
					
					$tiempo_entrega = $entrega.' días';
			
					
					$resultadoPRO = mysql_query("SELECT producto.clave, producto.nombre, producto.precio_web, producto.modelo, producto.color, producto.marca,
												producto.resurtible, producto.es_garantia, subcategoria.tipo_producto, categoria.tipo_producto AS tipo_prod_cat,
												categoria.garantias, categoria.planta, marca.nombre AS nombre_marca 
											FROM producto 
											LEFT JOIN marca ON producto.marca = marca.clave
											LEFT JOIN categoria ON producto.categoria = categoria.clave
											LEFT JOIN subcategoria ON producto.subcategoria = subcategoria.clave
											WHERE producto.clave = $clave_prod",$conexion);
					$rowPRO = mysql_fetch_array($resultadoPRO);
					
					$marca = $rowPRO['marca'];
					$marca_nombre = $rowPRO['nombre_marca'];
					$modelo = $rowPRO['modelo'];
					$nombre = $rowPRO['nombre'];
					$color = $rowPRO['color'];
					$es_resurtible = $rowPRO['resurtible'];
					$tipo_producto = $rowPRO['tipo_producto'];
					if (!$tipo_producto) $tipo_producto = $rowPRO['tipo_prod_cat'];

					
					if ($tipo_producto=='RM') {
						if ($tipo_entrega=='domicilio') {
							$costo_entrega = $costo_rm_dom+0;
							$sku_entrega = $sku_rm_dom;
						} else {
							$costo_entrega = $costo_rm_ocu+0;
							$sku_entrega = $sku_rm_ocu;
						}
						$cedis = $rm_origen_rm;
					} else { /// RS
						if ($tipo_entrega=='domicilio') {
							$costo_entrega = $costo_rs_dom+0;
							$sku_entrega = $sku_rs_dom;
						} else {
							$costo_entrega = $costo_rs_ocu+0;
							$sku_entrega = $sku_rs_ocu;
						}
						$cedis = $rm_origen_rs;
					}

					$puntos_a_generar = 0;
					// calcular los puntos a generar (solo empresas whirlpool o invitados)
					if ($empresa_whirlpool || $empresa == 178) { // o invitados whirlpool
						$puntos_a_generar = calcula_puntos($modelo,$precio_empleado);
					}
	
					// detectar si es garantía
					if ($rowPRO['es_garantia']) {
						$es_garantia = $rowPRO['es_garantia']; // 1 2 o 3
						//if (!$es_garantia) $es_garantia = 1;
						
						// generar token para validar garantias
						srand((double)microtime()*1000000);
						$token_garantia = '';
						for ($i=1; $i<=8; $i++) {
						 if (rand(0,1)==0) // letra
						   $token_garantia .= chr(rand(97,102));
						 else // numero
						   $token_garantia .= chr(rand(48,57));
						}
						// insertar registro en tabla de garantías
						$query = "INSERT garantia (fecha, pedido, modelo, token, origen)
									VALUES ('$fecha',$folio_pedido,'$modelo','$token_garantia','web')";
						$resultadoG = mysql_query($query,$conexion);
						$folio_garantia = mysql_insert_id();			

						// $cedis = 'RM11'; // ya se toma de la categoria
						$loc = '';
						$entrega = 0;
						$costo_entrega = 0;
						$sku_entrega = '';
						$fecha_entrega = date("Y-m-d");
						$tiempo_entrega = 'Entrega digital';
						
					} else {
						$es_garantia = 0;
						$folio_garantia = 0;
					}

					// validación adicional, si no trae precio
					if ($precio_empleado <=0)
						continue;

					
					$subtotal = ($precio_empleado + $costo_entrega) * $cantidad;
					$total_productos += ($precio_empleado  * $cantidad);
					$total_flete += $costo_entrega;

					$total += $subtotal;
					
			
					// insertar partida
					
					$query = "INSERT detalle_pedido (
								pedido,
								partida,
								marca,
								marca_nombre, 
								modelo,
								nombre,
								color,
								combo,
								lista_precios,
								precio_lista,
								precio_empleado,
								cantidad,
								subtotal,
								es_garantia,
								folio_garantia,
								rel_garantia,
								tiempo_entrega,
								fecha_entrega,
								costo_entrega,
								tipo_entrega,
								sucursal_ocurre,
								sku_entrega,
								cedis,
								loc,
								entrega,
								puntos_a_generar
							) VALUES (
								$folio_pedido,
								$partida,
								$marca,
								'$marca_nombre',
								'$modelo',
								'$nombre',
								'$color',
								$clave_combo,
								'$lista_precios',
								$precio_lista,
								$precio_empleado,
								$cantidad,
								$subtotal,
								$es_garantia,
								$folio_garantia,
								'$rel_garantia',
								'$tiempo_entrega',
								'$fecha_entrega',
								$costo_entrega,
								'$tipo_entrega',
								$sucursal_ocurre,
								'$sku_entrega',
								'$cedis',
								$loc,
								$entrega,
								$puntos_a_generar
							)";
					$resultado = mysql_query($query,$conexion);
					$ins_det = mysql_affected_rows();
			
					if ($ins_det <= 0) {
						$error .= "<br>No se pudo insertar detalle de pedido <br>".$query;
					} else { 
					  // disminuir existencias en verifica_pago			
					
					} // se insertó detalle de carrito
	
									
				} /// while detalle de producto de combo
				
			} // fin de combo
			
		} // if item disponible	
	
	} // foreach
	
	// actualiza total en encabezado
	$res = mysql_query("UPDATE pedido SET total = $total, subtotal = $total_productos, fecha_entrega = '$fecha_maxima', costo_envio = $total_flete, act = 1-act 
						 WHERE folio = $folio_pedido LIMIT 1",$conexion);
	$act = mysql_affected_rows();
	if ($act <= 0) {
		$error .= "<br>No se pudo grabar el total y fecha máxima de entrega del pedido";
	}
	
} // if folio_pedido




if (!$error) {

// enviar mail mientras aqui.. despues en verifica_pago
/*	$CR='\r\n';
	$BR='<br>';
	$mensaje = '';
*/

}


if ($error) { 
	mysql_query("ROLLBACK"); 
	$folio_pedido = 0;
}
else {
	mysql_query("COMMIT");
//	mysql_query("ROLLBACK");

}

	


?>
