<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>

<body>

<?php

include("../conexion.php");

$limite_pe = 15;	
$inicio = date("Y").'-01-01';  // compras desde el 01 enero 2016 formato YYYY-MM-DD
//$inicio = "2014-01-01";
$actualiza = false;  // true = actualizar campo cliente.pe_disponibles.. false = no lo actualiza, solo consulta
$solo_diferencias = false;  // true = solo muestra incongruencias


?>

<table width="100%" border="1">
  <tr>
    <td>id</td>
    <td>Num empleado</td>
    <td>Cliente</td>
    <td>Empresa</td>
    <td>PE Disponibles actualmente</td>
    <td>PE Comprados</td>
	<td>Invitados</td>

    <td>PE Comprados Invitados</td>
    <td>PE Disponibles (15-comprados)</td>
    <td>PE Comprados <br />
      Pendientes de Pago</td>
    <td>&nbsp;</td>
  </tr>
<? 
	$query = "SELECT cliente.*, empresa.nombre AS nombre_empresa FROM cliente 
				LEFT JOIN empresa ON cliente.empresa = empresa.clave
			   WHERE empresa.empresa_whirlpool = 1 
		    	ORDER BY cliente.empresa, cliente.nombre, cliente.apellido_paterno
				LIMIT 800000
			  ";
	$resultadoCTE = mysql_query($query,$conexion);
   while ($rowCTE = mysql_fetch_array($resultadoCTE))  { 
   
   	// buscar compras de precios especiales de lo que va del año. No considerar cancelados ni rechazados por TDC

	$clave_cliente = $rowCTE['clave'];
	
	$queryC = "SELECT SUM(detalle_pedido.cantidad) AS total_compras
				FROM detalle_pedido
				LEFT JOIN pedido ON detalle_pedido.pedido = pedido.folio
				WHERE pedido.cliente = $clave_cliente
				  AND pedido.fecha >= '$inicio'
				  AND pedido.estatus != '9' AND pedido.estatus != '2'
				  AND detalle_pedido.tipo_precio = 'especial'";
				  
	$resultadoCOM = mysql_query($queryC,$conexion);
	$rowCOM = mysql_fetch_array($resultadoCOM);
	
	$total_pe = $rowCOM['total_compras'];
	
	// calcular total de compras por invitados
	$total_pe_inv = 0;
	$total_invitados = 0;
	$resultadoINV = mysql_query("SELECT clave FROM cliente WHERE empresa=178 AND  invitado_por = $clave_cliente",$conexion);
	while ($rowINV = mysql_fetch_array($resultadoINV)) {
		$total_invitados ++;
		$clave_invitado = $rowINV['clave'];
		
		$queryPEI = "SELECT SUM(detalle_pedido.cantidad) AS total_compras
					FROM detalle_pedido
					LEFT JOIN pedido ON detalle_pedido.pedido = pedido.folio
					WHERE pedido.cliente = $clave_invitado
					  AND pedido.fecha >= '$inicio'
					  AND pedido.estatus != '9' AND pedido.estatus != '2'
					  AND detalle_pedido.tipo_precio = 'especial'";
					  
		$resultadoPEI = mysql_query($queryPEI,$conexion);
		$rowPEI = mysql_fetch_array($resultadoPEI);
		
		$total_pe_inv += $rowPEI['total_compras'];
	} // while invitados
	
	$pe_disponibles_hoy = $limite_pe - $total_pe -$total_pe_inv;
	if ($pe_disponibles_hoy>$limite_pe) $pe_disponibles_hoy = $limite_pe;
	if ($pe_disponibles_hoy<0) $pe_disponibles_hoy = 0;

	////////////
	///// calcular compras de PE por el cliente, pero que están pendientes de pago
	
	$queryCP = "SELECT SUM(detalle_pedido.cantidad) AS total_compras
				FROM detalle_pedido
				LEFT JOIN pedido ON detalle_pedido.pedido = pedido.folio
				WHERE pedido.cliente = $clave_cliente
				  AND pedido.fecha >= '$inicio'
				  AND pedido.estatus != '9' AND pedido.estatus != '2' AND pedido.estatus != '1'
				  AND detalle_pedido.tipo_precio = 'especial'";
				  
	$resultadoCP = mysql_query($queryCP,$conexion);
	$rowCP = mysql_fetch_array($resultadoCP);
	
	$total_pe_pendientes = $rowCP['total_compras'];


	
	if ($rowCTE['pe_disponibles'] == $pe_disponibles_hoy && $solo_diferencias) 
		continue;
	
	$accion = '';
	if ($actualiza) {
		
		//$queryUPD = mysql_query("UPDATE cliente SET pe_disponibles = $pe_disponibles_hoy, act=1-act WHERE clave = $clave_cliente LIMIT 1",$conexion);
		//$act=mysql_affected_rows();
		//if ($act>0) $accion = 'Update OK';
		//else $accion = 'Error update';
		
	}
   
?>
  <tr>
    <td><?=$rowCTE['clave'];?></td>
    <td><?=$rowCTE['numero_empleado'];?></td>
    <td><?=$rowCTE['nombre']." ".$rowCTE['apellido_paterno'];?></td>
    <td><?=$rowCTE['nombre_empresa'];?></td>
    <td><?=$rowCTE['pe_disponibles'];?></td>
    <td><?=$total_pe;?></td>
    <td><?=$total_invitados;?></td>
    <td><?=$total_pe_inv;?></td>
    <td><?=$pe_disponibles_hoy;?></td>
    <td><?=$total_pe_pendientes;?></td>
    <td><?=$accion;?></td>
  </tr>
<? } // while cliente ?>
</table>
</body>
</html>