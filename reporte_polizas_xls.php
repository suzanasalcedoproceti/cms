<?php

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Content-type: application/vnd.ms-excel");  
/*    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
	*/
header("Content-Disposition: attachment; filename=WP Reporte de Pólizas.xls"); 

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

?>

        <table >

      
		 <tr>
            <td nowrap="nowrap"><b>Pedido POS</b></td>
            <td nowrap="nowrap"><strong>Pedido SAP</strong></td>
            <td nowrap="nowrap"><strong>Folio Garant&iacute;a</strong></td>
            <td nowrap="nowrap"><strong>Fecha Pedido</strong></td>
            <td nowrap="nowrap"><strong>Inicio Cobertura</strong></td>
            <td nowrap="nowrap"><strong>Fin  Cobertura</strong></td>
            <td align="center"><strong>Modelo Relacionado</strong></td>
            <td align="center"><strong>N&uacute;mero serie</strong></td>            
            <td align="center"><strong>SKU</strong></td>
            <td align="center"><strong>Plazo</strong></td>
            <td align="center"><strong>Subtotal</strong></td>
            <td align="center"><strong>IVA</strong></td>
            <td align="center"><strong>Total</strong></td>
            <td align="center"><strong>Moneda</strong></td>
            <td align="center"><strong>Forma de Pago</strong></td>
            <td align="center"><strong>Vendedor</strong></td>
            <td align="center"><strong>Nombre(s)</strong></td>
            <td align="center"><strong>Apellidos</strong></td>
            <td align="center"><strong>Calle y N&uacute;mero</strong></td>
            <td align="center"><strong>Colonia</strong></td>
            <td align="center"><strong>Ciudad</strong></td>
            <td align="center"><strong>Regi&oacute;n</strong></td>
            <td align="center"><strong>C&oacute;digo Postal</strong></td>
            <td align="center"><strong>Pa&iacute;s</strong></td>
            <td align="center"><strong>Tel&eacute;fono 1</strong></td>
            <td align="center"><strong>Tel&eacute;fono 2</strong></td>
            <td align="center"><strong>E-mail</strong></td>
            <td align="center"><strong>Fecha Compra Prod</strong></td>
            <td align="center"><strong>Autorizaci&oacute;n TC</strong></td>
            <td align="center"><strong>Autorizaci&oacute;n TD</strong></td>
            <td align="center"><strong>Autorizaci&oacute;n TE</strong></td>
          </tr>
          <?php

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
				$resultadoGAR = mysql_query("SELECT inicio_garantia, fin_garantia, numero_serie, fecha_compra_producto, estatus, estatus_log FROM garantia WHERE folio = $folio_garantia AND pedido = $folio_pedido");
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
          <tr align="center">
            <td ><?php echo $row['folio_pos'];?></td>
            <td ><?php echo $row['folio_sap'];?></td>
            <td ><?php echo $row['folio_garantia'];?></td>
            <td ><?php echo fecha_dash($row['fecha_pedido']);?></td>
            <td ><?php echo fecha_dash($rowGAR['inicio_garantia'],'novacio');?></td>
            <td ><?php echo fecha_dash($rowGAR['fin_garantia'],'novacio');?></td>
            <td><?php echo $row['sku_garantia'];?></td>
            <td><?php echo $rowGAR['numero_serie'];?></td>            
            <td><?php echo $row['material'];?></td>
            <td><?php echo $rowPRO['es_garantia'];?></td>
            <td align="right"><?php echo $row['total_unitario'];?></td>
            <td align="right"><?php echo $row['iva'];?></td>
            <td align="right"><?php echo $row['total'];?></td>
            <td>MXN</td>
            <td><?php echo $fdp;?></td>
            <td><?=$row['nombre_vendedor'];?></td>
            <td align="left"><?php echo ($rowCTE['nombre']) ? ($rowCTE['nombre']) : ($row['nombre']);?></td>
            <td align="left"><?php echo $rowCTE['apellido_paterno'].' '.$rowCTE['apellido_paterno'];?></td>
            <td align="left"><?php echo $direccion;?></td>
            <td align="left"><?php echo $colonia;?></td>
            <td align="left"><?php echo $ciudad;?></td>
            <td><?php echo $nombre_estado;?></td>
            <td align="left"><?php echo $cp;?></td>
            <td>México</td>
            <td><?php echo $telefono;?></td>
            <td><?php echo $celular;?></td>
            <td align="left"><?php echo $rowCTE['email'];?></td>
            <td><?php echo fecha_dash($rowGAR['fecha_compra_producto'],'novacio');?></td>
            <td><?php echo $row['fdp_tdc_folio'];?></td>
            <td><?php echo $row['fdp_tdd_folio'];?></td>
            <td><?php echo $row['fdp_te_folio'];?></td>
            <td><?php echo $row['estatus'];?></td>
		  </tr>
          <?php
                 } // WHILE
                 mysql_close();
              ?>
        </table>