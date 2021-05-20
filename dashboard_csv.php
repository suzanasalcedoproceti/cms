<?php
// Control de Cambios
// 3 Oct 2016:B+  Agregar código de payment terms (plazo) en Dashboard
if (!include('ctrl_acceso.php')) return;
include('funciones.php');

$modulo=24;
if (!op($modulo))  {
	return;
}
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream; charset=utf-8");
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=WP_Dashboard.csv"); 
ini_set("memory_limit", "256M");

	include('../conexion.php');
	include('lib.php');
	
	$condicion = $_SESSION['ss_condicion_dashboard'];


// en un solo renglón	
$array_enc = array('Folio POS',
                    'Fecha Pedido',
                    'Tipo',
                    'Tienda',
                    'Vendedor',
                    'PO Number',
                    'Cliente',
                    'Tipo Cliente',
                    '#Empleado',
                    'Empresa',
                    '#Empresa',
                    'Material',
                    'Categoria',
                    'Subcategoria',
                    'Vol Rebate',
                    'Lista Precios',
                    'Precio Unitario',
                    '% Descuento',
                    'Descuento',
                    'Razón Descuento',
                    'Total Unitario',
                    'IVA',
                    'Puntos Generados',
                    'Efectivo',
                    'Débito',
                    'Crédito',
                    'MSI',
                    'ODC',
                    'Cheque',
                    'CEP',
                    'Dep Directo',
                    'Puntos WP',
                    'Puntos Flex',
                    'Puntos PEP',
                    'GiftCard',
                    'Sustituto',
                    'Refacturación',
                    'Folio ODC',
					'Plazo',
                    'SKU Póliza',
                    'Folio Garantía',
                    'Entrega',
                    'Costo Entrega',
                    'Total',
                    'Estatus Pago',
                    'Fecha Pago',
                    'Confirmó Pago',
                    'Compromiso Entrega',
                    'Pedido SAP',
                    'Fecha Pedido SAP',
                    'Tipo Pedido',
                    'Unidades',
                    'Delivery',
                    'Fecha Delivery',
                    'Unidades Delivery',
                    'Shipment',
                    'Factura',
                    'Fecha Factura',
                    'Delivery Block',
                    'Cancelación',
                    'Crédito',
                    'Estatus General SAP',
                    'Planta',
                    'Storage',
                    'Sales Org',
                    'Canal',
                    'División',
                    'Proveedor',
                    'Proveedor Logistica',
                    'No.Guia',
                    'Compromiso Entrega',
                    'Fecha Entrega Final',
                    'Estatus',
                    'Entrega',
                    'Comentarios',
                    'Estatus Material',
                    'Estatus Pedido',
                    'Semáforo',
                    'Periodo Pag-Entr',
                    'Periodo Fac-Entr',
                    'Periodo Compr-Entr',
                    'No Return',
                    'Folio Devolución',
                    'Folio Inconformidad',
                    'Tipo Inconformidad',
                    'Fecha Inconformidad',
                    'Estatus Inconformidad',
                    'Feedback');

foreach ($array_enc AS $enc) {
    $var_enc .= utf8_decode($enc).',';
}
$var_enc = substr($var_enc,0,-1);

echo $var_enc;

