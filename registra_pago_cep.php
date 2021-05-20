<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=15;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	include('../conexion.php');
	include_once('lib.php');

	$pedido=$_POST['pedido'];
	$resultado = mysql_query("SELECT pedido.*, CONCAT(cliente.nombre,' ',cliente.apellido_paterno,' ',cliente.apellido_materno) AS nombre_cliente, empresa.nombre AS nombre_empresa 
								FROM pedido 
								LEFT JOIN empresa ON pedido.empresa = empresa.clave
								LEFT JOIN cliente ON pedido.cliente = cliente.clave
								WHERE folio = $pedido",$conexion);
	$row = mysql_fetch_array($resultado);

// include("_checa_vars.php");
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript">
  function descarta() {
   document.forma.action='lista_pedidos.php';
   document.forma.submit();
  }
  function mo_tabla(tabla) {
     if(document.getElementById(tabla).style.display == 'none') {
         document.getElementById(tabla).style.display = 'block';
		 return;
	 }
     if(document.getElementById(tabla).style.display == 'block') {
	     document.getElementById(tabla).style.display = 'none';
		 return;
	 }
  }
  function isEmail(string) {
    if (string.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)
        return true;
    else
        return false;
  }
  function valida() {
   if (document.forma.codigo_autorizacion.value=='') {
   	alert("Debes ingresar el código de autorización del banco (CEP)");
	document.forma.codigo_autorizacion.focus();
    return;
   }
   if (document.forma.mensaje_banco.value=='') {
   	alert("Debes ingresar el mensaje de respuesta del banco (Payworks CLABE)");
	document.forma.mensaje_banco.focus();
    return;
   }
   if (document.forma.mail_ccp.value != '' && !isEmail(document.forma.mail_ccp.value)) {
   	alert("Formato incorrecto de correo para copia");
	document.forma.mail_ccp.focus();
	return;
   }
   document.forma.action='graba_registra_pago_cep.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Cambiar estatus de pedido a Pagado (PAGO DIRECTO CEP)'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input type="hidden" name="empresa" value="<?=$_POST['empresa'];?>" />
        <input type="hidden" name="estatus" value="<?=$_POST['estatus'];?>" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td width="17%">&nbsp;</td>
            <td width="83%">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right"><strong>Folio:</strong></div></td>
            <td><?=$row['folio'];?>
            <input type="hidden" name="pedido" id="pedido" value="<?=$pedido;?>"/></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Estatus:</strong></div></td>
            <td><? switch ($row['estatus']) {
						case '0' : echo 'Pendiente'; break;
						case '1' : echo 'Pagado'; break;
						case '2' : echo 'Rechazado'; break;
						case '4' : echo 'Revisión CEP'; break;
				   }
				?></td>
          </tr>
          <tr>
            <td><div align="right"><strong>L&iacute;nea Captura:</strong></div></td>
            <td><?= $row['fdp_cep_folio'];?></td>
          </tr>
          <? if ($row['estatus'] == 1) { ?>
          <tr>
            <td><div align="right"><strong>C&oacute;digo Autorizaci&oacute;n:</strong></div></td>
            <td><?= $row['codigo_autorizacion'];?> </td>
          </tr>
          <? }
		     if ($row['estatus'] > 0) { ?>
          <tr>
            <td><div align="right"><strong>Mensaje:</strong></div></td>
            <td><?= $row['mensaje'];?></td>
          </tr>
          <?  
		     } ?>
          <tr>
            <td><div align="right"><strong>Fecha de pedido:</strong></div></td>
            <td><?=fecha($row['fecha']);?></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Fecha de entrega:</strong></div></td>
            <td><?=fecha($row['fecha_entrega']);?></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Cliente:</strong></div></td>
            <td><?=$row['nombre_cliente'];?></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Empresa:</strong></div></td>
            <td><?=$row['nombre_empresa'];?></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Correo-e:</strong></div></td>
            <td><?=$row['envio_email'];?></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Requiere Factura:</strong></div></td>
            <td><?= ($row['requiere_factura']) ? ('SI') : ('NO');?></td>
          </tr>
          <tr>
            <td align="right"><strong><a href="javascript:mo_tabla('t_envio');" class="texto">DATOS DE ENVIO</a> </strong></td>
            <td><strong>[ <a href="javascript:mo_tabla('t_envio');" class="texto">+</a> ]</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <? if ($row['requiere_factura']) { ?>
            <strong><a href="javascript:mo_tabla('t_fact');" class="texto">DATOS DE FACTURACIÓN</a> [ <a href="javascript:mo_tabla('t_fact');" class="texto">+</a> ]</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <? } ?>            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><table width="100%" border="0" cellpadding="3" cellspacing="0" id="t_envio" <? echo 'style="display:none;"';?>>
              <tr class="texto">
                <td width="12%"><div align="right"><strong>Domicilio:</strong></div></td>
                <td width="88%"><?=$row['envio_calle'];?>
                  #
                  <?=$row['envio_exterior'];?>
        <? if ($row['envio_interior']) echo "Int: ".$row['envio_interior'];?>                </td>
              </tr>
              <tr class="texto">
                <td><div align="right"><strong>Colonia:</strong></div></td>
                <td><?=$row['envio_colonia'];?></td>
              </tr>
              <tr class="texto">
                <td><div align="right"><strong>Ciudad:</strong></div></td>
                <td><?=$row['envio_ciudad_nombre'];?></td>
              </tr>
              <tr class="texto">
                <td><div align="right"><strong>Estado:</strong></div></td>
                <td><?=$row['envio_estado'];?></td>
              </tr>
              <tr class="texto">
                <td><div align="right"><strong>C.P.:</strong></div></td>
                <td><?=$row['envio_cp'];?></td>
              </tr>
              <tr class="texto">
                <td><div align="right"><strong>Referencias:</strong></div></td>
                <td><?=$row['envio_referencias'];?></td>
              </tr>
              <tr class="texto">
                <td><div align="right"><strong>Observaciones de envío:</strong></div></td>
                <td><?=str_replace(chr(10),'<br>',$row['envio_observaciones']);?></td>
              </tr>
              <tr class="texto">
                <td><div align="right"><strong>Tel&eacute;fonos:</strong></div></td>
                <td>Casa:
                  <?=$row['envio_telefono_casa'];?>
                  Oficina:
                  <?=$row['envio_telefono_oficina'];?>
                  Celular:
                  <?=$row['envio_telefono_celular'];?></td>
              </tr>
            </table>
              <table width="100%" border="0" cellpadding="3" cellspacing="0" id="t_cliente" <? echo 'style="display:none;"';?>>
                <tr class="texto">
                  <td width="12%"><div align="right"><strong>Domicilio:</strong></div></td>
                  <td width="88%"><?=$row['pers_calle'];?></td>
                </tr>
                <tr class="texto">
                  <td><div align="right"><strong>Colonia:</strong></div></td>
                  <td><?=$row['envio_colonia'];?></td>
                </tr>
                <tr class="texto">
                  <td><div align="right"><strong>Ciudad:</strong></div></td>
                  <td><?=$row['pers_ciudad'];?></td>
                </tr>
                <tr class="texto">
                  <td><div align="right"><strong>Estado:</strong></div></td>
                  <td><?=$row['pers_estado'];?></td>
                </tr>
                <tr class="texto">
                  <td><div align="right"><strong>C.P.:</strong></div></td>
                  <td><?=$row['pers_cp'];?></td>
                </tr>

                <tr class="texto">
                  <td><div align="right"><strong>Tel&eacute;fono:</strong></div></td>
                  <td><?=$row['pers_telefono'];?></td>
                </tr>
              </table>
              <table width="100%" border="0" cellpadding="3" cellspacing="0" id="t_fact" <? echo 'style="display:none;"';?>>
                <tr class="texto">
                  <td><div align="right"><strong>Raz&oacute;n Social:</strong></div></td>
                  <td><?=$row['fact_razon_social'];?></td>
                </tr>
                <tr class="texto">
                  <td><div align="right"><strong>RFC:</strong></div></td>
                  <td><?=$row['fact_rfc'];?></td>
                </tr>

                <tr class="texto">
                  <td width="12%"><div align="right"><strong>Domicilio:</strong></div></td>
                  <td width="88%"><?=$row['fact_calle'];?></td>
                </tr>
                <tr class="texto">
                  <td><div align="right"><strong>Colonia:</strong></div></td>
                  <td><?=$row['fact_colonia'];?></td>
                </tr>
                <tr class="texto">
                  <td><div align="right"><strong>Ciudad:</strong></div></td>
                  <td><?=$row['fact_ciudad'];?></td>
                </tr>
                <tr class="texto">
                  <td><div align="right"><strong>Estado:</strong></div></td>
                  <td><?=$row['fact_estado'];?></td>
                </tr>
                <tr class="texto">
                  <td><div align="right"><strong>C.P.:</strong></div></td>
                  <td><?=$row['fact_cp'];?></td>
                </tr>

                <tr class="texto">
                  <td><div align="right"><strong>Tel&eacute;fonos:</strong></div></td>
                  <td><?=$row['fact_telefono'];?></td>
                </tr>
              </table>              </td>
          </tr>
          <tr>
            <td align="right"><strong>DETALLE DEL PEDIDO</strong></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">
              <table width="100%" border="0" cellpadding="2" cellspacing="3">
              <tr>
                <td align="center" bgcolor="#F4F4F2"><b>Cantidad</b></td>
                <td bgcolor="#F4F4F2"><b>Modelo</b></td>
                <td bgcolor="#F4F4F2"><b>Nombre</b></td>
                <td nowrap="nowrap" bgcolor="#F4F4F2"><strong>Marca</strong></td>
                <td align="right" nowrap="nowrap" bgcolor="#F4F4F2"><b>Precio Empl</b></td>
                <td align="right" bgcolor="#F4F4F2"><strong>Costo Entrega</strong></td>
                <td align="right" bgcolor="#F4F4F2"><b>Subtotal</b></td>
                <td align="center" bgcolor="#F4F4F2"><strong>Entrega</strong></td>
                <td align="center" bgcolor="#F4F4F2"><strong>CEDIS</strong></td>
                <td align="center" bgcolor="#F4F4F2"><strong>LOC</strong></td>
              </tr>
              <? $query = "SELECT detalle_pedido.*, marca.nombre AS nombre_marca
			  				 FROM detalle_pedido 
							 LEFT JOIN marca ON detalle_pedido.marca = marca.clave
							 WHERE pedido = $pedido ORDER BY partida";
			     $resultadoDP = mysql_query($query,$conexion);
				 $total_productos = 0;
				 while ($rowDP = mysql_fetch_array($resultadoDP)) { 
				 	$total_productos += $rowDP['cantidad'];
					
					$sucursal_almex = $rowDP['sucursal_ocurre'];
					$resultadoSA = mysql_query("SELECT * FROM sucursal_ocurre WHERE clave = $sucursal_almex");
					$rowSA = mysql_fetch_array($resultadoSA);
					$suc = "[".$rowSA['estado']."] ".$rowSA['nombre'];
					
			  ?>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><?= $rowDP['cantidad']; ?></td>
                <td nowrap="nowrap" bgcolor="#FFFFFF" ><?= $rowDP['modelo']; ?></td>
                <td bgcolor="#FFFFFF" ><?= $rowDP['nombre']; ?></td>
                <td bgcolor="#FFFFFF"><?= $rowDP['nombre_marca']; ?></td>
                <td align="right" bgcolor="#FFFFFF"><?= number_format($rowDP['precio_empleado'],2); ?></td>
                <td align="right" bgcolor="#FFFFFF"><?= number_format($rowDP['costo_entrega'],2); ?>
                <? if ($rowDP['costo_entrega']>0) echo '<br>SKU: '.$rowDP['sku_entrega'];?></td>
                <td align="right" bgcolor="#FFFFFF"><?= number_format($rowDP['subtotal'],2); ?></td>
                <td align="center" bgcolor="#FFFFFF">
				<? 
					if ($rowDP['es_garantia'] && $rowDP['folio_garantia']) {
						$folio_garantia=$rowDP['folio_garantia']; 
						$resultadoG = mysql_query("SELECT * FROM garantia WHERE folio = $folio_garantia");
						$rowG=mysql_fetch_array($resultadoG);
						if ($rowG['token']) { 
							$link_g = '
							  <a href="../pdf_garantia.php?folio='.$folio_garantia.'&token='.$rowG['token'].'" target=_blank>Descargar</a>';
							echo $link_g; 
						}
					} else { 
						echo $rowDP['tiempo_entrega']." (".fecha($rowDP['fecha_entrega']).")";
						switch ($rowDP['tipo_entrega']) {
							case 'domicilio': echo '<br>a domicilio'; break;
							case 'ocurre'   : echo '<br><a href="#" title="'.$suc.'" alt="'.$suc.'">a ocurre</a>'; 
											  break;
						}
					}
				
				?>
                <td align="center" bgcolor="#FFFFFF"><?= $rowDP['cedis'];?></td>
                <td align="center" bgcolor="#FFFFFF"><?= $rowDP['loc'];?></td>
              </tr>
              <? } // while ?>
              <tr>
                <td align="right"><div align="center"><strong>
                    <?= $total_productos; ?>
                </strong></div></td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right"><b>$
                      <?= number_format($row['total'],2); ?>
                </b></td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">
            <? if ($row['estatus'] == '4') { ?>
              <table width="79%" border="0" cellpadding="00">
              <tr>
                <td colspan="2"><strong>Por favor ingresa estos datos de CEP para autorizar el pedido</strong></td>
              </tr>
              <tr>
                <td width="21%">C&oacute;digo de Autorizaci&oacute;n:</td>
                <td width="79%"><input name="codigo_autorizacion" type="text" id="codigo_autorizacion" size="20" maxlength="20" /></td>
              </tr>
              <tr>
                <td>Mensaje:</td>
                <td><input name="mensaje_banco" type="text" id="mensaje_banco" size="40" maxlength="250" value="" /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><label><input name="envia_mail" type="checkbox" id="envia_mail" value="1" />Enviar mail al cliente,</label>
                   CCP: 
                    <label>
                  <input name="mail_ccp" type="text" id="mail_ccp" size="40" maxlength="80"/>
                   </label></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><label>
                  <input name="genera_sap" type="checkbox" disabled id="genera_sap" value="1" checked="checked" />
                  Generar TXT para SAP y afectar existencias</label></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input name="bgrab" type="button" class="boton" onclick="valida();" value="CAMBIAR ESTATUS A &quot;PAGADO&quot;" id="bgrab" /></td>
              </tr>
            </table>
            <? } ?>            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
