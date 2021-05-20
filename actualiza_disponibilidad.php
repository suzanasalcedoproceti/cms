<?php
// script para actualizar estatus de producto (mostrar/no mostrar, vender/no vender)

include("../conexion.php");
$producto = mysql_real_escape_string($_GET['producto']);

if ($producto) {

	$res = mysql_query("SELECT ocultar, mostrar FROM producto WHERE modelo = '$producto'",$conexion);
	$row = mysql_fetch_assoc($res);
	
	echo "<br>PRODUCTO: ".$producto;
	echo "<br>Valores anteriores:";
	echo "<br>Mostrar:";
	if ($row['ocultar']) echo 'NO'; else echo 'SI';
	
	echo "<br>Vender:";
	if ($row['mostrar']) echo 'SI'; else echo 'NO';
	
	echo "<br><br>Analizando estatus actual.....<br><br>";
	
	$query = "SELECT SUM(existencia) AS total_ex FROM existencia WHERE producto = '$producto' 
					 AND ( ((cedis = 'RM01' OR cedis = 'RM02' OR cedis = 'RM06' OR cedis = 'RS01') AND loc = 1) OR (cedis = 'RM12')  )
					 AND existencia > 0 
					GROUP BY producto ";
					
    $resultado_acum = mysql_query($query,$conexion);
    $row_acum = mysql_fetch_assoc($resultado_acum);
	$existencias = $row_acum['total_ex']+0;

    $query = "SELECT estatus, vol_reb FROM existencia WHERE producto = '$producto' AND vol_reb != '' LIMIT 1";
    $resultado_vr = mysql_query($query,$conexion);
    $row_vr = mysql_fetch_array($resultado_vr);
	$estatus = $row_vr['estatus'];

 
	echo "<br>Estatus: ".$estatus;
	echo "<br>Existencias RM01,RM02,RM06 LOC1 / RM12: ".$existencias;
	echo "<br><br>Nueva configuracion: <br>";
	
	include("disponibilidad_prod.php");
	
	
	$query_upd = "UPDATE producto SET mostrar = $mostrar, ocultar = $ocultar, act=1-act WHERE modelo = '$producto' LIMIT 1";
	$resultadoU = mysql_query($query_upd,$conexion);
	
	echo "<br>Mostrar:";
	if ($ocultar) echo 'NO'; else echo 'SI';
	
	echo "<br>Vender:";
	if ($mostrar) echo 'SI'; else echo 'NO';
	echo $query_upd;
	
	echo "<br>Fin";

}


?>