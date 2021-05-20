<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=13;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	include('../conexion.php');
	include_once('lib.php');

	$pedido=$_POST['pedido'];
	$resultado = mysql_query("SELECT pedido.*, CONCAT(cliente.nombre,' ',cliente.apellido_paterno,' ',cliente.apellido_materno) AS nombre_cliente, empresa.nombre AS nombre_empresa, cliente.invitado_por, tienda.nombre AS nombre_tienda, cliente.numero_empleado
								FROM pedido 
								LEFT JOIN empresa ON pedido.empresa = empresa.clave
								LEFT JOIN cliente ON pedido.cliente = cliente.clave
								LEFT JOIN tienda ON pedido.tienda = tienda.clave
								WHERE folio = $pedido",$conexion);
	$row = mysql_fetch_array($resultado);
	
	if ($row['invitado_por']) {
		$cliente_inv = $row['invitado_por'];
		$resultadoIC = mysql_query("SELECT nombre FROM cliente WHERE clave = $cliente_inv");
		$rowIC = mysql_fetch_array($resultadoIC);
	
	}

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
</script>
</head>

<body>
<div id="container">
	<? $tit='Detalle del pedido'; include('top.php'); ?>
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
            <td><?=substr(str_replace('-','',$row['fecha']).$row['folio'].'_L',2,50);?></td>
          </tr>
          <tr>
            <td><div align="right"><strong>Origen:</strong></div></td>
            <td><?=strtoupper($row['origen']);?>
             <? if ($row['origen']=='pos') echo '&nbsp;&nbsp;&nbsp;&nbsp;<strong>Tienda:</strong> '.$row['nombre_tienda'];?></td>
          </tr>
          <tr>
            <td valign="top"><div align="right"><strong>Estatus:</strong></div></td>
            <td><? switch ($row['estatus']) {
						case '0' : echo 'Pendiente'; break;
						case '1' : echo 'Pagado'; break;
						case '2' : echo 'Rechazado'; break;
						case '3' : echo 'Revisión TDD'; break;
						case '4' : echo 'Revisión CEP'; break;
						case '9' : echo 'Cancelado'; break;
				   }
				   if ($row['detalle_pago_cms']) echo '<br>'.$row['detalle_pago_cms'];
				?>
                
            </td>
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
          <?  if ($row['mensaje'] != $row['mensaje_largo']) { ?>
          <tr>
            <td><div align="right"><strong>Text:</strong></div></td>
            <td><?= $row['mensaje_largo'];?></td>
          </tr>
          
          <?  } 
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
            <td><?=$row['numero_empleado']." - ".$row['nombre_cliente'];?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?
				if ($row['invitado_por']) {
					echo "<strong>Invitado por: </strong>".$rowIC['nombre'];
				}
				
			?>            </td>
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
            <strong><a href="javascript:mo_tabla('t_fact');" class="texto">DATOS DE FACTURACIÓN</a> [ <a href="javascript:mo_tabla('t_fact');" class="texto">+</a> ]</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;            <? } ?>            
            <strong><a href="javascript:mo_tabla('t_fdp');" class="texto">FORMAS DE PAGO </a> [ <a href="javascript:mo_tabla('t_fdp');" class="texto">+</a> ]</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><table width="50%" border="0" cellpadding="2" cellspacing="2" id="t_envio" <? echo 'style="display:none;"';?>>
              <tr class="texto">
                <td width="38%" bgcolor="#AAAAAA"><div align="right"><strong>Domicilio:</strong></div></td>
                <td width="62%" bgcolor="#FFFFFF"><?=$row['envio_calle'];?>
                  #
                  <?=$row['envio_exterior'];?>
        <? if ($row['envio_interior']) echo "Int: ".$row['envio_interior'];?>                </td>
              </tr>
              <tr class="texto">
                <td bgcolor="#AAAAAA"><div align="right"><strong>Colonia:</strong></div></td>
                <td bgcolor="#FFFFFF"><?=$row['envio_colonia'];?></td>
              </tr>
              <tr class="texto">
                <td bgcolor="#AAAAAA"><div align="right"><strong>Ciudad:</strong></div></td>
                <td bgcolor="#FFFFFF"><?=$row['envio_ciudad_nombre'];?></td>
              </tr>
              <tr class="texto">
                <td bgcolor="#AAAAAA"><div align="right"><strong>Estado:</strong></div></td>
                <td bgcolor="#FFFFFF"><?=$row['envio_estado'];?></td>
              </tr>
              <tr class="texto">
                <td bgcolor="#AAAAAA"><div align="right"><strong>C.P.:</strong></div></td>
                <td bgcolor="#FFFFFF"><?=$row['envio_cp'];?></td>
              </tr>
              <tr class="texto">
                <td bgcolor="#AAAAAA"><div align="right"><strong>Referencias:</strong></div></td>
                <td bgcolor="#FFFFFF"><?=$row['envio_referencias'];?></td>
              </tr>
              <tr class="texto">
                <td bgcolor="#AAAAAA"><div align="right"><strong>Entre calles:</strong></div></td>
                <td bgcolor="#FFFFFF"><?=str_replace(chr(10),'<br>',$row['envio_observaciones']);?></td>
              </tr>
              <tr class="texto">
                <td bgcolor="#AAAAAA"><div align="right"><strong>Tel&eacute;fonos:</strong></div></td>
                <td bgcolor="#FFFFFF">Casa:
                  <?=$row['envio_telefono_casa'];?>
                  Oficina:
                  <?=$row['envio_telefono_oficina'];?>
                  Celular:
                  <?=$row['envio_telefono_celular'];?></td>
              </tr>
            </table>
              <table width="50%" border="0" cellpadding="2" cellspacing="2" id="t_cliente" <? echo 'style="display:none;"';?>>
                <tr class="texto">
                  <td width="38%" bgcolor="#AAAAAA"><div align="right"><strong>Domicilio:</strong></div></td>
                  <td width="62%" bgcolor="#FFFFFF"><?=$row['pers_calle'];?></td>
                </tr>
                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>Colonia:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['envio_colonia'];?></td>
                </tr>
                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>Ciudad:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['pers_ciudad'];?></td>
                </tr>
                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>Estado:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['pers_estado'];?></td>
                </tr>
                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>C.P.:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['pers_cp'];?></td>
                </tr>

                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>Tel&eacute;fono:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['pers_telefono'];?></td>
                </tr>
              </table>
              <table width="50%" border="0" cellpadding="2" cellspacing="2" id="t_fact" <? echo 'style="display:none;"';?>>
                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>Raz&oacute;n Social:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['fact_razon_social'];?></td>
                </tr>
                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>RFC:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['fact_rfc'];?></td>
                </tr>

                <tr class="texto">
                  <td width="37%" bgcolor="#AAAAAA"><div align="right"><strong>Domicilio:</strong></div></td>
                  <td width="63%" bgcolor="#FFFFFF"><?=$row['fact_calle'];?></td>
                </tr>
                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>Colonia:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['fact_colonia'];?></td>
                </tr>
                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>Ciudad:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['fact_ciudad'];?></td>
                </tr>
                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>Estado:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['fact_estado'];?></td>
                </tr>
                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>C.P.:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['fact_cp'];?></td>
                </tr>

                <tr class="texto">
                  <td bgcolor="#AAAAAA"><div align="right"><strong>Tel&eacute;fonos:</strong></div></td>
                  <td bgcolor="#FFFFFF"><?=$row['fact_telefono'];?></td>
                </tr>
              </table>              </td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
            <td>
             <table width="50%" border="0" cellpadding="2" cellspacing="2" id="t_fdp" <? echo 'style="display:none;"';?>>
              <tr bgcolor="AAAAAA">
                <th><div align="center">Forma de Pago</div></th>
                <th bgcolor="#AAAAAA"><div align="right" class="pr05">Monto</div></th>
                <th><div align="left">Detalles</div></th>
              </tr>
              <? if ($row['fdp_efectivo']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td nowrap="nowrap" ><div align="right">Efectivo</div></td>
                <td width="30%" class="numerico"><div align="right" class="pr05">
                  <?=$row['fdp_efectivo'];?>
                </div></td>
                <td width="46%" bgcolor="#FFFFFF">&nbsp;</td>
              </tr>
              <? } ?>
              <? if ($row['fdp_tdc']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td width="24%" nowrap="nowrap" ><div align="right">Tarjeta de cr&eacute;dito</div>
                 <? if ($row['pago_msi']>0) echo '<div align="center">'.$row['pago_msi'].' msi</div>';?>
                </td>
                <td class="numerico"><div align="right" class="pr05">
                  <?=$row['fdp_tdc'];?>
                </div></td>
                <td>Banco:
                  <?=$row['fdp_tdc_banco'];?>
                  <br />
                  Folio:
                  <?=$row['fdp_tdc_folio'];?></td>
              </tr>
              <? } ?>
              <? if ($row['fdp_tdd']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td nowrap="nowrap" ><div align="right">Tarjeta de d&eacute;bito</div></td>
                <td  class="numerico"><div align="right" class="pr05">
                  <?=$row['fdp_tdd'];?>
                </div></td>
                <td>Banco:
                  <?=$row['fdp_tdd_banco'];?>
                  <br />
                  Folio:
                  <?=$row['fdp_tdd_folio'];?></td>
              </tr>
              <? } ?>
              <? if ($row['fdp_cheque']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td nowrap="nowrap" ><div align="right">Cheque</div></td>
                <td  class="numerico"><div align="right" class="pr05">
                  <?=$row['fdp_cheque'];?>
                </div></td>
                <td>Banco:
                  <?=$row['fdp_cheque_banco'];?>
                  <br />
                  Folio:
                  <?=$row['fdp_cheque_folio'];?></td>
              </tr>
              <? } ?>
              <? if ($row['fdp_deposito']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td nowrap="nowrap" ><div align="right">Dep&oacute;sito Directo</div></td>
                <td  class="numerico"><div align="right" class="pr05">
                  <?=$row['fdp_deposito'];?>
                </div></td>
                <td>Banco:
                  <?=$row['fdp_deposito_banco'];?>
                  <br />
                  Fecha dep&oacute;sito
                  <?= fechamy2mx($row['fdp_deposito_fecha'],'novacio');?>
                  <br />
                  Cantidad depositada:
                  <?=$row['fdp_deposito_folio'];?></td>
              </tr>
              <? } ?>
              <? if ($row['fdp_credito_nomina']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td nowrap="nowrap" ><div align="right">Orden de Compra</div></td>
                <td  class="numerico"><div align="right" class="pr05">
                  <?=$row['fdp_credito_nomina'];?>
                </div></td>
                <td><span class="numerico">Folio:
                  <?=$row['fdp_credito_nomina_folio'];?>
                </span></td>
              </tr>
              <? } ?>
              <? if ($row['fdp_puntos']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td nowrap="nowrap" ><div align="right">Puntos</div></td>
                <td  class="numerico"><div align="right" class="pr05">
                  <?=$row['fdp_puntos'];?>
                </div></td>
                <td>&nbsp;</td>
              </tr>
              <? } ?>
              <? if ($row['fdp_puntos_flex']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td nowrap="nowrap" ><div align="right">Puntos Flex</div></td>
                <td  class="numerico"><div align="right" class="pr05"><?=$row['fdp_puntos_flex'];?></div></td>
                <td>&nbsp;</td>
              </tr>
              <? } ?>
              <? if ($row['fdp_puntos_pep']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td nowrap="nowrap" ><div align="right">Puntos PEP</div></td>
                <td  class="numerico"><div align="right" class="pr05"><?=$row['fdp_puntos_pep'];?></div></td>
                <td>&nbsp;</td>
              </tr>
              <? } ?>
              <? if ($row['fdp_sustitucion']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td nowrap="nowrap" ><div align="right">Sustituci&oacute;n:</div></td>
                <td  class="numerico"><div align="right" class="pr05">
                  <?=$row['fdp_sustitucion'];?>
                </div></td>
                <td>Folio:
                  <?=$row['fdp_sustitucion_folio'];?>
                  <br />
                  Motivo:
                  <?=$row['fdp_sustitucion_motivo'];?>                </td>
              </tr>
              <? } ?>
              <? if ($row['fdp_cep']>0) { ?>
              <tr bgcolor="#FFFFFF" >
                <td nowrap="nowrap" ><div align="right">CEP Online:</div></td>
                <td  class="numerico"><div align="right" class="pr05">
                  <?=$row['fdp_cep'];?>
                </div></td>
                <td>Folio:
                  <?=$row['fdp_cep_folio'];?>                </td>
              </tr>
              <? } ?>
              <tr bgcolor="#FFFFFF" >
                <td align="center" nowrap="nowrap" ><div align="right"><strong>TOTAL</strong></div></td>
                <td  class="numerico"><div align="right" class="pr05"><strong>
                  <?=$row['total'];?>
                </strong></div></td>
                <td bgcolor="#CCCCCC">&nbsp;</td>
              </tr>
            </table></td>
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
                <td align="center" nowrap="nowrap" bgcolor="#F4F4F2"><strong>Lista Precios</strong></td>
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
          $resultadoSV = mysql_query("SELECT * FROM detalle_servicio WHERE pedido=".$rowDP['pedido']." AND partida=".$rowDP['partida']." ");

			  ?>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><?= $rowDP['cantidad']; ?></td>
                <td nowrap="nowrap" bgcolor="#FFFFFF" ><?= $rowDP['modelo']; ?>
                <? if ($rowDP['es_garantia']) echo '<br>Aplica para: '.$rowDP['rel_garantia'];?></td>
                <td bgcolor="#FFFFFF" ><?= $rowDP['nombre']; ?></td>
                <td bgcolor="#FFFFFF"><?= $rowDP['nombre_marca']; ?></td>
                <td align="center" bgcolor="#FFFFFF">
                  <? if ($rowDP['lista_precios']=='web') echo 'TE'; else echo $rowDP['lista_precios'];?></td>
                <td align="right" bgcolor="#FFFFFF"><?= number_format($rowDP['precio_empleado'],2); ?></td>
                <td align="right" bgcolor="#FFFFFF"><?= number_format($rowDP['costo_entrega'],2); ?></td>
                <td align="right" bgcolor="#FFFFFF"><?= number_format($rowDP['subtotal'],2); ?></td>
                <td align="center" bgcolor="#FFFFFF"><? 
					if ($rowDP['es_garantia'] && $rowDP['folio_garantia']) {
						
						
						// ahora ambos se generan automaticamente
						$folio_garantia=$rowDP['folio_garantia']; 
						$resultadoG = mysql_query("SELECT * FROM garantia WHERE folio = $folio_garantia AND pedido =$pedido");
						$rowG=mysql_fetch_array($resultadoG);
						if ($rowG['token']) { 
							$link_g = '
							  <a href="../pdf_garantia.php?folio='.$folio_garantia.'&token='.$rowG['token'].'" target=_blank>Descargar</a>';
							echo $link_g; 
						} else {
							echo 'Folio: '.$rowDP['folio_garantia'];
						}
						
					} else { 
						echo $rowDP['tiempo_entrega']." (".fecha($rowDP['fecha_entrega']).")";
						switch ($rowDP['tipo_entrega']) {
							case 'domicilio': echo '<br>a domicilio'; break;
							case 'ocurre'   : echo '<br><a href="#" title="'.$suc.'" alt="'.$suc.'">a ocurre</a>'; 
											  break;
						}
						
					}
				
				?>&nbsp;</td>
                <td align="center" bgcolor="#FFFFFF"><?= $rowDP['cedis'];?></td>
                <td align="center" bgcolor="#FFFFFF"><?= $rowDP['loc'];?></td>
              </tr>
              <? 
              //SERVICIOS ADICIONALES
              while ($rowSV = mysql_fetch_array($resultadoSV)) { 
                $total_productos += $rowSV['cantidad'];
                ?>

              <tr>
                <td align="center" bgcolor="#FFFFFF"><?= $rowSV['cantidad']; ?></td>
                <td nowrap="nowrap" bgcolor="#FFFFFF" ><?= $rowSV['modelo']; ?></td>
                <td bgcolor="#FFFFFF" ><?= $rowSV['nombre']; ?></td>
                <td bgcolor="#FFFFFF"></td>
                <td align="center" bgcolor="#FFFFFF">
                  <? if ($rowDP['lista_precios']=='web') echo 'TE'; else echo $rowDP['lista_precios'];?></td>
                <td align="right" bgcolor="#FFFFFF"><?= number_format($rowSV['subtotal'],2); ?></td>
                <td align="right" bgcolor="#FFFFFF"><?= number_format(0,2); ?></td>
                <td align="right" bgcolor="#FFFFFF"><?= number_format($rowSV['subtotal'],2); ?></td>
                <td align="center" bgcolor="#FFFFFF"></td>
                <td align="center" bgcolor="#FFFFFF"><?= $rowDP['cedis'];?></td>
                <td align="center" bgcolor="#FFFFFF"><?= $rowDP['loc'];?></td>
              </tr>

                <? } //while SERVICIOS ADICIONALES
                } // while ?>
              <tr>
                <td align="right"><div align="center"><strong>
                    <?= $total_productos; ?>
                </strong></div></td>
                <td align="right">&nbsp;</td>
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