$query = "SELECT dashboard.*, pedido.pago_msi as msi
		 FROM dashboard 
		 INNER JOIN pedido ON dashboard.folio_pedido = pedido.folio
		 $condicion
			 ORDER BY dashboard.folio_pedido";
 $resultado = mysql_query($query);
 while ($row = mysql_fetch_array($resultado)) {

    switch ($row['tipo_pedido']) {
        case 'V': $tipo_pedido =  'Venta'; break;
        case 'S': $tipo_pedido =  'Sustitucion'; break;
        case 'R': $tipo_pedido =  'Refacturacon'; break;
    }
    switch ($row['tipo_cliente']) {
        case 'E': $tipo_cliente = 'Empleado'; break;
        case 'I': $tipo_cliente = 'Invitado'; break;
        case 'C': $tipo_cliente = 'Corporate'; break;
        case 'A': $tipo_cliente = 'Mercado Abierto'; break;
    }
    if ($row['pct_descuento']>0) 
        $pctdesc = '%';
    else 
        $pctdesc = '';

    if ($row['avance_pedido']==1) $avance = '100%';
    elseif ($row['avance_pedido']==0) $avance = '0%'; 
    else  $avance = ($row['avance_pedido']*100).'%';

    $semaforo = $row['semaforo'];

    // dejar un renglón en blanco fuera del php para que brinque de renglón el CSV
?>

<?php 
// no brincar de renglón
//echo $row['folio_fpedido'].','.fechamy2mx($row['fecha_pedido'],'novacio').','.$tipo_pedido.','.$row['nombre_tienda'].','.$row['nombre_vendedor'].','.$row['po_number'].','.$row['nombre_cliente'].','.$tipo_cliente.','.$row['numero_empleado'].','.$row['nombre_empresa'].','.$row['numero_empresa'].','.$row['material'].','.$row['nombre_categoria'].','.$row['nombre_subcategoria'].','.$row['vol_reb'].','.$row['lista_precios'].','.$row['precio_unitario'].','.nocero($row['pct_descuento']).$pct_descuento.','.nocero($row['descuento']).','.$row['motivo_descuento'].','.$row['total_unitario'].','.$row['iva'].','.$row['puntos_generados'].','.nocero($row['fdp_efectivo']).','.nocero($row['fdp_tdd']).','.nocero($row['fdp_tdc']).','.nocero($row['fdp_odc']).','.nocero($row['fdp_cheque']).','.nocero($row['fdp_cep']).','.nocero($row['fdp_dep']).','.nocero($row['fdp_puntos']).','.nocero($row['fdp_puntos_flex']).','.nocero($row['fdp_puntos_pep']).','.nocero($row['fdp_gc']).','.nocero($row['fdp_sustitucion']).','.nocero($row['fdp_refacturacion']).','.$row['folio_odc'].','.$row['sku_garantia'].','.$row['folio_garantia'].','.$row['entrega'].','.nocero($row['costo_entrega']).','.$row['total'].','.$row['estatus_pago'].','.fecha($row['fecha_pago'],'novacio').','.$row['confirmo_pago'].','.fechamy2mx($row['compromiso_entrega'],'novacio').','.$row['folio_sap'].','.$row['order_date'].','.$row['saty'].','.$row['cantidad_pedido'].','.$row['delivery'].','.$row['delivery_date'].','.$row['cantidad_delivery'].','.$row['shipment'].','.$row['billing_doc'].','.fechamy2mx($row['fecha_factura']).','.$row['delivery_block'].','.nocero($row['reason_rejection']).','.$row['credit_status'].','.$row['overall_status'].','.$row['planta'].','.$row['store_loc'].','.$row['organizacion'].','.$row['canal'].','.$row['division'].','.$row['proveedor'].','.$row['guia'].','.fechamy2mx($row['compromiso_entrega'],'novacio').','.fechamy2mx($row['fecha_entrega'],'novacio').','.$row['estatus_entrega'].','.$row['adicionales'].','.$row['estatus_material'].','.$avance,','.$semaforo.','.nocero($row['periodo_ped_entr']).','.nocero($row['periodo_fac_entr']).','.nocero($row['periodo_com_entr']).','.nocero($row['no_return']).','.$row['folio_devolucion'].','.nocero($row['folio_inconformidad']).','.$row['tipo_inconformidad'].','.fecha($row['fecha_inconformidad'],'novacio').','.$row['estatus_inconformidad'].','.$row['feedback'];

$array_val = array($row['folio_fpedido'],
                    fechamy2mx($row['fecha_pedido'],'novacio'),
                    $tipo_pedido,
                    $row['nombre_tienda'],
                    $row['nombre_vendedor'],
                    $row['po_number'],
                    $row['nombre_cliente'],
                    $tipo_cliente,
                    $row['numero_empleado'],
                    $row['nombre_empresa'],
                    $row['numero_empresa'],
                    $row['material'],
                    $row['nombre_categoria'],
                    $row['nombre_subcategoria'],
                    $row['vol_reb'],
                    $row['lista_precios'],
                    $row['precio_unitario'],
                    nocero($row['pct_descuento']).$pct_descuento,
                    nocero($row['descuento']),
                    $row['motivo_descuento'],
                    $row['total_unitario'],
                    $row['iva'],
                    $row['puntos_generados'],
                    nocero($row['fdp_efectivo']),
                    nocero($row['fdp_tdd']),
                    nocero($row['fdp_tdc']),
                    nocero($row['msi']),
                    nocero($row['fdp_odc']),
                    nocero($row['fdp_cheque']),
                    nocero($row['fdp_cep']),
                    nocero($row['fdp_dep']),
                    nocero($row['fdp_puntos']),
                    nocero($row['fdp_puntos_flex']),
                    nocero($row['fdp_puntos_pep']),
                    nocero($row['fdp_gc']),
                    nocero($row['fdp_sustitucion']),
                    nocero($row['fdp_refacturacion']),
                    $row['folio_odc'],
					$row['plazo_odc'],
                    $row['sku_garantia'],
                    $row['folio_garantia'],
                    $row['entrega'],
                    nocero($row['costo_entrega']),
                    $row['total'],
                    $row['estatus_pago'],
                    fecha($row['fecha_pago'],'novacio'),
                    $row['confirmo_pago'],
                    fechamy2mx($row['compromiso_entrega'],'novacio'),
                    $row['folio_sap'],
                    $row['order_date'],
                    $row['saty'],
                    $row['cantidad_pedido'],
                    $row['delivery'],
                    $row['delivery_date'],
                    $row['cantidad_delivery'],
                    $row['shipment'],
                    $row['billing_doc'],
                    $row['billing_date'],
                    $row['delivery_block'],
                    nocero($row['reason_rejection']),
                    $row['credit_status'],
                    $row['overall_status'],
                    $row['planta'],
                    $row['store_loc'],
                    $row['organizacion'],
                    $row['canal'],
                    $row['division'],
                    $row['proveedor'],
                    $row['proveedor_logistica'],
                    $row['guia'],
                    fechamy2mx($row['compromiso_entrega'],'novacio'),
                    fechamy2mx($row['fecha_entrega'],'novacio'),
                    $row['estatus_entrega'],
                    $row['adicionales'],
                    $row['estatus_material'],
                    $avance,
                    $semaforo,
                    nocero($row['periodo_ped_entr']),
                    nocero($row['periodo_fac_entr']),
                    nocero($row['periodo_com_entr']),
                    nocero($row['no_return']),
                    $row['folio_devolucion'],
                    nocero($row['folio_inconformidad']),
                    $row['tipo_inconformidad'],
                    fecha($row['fecha_inconformidad'],'novacio'),
                    $row['estatus_inconformidad'],
                    $row['feedback']);

$var_val = '';
foreach ($array_val AS $val) {
    $xval = str_replace(',', '', $val);
    $var_val .= trim($xval).',';
}
$var_val = substr($var_val,0,-1);

echo $var_val;

}
?>