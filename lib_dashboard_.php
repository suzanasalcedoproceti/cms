<?php
// Control de Cambios
// 3 Oct 2016:B+  Actualizar código de payment terms (plazo) en Dashboard

function actualiza_reg_dash($id,$verbose=0) {

	include_once("../conexion.php");
	$resultadoDASH = mysql_query("SELECT * FROM dashboard WHERE id = $id");
	$rowDASH = mysql_fetch_array($resultadoDASH);
	

    $folio_sap = $rowDASH['folio_sap'];
    $folio_pos = $rowDASH['folio_pos'];
    $material  = trim($rowDASH['material']);
    
    if ($verbose) echo "<br>Folio SAP: ".$folio_sap." Folio POS: ".$folio_pos." [ ".$material." ] ";
    
    // extraer folio puro de TW y sufijo
    if ($folio_pos) {
        $arr_folio 		= explode("_",$folio_pos);
        $folio_puro 	= trim(substr($arr_folio[0],6,10))+0;
        $folio_fpedido 	= $arr_folio[0];
        $sufijo			= trim($arr_folio[1]);
    } else {
        $folio_puro 	= 0;
        $folio_fpedido 	= '';
        $sufijo 		= '';
    }
    // identificar sufijos de POS y externos (MAP-> _MA _MP) no se encuentran en POS
    
    if ($folio_puro>0) {
    
        if ($sufijo=='MA' || $sufijo=='MP') 
            $es_folio_pos = false;
        else
            $es_folio_pos = true;
            
    } else {
        $es_folio_pos = false;
    }
    
    if ($verbose) echo "... ".$folio_puro." _ ".$sufijo;
	
	// detectar cambio de estatus delivery_block | credit_status | reason_rejection
	// si cualquiera de esos datos cambia, guardar en fecha_cambio_estatus la fecha actual
	//// ACTUALMENTE NO ES POSIBLE SABER SI CAMBIÓ, ya que al importar de dashboard se eliminan los registros del pedido.
	
    
    if ($es_folio_pos) {
        if ($verbose) echo " >>";
        // obtener datos de POS en HEADER
        $query = "SELECT pedido.*,
                         tienda.login AS clave_tienda, 
                         vendedor, usuario_tienda.login AS login_vendedor,  usuario_tienda.nombre AS nombre_vendedor, usuario_tienda.login AS login_vendedor, 
                         cliente, cliente.invitado, cliente.tipo AS tipo_cliente, CONCAT(cliente.nombre, ' ', cliente.apellido_paterno, ' ', cliente.apellido_materno) AS nombre_cliente, cliente.numero_empleado,
                         pedido.empresa, empresa.nombre AS nombre_empresa, empresa.cliente_sap AS numero_empresa, empresa.empresa_whirlpool
                    FROM pedido
                    LEFT JOIN tienda ON pedido.tienda = tienda.clave
                    LEFT JOIN usuario_tienda ON pedido.tienda = usuario_tienda.tienda AND pedido.vendedor = usuario_tienda.clave
                    LEFT JOIN cliente ON pedido.cliente = cliente.clave
                    LEFT JOIN empresa ON pedido.empresa = empresa.clave
                   WHERE folio = $folio_puro";
    
        $resultadoPED = mysql_query($query);
        
        $enc_ped = mysql_num_rows($resultadoPED);
        
        if ($enc_ped>0) {
            $rowPED = mysql_fetch_array($resultadoPED);
    
            // actualizar tabla dashboard con datos de POS (Encabezado de pedido, datos globales de sus partidas)
            
            $folio_pedido 	 = $folio_puro;
            $origen 		 = $rowPED['origen'];
            $tipo_pedido 	 = $rowPED['tipo_pedido']; // V/S/R
            $adicionales = '';
            if ($tipo_pedido=='S') $adicionales = "Sustituto ".$rowPED['folio_sustitucion']; 
            if ($tipo_pedido=='R') $adicionales = utf8_decode("Refacturación ").$rowPED['folio_refacturacion']; 
			$pct_iva = $rowPED['pct_iva'];
			$factor_iva = 1+$pct_iva;
			if (!$pct_iva) { $pct_iva = 0.16; $factor_iva = 1.16; }
            
            $tienda 		 = $rowPED['tienda'];
            if ($origen=='web') {
                $nombre_tienda = 'tw.com'; 
                $nombre_vendedor = 'tw.com';
                $login_vendedor = 'tw.com';
            } else {
                $nombre_tienda 	 = $rowPED['clave_tienda'];
                $nombre_vendedor = $rowPED['nombre_vendedor'];
                $login_vendedor = $rowPED['login_vendedor'];
            }
			
            $vendedor 		 = $rowPED['vendedor']+0;
            $po_number 		 = $rowPED['po_number'];
            $fecha_pedido 	 = $rowPED['fecha'];
            $cliente		 = $rowPED['cliente']+0;
            $nombre_cliente	 = substr($rowPED['nombre_cliente'],0,100);
            $numero_empleado = $rowPED['numero_empleado'];
            $empresa 		 = $rowPED['empresa']+0;

			$tipo_cliente = $rowPED['tipo_cliente'];
			if (empty($tipo_cliente)) {  // cliente.tipo_cliente se implementó posterior, por si hubiera algún registro sin tipo de cliente.
            	$tipo_cliente	 = ($rowPED['invitado']) ? 'I' : 'E';
				if ($tipo_cliente == 'E' && $rowPED['empresa_whirlpool']==0)
				$tipo_cliente = 'C'; // los empleados de empresas no whirlpool se consideran Corporate; los de WP se consideran tipo Empleado
			}
			
            $nombre_empresa	 = $rowPED['nombre_empresa'];
            $numero_empresa	 = $rowPED['numero_empresa'];
			$pct_descuento   = $rowPED['pct_descuento']+0;
			
            switch ($rowPED['estatus']) {
                case '0' : $estatus_pago = 'Pendiente'; break;
                case '2' : $estatus_pago = 'Pendiente'; break;
                case '3' : $estatus_pago = 'Pendiente'; break;
                case '4' : $estatus_pago = 'Pendiente'; break;
                case '1' : $estatus_pago = 'Pagado'; 	break;
                case '9' : $estatus_pago = 'Cancelado'; break;
            }
            $fecha_pago = $rowPED['fecha_pago'];
			$confirmo_pago = $rowPED['confirmo_pago'];
            
            // obtener las formas de pago
            $fdp = '';
            if ($rowPED['fdp_efectivo']>0) 		 $fdp.= '/EFE';
            if ($rowPED['fdp_tdd']>0) 			 $fdp.= '/TDD';
            if ($rowPED['fdp_tdc']>0) 			 $fdp.= '/TDC';
            if ($rowPED['fdp_credito_nomina']>0) $fdp.= '/ODC';
            if ($rowPED['fdp_cheque']>0) 		 $fdp.= '/CHE';
            if ($rowPED['fdp_cep']>0)			 $fdp.= '/CEP';
            if ($rowPED['fdp_deposito']>0) 		 $fdp.= '/DEP';
            if ($rowPED['fdp_puntos']>0) 		 $fdp.= '/PTS';
            if ($rowPED['fdp_puntos_flex']>0) 	 $fdp.= '/EFX';
            if ($rowPED['fdp_puntos_pep']>0) 	 $fdp.= '/PEP';
            if ($rowPED['fdp_sustitucion']>0) 	 $fdp.= '/SUS';
            if ($rowPED['fdp_refacturacion']>0)	 $fdp.= '/REF';

			// calcular el % de cada forma de pago respecto al total, para calcular por cada item la forma de pago proporcional
			$fdp_efectivo 	 = $rowPED['fdp_efectivo']+0;
			$pct_efectivo	 = $fdp_efectivo / $rowPED['total'];

			$fdp_tdc 		 = $rowPED['fdp_tdc']+0;
			$pct_tdc		 = $fdp_tdc / $rowPED['total'];

			$fdp_tdd 		 = $rowPED['fdp_tdd']+0;
			$pct_tdd		 = $fdp_tdd / $rowPED['total'];

			$fdp_odc 		 = $rowPED['fdp_credito_nomina']+0;
			$pct_odc		 = $fdp_odc / $rowPED['total'];

			$fdp_cheque 	 = $rowPED['fdp_cheque']+0;
			$pct_cheque		 = $fdp_cheque / $rowPED['total'];

			$fdp_cep 		 = $rowPED['fdp_cep']+0;
			$pct_cep		 = $fdp_cep / $rowPED['total'];

			$fdp_dep 		 = $rowPED['fdp_deposito']+0;
			$pct_dep		 = $fdp_dep / $rowPED['total'];

			$fdp_puntos		 = $rowPED['fdp_puntos']+0;
			$pct_puntos		 = $fdp_puntos / $rowPED['total'];

			$fdp_puntos_flex = $rowPED['fdp_puntos_flex']+0;
			$pct_puntos_flex = $fdp_puntos_flex / $rowPED['total'];
			
			$fdp_puntos_pep	 = $rowPED['fdp_puntos_pep']+0;
			$pct_puntos_pep  = $fdp_puntos_pep / $rowPED['total'];

			$fdp_gc			 = $rowPED['fdp_gc']+0;
			$pct_gc			 = $fdp_gc / $rowPED['total'];

			$fdp_sustitucion = $rowPED['fdp_sustitucion']+0;
			$pct_sustitucion = $fdp_sustitucion / $rowPED['total'];

			$fdp_refacturacion = $rowPED['fdp_refacturacion']+0;
			$pct_refacturacion = $fdp_refacturacion/ $rowPED['total'];
			
			$folio_odc		 = $rowPED['fdp_credito_nomina_folio'];
			$plazo_odc		 = $rowPED['payment_terms'];
           
            $forma_pago = trim(substr($fdp,1,100));
			
			// obtener inconformidades si las hay
			$resultadoINC = mysql_query("SELECT * FROM comentario_pedido WHERE pedido = $folio_puro AND tipo = 'I' ORDER BY fecha, hora LIMIT 1");
			$encINC = mysql_num_rows($resultadoINC);
			if ($encINC>0) {
				$rowINC = mysql_fetch_array($resultadoINC);
				$folio_inconformidad = $rowINC['folio'];
				$tipo_inconformidad = 'I';
				$fecha_inconformidad = $rowINC['fecha'];
			} else {
				$folio_inconformidad = 0;
				$tipo_inconformidad = '';
				$fecha_inconformidad = '0000-00-00';
			}
			
          
            $query = "UPDATE dashboard SET 
                        folio_pedido = $folio_pedido, folio_fpedido = '$folio_fpedido', origen = '$origen', 
                        po_number = '$po_number', 
                        tipo_cliente = '$tipo_cliente', cliente = $cliente, nombre_cliente = '$nombre_cliente', numero_empleado = '$numero_empleado',
                        empresa = $empresa, nombre_empresa = '$nombre_empresa', numero_empresa = '$numero_empresa', estatus_pago = '$estatus_pago',
                        fecha_pago = '$fecha_pago', confirmo_pago = '$confirmo_pago',
                        forma_pago = '$forma_pago',	
						folio_odc = '$folio_odc',
						plazo_odc = '$plazo_odc',
						adicionales = '$adicionales',
						pct_descuento = $pct_descuento,
						folio_inconformidad = $folio_inconformidad, tipo_inconformidad = '$tipo_inconformidad', fecha_inconformidad = '$fecha_inconformidad',
                        procesado = 0, act = 1-act
                      WHERE id = $id	
            
            ";
            // if ($verbose) echo "<br>".$query;
            $resUD = mysql_query($query);
            if (mysql_affected_rows()<=0) {
                if ($verbose) echo " .. no completado (header) <br>".mysql_error()."<br>";  
            } else {
                if ($verbose) echo " .. completado (header) ";
    
                // ahora obtener datos de Detalle; 
                // los fletes no están en una partida del pedido, sino en un campo complementario (sku_entrega), por eso se busca en cualquiera de los 2 campos
                
                $query = "SELECT detalle_pedido.*, producto.categoria, producto.subcategoria, categoria.nombre AS nombre_categoria, subcategoria.nombre AS nombre_subcategoria, 
								 producto.vol_reb
                            FROM detalle_pedido
							LEFT JOIN producto  ON detalle_pedido.modelo = producto.modelo
							LEFT JOIN categoria ON producto.categoria = categoria.clave
							LEFT JOIN subcategoria ON producto.categoria = subcategoria.categoria AND producto.subcategoria = subcategoria.clave
                           WHERE pedido = $folio_puro AND (TRIM(detalle_pedido.modelo) = '$material' OR TRIM(sku_entrega) = '$material' )";
						   
                $query = "SELECT * FROM detalle_pedido 
				           WHERE pedido = $folio_puro AND (TRIM(detalle_pedido.modelo) = '$material' OR TRIM(detalle_pedido.sku_entrega) = '$material' ) 
						   (es_garantia=0 OR (es_garantia<>0 AND folio_garantia NOT IN (SELECT folio_garantia FROM dashboard where folio_pedido=$folio_puro)))";
						   
                $resultadoDPED = mysql_query($query);
                
                $enc_dped = mysql_num_rows($resultadoDPED);
                
                if ($enc_dped>0) {

                    $rowDPED = mysql_fetch_array($resultadoDPED);
                    $entrega 		= $rowDPED['tipo_entrega'];
                    $compromiso_entrega = $rowDPED['fecha_entrega'];

					// obtener datos de producto, categoría y subcategoria
					$query = "SELECT producto.categoria, producto.subcategoria, producto.vol_reb, 
									 marca.nombre AS nombre_marca,
									 categoria.nombre AS nombre_categoria, subcategoria.nombre AS nombre_subcategoria
								FROM producto
								LEFT JOIN marca ON producto.marca = marca.clave
								LEFT JOIN categoria ON producto.categoria = categoria.clave
								LEFT JOIN subcategoria ON producto.categoria = subcategoria.categoria AND producto.subcategoria = subcategoria.clave
							   WHERE TRIM(producto.modelo) = '$material' ";
					$resultadoPROD = mysql_query($query);
					$rowPROD = mysql_fetch_array($resultadoPROD);


					// identificar si es un flete (en POS viene en la misma partida del producto, pero de SAP viene en partidas diferentes)
					if ($rowDPED['sku_entrega']==$material) {

						// precios
						$lista_precios	= $rowDPED['lista_precios'];
						$precio_unitario = $rowDPED['costo_entrega'] / ($factor_iva); // 1.16  // antes de iva
						$descuento = $precio_unitario * $pct_descuento / 100;
						$motivo_descuento = $rowPED['motivo_descuento'];
						$total_unitario = $precio_unitario - $descuento;
						$iva = $total_unitario * $pct_iva;
						if ($iva<0) $iva = 0;
						$puntos_generados = 0; 	// este valor no se tiene actualmente
						$total_pagar = $rowDPED['costo_entrega'] - $descuento;					
						$total	= $total_pagar;
						$costo_entrega  = 0;
					
					} else {

						// precios
						$lista_precios	= $rowDPED['lista_precios'];
						$precio_unitario = $rowDPED['precio_empleado'] / ($factor_iva); // 1.16  // antes de iva
						$descuento = $precio_unitario * $pct_descuento / 100;
						$motivo_descuento = $rowPED['motivo_descuento'];
						$total_unitario = $precio_unitario - $descuento;
						$iva = $total_unitario * $pct_iva;
						if ($iva<0) $iva = 0;
						$puntos_generados = 0; 	// este valor no se tiene actualmente
						$total_pagar = $rowDPED['precio_empleado'] - $descuento;
						$total	= $total_pagar; // $rowDPED['subtotal']+0;
						$costo_entrega  = $rowDPED['costo_entrega']+0;

					}				
					
					// calcular el proporcional para este item, de cada forma de pago, ya que se tiene por el total del pedido
					$fdp_efectivo = $total_pagar * $pct_efectivo;
					$fdp_tdc = $total_pagar * $pct_tdc;
					$fdp_tdd = $total_pagar * $pct_tdd;
					$fdp_odc = $total_pagar * $pct_odc;
					$fdp_cheque = $total_pagar * $pct_cheque;
					$fdp_cep = $total_pagar * $pct_cep;
					$fdp_dep = $total_pagar * $pct_dep;
					$fdp_puntos = $total_pagar * $pct_puntos;
					$fdp_puntos_flex = $total_pagar * $pct_puntos_flex;
					$fdp_puntos_pep = $total_pagar * $pct_puntos_pep;
					$fdp_gc = $total_pagar * $pct_gc;
					$fdp_sustitucion = $total_pagar * $pct_sustitucion;
					$fdp_refacturacion = $total_pagar * $pct_refacturacion;
					
					//datos del producto
					/*
					$marca			= $rowDPED['marca']+0;
					$nombre_marca	= $rowDPED['marca_nombre']; 
					$categoria 		= $rowDPED['categoria']+0;
					$subcategoria 	= $rowDPED['subcategoria']+0;
					$nombre_categoria    = $rowDPED['nombre_categoria'];
					$nombre_subcategoria = $rowDPED['nombre_subcategoria'];
					$vol_reb 		= $rowDPED['vol_reb'];
*/
					$marca			= $rowPROD['marca']+0;
					$nombre_marca	= $rowPROD['nombre_marca']; 
					$categoria 		= $rowPROD['categoria']+0;
					$subcategoria 	= $rowPROD['subcategoria']+0;
					$nombre_categoria    = $rowPROD['nombre_categoria'];
					$nombre_subcategoria = $rowPROD['nombre_subcategoria'];
					$vol_reb 		= $rowPROD['vol_reb'];
					// obtener datos de la póliza
					if ($rowDPED['es_garantia']) {  
						$folio_garantia = $rowDPED['folio_garantia'];
						$resultadoGAR = mysql_query("SELECT aplica_para, fecha_compra_producto FROM garantia WHERE folio = $folio_garantia AND pedido = $folio_puro");
						$rowGAR = mysql_fetch_array($resultadoGAR);
						$sku_garantia	= $rowGAR['aplica_para'];
						$fecha_compra_producto_garantia = $rowGAR['fecha_compra_producto'];
					} else {
						$sku_garantia	= '';
						$folio_garantia = '';
						$fecha_compra_producto_garantia = '';
					}
                    
                    $query = "UPDATE dashboard SET 
                                entrega = '$entrega', total = $total, compromiso_entrega =  '$compromiso_entrega', costo_entrega = $costo_entrega, lista_precios = '$lista_precios',
								precio_unitario = $precio_unitario, descuento = $descuento, motivo_descuento = '$motivo_descuento', iva = $iva, total_unitario = $total_unitario,
								categoria = $categoria, subcategoria = $subcategoria, nombre_categoria = '$nombre_categoria', nombre_subcategoria = '$nombre_subcategoria', 
								marca = $marca, nombre_marca = '$nombre_marca', vol_reb = '$vol_reb',
								fdp_efectivo = $fdp_efectivo, fdp_tdc = $fdp_tdc, fdp_tdd = $fdp_tdd, fdp_odc = $fdp_odc, fdp_cheque = $fdp_cheque, fdp_cep = $fdp_cep, fdp_puntos = $fdp_puntos, fdp_dep = $fdp_dep,
								fdp_puntos_flex = $fdp_puntos_flex, fdp_puntos_pep = $fdp_puntos_pep,  fdp_gc = $fdp_gc, fdp_sustitucion = $fdp_sustitucion, fdp_refacturacion = $fdp_refacturacion,								
								sku_garantia = '$sku_garantia', folio_garantia = '$folio_garantia', fecha_compra_producto_garantia = '$fecha_compra_producto_garantia'
								procesado = 0, act = 1-act
                              WHERE id = $id
                
                    ";
                    $resUD = mysql_query($query);
                    if (mysql_affected_rows()>0) {
						if ($verbose) echo " .. completado (detail)"; 
					} else {
						if ($verbose) echo " .. no completado (detail)  <br>".mysql_error();
					}
    
                    
                } else {
					if ($verbose) echo '.. no se encontro detalle (material) en POS ';
				}
    
            } // affected header
            
        } else {
			// si no se encontró pedido en POS:
            
            $fecha_pago = '0000-00-00';
			$confirmo_pago = '';
            $compromiso_entrega = '0000-00-00';
			$costo_entrega = 0;
	        $fecha_pedido = convierte_fecha_dash($rowDASH['order_date']);
            if ($verbose) echo '.. no se encontro pedido en POS'; // encontrado pedido
        }
    
    	$update_tipo_entrega = '';
    
    
    } else { 
		
		// si no es folio POS: (no se generó en POS)
    
        $fecha_pedido = convierte_fecha_dash($rowDASH['order_date']);
        if ($sufijo=='MA' || $sufijo=='MP') {
            $tienda = 0;
            $vendedor = 0;
            $nombre_tienda 	 = 'MAP';
            $nombre_vendedor = 'MAP';
            $login_vendedor = 'MAP';
            $tipo_pedido = 'V';
        } else {
            $tienda = 0;
            $vendedor = 0;
            $nombre_tienda = '';
            $nombre_vendedor = '';
            $login_vendedor = '';
            $tipo_pedido = '';
            
        }
        
        $fecha_pago = '0000-00-00';
		$confirmo_pago = '';
        $compromiso_entrega = '0000-00-00';
		
		// actualizar tipo entrega (no se trae en importacion de dashboard) para folios no POS

    	$update_tipo_entrega = '';
		$planta = $rowDASH['planta'];
		/*
		if ($planta == 'RM08' || $planta == 'RM50' || $planta == 'RM12') {
			// esta regla ya no aplica, desde que integraron el call center de MAS
			// $entrega = 'inmediata';
			// $update_tipo_entrega = " entrega = '".$entrega."', ";

		}
		if ($planta == 'RM11') {
			$entrega = 'digital';
	    	$update_tipo_entrega = " entrega = '".$entrega."', ";
		}
		*/
    
    }
    
	// obtener datos del producto (aun si no hay pedido)
	$modelo = trim($rowDASH['material']);
	$resultadoPROD = mysql_query("SELECT clasificacion FROM producto WHERE modelo = '$modelo'");
	$rowPROD = mysql_fetch_array($resultadoPROD);
	$tipo_producto = $rowPROD['clasificacion'];
	
	
    // calcular datos faltantes (estadísticas) para cada registro de dashboard recorrido.
    // se calcula hasta aqui porque en el ciclo anterior podría no entrar, si no hay folio_pos, (pedidos hechos directamente en SAP)
    
    $fecha_factura = convierte_fecha_dash2($rowDASH['billing_date']);
    $factura = $rowDASH['billing_doc'];
    $cancelado = ($rowDASH['reason_rejection']>0) ? 1 : 0;
    
    if ($factura) {
        $estatus_material = 'Completo';
        $avance_material = 1;
    } else {
        $estatus_material = 'Incompleto';
        $avance_material = 0;
    }
    if ($cancelado) {
        $estatus_material = 'Cancelado';
        $avance_material = 0;
    }
    
	$vendedor+=0;
	$avance_material+=0;
	$tienda+=0;
    // actualizar estatus y avance del material, así como datos de _MAP que no se pudieron obtener de la pasada 2
    $query = "UPDATE dashboard SET 
                estatus_material = '$estatus_material', avance_material= $avance_material, procesado = 0, fecha_pedido = '$fecha_pedido',
                tipo_pedido = '$tipo_pedido', sufijo = '$sufijo',
                tienda = $tienda, nombre_tienda = '$nombre_tienda',
                vendedor = $vendedor, nombre_vendedor = '$nombre_vendedor', login_vendedor = '$login_vendedor',
				fecha_factura = '$fecha_factura',
				tipo_producto = '$tipo_producto', 
				$update_tipo_entrega
                act = 1-act
              WHERE id = $id
    
    ";
    
    $resUD = mysql_query($query);
    if (mysql_affected_rows()>0) {
		if ($verbose) 
			echo " .. completado (avance mat) ".$estatus_material." = ".$avance_material; 
	} else {
		if ($verbose) echo " .. no completado (avance mat)  <br>".mysql_error()."<br>";
	}
  
  	return 0;
}

