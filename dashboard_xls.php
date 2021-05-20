<?
    if (!include('ctrl_acceso.php')) return;
   	include('funciones.php');

	$modulo=24;
	if (!op($modulo))  {
		return;
	}

	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Content-type: application/vnd.ms-excel");  
	header("Content-Disposition: attachment; filename=WP Dashboard.xls");  


	include('../conexion.php');
	include('lib.php');
	
	$condicion = $_SESSION['ss_condicion_dashboard'];


	  
?>            
              <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
                <thead>
                  <tr>
                    <th nowrap="nowrap">Folio POS</th>
                    <th nowrap="nowrap">Fecha<br />
                      Pedido</th>
                    <th nowrap="nowrap">Tipo</th>
                    <th nowrap="nowrap">Tienda</th>
                    <th nowrap="nowrap">Vendedor</th>
                    <th nowrap="nowrap">PO Number</th>
                    <th nowrap="nowrap">Cliente</th>
                    <th nowrap="nowrap">Tipo<br />
                      Cliente</th>
                    <th align="center">#Empleado</th>
                    <th align="center">Empresa</th>
                    <th align="center">#Empresa</th>
                    <th nowrap="nowrap">Material</th>
                    <th align="center">Categor&iacute;a</th>
                    <th align="center">Subcategor&iacute;a</th>
                    <th align="center">Vol Rebate</th>
                    <th align="center">Lista<br />
                      Precios</th>
                    <th align="center">Precio<br />
                      Unitario</th>
                    <th align="center">% Descuento</th>
                    <th align="center">Descuento</th>
                    <th align="center">Raz&oacute;n Descuento</th>
                    <th align="center">Total Unitario</th>
                    <th align="center">IVA</th>
                    <th align="center">Puntos Generados</th>
                    <!--th align="center">Forma Pago</th-->
                    <th align="center">Efectivo</th>
                    <th align="center">D&eacute;bito</th>
                    <th align="center">Cr&eacute;dito</th>
                    <th align="center">ODC</th>
                    <th align="center">Cheque</th>
                    <th align="center">CEP</th>
                    <th align="center">Dep<br />
                      Directo</th>
                    <th align="center">Puntos WP</th>
                    <th align="center">Puntos<br />
                      Flex</th>
                    <th align="center">Puntos<br />
                      PEP</th>
                    <th align="center">GiftCard</th>
                    <th align="center">Sustituto</th>
                    <th align="center">Refacturaci&oacute;n</th>
                    <th align="center">Folio<br />
                      ODC</th>
                    <th align="center">SKU<br />
                      P&oacute;liza</th>
                    <th align="center">Folio Garant&iacute;a</th>
                    <th align="center">Entrega</th>
                    <th nowrap="nowrap">Costo<br />
                      Entrega</th>
                    <th nowrap="nowrap">Total</th>
                    <th nowrap="nowrap">Estatus <br />
                      Pago</th>
                    <th nowrap="nowrap">Fecha Pago</th>
                    <th nowrap="nowrap">Confirm&oacute;<br />
                      Pago</th>
                    <th nowrap="nowrap">Compromiso<br />
                      Entrega</th>
                    <th nowrap="nowrap">Pedido SAP</th>
                    <th nowrap="nowrap">Fecha <br />
                      Pedido SAP</th>
                    <th nowrap="nowrap">Tipo <br />
                      Pedido</th>
                    <th nowrap="nowrap">Unidades</th>
                    <th nowrap="nowrap">Delivery</th>
                    <th nowrap="nowrap">Fecha<br />
                      Delivery</th>
                    <th nowrap="nowrap">Unidades<br />
                      Delivery</th>
                    <th nowrap="nowrap">Shipment</th>
                    <th nowrap="nowrap">Factura</th>
                    <th nowrap="nowrap">Fecha Factura</th>
                    <th>Delivery Block</th>
                    <th nowrap="nowrap">Cancelaci&oacute;n</th>
                    <th nowrap="nowrap">Cr&eacute;dito</th>
                    <th>Estatus General <br />
                      SAP</th>
                    <!--<th>Fecha Cambio <br />
                      Estatus</th>-->
                    <th nowrap="nowrap">Planta</th>
                    <th nowrap="nowrap">Storage</th>
                    <th nowrap="nowrap">Sales Org</th>
                    <th nowrap="nowrap">Canal</th>
                    <th nowrap="nowrap">Division</th>
                    <th nowrap="nowrap">Proveedor</th>
                    <th nowrap="nowrap">No.Gu&iacute;a</th>
                    <th nowrap="nowrap">Compromiso<br />
                      Entrega</th>
                    <th nowrap="nowrap">Fecha Entrega<br />
                      Final</th>
                    <th nowrap="nowrap">Estatus<br />
                      Entrega</th>
                    <th nowrap="nowrap">Comentarios</th>
                    <th nowrap="nowrap">Estatus<br />
                      Material</th>
                    <th nowrap="nowrap">Estatus<br />
                      Pedido</th>
                    <th nowrap="nowrap">Sem&aacute;foro</th>
                    <th nowrap="nowrap">Periodo<br />
                      Pag-Entr</th>
                    <th nowrap="nowrap">Periodo<br />
                      Fac-Entr</th>
                    <th nowrap="nowrap">Periodo<br />
                      Compr-Entr</th>
                    <th nowrap="nowrap">No Return</th>
                    <th>Folio<br />
                      Devoluci&oacute;n</th>
                    <th nowrap="nowrap">Folio<br />
                      Inconformidad</th>
                    <th nowrap="nowrap">Tipo<br />
                      Inconformidad</th>
                    <th nowrap="nowrap">Fecha<br />
                      Inconformidad</th>
                    <th nowrap="nowrap">Estatus<br />
                      Inconformidad</th>
                    <th nowrap="nowrap">Feedback</th>
                  </tr>
                </thead>
                <tbody>
                  <? 
			  
			  	 $query = "SELECT *
							 FROM dashboard 
							 $condicion
				 			 ORDER BY dashboard.folio_pedido";
			  	 $resultado = mysql_query($query);
			     while ($row = mysql_fetch_array($resultado)) {

			  ?>
                  <tr>
                    <td><?=$row['folio_fpedido'];?></td>
                    <td nowrap="nowrap"><?=fechamy2mx($row['fecha_pedido'],'novacio');?></td>
                    <td><? switch ($row['tipo_pedido']) {
						case 'V' : echo 'Venta'; break;
						case 'S' : echo 'Sustituci&oacute;n'; break;
						case 'R' : echo 'Refacturaci&oacute;n'; break;
					   }
					?>
                    </td>
                    <td><?=$row['nombre_tienda'];?></td>
                    <td><div align="left">
                        <?=$row['nombre_vendedor'];?>
                    </div></td>
                    <td><?=$row['po_number'];?></td>
                    <td><div align="left">
                        <?=$row['nombre_cliente'];?>
                    </div></td>
                    <td><? switch ($row['tipo_cliente']) {
							case 'E' : echo 'Empleado'; break;
							case 'I' : echo 'Invitado'; break;
							case 'C' : echo 'Corporate'; break;
							case 'A' : echo 'Mercado Abierto'; break;
					   }
				   ?>
                    </td>
                    <td><?=$row['numero_empleado'];?></td>
                    <td><?=$row['nombre_empresa'];?></td>
                    <td><?=$row['numero_empresa'];?></td>
                    <td><?=$row['material'];?></td>
                    <td><div align="left">
                        <?=$row['nombre_categoria'];?>
                    </div></td>
                    <td><div align="left">
                        <?=$row['nombre_subcategoria'];?>
                    </div></td>
                    <td><?=$row['vol_reb'];?></td>
                    <td><?=$row['lista_precios'];?></td>
                    <td><?=$row['precio_unitario'];?></td>
                    <td><?=nocero($row['pct_descuento']);?>
                        <? if ($row['pct_descuento']>0) echo ' %';?></td>
                    <td><?=nocero($row['descuento']);?></td>
                    <td><?=$row['motivo_descuento'];?></td>
                    <td><?=$row['total_unitario'];?></td>
                    <td><?=$row['iva'];?></td>
                    <td><?=$row['puntos_generados'];?></td>
                    <!--td><?=$row['forma_pago'];?></td-->
                    <td><?=nocero($row['fdp_efectivo']);?></td>
                    <td><?=nocero($row['fdp_tdd']);?></td>
                    <td><?=nocero($row['fdp_tdc']);?></td>
                    <td><?=nocero($row['fdp_odc']);?></td>
                    <td><?=nocero($row['fdp_cheque']);?></td>
                    <td><?=nocero($row['fdp_cep']);?></td>
                    <td><?=nocero($row['fdp_dep']);?></td>
                    <td><?=nocero($row['fdp_puntos']);?></td>
                    <td><?=nocero($row['fdp_puntos_flex']);?></td>
                    <td><?=nocero($row['fdp_puntos_pep']);?></td>
                    <td><?=nocero($row['fdp_gc']);?></td>
                    <td><?=nocero($row['fdp_sustitucion']);?></td>
                    <td><?=nocero($row['fdp_refacturacion']);?></td>
                    <td><?=$row['folio_odc'];?></td>
                    <td><?=$row['sku_garantia'];?></td>
                    <td><?=$row['folio_garantia'];?></td>
                    <td><?=$row['entrega'];?></td>
                    <td><?=nocero($row['costo_entrega']);?></td>
                    <td><?=$row['total'];?></td>
                    <td><?=$row['estatus_pago'];?></td>
                    <td><?=fecha($row['fecha_pago'],'novacio');?></td>
                    <td><?=$row['confirmo_pago'];?></td>
                    <td><?=fechamy2mx($row['compromiso_entrega'],'novacio');?></td>
                    <td><?=$row['folio_sap'];?></td>
                    <td><?=$row['order_date'];?></td>
                    <td><?=$row['saty'];?></td>
                    <td><?=$row['cantidad_pedido'];?></td>
                    <td><?=$row['delivery'];?></td>
                    <td><?=$row['delivery_date'];?></td>
                    <td><?=$row['cantidad_delivery'];?></td>
                    <td><?=$row['shipment'];?></td>
                    <td><?=$row['billing_doc'];?></td>
                    <td><?=fechamy2mx($row['fecha_factura']);?></td>
                    <td><?=$row['delivery_block'];?></td>
                    <td><?=nocero($row['reason_rejection']);?></td>
                    <td><?=$row['credit_status'];?></td>
                    <td><?=$row['overall_status'];?></td>
                    <td>&nbsp;</td>
                    <td><?=$row['planta'];?></td>
                    <td><?=$row['store_loc'];?></td>
                    <td><?=$row['organizacion'];?></td>
                    <td><?=$row['canal'];?></td>
                    <td><?=$row['division'];?></td>
                    <td><?=$row['proveedor'];?></td>
                    <td><?=$row['guia'];?></td>
                    <td><?=fechamy2mx($row['compromiso_entrega'],'novacio');?></td>
                    <td><?=fechamy2mx($row['fecha_entrega'],'novacio');?></td>
                    <td><?=$row['estatus_entrega'];?></td>
                    <td><?=$row['adicionales'];?></td>
                    <td><?=$row['estatus_material'];?></td>
                    <td><? 
            if ($row['avance_pedido']==1) echo '100%';
            elseif ($row['avance_pedido']==0) echo '0%'; 
            else  echo number_format($row['avance_pedido']*100,2).'%';?></td>
                    <td <? switch ($row['semaforo']) {
             case 'amarillo' : echo ' bgcolor="#FFFF99" '; break;
             case 'rojo' 	 : echo ' bgcolor="#FF0033" '; break;
             case 'verde'	 : echo ' bgcolor="#66CC33" '; break;
            }
         ?>></td>
                    <td><?=nocero($row['periodo_ped_entr']);?></td>
                    <td><?=nocero($row['periodo_fac_entr']);?></td>
                    <td><?=nocero($row['periodo_com_entr']);?></td>
                    <td><?=nocero($row['no_return']);?></td>
                    <td><?=$row['folio_devolucion'];?></td>
                    <td><?=nocero($row['folio_inconformidad']);?></td>
                    <td><?=$row['tipo_inconformidad'];?></td>
                    <td><?=fecha($row['fecha_inconformidad'],'novacio');?></td>
                    <td><?=$row['estatus_inconformidad'];?></td>
                    <td><?=$row['feedback'];?></td>
                  </tr>
                  <? } ?>
                </tbody>
              </table>
