<?php
if (!include('ctrl_acceso.php')) return;
include('funciones.php');
include('lib.php');
$modulo=13;
if (!op($modulo))  {
	$aviso = 'Usuario sin permiso para acceder a este módulo';
	$aviso_link = 'principal.php';
	include('mensaje_sistema.php');
	return;
}

include('../conexion.php');

$tienda = $_POST['tienda']+0;
$fecha = $_POST['fecha'];
$folio = $_POST['folio'];
$folio_oc = $_POST['folio_oc'];
$folio_tc = $_POST['folio_tc'];
$folio_td = $_POST['folio_td'];
$tipo = $_POST['tipo'];
$empresa = $_POST['empresa'];
$vendedor = $_POST['vendedor'];
$buscar = $_POST['buscar']+0;

$ver = $_POST['ver'];
$numpag = $_POST['numpag'];
$ord = $_POST['ord'];
if (empty($ver)) $ver='20';
if (empty($numpag)) $numpag='1';
if (empty($ord)) $ord='folio';
if     ($ord=='folio') $orden='ORDER BY folio DESC';
if (!$fecha) $fecha = "01/".date("m/Y")." - ".date("d/m/Y");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Punto de Venta</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<link href="js/src/shadowbox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/src/shadowbox.js"></script>
<link href="css/jquery.datepick.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.datepick.js" type="text/javascript" language="javascript1.2"></script>
<script src="js/jquery.datepick-es.js" type="text/javascript" language="javascript1.2"></script>

<script type="text/javascript">
$(document).ready(function() {
  $('#fecha').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/calendario.gif', rangeSelect: true, numberOfMonths: 2, minDate: '01/01/2011', maxDate: '+1m' } );
});
</script>

<script type="text/javascript">
	Shadowbox.init({
		language: 'es',
		players:  ['img', 'html', 'iframe', 'qt', 'wmp', 'swf', 'flv']
	});
</script>

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
	form.target = '_self';
    form.action='corte_caja.php';
    form.buscar.value=1;
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
	document.forma.target = '_self';
    document.forma.action='corte_caja.php';
    form.buscar.value=1;
    document.forma.submit();
  }
  function ver_detalle(folio) {
    document.forma.pedido.value = folio;
	document.forma.target = '_self';
    document.forma.action='detalle_pedido.php';
    document.forma.submit();
  }
  function exportar() {
  	document.forma.target = '_blank';
    document.forma.action='corte_caja_xls.php';
    document.forma.buscar.value=1;
    document.forma.submit();
	document.forma.target = '_self';
  }
</script>
</head>