function actualiza_reg_dash_logistica($id,$verbose=0) {

	include_once("../conexion.php");
	$resultadoDASH = mysql_query("SELECT * FROM dashboard WHERE id = $id");
	$rowDASH = mysql_fetch_array($resultadoDASH);

    $folio_sap = $rowDASH['folio_sap'];
    $material  = $rowDASH['material'];
	$compromiso_entrega = $rowDASH['compromiso_entrega'];
	$fecha_pago = $rowDASH['fecha_pago'];
	$entrega = $rowDASH['entrega'];
    
    if ($verbose) echo "<br>Folio SAP: ".$folio_sap." [ ".$material." ] ";
	
	
	
//	$update_tipo_entrega = '';
	$planta = $rowDASH['planta'];
	if ($entrega == 'inmediata' || $entrega == 'digital') {	
		if ($entrega == 'inmediata') {
			$proveedor				= $rowDASH['nombre_tienda'];
			$proveedor_logistica	= $rowDASH['nombre_tienda'];
			$guia 					= "No Aplica";
			$fecha_entrega_promesa 	= $rowDASH['fecha_pago'];
			$fecha_entrega_final 	= $rowDASH['fecha_pago'];
			$estatus_entrega 		= "Entregado";
			$comentarios 			= "Entrega Inmediata";
			$estatus_material 		= "Completo";
			$semaforo				= "verde";
			$periodo_fac_entr 		= 0;
			$periodo_ped_entr		= 0;
			$periodo_com_entr		= 0; 
		}
		if ($entrega == 'digital') {
			$proveedor				= $rowDASH['nombre_tienda'];
			$proveedor_logistica	= $rowDASH['nombre_tienda'];
			$guia 					= "No Aplica";
			$fecha_entrega_promesa 	= $rowDASH['fecha_pago'];
			$fecha_entrega_final 	= $rowDASH['fecha_pago'];
			$estatus_entrega 		= "Entregado";
			$comentarios 			= "Entrega Digital";
			$estatus_material 		= "Completo";
			$semaforo				= "verde";
			$periodo_fac_entr 		= 0;
			$periodo_ped_entr		= 0;
			$periodo_com_entr		= 0; 
		}
		$query = "UPDATE dashboard SET 
					proveedor = '$proveedor', proveedor_logistica = '$proveedor_logistica', guia = '$guia', estatus_entrega = '$estatus_entrega', comentarios = '$comentarios',
					periodo_ped_entr = $periodo_ped_entr, periodo_fac_entr = $periodo_fac_entr, periodo_com_entr = $periodo_com_entr, 
					semaforo = '$semaforo', fecha_entrega_promesa = '$fecha_entrega_promesa', fecha_entrega_final = '0000-00-00',
					act = 1-act
				  WHERE id = $id";
		$resUD = mysql_query($query);
		if (mysql_affected_rows()>0) {
			if ($verbose) 
				echo " .. completado (DIG/EI)"; 
		} else {
			if ($verbose) echo " .. no completado (DIG/EI)  <br>".mysql_error()."<br>";
		}
		
	} else {	
	
    	$tipo_producto = $rowDASH['tipo_producto'];
		$fecha_hoy = date("Y-m-d");
		$query = "SELECT * FROM dashboard_logistica WHERE folio_sap = '$folio_sap' AND material = '$material'";
		$resultadoDL = mysql_query($query);
		if (mysql_num_rows($resultadoDL)>0) {
			if ($verbose) echo ' [logist] ';
			$rowDL = mysql_fetch_array($resultadoDL);
			$proveedor_logistica = $rowDL['proveedor'];
			$guia = $rowDL['guia'];
			$estatus_entrega = $rowDL['estatus_entrega'];
			$no_delivery = $rowDL['no_delivery'];
			$comentarios = $rowDL['comentarios'];
			$fecha_entrega_promesa = $rowDL['fecha_entrega_promesa'];
			$fecha_entrega_final = $rowDL['fecha_entrega_final'];
			
			// solo que ya se tenga fecha de entrega.
			if ($fecha_entrega_final == '' || $fecha_entrega_final =='0000-00-00' || $fecha_entrega_final == '#N/A') 
				$fecha_entrega_final = '0000-00-00';
			else 
				$fecha_entrega_final = convierte_fecha_dash2($fecha_entrega_final);  // viene 04/07/2015

			//echo "<br>FEF!: ".$fecha_entrega_final."<br>";
			
			$fecha_entrega_calculo = $fecha_entrega_final;
			if ($fecha_entrega_final == '') $fecha_entrega_final = '0000-00-00';
			if ($fecha_entrega_calculo == '' || $fecha_entrega_calculo = '0000-00-00') $fecha_entrega_calculo = date("Y-m-d");
			if ($fecha_entrega_promesa == '') $fecha_entrega_promesa = '0000-00-00';
			
			$no_return= $rowDL['no_return']+0;
			$folio_devolucion = $rowDL['folio_devolucion'];
			
			// calcular periodo de pedido - entrega y factura - entrega, y semáforo (compromiso vs entrega)
			// factura - entrega
			$fecha_factura = convierte_fecha_dash2($rowDASH['billing_date']);
							
			if ($fecha_factura == '0000-00-00' || $fecha_factura == '' || $fecha_entrega_final == '0000-00-00' || $fecha_entrega_final == '') {
				$periodo_fac_entr = 0;
			} else {
											// mes						// dia					 //año		
				$fecha1 = mktime(0,0,0,substr($fecha_factura,5,2),substr($fecha_factura,8,2),substr($fecha_factura,0,4));
				$fecha2 = mktime(0,0,0,substr($fecha_entrega_final,5,2),substr($fecha_entrega_final,8,2),substr($fecha_entrega_final,0,4)); 
				$periodo_fac_entr = floor(($fecha2 - $fecha1) / (60*60*24));
			}
			
			if ($fecha_pago == '0000-00-00' || $fecha_pago == '' || $fecha_entrega_final == '0000-00-00' || $fecha_entrega_final == '') {
				$periodo_ped_entr = 0;
			} else {
				$fecha1 = mktime(0,0,0,substr($fecha_pago,5,2),substr($fecha_pago,8,2),substr($fecha_pago,0,4));
				$fecha2 = mktime(0,0,0,substr($fecha_entrega_final,5,2),substr($fecha_entrega_final,8,2),substr($fecha_entrega_final,0,4));
				$periodo_ped_entr = floor(($fecha2 - $fecha1) / (60*60*24));
			}
			
			if ($compromiso_entrega == '0000-00-00' || $compromiso_entrega == '' ) {
				$periodo_com_entr = 0;
				$semaforo = '';
			} else {
				$fecha1 = mktime(0,0,0,substr($compromiso_entrega,5,2),substr($compromiso_entrega,8,2),substr($compromiso_entrega,0,4));
				$fecha2 = mktime(0,0,0,substr($fecha_entrega_calculo,5,2),substr($fecha_entrega_calculo,8,2),substr($fecha_entrega_calculo,0,4));
				$periodo_com_entr = floor(($fecha2 - $fecha1) / (60*60*24));
			}

			// calcular semáforo, feccha de pago vs fecha de entrega, o en su defecto, contra fecha actual
			if ($fecha_pago == '0000-00-00' || $fecha_pago == '') {
				$semaforo = '';
			} else {
				if ($fecha_entrega_final == '0000-00-00' || $fecha_entrega_final == '') 
					$fecha_comp = $fecha_hoy;
				else
					$fecha_comp = $fecha_entrega_final;
					
				$fecha1 = mktime(0,0,0,substr($fecha_pago,5,2),substr($fecha_pago,8,2),substr($fecha_pago,0,4));
				$fecha2 = mktime(0,0,0,substr($fecha_comp,5,2),substr($fecha_comp,8,2),substr($fecha_comp,0,4));
				$dias = floor(($fecha2 - $fecha1) / (60*60*24));

				if ($tipo_producto=='low') {
					if ($dias <= 2) 	 $semaforo = 'verde';	 // 2 o menos días
					elseif ($dias <= 5)  $semaforo = 'amarillo'; // 3 a 5 días
					elseif ($dias >= 6)  $semaforo = 'rojo';	 // 6 o m´sa
				}
				if ($tipo_producto=='ltl') {
					if ($dias <= 10) 	 $semaforo = 'verde';	 // 10 o menos días
					elseif ($dias <= 15) $semaforo = 'amarillo'; // 11 a 15 días
					elseif ($dias >= 16) $semaforo = 'rojo';	 // 16 o m´sa
				}
				
			}
			
			// se permiten negativos
			// if ($periodo_fac_entr < 0) $periodo_fac_entr = 0;
			// if ($periodo_ped_entr < 0) $periodo_ped_entr = 0;
			// if ($periodo_com_entr < 0) $periodo_com_entr = 0;
			if ($verbose) echo " { p-e ".$periodo_ped_entr." dias } ";
			if ($verbose) echo " { f-e ".$periodo_fac_entr." dias } ";
			if ($verbose) echo " { c-e ".$periodo_com_entr." dias } ";
			
			if ($verbose) echo $semaforo;
			if ($verbose) echo "FE: ".$fecha_entrega_final;
			
			// actualizar estatus y avance del material, así como datos de _MAP que no se pudieron obtener de la pasada 2
			$query = "UPDATE dashboard SET 
						periodo_ped_entr = $periodo_ped_entr, periodo_fac_entr = $periodo_fac_entr, periodo_com_entr = $periodo_com_entr, 
						semaforo = '$semaforo',
						proveedor_logistica = '$proveedor_logistica', guia = '$guia', estatus_entrega = '$estatus_entrega', no_delivery = '$no_delivery', comentarios = '$comentarios',
						no_return = $no_return, folio_devolucion = '$folio_devolucion', fecha_entrega_promesa = '$fecha_entrega_promesa', fecha_entrega_final = '$fecha_entrega_final',
						act = 1-act
					  WHERE id = $id
			
			";
//			echo "upd_x".$query;
			
			$resUD = mysql_query($query);
			if (mysql_affected_rows()>0) {
				if ($verbose) 
					echo " .. completado (LOGISTICA) "; 
			} else {
				if ($verbose) echo " .. no completado (LOGISTICA)  <br>".mysql_error()."<br>";
			}
		} else {
			if ($verbose) echo " .. no se encontro registro de logistica";
			
			// si no hay registro de logística, se compara contra la fecha actual (fecha de pago vs fecha actual)
			$periodo_fac_entr = 0;
			$periodo_ped_entr = 0;
			
			if ($fecha_pago == '0000-00-00' || $fecha_pago == '' ) {
				$semaforo = '';
			} else {
				$fecha1 = mktime(0,0,0,substr($fecha_pago,5,2),substr($fecha_pago,8,2),substr($fecha_pago,0,4));
				$fecha2 = mktime(0,0,0,substr($fecha_hoy,5,2),substr($fecha_hoy,8,2),substr($fecha_hoy,0,4));

				$dias = floor(($fecha2 - $fecha1) / (60*60*24));  // dias que han pasado desde que se pagó

				if ($tipo_producto=='low') {
					if ($dias <= 2) 	 $semaforo = 'verde';	 // 2 o menos días
					elseif ($dias <= 5)  $semaforo = 'amarillo'; // 3 a 5 días
					elseif ($dias >= 6)  $semaforo = 'rojo';	 // 6 o m´sa
				}
				if ($tipo_producto=='ltl') {
					if ($dias <= 10) 	 $semaforo = 'verde';	 // 10 o menos días
					elseif ($dias <= 15) $semaforo = 'amarillo'; // 11 a 15 días
					elseif ($dias >= 16) $semaforo = 'rojo';	 // 16 o m´sa
				}

			}
			$query = "UPDATE dashboard SET semaforo = '$semaforo', act = 1-act WHERE id = $id";
			$resUD = mysql_query($query);
			if (mysql_affected_rows()>0) {
				if ($verbose) 
					echo " .. semaforo completado (SIN LOG) "; 
			} else {
				if ($verbose) echo " .. no completado semarforo (SIN LOG)";
			}			
			
		}
		
		
		
	} // entrega digital o inmediata, no requieren archivo logistica
  
  	return 0;
}

