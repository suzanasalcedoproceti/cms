<?php

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//header("Content-type: application/vnd.ms-excel");  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=WP_Reporte_de_Pólizas.csv"); 

date_default_timezone_set("America/Mexico_City");

if (!include('ctrl_acceso.php')) return;
include('funciones.php');
include('lib.php');
$modulo=30;
if (!op($modulo)) return;

include('../conexion.php');

$fecha = $_POST['fecha'];
$folio = $_POST['folio'];
$folio_gar = $_POST['folio_gar'];
$folio_sap = $_POST['folio_sap'];

$orden='ORDER BY folio DESC';

 // construir la condición de búsqueda
$condicion = "WHERE (    (dashboard.categoria = 15 OR (dashboard.categoria = 21 AND dashboard.subcategoria = 130)) 
                                            OR (dashboard.categoria = 19 AND (dashboard.subcategoria = 98 OR dashboard.subcategoria = 99))
                                            OR (dashboard.categoria = 25 AND (dashboard.subcategoria = 36 OR dashboard.subcategoria = 101))
                                           
                                         )";

$fecha_desde = convierte_fecha(substr($fecha,0,10));
$fecha_hasta = convierte_fecha(substr($fecha,13,10));
$condicion .= " AND dashboard.fecha_pedido BETWEEN '$fecha_desde' AND '$fecha_hasta' ";

if ($folio) 
	$condicion .= " AND dashboard.folio_pos = '$folio' ";

if ($folio_sap) 
	$condicion .= " AND dashboard.folio_sap = '$folio_sap' ";	

if ($folio_gar) 
	$condicion .= " AND dashboard.folio_garantia = '$folio_gar' ";



 $query = "SELECT dashboard.*, pedido.nombre, 
				  CONCAT(pedido.envio_calle,' ',pedido.envio_exterior,' ',pedido.envio_interior) AS direccion, pedido.envio_colonia, envio_ciudad_nombre, 
						 envio_estado, envio_cp, envio_telefono_casa,
				  CONCAT(pedido.pers_calle,' ',pedido.pers_exterior) AS direccion_pers, pedido.pers_colonia, pers_ciudad, pers_estado, pers_cp, pers_telefono, pedido.fdp_tdc_folio, pedido.fdp_tdd_folio, pedido.fdp_te_folio
			FROM dashboard
			LEFT JOIN pedido ON dashboard.folio_pedido = pedido.folio


		$condicion $orden ";

