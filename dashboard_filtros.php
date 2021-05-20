<?php

// tipo_pedido
$arr_tipo_pedido = array();
$iel = 0;
$arr_tipo_pedido[1]['clave'] = 'V';
$arr_tipo_pedido[1]['nombre'] = 'Venta';
$arr_tipo_pedido[2]['clave'] = 'S';
$arr_tipo_pedido[2]['nombre'] = 'Sustituto';
$arr_tipo_pedido[3]['clave'] = 'R';
$arr_tipo_pedido[3]['nombre'] = 'Refacturación';


// empresa
$arr_empresa = array();
$iel = 0;
$resEMP = mysql_query("SELECT clave, nombre FROM empresa ORDER BY nombre",$conexion);
while ($rowEMP = mysql_fetch_array($resEMP)) {
	$iel++;
	$arr_empresa[$iel]['clave'] = $rowEMP['clave'];
	$arr_empresa[$iel]['nombre'] = $rowEMP['nombre'];
}	

// tienda
$arr_tienda = array();
$iel = 0;
if( op(32) && op(24) )	
{
	$tienda_service = "";
}
elseif(op(24))
{
	$tienda_service = " WHERE tienda_service=0 ";
}
else
{
	$tienda_service = " WHERE tienda_service=1 ";
}

$resTIE = mysql_query("SELECT clave, nombre FROM tienda ".$tienda_service." ORDER BY nombre",$conexion);
while ($rowTIE = mysql_fetch_array($resTIE)) {	
	$iel++;
	$arr_tienda[$iel]['clave'] = $rowTIE['clave'];
	$arr_tienda[$iel]['nombre'] = $rowTIE['nombre'];
}	

// vendedor
$arr_vendedor = array();
$iel = 0;
$resVEN = mysql_query("SELECT usuario_tienda.clave, usuario_tienda.nombre, tienda.login 
						 FROM usuario_tienda 
						 LEFT JOIN tienda ON usuario_tienda.tienda = tienda.clave 
						ORDER BY tienda.login, usuario_tienda.nombre ",$conexion);
while ($rowVEN = mysql_fetch_array($resVEN)) {	
	$iel++;
	$arr_vendedor[$iel]['clave'] = $rowVEN['clave'];
	$arr_vendedor[$iel]['nombre'] = "[".$rowVEN['login']."] ".$rowVEN['nombre'];
}	

// tipo_cliente
$arr_tipo_cliente = array();
$iel = 0;
$arr_tipo_cliente[1]['clave'] = 'E';
$arr_tipo_cliente[1]['nombre'] = 'Empleado';
$arr_tipo_cliente[2]['clave'] = 'I';
$arr_tipo_cliente[2]['nombre'] = 'Invitado';
$arr_tipo_cliente[3]['clave'] = 'C';
$arr_tipo_cliente[3]['nombre'] = 'Corporate';
$arr_tipo_cliente[4]['clave'] = 'A';
$arr_tipo_cliente[4]['nombre'] = 'Mercado Abierto';

// proveedor
$arr_proveedor = array();
$iel = 0;
$resPRV = mysql_query("SELECT DISTINCT proveedor FROM dashboard WHERE proveedor != '' ORDER BY proveedor",$conexion);
while ($rowPRV = mysql_fetch_array($resPRV)) {
	$iel++;
	$cod_proveedor = str_replace(' ','_',trim($rowPRV['proveedor']));
	$arr_proveedor[$iel]['clave'] = $cod_proveedor;
	$arr_proveedor[$iel]['nombre'] = $rowPRV['proveedor'];
}	

// estatus_pedido
$arr_estatus_pedido = array();
$iel = 0;
$arr_estatus_pedido[1]['clave'] = 'Completo';
$arr_estatus_pedido[1]['nombre'] = 'Completo';
$arr_estatus_pedido[2]['clave'] = 'Incompleto';
$arr_estatus_pedido[2]['nombre'] = 'Incompleto';
$arr_estatus_pedido[3]['clave'] = 'Cancelado';
$arr_estatus_pedido[3]['nombre'] = 'Cancelado';

// estatus_entrega
$arr_estatus_entrega = array();
$iel = 0;
$resEE = mysql_query("SELECT DISTINCT estatus_entrega FROM dashboard WHERE estatus_entrega != '' ORDER BY estatus_entrega",$conexion);
while ($rowEE = mysql_fetch_array($resEE)) {
	$iel++;
  	$cod_estatus_entrega = str_replace(' ','_',trim($rowEE['estatus_entrega']));
	$arr_estatus_entrega[$iel]['clave'] = $cod_estatus_entrega;
	$arr_estatus_entrega[$iel]['nombre'] = $rowEE['estatus_entrega'];
}	
	
// semaforo
$arr_semaforo = array();
$iel = 0;
$arr_semaforo[1]['clave'] = 'verde';
$arr_semaforo[1]['nombre'] = 'Verde';
$arr_semaforo[2]['clave'] = 'amarillo';
$arr_semaforo[2]['nombre'] = 'Amarillo';
$arr_semaforo[3]['clave'] = 'rojo';
$arr_semaforo[3]['nombre'] = 'Rojo';




?>