function actualiza_avg_avance($folio_pedido,$verbose=0) {
	
	include("../conexion.php");
	
	$query = "SELECT folio_sap, folio_pos, folio_pedido, sufijo, AVG(avance_material) AS avg_mat 
				FROM dashboard 
			   WHERE folio_pedido = $folio_pedido
				 AND sufijo != 'G' AND sufijo !='MA' AND sufijo != 'MP' AND estatus_material != 'Cancelado' 
			   GROUP BY folio_pedido ORDER BY folio_pos ";

	$resultadoGD = mysql_query($query);
	$rowGD = mysql_fetch_array($resultadoGD);
	$avg = $rowGD['avg_mat'];

	
	if ($verbose) echo "<br>Folio SAP: ".$rowGD['folio_sap']." Folio POS: ".$rowGD['folio_pos']." Sufijo: ".$rowGD['sufijo']." AVG: ".$avg." %";
	$resultadoUPP = mysql_query("UPDATE dashboard SET avance_pedido = $avg WHERE folio_pedido = $folio_pedido");
	
	return 0;
}

function convierte_fecha_dash($vfecha) {
	// 20140917
	if (empty($vfecha)) {
		return "0000-00-00";
	} else {
		return substr($vfecha,0,4)."-".substr($vfecha,4,2)."-".substr($vfecha,6,2);
	}
}