$resultado = mysql_query($query,$conexion);
while ($row = mysql_fetch_assoc($resultado)){ 

$folio_pedido = $row['folio_pedido']+0;
$folio_garantia = $row['folio_garantia']+0;
$resultadoGAR = mysql_query("SELECT inicio_garantia, fin_garantia,numero_serie, fecha_compra_producto, estatus, estatus_log FROM garantia WHERE folio = $folio_garantia AND pedido = $folio_pedido");
$rowGAR = mysql_fetch_assoc($resultadoGAR);
$modelo = $row['material'];
$resultadoPRO = mysql_query("SELECT es_garantia FROM producto WHERE modelo = '$modelo'",$conexion);
$rowPRO = mysql_fetch_assoc($resultadoPRO);
$cliente = $row['cliente'];
$resultadoCTE = mysql_query("SELECT nombre, apellido_paterno, apellido_materno, pers_celular, email FROM cliente WHERE clave = $cliente",$conexion);
$rowCTE = mysql_fetch_assoc($resultadoCTE);

$fdp = '';
if ($row['fdp_efectivo']>0) $fdp.= ' EFE';
if ($row['fdp_tdc']>0) $fdp.= ' TDC';
if ($row['fdp_tdd']>0) $fdp.= ' TDD';
if ($row['fdp_cep']>0) $fdp.= ' CEP';
if ($row['fdp_cheque']>0) $fdp.= ' CHE';
if ($row['fdp_dep']>0) $fdp.= ' DEP';
if ($row['fdp_odc']>0) $fdp.= ' ODC';
if ($row['fdp_puntos']>0) $fdp.= '/ PTS';
if ($row['fdp_puntos_flex']>0) $fdp.= ' PFLX';
if ($row['fdp_puntos_pep']>0) $fdp.= ' PPEP';
if ($row['fdp_sustitucion']>0) $fdp.= ' SUS';
if ($row['fdp_refacturacion']>0) $fdp.= ' REF';
if ($row['fdp_gc']>0) $fdp.= ' GIFT';

$direccion = str_replace(',','',$row['direccion']);
$colonia = str_replace(',','',$row['envio_colonia']);
$ciudad = str_replace(',','',$row['envio_ciudad_nombre']);
$estado = str_replace(',','',$row['envio_estado']);
$cp = $row['envio_cp'];
$telefono = str_replace(',','',$row['envio_telefono_casa']);
$celular =  str_replace(',','',$row['envio_telefono_celular']);

if (!$direccion) {
	$direccion = str_replace(',','',$row['direccion_pers']);
	$colonia = str_replace(',','',$row['pers_colonia']);
	$ciudad = str_replace(',','',$row['pers_ciudad']);
	$estado = str_replace(',','',$row['pers_estado']);
	$cp = $row['pers_cp'];
	$telefono = str_replace(',','',$row['pers_telefono']);
	$celular = '';
}
$resultadoEDO = mysql_query("SELECT clave_polizas AS estado_polizas FROM estado WHERE clave = '$estado'",$conexion);
$rowEDO = mysql_fetch_assoc($resultadoEDO);
$nombre_estado = $rowEDO['estado_polizas'];

?>
<?php echo $row['folio_pos'];?>,<?php echo $row['folio_sap'];?>,<?php echo $row['folio_garantia'];?>,<?php echo fecha_dash($row['fecha_pedido']);?>,<?php echo fecha_dash($rowGAR['inicio_garantia'],'novacio');?>,<?php echo fecha_dash($rowGAR['fin_garantia'],'novacio');?>,<?php echo $row['sku_garantia'];?>,<?php echo $rowGAR['numero_serie'];?>,<?php echo $row['material'];?>,<?php echo $rowPRO['es_garantia'];?>,<?php echo $row['total_unitario'];?>,<?php echo $row['iva'];?>,<?php echo $row['total'];?>,MXN,<?php echo $fdp;?>,<?php echo $row['nombre_vendedor'];?>,<?php echo ($rowCTE['nombre']) ? ($rowCTE['nombre']) : ($row['nombre']);?>,<?php echo $rowCTE['apellido_paterno'].' '.$rowCTE['apellido_materno'];?>,<?php echo $direccion;?>,<?php echo $colonia;?>,<?php echo $ciudad;?>,<?php echo $nombre_estado;?>,<?php echo $cp;?>,México,<?php echo $telefono;?>,<?php echo $celular;?>,<?php echo $rowCTE['email']?>,<?php echo  fecha_dash($row['fecha_compra_producto'],'novacio');?>,<?php echo $row['fdp_tdc_folio']?>,<?php echo $row['fdp_tdd_folio']?>,<?php echo $row['fdp_te_folio']?>,<?php echo $rowGAR['estatus']?>

<?php	 } // WHILE
	 mysql_close();
	 
/*
?>
<?php echo $row['folio_pos'];?>","<?php echo $row['folio_sap'];?>","<?php echo $row['folio_garantia'];?>",<?php echo fecha_dash($row['fecha_pedido']);?>,<?php echo fecha_dash($rowGAR['inicio_garantia'],'novacio');?>,<?php echo fecha_dash($rowGAR['fin_garantia'],'novacio');?>,<?php echo $row['sku_garantia'];?>,<?php echo $row['material'];?>,<?php echo $rowPRO['es_garantia'];?>,<?php echo $row['total_unitario'];?>,<?php echo $row['iva'];?>,<?php echo $row['total'];?>,MXN,<?php echo $fdp;?>,"<?php echo $row['nombre_vendedor'];?>","<?php echo ($rowCTE['nombre']) ? ($rowCTE['nombre']) : ($row['nombre']);?>","<?php echo $rowCTE['apellido_paterno'].' '.$rowCTE['apellido_paterno'];?>","<?php echo $row['direccion'];?>","<?php echo $row['pers_colonia'];?>","<?php echo $row['pers_ciudad'];?>","<?php echo $rowEDO['estado_polizas'];?>","<?php echo $row['pers_cp'];?>","México","<?php echo $row['pers_telefono'];?>","<?php echo $rowCTE['pers_celular'];?>","<?php echo $rowCTE['email'].chr(10).chr(13);?>"
<?php	 } // WHILE
	 mysql_close();
?>


*/	 
	 
	 
?>