<body>
<div id="container">
	<?php $tit='Reporte: Corte de Caja'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input name="pedido" type="hidden" id="pedido" />
        <input name="ord" type="hidden" id="ord" value="<?php echo $ord; ?>" />
        <input name="numpag" type="hidden" id="numpag" value="<?php echo  $numpag; ?>" />
        <input name="buscar" type="hidden" id="buscar" />
      
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td valign="bottom">&nbsp;</td>
            <td align="left"><table width="650" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td align="right">Fechas:</td>
                <td nowrap="nowrap">
                  <input name="fecha" type="text" class="fLeft fechas" id="fecha" value="<?php echo $fecha;?>" readonly="readonly" /></td>
                <td align="right">Tienda:</td>
                <td><select name="tienda" class="campo" id="tienda">
                  <option value="0" <?php if ($tienda=='0') echo 'selected';?>>Cualquiera...</option>
                  <?php  $resTIE= mysql_query("SELECT * FROM tienda ORDER BY nombre",$conexion);
                      while ($rowTIE = mysql_fetch_array($resTIE)) {
					  	 echo '<option value="'.$rowTIE['clave'].'"';
						 if ($tienda==$rowTIE['clave']) echo 'selected';
						 echo '>'.$rowTIE['nombre'].'</option>';
					  } ?>
                </select></td>
              </tr>
              <tr>
                <td align="right">Empresa:</td>
                <td>
                  <select name="empresa" class="campo" id="empresa">
                    <option value="" selected="selected">Cualquiera...</option>
                    <? 
				
					$resEMP = mysql_query("SELECT clave, nombre FROM empresa ORDER BY nombre",$conexion);
					while ($rowEMP = mysql_fetch_array($resEMP)) {
					  echo '<option value="'.$rowEMP['clave'].'"';
					  if ($rowEMP['clave']==$empresa) echo ' selected';
					  echo '>'.$rowEMP['nombre'].'</option>';
					}
			  ?>
                  </select>
                </td>
                <td align="right">Vendedor:</td>
                <td><select name="vendedor" class="campo" id="vendedor">
                  <option value="" selected="selected">Cualquiera...</option>
                  <? 
					if ($tienda>0) $filt = " WHERE tienda = $tienda ";
					$resEMP = mysql_query("SELECT clave, nombre FROM usuario_tienda $filt ORDER BY tienda, nombre",$conexion);
					while ($rowEMP = mysql_fetch_array($resEMP)) {
					  echo '<option value="'.$rowEMP['clave'].'"';
					  if ($rowEMP['clave']==$vendedor) echo ' selected';
					  echo '>'.$rowEMP['nombre'].'</option>';
					}
				
			  ?>
                </select></td>
              </tr>
              <tr>
                <td><div align="right">Folio:</div></td>
                <td>
                  <input name="folio" type="text" class="campo" id="folio" size="15" maxlength="15" value="<?php echo $folio;?>"/></td>
                <td width="87"><div align="right">Folio OC:</div></td>
                <td width="221"><input name="folio_oc" type="text" class="campo" id="folio_oc" size="15" maxlength="15" value="<?php echo $folio_oc;?>"/></td>
              </tr>
              <tr>
                <td nowrap=""><div align="right">Folio TC:</div></td>
                <td><input name="folio_tc" type="text" class="campo" id="folio_tc" size="15" maxlength="15" value="<?php echo $folio_tc?>"/></td>
                <td><div align="right">Folio TD:</div></td>
                <td><input name="folio_td" type="text" class="campo" id="folio_td" size="15" maxlength="15" value="<?php echo $folio_td;?>"/></td>
              </tr>
              <tr>
                <td align="right" >Tipo:</td>
                <td>
				<select name="tipo" class="campo" id="tipo">
                    <option value="" <?php if ($tipo=='x') echo 'selected';?>>Cualquiera..</option>
                    <option value="V" <?php if ($tipo=='V') echo 'selected';?>>Venta</option>
                    <option value="R" <?php if ($tipo=='R') echo 'selected';?>>Sustitución</option>
                    <option value="S" <?php if ($tipo=='S') echo 'selected';?>>Refacturación</option>
                </select></td>
                <td nowrap="nowrap">&nbsp;</td>
                <td nowrap="nowrap">&nbsp;
                  </td>
              </tr> 
              <tr>
                <td>&nbsp;</td>
                <td>
				<input name="Submit" type="button" class="boton" onclick="ir(document.forma,1);" value="Buscar" /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table>
        <?php if ($buscar) { ?>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
            <tr class="texto">
            <td nowrap="nowrap" colspan="12">
				<?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                      $condicion = "WHERE pedido.origen = 'pos' ";

					if (!empty($fecha)) {
						$fecha_desde = convierte_fecha(substr($fecha,0,10));
						$fecha_hasta = convierte_fecha(substr($fecha,13,10));
						$condicion .= " AND pedido.fecha BETWEEN '$fecha_desde' AND '$fecha_hasta' ";
					 }

                     if ($tipo != '')
					 	$condicion .= " AND pedido.tipo_pedido = '$tipo' ";

					 if ($tienda > 0) 
					 	$condicion .= " AND pedido.tienda = $tienda";

					 if ($empresa) 
					 	$condicion .= " AND pedido.empresa = $empresa ";
					
					 if ($vendedor)
					 	$condicion .= " AND pedido.vendedor = $vendedor ";

					 if ($folio) 
					 	$condicion .= " AND pedido.folio = $folio ";
												
					 if ($folio_oc) 
					 	$condicion .= " AND pedido.fdp_credito_nomina_folio LIKE '%".trim($folio_oc)."%' ";

					 if ($folio_tc) 
					 	$condicion .= " AND pedido.fdp_tdc_folio LIKE '%".trim($folio_tc)."%' ";

					 if ($folio_td) 
					 	$condicion .= " AND pedido.fdp_tdd_folio LIKE '%".trim($folio_td)."%' ";
					 	
					//  $condicion .= " AND pedido.estatus = 1 ";
					 $query = "SELECT * FROM pedido $condicion";
//echo $query;
                     $resultadotot= mysql_query($query,$conexion);
                     $totres = mysql_num_rows ($resultadotot);
                     $totpags = ceil($totres/$ver);
                     if ($totres==0)
                         $numpag = 0;
						  
					 echo 'Total de pedidos en la lista: <b>'.$totres.'</b>';
			
			  ?>            
            </td>
            <td nowrap="nowrap" colspan="9" align="right">
                <?
                     $regini = ($numpag * $ver) - $ver;
					 if ($regini<0) $regini=0;

                     // poner flechitas anterior, primero, último, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Página anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "P&aacute;gina ";
					 echo '<input type="text" name="pagina" value="'.$numpag.'" size="2" onchange="javascript:ir(document.forma,this.value);" style="text-align:center"/>';
					 echo " de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";

                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Última página"></a>';
                     }
              ?>            
            </td>

          </tr>
      
<tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><b>Fecha</b></td>
            <td nowrap="nowrap"><strong>Tipo de Pedido</strong></td>
            <td nowrap="nowrap"><strong>Empresa</strong></td>
            <td nowrap="nowrap"><strong>Vendedor</strong></td>
            <td nowrap="nowrap"><strong>Pedido POS</strong></td>
            <td nowrap="nowrap"><strong>Estatus</strong></td>
            <td><strong>OC</strong></td>
            <td><strong>Folio OC</strong></td>
            <td><strong>Tarjeta de Débito</strong></td>
            <td align="center"><strong>Folio TD</strong></td>
            <td align="center"><strong>Tarjeta de Crédito</strong></td>
            <td align="center"><strong>Folio TC</strong></td>
            <td align="center"><strong>Cheque</strong></td>
            <td align="center"><strong>Folio Cheque</strong></td>
            <td align="center"><strong>Depósito Directo</strong></td>
            <td align="center"><strong>Efectivo</strong></td>
            <td align="center"><strong>Puntos</strong></td>
            <td align="center"><strong>Puntos Flex</strong></td>
            <td align="center"><strong>Puntos PEP</strong></td>
            <td><div align="center"><strong>Total</strong></div></td>
          </tr>
          <?php

			 $query = "SELECT pedido.*, empresa.nombre AS nombre_empresa, usuario_tienda.nombre AS nombre_vendedor
			 			FROM pedido
			 			LEFT JOIN empresa ON pedido.empresa = empresa.clave 
						LEFT JOIN usuario_tienda ON pedido.vendedor = usuario_tienda.clave
						$condicion $orden LIMIT $regini,$ver";

			 $gran_total = 0;
			 $resultado = mysql_query($query,$conexion);
			 while ($row = mysql_fetch_array($resultado)){ 

				$odd = !$odd;
				$total = $row['fdp_credito_nomina'] + $row['fdp_tdd']+ $row['fdp_tdc'] + $row['fdp_cheque'] + $row['fdp_deposito'] + 
							 $row['fdp_efectivo'] + $row['fdp_puntos'] + $row['fdp_puntos_flex'] +$row['fdp_puntos_pep'];
				
				if ($row['estatus']==1 && ($row['tipo_pedido']=='V' || $row['tipo_pedido']=='S')) {
					$pedido = $row['folio'];
					$total_credito_nomina += $row['fdp_credito_nomina'];
					$total_tdd += $row['fdp_tdd'];
					$total_tdc += $row['fdp_tdc'];
					$total_cheque += $row['fdp_cheque'];
					$total_deposito += $row['fdp_deposito'];
					$total_efectivo += $row['fdp_efectivo'];
					$total_puntos += $row['fdp_puntos'];
					$total_puntos_flex += $row['fdp_puntos_flex'];
					$total_puntos_pep += $row['fdp_puntos_pep'];
					$gran_total += $total;
				}


          ?>
          <tr class="texto"  bgcolor="#FFFFFF">
            <td ><?php echo  fechamy2mx($row['fecha']); ?></td>
            <td >
			<?php  switch ($row['tipo_pedido']) {
			
				case 'V' : echo 'Venta'; break;
				case 'S' : echo 'Sustitución'.$pagado_cms; break;
				case 'R' : echo 'Refacturación'; break;
			   }
			 ?>
			  </td>
            <td ><?php echo $row['nombre_empresa'];?></td>
            <td ><?=$row['nombre_vendedor'];?></td>
            <td ><div align="center">
            <?php echo $row['folio'];?>
            </div></td>
            <td align="left" nowrap="nowrap" bgcolor="#FFFFFF">
			<?php  switch ($row['estatus']) {
			
				case '0' : echo 'Pendiente'; break;
				case '1' : echo 'Pagado'.$pagado_cms; break;
				case '2' : echo 'Rechazado'; break;
				case '3' : echo 'Revisión TDD'; break;
				case '4' : echo 'Revisión CEP'; break;
				case '9' : echo 'Cancelado'; break;
			   }
			 ?>
			 </td>
            <td align="right"><?php echo nocero(number_format($row['fdp_credito_nomina'],2));?></td>
            <td><?php echo $row['fdp_odc_folio'];?></td>
            <td align="right"><?php echo nocero(number_format($row['fdp_tdd'],2));?></td>
            <td><?php echo $row['fdp_tdd_folio'];?></td>
            <td align="right"><?php echo nocero(number_format($row['fdp_tdc'],2));?>            </td>
            <td><?php echo $row['fdp_tdc_folio'];?></td>
            <td align="right"><?php echo nocero(number_format($row['fdp_cheque'],2));?></td>
            <td><?php echo $row['fdp_cheque_folio'];?></td>
            <td align="right"><?php echo nocero(number_format($row['fdp_deposito'],2));?></td>
            <td align="right"><?php echo nocero(number_format($row['fdp_efectivo'],2));?></td>
            <td align="right"><?php echo nocero(number_format($row['fdp_puntos'],2));?></td>
            <td align="right"><?php echo nocero(number_format($row['fdp_puntos_flex'],2));?></td>
            <td align="right"><?php echo nocero(number_format($row['fdp_puntos_pep'],2));?></td>
            <td align="right"><?php echo number_format($total,2); ?></td>
		  </tr>
          <?php
                 } // WHILE
                 mysql_close();
              ?>

		  <tr class="texto" valign="top" bgcolor="#e5e5e2">
            <td colspan="6" align="left"><strong>Totales</strong></td>
            <td align="right"><?=nocero(number_format($total_credito_nomina,2));?></td>
            <td></td>
            <td align="right"><?=nocero(number_format($total_tdd,2));?></td>
            <td>&nbsp;</td>
            <td align="right"><?=nocero(number_format($total_tdc,2));?></td>
            <td>&nbsp;</td>
            <td align="right"><?=nocero(number_format($total_cheque,2));?></td>
            <td>&nbsp;</td>
            <td align="right"><?=nocero(number_format($total_deposito,2));?></td>
            <td align="right"><?=nocero(number_format($total_efectivo,2));?></td>
            <td align="right"><?=nocero(number_format($total_puntos,2));?></td>
            <td align="right"><?=nocero(number_format($total_puntos_flex,2));?></td>
            <td align="right"><?=nocero(number_format($total_puntos_pep,2));?></td>
            <td align="right"><?=number_format($gran_total,2);?></td>
          </tr>          
          
          
        </table>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="right"><div align="center">
              <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Exportar a XLS" />
            </div></td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB" align="right">
               <?php

                     // poner flechitas anterior, primero, &uacute;ltimo, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="P&aacute;gina anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "P&aacute;gina ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="&Uacute;ltima p&aacute;gina"></a>';
                     }
              ?>
            </td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
          </tr>
        </table>
        <?php } // if buscar ?>
      </form>    
    </div>
</div>
</body>
</html>