function convierte_fecha_dash2($vfecha) {
	// 09/28/2014
	if (empty($vfecha) || $vfecha == 'No Aplica') {
		return "0000-00-00";
	} else {
		return substr($vfecha,6,4)."-".substr($vfecha,0,2)."-".substr($vfecha,3,2);
	}
}

function aplica_filtros($arr_filtros) {

		   $condicion = " WHERE 1  ";

		   if ($fechas) {
				$condicion .= " AND fecha_pedido BETWEEN '$desde' AND '$hasta' ";
		   }
		   if ($fecha_fac) {
				$condicion .= " AND fecha_factura BETWEEN '$desde_fac' AND '$hasta_fac' ";
		   }
		   if ($texto) {
				$condicion .= " AND (folio_pos LIKE '%$texto%' OR folio_sap LIKE '%$texto%' OR guia LIKE '%$texto%') ";
		   }
		   if ($filtro_tipo_pedido) {
				$condicion .= " AND tipo_pedido IN ( ".$filtro_tipo_pedido." ) ";
		   }
		   if ($filtro_empresa) {
				$condicion .= " AND empresa IN ( ".$filtro_empresa." ) ";
		   }
		   if ($filtro_tienda) {
				$condicion .= " AND tienda IN ( ".$filtro_tienda." ) ";
		   }
		   if ($filtro_vendedor) {
				$condicion .= " AND vendedor IN ( ".$filtro_vendedor." ) ";
		   }
		   if ($filtro_tipo_cliente) {
				$condicion .= " AND tipo_cliente IN ( ".$filtro_tipo_cliente." ) ";
		   }
		   if ($filtro_proveedor) {
				$condicion .= " AND proveedor IN ( ".$filtro_proveedor." ) ";
		   }
		   /*if ($po_number != 'x' && $po_number != '') {
				$condicion .= " AND po_number = '$po_number' ";
		   }*/
		   if ($filtro_estatus_pedido != ' A )') { // ver condicion en filtro
				$condicion .= $filtro_estatus_pedido;
		   }
		   if ($filtro_estatus_entrega) {
				$condicion .= " AND estatus_entrega IN ( ".$filtro_estatus_entrega." ) ";
		   }
		   if ($filtro_semaforo) {
				$condicion .= " AND semaforo IN ( ".$filtro_semaforo." ) ";
		   }

}

?>