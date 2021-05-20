<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=8;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	include('../conexion.php');
	include_once('lib.php');

	$dato_buscar=$_GET['dato_buscar'];
	$dato = $_GET['dato'];
	$dato1 = $_GET['dato1'];
	$dato2 = $_GET['dato2'];
	
	if ($dato_buscar == 'numero_empleado') {
		
		$resultadoEMP = mysql_query("SELECT nombre FROM empresa WHERE clave = $dato1");
		$rowEMP = mysql_fetch_array($resultadoEMP);
		$dato = "Empresa: ".$rowEMP['nombre']." Número de Cliente: ".$dato2;
	}

	if ($dato_buscar == 'numero_empleado_wp') {
		
		$dato = "Cualquier empresa Whirlpool; Número de Cliente: ".$dato2;
	}

	$accion = $_POST['accion'];


	if ($accion=='depurar') {
	
		$cliente_dejar = $_POST['cliente_dejar']+0;
		// obtener datos de empresa
		$resultadoQD = mysql_query("SELECT empresa FROM cliente WHERE clave = $cliente_dejar");
		$rowQD = mysql_fetch_array($resultadoQD);
		$empresa = $rowQD['empresa'];
		$resultadoEMP = mysql_query("SELECT nombre, empresa_whirlpool FROM empresa WHERE clave = $empresa");
		$rowEMP = mysql_fetch_array($resultadoEMP);
		
		$clientes_quitar = '';
		for (reset($_POST); list($variable, $valor) = each($_POST); ){
		   $encontrado=0;
		   if(substr($variable,0,7)=="borrar_") {
				if (!empty($valor)) {
					$item = substr($variable,7,10);
					$cliente_borrar = $item;
					$clientes_quitar .= trim($cliente_borrar).", ";
					

//		include("_checa_vars.php"); return;

				} // dato check
			} // elemento form es borrar_
		} // for
		
		
		$clientes_quitar = substr($clientes_quitar,0,-2); // eliminar coma final
//		echo "<br>Borrar: ".$clientes_quitar."<br>";
		$pedidos_movidos = 0;
		$direcciones_movidas = 0;
		$invitados_movidos = 0;
		$clientes_quitados = 0;
		$pe_movidos = 0;
		$pn_movidos = 0;
		$pf_movidos = 0;
		$pp_movidos = 0;
		$kad_movidos = 0;


		
		if ($cliente_dejar>0 && $clientes_quitar) {
		
			// pasar pedidos
			$resultadoUP = mysql_query("UPDATE pedido SET cliente = $cliente_dejar, act = 1-act WHERE cliente IN ($clientes_quitar)");
			$encUP = mysql_affected_rows();
			if ($encUP>0)
				$pedidos_movidos += $encUP; 

			// pasar direcciones de envio
			$resultadoUD = mysql_query("UPDATE direccion_envio SET cliente = $cliente_dejar, act = 1-act WHERE cliente IN ($clientes_quitar)");
			$encUD = mysql_affected_rows();
			if ($encUD>0)
				$direcciones_movidas += $encUD;
				
			// pasar invitados
			$resultadoUI = mysql_query("UPDATE cliente SET invitado_por = $cliente_dejar, act = 1-act WHERE invitado_por IN ($clientes_quitar)");
			$encUI = mysql_affected_rows();
			if ($encUI>0)
				$invitados_movidos += $encUI; 
				
			// pasar precios especiales, solo que el cliente dejar sea de empresa_whirlpool
			// pasar puntos flex / pep, solo empresa_whirlpool
			// recorrer cada cliente a depurar
			if ($rowEMP['empresa_whirlpool']==1) {
				$ano = date("Y");
				$arr_quitar = explode(",",$clientes_quitar);
				foreach ($arr_quitar AS $cte_quitar) {

					/*
					-- Empleados whirlpool duplicados
SELECT empresa, numero_empleado,count(*) FROM cliente WHERE cliente.tipo='E' AND empresa<>168 GROUP BY numero_empleado HAVING COUNT(*) > 1 order by count(*) desc;
-- Empleados embraco duplicados
SELECT empresa, numero_empleado,count(*) FROM cliente WHERE empresa=168 GROUP BY numero_empleado HAVING COUNT(*) > 1 order by count(*) desc;

-- 10023658 obtener el registro con la fecha de reg mas nuevo
SELECT clave, fecha FROM cliente WHERE numero_empleado='10023658' and tipo='E' and fecha<>'0000-00-00' order by fecha desc;

-- 10023658 obtener el registro con la fecha de compra mas nuevo
SELECT clave, pedido.fecha FROM cliente inner join pedido on cliente.clave=pedido.cliente WHERE numero_empleado='10023658' and tipo='E' order by pedido.fecha desc;



					// revisar si el cliente quitar tiene registro de precios especiales
					
					$resPEQ = mysql_query("SELECT cantidad FROM precios_especiales WHERE ano = '$ano' AND cliente = $cte_quitar");
					$encPEQ = mysql_num_rows($resPEQ);
					if ($encPEQ>0) {

						$rowPEQ = mysql_fetch_array($resPEQ);
						$cant_pe = $rowPEQ['cantidad'];
						// revisar si el cliente Dejar ya tiene registro, acumular, si no, insertar
						$resPED = mysql_query("SELECT cantidad FROM precios_especiales WHERE ano = '$ano' AND cliente = $cliente_dejar");
						$encPED = mysql_num_rows($resPED);
						if ($encPED>0) {

							$resUPE = mysql_query("UPDATE precios_especiales SET cantidad = cantidad + $cant_pe WHERE ano = '$ano' AND cliente = $cliente_dejar");
							$encUPE = mysql_affected_rows();
							if ($encUPE>0)
								$pe_movidos += $cant_pe;
						} else {
							$resUPE = mysql_query("INSERT INTO precios_especiales (ano, cliente, cantidad, act) VALUES ('$ano', $cliente_dejar, $cant_pe, 0)");
							$encUPE = mysql_affected_rows();
							if ($encUPE>0)
								$pe_movidos += $cant_pe;
						
						}
					} // tiene PE
					*/
					// mover puntos flex, pep y normales, así como pe_disponibles
					$resPFP = mysql_query("SELECT puntos, puntos_flex, puntos_pep, kad_comprados, pe_disponibles FROM cliente WHERE clave = $cte_quitar");
					$rowPFP = mysql_fetch_array($resPFP);
					$puntos_norm = $rowPFP['puntos']+0;
					if ($puntos_norm<0) $puntos_norm=0;
					$puntos_flex = $rowPFP['puntos_flex']+0;
					if ($puntos_flex<0) $puntos_flex=0;
					$puntos_pep  = $rowPFP['puntos_pep']+0;
					if ($puntos_pep<0) $puntos_pep=0;
					$kad_comprados = $rowPFP['kad_comprados']+0;
					if ($kad_comprados<0) $kad_comprados = 0;

					$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
					$rowCFG = mysql_fetch_array($resultadoCFG);
					$limite_precios_especiales = $rowCFG['limite_precios_especiales']+0;
					$pe_consumidos = $limite_precios_especiales - $rowPFP['pe_disponibles'];
					if ($pe_consumidos<0) $pe_consumidos = 0;
					
					if ($puntos_flex>0 || $puntos_pep>0 || $puntos_norm > 0 || $kad_comprados>0 || $pe_consumidos>0) {
						$resultadoUPFP = mysql_query("UPDATE cliente SET puntos = puntos + $puntos_norm, puntos_flex = puntos_flex + $puntos_flex, puntos_pep = puntos_pep + $puntos_pep, 
															 kad_comprados = kad_comprados + $kad_comprados, pe_disponibles = pe_disponibles - $pe_consumidos, act = 1-act 
													   WHERE clave = $cliente_dejar");
						$encUPFP = mysql_affected_rows();
						if ($encUPFP>0) {
							$pf_movidos += $puntos_flex;
							$pp_movidos += $puntos_pep;
							$pn_movidos += $puntos_norm;
							$kad_movidos += $kad_comprados;
						}
					}
					
					
						
				}	// for clientes quitar
			} // empresa_whirlpool

			// pasar puntos flex/pep

			// eliminar cliente quitar
			$resultadoQC = mysql_query("DELETE FROM cliente WHERE clave IN ($clientes_quitar) ");
			$encQC = mysql_affected_rows();
			if ($encQC>0)
				$clientes_quitados += $encQC; 
				
		
			$mensaje  = 'Proceso de depuración.<br><br>';
			$mensaje .= 'Se movieron '.$pedidos_movidos.' pedidos de un cliente a otro<br>';
			$mensaje .= 'Se movieron '.$direcciones_movidas.' direcciones de envío de un cliente a otro<br>';
			$mensaje .= 'Se movieron '.$invitados_movidos.' invitados de un cliente a otro<br>';
			$mensaje .= 'Se movieron '.$pe_movidos.' productos especiales vendidos de un cliente a otro<br>';
			$mensaje .= 'Se movieron '.$kad_movidos.' productos KAD Mayores comprados por un cliente a otro<br>';
			$mensaje .= 'Se movieron '.$pn_movidos.' puntos de un cliente a otro<br>';
			$mensaje .= 'Se movieron '.$pf_movidos.' puntos flex disponibles de un cliente a otro<br>';
			$mensaje .= 'Se movieron '.$pp_movidos.' puntos pep disponibles de un cliente a otro<br>';
			$mensaje .= 'Se eliminaron '.$clientes_quitados.' clientes repetidos<br>';
			
			$link = 'javascript:parent.Shadowbox.close();';
			
		} // cliente_dejar > 0


	}

	if ($accion=='cambia_a_invitado') {

		$cliente_dejar = $_POST['cliente_dejar']+0;
		$clientes_quitar = '';
		for (reset($_POST); list($variable, $valor) = each($_POST); ){
		   $encontrado=0;
		   if(substr($variable,0,7)=="borrar_") {
				if (!empty($valor)) {
					$item = substr($variable,7,10);
					$cliente_borrar = $item;
					$clientes_quitar .= trim($cliente_borrar).", ";
				} // dato 
			} // elemento form es borrar_
		} // for
		$clientes_quitar = substr($clientes_quitar,0,-2); // eliminar coma final
		$clientes_cambiados = 0;
		
		if ($clientes_quitar) {
		
			// pasar clientes a invitados
			$resultadoUI = mysql_query("UPDATE cliente SET invitado_por = $cliente_dejar, invitado = 1, tipo = 'I', empresa = 178, act = 1-act WHERE clave IN ($clientes_quitar)");
			$encUI = mysql_affected_rows();
			if ($encUI>0)
				$clientes_cambiados += $encUI; 
	
			$mensaje  = 'Proceso de cambio a invitados.<br><br>';
			$mensaje .= 'Se movieron '.$clientes_cambiados.' clientes a empresa Invitados Whirlpool<br>';
			
			$link = 'javascript:parent.Shadowbox.close();';
			
		} // cliente_dejar > 0


	}
	
// include("_checa_vars.php");
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script src="js/jquery.js" type="text/javascript" language="javascript1.2"></script>
<script src="js/lib.js" type="text/javascript" language="javascript1.2"></script>
<script type="text/javascript" src="js/src/shadowbox.js"></script>
<link href="js/src/shadowbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript">

function depurar_clientes() {
	o = document.forma;
	
	if (o.cliente_dejar.value==0) {
		alert("Selecciona el registro principal, que se conservará");
		return;
	}
	if (!hayChecados('forma', 'borrar')) {
		alert("Selecciona el (los) registros que se eliminarán");
		return;
	}
	continuar = window.confirm("Deseas depurar la información, pasando los pedidos, direcciones de entrega, puntos e invitados de un cliente a otro?");
	if (!continuar) {
		return;
	}
	o.accion.value = 'depurar';
	o.action = "detalle_repetido.php";
	o.submit();
}
function cambiar_invitados() {
	o = document.forma;

	if (o.cliente_dejar.value==0) {
		alert("Selecciona el registro principal, que se registrará como quien invitó a los demás");
		return;
	}	
	if (!hayChecados('forma', 'borrar')) {
		alert("Selecciona el (los) registros que se van a cambiar a Invitados Whirlpool");
		return;
	}

	continuar = window.confirm("Deseas cambiar los registros a: Invitados Whirlpool, tipo Invitado?");
	if (!continuar) {
		return;
	}
	o.accion.value = 'cambia_a_invitado';
	o.action = "detalle_repetido.php";
	o.submit();
}

</script>
</head>

<body class="body_sb">
      <form action="" method="post" name="forma" id="forma">
        <input type="hidden" name="empresa" value="<?=$_POST['empresa'];?>" />
        <input type="hidden" name="estatus" value="<?=$_POST['estatus'];?>" />
        <input type="hidden" name="accion" value="" />
        
        <? if ($accion != '') { ?>
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td align="center"><div align="center"><? include("mensaje.php");?></div></td>
          </tr>
		</table>        
        <? } else { ?>
        
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td width="17%">&nbsp;</td>
            <td width="83%">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right"><strong>Dato a buscar:</strong></div></td>
            <td><div align="left">
              <?=$dato;?>
            </div></td>
          </tr>

          <tr>
            <td align="right"><strong>RESULTADOS</strong></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">
              <table width="100%" border="0" cellpadding="2" cellspacing="1">
                <tr class="texto" bgcolor="#F4F4F2">
                  <td nowrap="nowrap"><div align="center"><b>
                    Nombre
                  </b></div></td>
                  <td><div align="center"><strong>Correo-e</strong></div></td>
                  <td><div align="center"><strong>Empresa</strong></div></td>
                  <td><div align="center"><strong># Empl</strong></div></td>
                  <td><div align="center"><strong>Tipo</strong></div></td>
                  <td><div align="center"><strong>Pedidos</strong></div></td>
                  <td><div align="center"><strong>Ultimo Pedido</strong></div></td>
                  <td><div align="center"><strong>Invitados</strong></div></td>
                  <td><div align="center"><b>Conservar</b></div></td>
                  <td><div align="center"><strong><span class="encab">
                    <input id="sel_boxes" name="sel_boxes" type="checkbox" value="1" onclick="SetAllCheckBoxes('forma', 'borrar', this.checked);SetAllCheckBoxes('forma', 'dejar', false); document.forma.cliente_dejar.value = ''; "/>
                  </span>Eliminar</strong></div></td>
                </tr>
            <?

				 $condicion = "WHERE 1=1 ";

				 if ($dato_buscar == 'nombre') 
					 $query = "SELECT cliente.clave, tipo, CONCAT(TRIM(nombre),' ',TRIM(apellido_paterno),' ',TRIM(apellido_materno)) AS nombre, email, empresa, numero_empleado, invitado FROM cliente WHERE CONCAT(TRIM(nombre),' ',TRIM(apellido_paterno),' ',TRIM(apellido_materno)) = '$dato'";
				 if ($dato_buscar == 'email') 
					 $query = "SELECT cliente.clave, tipo, CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) AS nombre, email, empresa, numero_empleado, invitado FROM cliente WHERE email = '$dato'";
				 if ($dato_buscar == 'numero_empleado') 
	 				 $query = "SELECT cliente.clave, tipo, CONCAT(cliente.nombre,' ',apellido_paterno,' ',apellido_materno) AS nombre, cliente.email, cliente.empresa, cliente.numero_empleado, invitado
					 			 FROM cliente LEFT JOIN empresa ON cliente.empresa = empresa.clave
					 		    WHERE empresa = $dato1 AND numero_empleado = '$dato2'";
				 if ($dato_buscar == 'numero_empleado_wp') 
	 				 $query = "SELECT cliente.clave, tipo, CONCAT(cliente.nombre,' ',apellido_paterno,' ',apellido_materno) AS nombre, cliente.email, cliente.empresa, cliente.numero_empleado, invitado 
					 			 FROM cliente LEFT JOIN empresa ON cliente.empresa = empresa.clave
					 		    WHERE empresa_whirlpool = 1 AND numero_empleado = '$dato2'";

//echo $query;
	
				 $resultado= mysql_query($query,$conexion);
				 while ($row = mysql_fetch_array($resultado)){ 
					
					$cve_empleado = $row['clave'];
					$cve_empresa=$row['empresa'];
					$resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$cve_empresa'",$conexion);
					$rowEMP= mysql_fetch_array($resEMP); 
	
					$cve_cliente=$row['clave'];
					$resPED= mysql_query("SELECT * FROM pedido WHERE cliente='$cve_cliente'",$conexion);
					$cant_compras = mysql_num_rows($resPED);

					$resPED= mysql_query("SELECT MAX(fecha) AS ultimo_pedido FROM pedido WHERE cliente='$cve_cliente'",$conexion);
					$rowPED = mysql_fetch_array($resPED);
					$ultimo_pedido = $rowPED['ultimo_pedido'];
	
					$resultadoINVS = mysql_query("SELECT COUNT(*) AS total_invitados FROM cliente WHERE invitado AND invitado_por = $cve_cliente",$conexion);
					$rowINVS = mysql_fetch_array($resultadoINVS);
					$total_invitados = $rowINVS['total_invitados'];
	
			  ?>
                <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
                  <td align="center" valign="top" bgcolor="#FFFFFF"><?= $row['nombre']; ?>
                  </td>
                  <td valign="top" bgcolor="#FFFFFF" align="center"><?= $row['email']; ?></td>
                  <td valign="top" bgcolor="#FFFFFF" align="center"><?=$rowEMP['nombre'];?></td>
                  <td align="center" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><?=$row['numero_empleado'];?></td>
                  <td align="center" valign="top" nowrap="nowrap" bgcolor="#FFFFFF">
                  <? switch ($row['tipo']) {
				  		case 'E' : echo 'Empleado'; break;
				  		case 'I' : echo 'Invitado'; break;
				  		case 'C' : echo 'Corporate'; break;
				  		case 'A' : echo 'M.Abierto'; break;
				     }
				  ?>
                  </td>
                  <td align="center" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><?= $cant_compras; ?></td>
                  <td align="center" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><?=fecha($rowPED['ultimo_pedido']);?></td>
                  <td align="center" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><?= $total_invitados; ?></td>
                  <td align="center" valign="top" nowrap="nowrap" bgcolor="#FFFFFF">
                   <input type="radio" name="dejar" id="dejar_<?=$row['clave'];?>" value="<?=$row['clave'];?>" onchange="document.forma.cliente_dejar.value = this.value; document.forma.borrar_<?=$row['clave'];?>.checked = false" />
                  </td>
                  <td align="center" valign="top" nowrap="nowrap" bgcolor="#FFFFFF">
				    <input name="borrar_<?=$row['clave'];?>" type="checkbox" id="borrar"  onchange="if (this.checked) { if (document.forma.dejar_<?=$row['clave'];?>.checked) { document.forma.dejar_<?=$row['clave'];?>.checked = false; document.forma.cliente_dejar.value = '';}}" value="1" />
                  </td>
                </tr>

              <? } // while ?>
            </table></td>
          </tr>
          <tr>
            <td colspan="2">
                <input type="hidden" name="cliente_dejar" value="0" />
                
                <input name="desc2" type="button" class="boton" onclick="javascript:depurar_clientes();" value="DEPURAR seleccionados" id="desc2"  />
                <p>
                <input name="desc3" type="button" class="boton" onclick="javascript:cambiar_invitados();" value="Cambiar a Invitados los seleccionados" id="desc2"  />
                </p>
                <input name="desc" type="button" class="boton" onclick="javascript:parent.Shadowbox.close();" value="Cancelar" id="desc" />            
             </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
        
        <? } ?>
      </form>    
</body>
</html>
