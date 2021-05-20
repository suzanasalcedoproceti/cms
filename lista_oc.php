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
    form.action='lista_oc.php';
    form.buscar.value=1;
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
	document.forma.target = '_self';
    document.forma.action='lista_oc.php';
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
  	document.forma.target = '_self';
    document.forma.action='lista_oc_xls.php';
    document.forma.buscar.value=1;
    document.forma.submit();
	document.forma.target = '_self';
  }

  function confirmar() {
    document.forma.target = '_self';
    document.forma.action='graba_confirma_oc.php';
    document.forma.buscar.value=1;
    document.forma.submit();
  document.forma.target = '_self';
  }

  function exportar_c() {
    document.forma.target = '_self';
    document.forma.action='lista_oc_xls_c.php';
    document.forma.buscar.value=1;
    document.forma.submit();
  document.forma.target = '_self';
  }

  function importar() {
    document.forma.target = '_blank';
    document.forma.action='importa_oc.php';
    document.forma.buscar.value=1;
    document.forma.submit();
  document.forma.target = '_self';
  }
</script>
</head>

<body>
<div id="container">
	<?php $tit='Reporte: Orden de Compra Corporate'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input name="pedido" type="hidden" id="pedido" />
        <input name="ord" type="hidden" id="ord" value="<?php echo $ord; ?>" />
        <input name="numpag" type="hidden" id="numpag" value="<?php echo  $numpag; ?>" />
        <input name="buscar" type="hidden" id="buscar" />
      
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="center">
              <table width="950" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
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
                <td width="87"><div align="right">Folio OC:</div></td>
                <td width="221"><input name="folio_oc" type="text" class="campo" id="folio_oc" size="15" maxlength="15" value="<?php echo $folio_oc;?>"/></td>
                <td align="right" >Credito aprobado:</td>
                <td>
				<select name="tipo" class="campo" id="tipo">
                    <option value="x" <?php if ($tipo=='x') echo 'selected';?>>Cualquiera..</option>
                    <option value="0" <?php if ($tipo=='0') echo 'selected';?>>Blanks</option>
                    <option value="1" <?php if ($tipo=='1') echo 'selected';?>>Aprobada</option>
                    <option value="2" <?php if ($tipo=='2') echo 'selected';?>>Rechazada</option>
                    <option value="3" <?php if ($tipo=='3') echo 'selected';?>>Utilizada</option>
                    <option value="4" <?php if ($tipo=='4') echo 'selected';?>>Cancelada</option>
                    <option value="9" <?php if ($tipo=='9') echo 'selected';?>>Vencida</option>
                </select></td>
              </tr> 
              <tr>
                <td>&nbsp;</td>
                <td>
				<input name="Submit" type="button" class="boton" onclick="ir(document.forma,1);" value="Buscar" /></td>
                <td colspan="2" align="center"><input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Descargar OC" />&nbsp;&nbsp;<input name="imp_xls" type="button" class="boton" onclick="javascript:importar();" value="Subir OC" />
                </td><td align="center">
                <input name="exp_xls" type="button" class="boton" onclick="javascript:confirmar();" value="Confirmar Pedidos" />
              </td><td align="center"><input name="exp_xls_c" type="button" class="boton" onclick="javascript:exportar_c();" value="Pedidos Confirmados" /></td>

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

           if ($tipo != 'x')
					 	$condicion .= " AND orden_compra.estatus = '$tipo' ";

					 if ($tienda > 0) 
					 	$condicion .= " AND pedido.tienda = $tienda";

					 if ($empresa) 
					 	$condicion .= " AND pedido.empresa = $empresa ";
					
					 if ($vendedor)
					 	$condicion .= " AND pedido.vendedor = $vendedor ";
												
					 if ($folio_oc) 
					 	$condicion .= " AND pedido.fdp_credito_nomina_folio LIKE '%".trim($folio_oc)."%' ";
					 	
					//  $condicion .= " AND pedido.estatus = 1 ";
					 $query = "SELECT * FROM pedido inner join orden_compra on pedido.fdp_credito_nomina_folio=orden_compra.folio
           inner join plazo on plazo.clave = pedido.payment_terms $condicion";
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
            <td nowrap="nowrap"><strong>Empresa</strong></td>
            <td nowrap="nowrap"><strong>Pedido POS</strong></td>
            <td nowrap="nowrap"><strong>Cliente</strong></td>
            <td nowrap="nowrap"><strong>No. Empleado</strong></td>
            <td align="center"><strong>Monto a Financiar</strong></td>
            <td nowrap="nowrap"><strong>Plazo</strong></td>
            <td nowrap="nowrap"><strong>Pagos</strong></td>
            <td align="center"><strong>Descuento</strong></td>
            <td nowrap="nowrap"><strong>Articulos</strong></td>
            <td nowrap="nowrap"><strong>Credito Aprobado</strong></td>
            <td align="center"><strong>Monto Disponible</strong></td>
            <td nowrap="nowrap"><strong>Estatus</strong></td>
            <td nowrap="nowrap"><strong>Confirmaci&oacute;n</strong></td>
          </tr>
          <?php

			 $query = "SELECT pedido.*, empresa.nombre AS nombre_empresa, usuario_tienda.nombre AS nombre_vendedor, orden_compra.aprobacion,
            orden_compra.estatus as oc_estatus,orden_compra.monto_financiar,orden_compra.monto_aprobado,orden_compra.confirmado,
            (SELECT COUNT(detalle_pedido.pedido) FROM detalle_pedido where detalle_pedido.pedido=pedido.folio) as articulos,
            concat_ws(' ',cliente.nombre,cliente.apellido_paterno,cliente.apellido_materno) as cliente_nombre, cliente.numero_empleado
			 			FROM pedido
            inner join orden_compra on pedido.fdp_credito_nomina_folio=orden_compra.folio
            inner join plazo on plazo.clave = pedido.payment_terms
            left join cliente on pedido.cliente=cliente.clave
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
            <td ><?php echo $row['nombre_empresa'];?></td>
            <td ><div align="center">
            <?php echo $row['folio'];?>
            </div>
            </td>            
            <td ><?=$row['cliente_nombre'];?></td>
            <td ><div align="center">
            <?php echo $row['numero_empleado'];?>
            </div></td>
            <td align="right"><?php echo nocero(number_format($row['monto_financiar'],2));?></td>
            <td align="right"><?php echo $row['fdp_plazo'];?></td>
            <td align="right"><?php echo $row['fdp_periodo'];?></td>
            <td align="right"><?php echo $row['fdp_periodo_monto'];?></td>
            <td align="right"><?php echo $row['articulos'];?></td>
            <td align="right"><?php echo $row['aprobacion'];?></td>
            <td align="right"><?php echo nocero(number_format($row['monto_aprobado'],2));?></td>
            <td align="left" nowrap="nowrap" bgcolor="#FFFFFF">
			<?php  switch ($row['oc_estatus']) {
			
				case '0' : echo 'Pendiente Aprobacion'; break;
				case '1' : echo 'Aprobada'; break;
				case '2' : echo 'Rechazada'; break;
        case '3' : echo 'Utilizada'; break;
				case '4' : echo 'Cancelada'; break;
				case '9' : echo 'Vencida'; break;
			   }
			 ?>
			 </td>
            <td align="left" nowrap="nowrap" bgcolor="#FFFFFF">
      <?php  switch ($row['confirmado']) {
      
        case '0' : echo 'Sin confirmar'; break;
        case '1' : echo 'Confirmado'; break;
         }
       ?>
       </td>
		  </tr>
          <?php
                 } // WHILE
                 mysql_close();
              ?>

		  <tr class="texto" valign="top" bgcolor="#e5e5e2">
            <td colspan="13" align="left"><strong></strong></td>
            <td align="right"></td>
          </tr>          
          
          
        </table>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">

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