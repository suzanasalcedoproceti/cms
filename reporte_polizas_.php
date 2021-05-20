<?php
if (!include('ctrl_acceso.php')) return;
include('funciones.php');
include('lib.php');
$modulo=30;
if (!op($modulo))  {
	$aviso = 'Usuario sin permiso para acceder a este módulo';
	$aviso_link = 'principal.php';
	include('mensaje_sistema.php');
	return;
}


include('../conexion.php');

$fecha = $_POST['fecha'];
$folio = $_POST['folio'];
$folio_gar = $_POST['folio_gar'];
$folio_sap = $_POST['folio_sap'];
$tipo = $_POST['tipo'];
$empresa = $_POST['empresa'];

$ver = $_POST['ver'];
$numpag = $_POST['numpag'];
$ord = $_POST['ord'];
$buscar = $_POST['buscar'];
if (empty($ver)) $ver='10';
if (empty($numpag)) $numpag='1';
if (empty($ord)) $ord='folio';
if     ($ord=='folio') $orden='ORDER BY folio DESC';
if (!$fecha) $fecha = "01/".date("m/Y")." - ".date("d/m/Y");

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
    form.action='reporte_polizas.php';
    form.buscar.value=1;
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
	document.forma.target = '_self';
    document.forma.action='reporte_polizas.php';
    form.buscar.value=1;
    document.forma.submit();
  }
  function ver_detalle(folio) {
    document.forma.pedido.value = folio;
	document.forma.target = '_self';
    document.forma.action='detalle_pedido.php';
    document.forma.submit();
  }
  function exportar(tipo) {
  	document.forma.target = '_blank';
    document.forma.action='reporte_polizas_'+tipo+'.php';
    document.forma.buscar.value=1;
    document.forma.submit();
	document.forma.target = '_self';
  }
</script>
</head>

<body>
<div id="container">
	<?php $tit='Reporte: Pólizas de Garantía'; include('top.php'); ?>
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
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><div align="right">Folio POS:</div></td>
                <td>
                  <input name="folio" type="text" class="campo" id="folio" size="15" maxlength="15" value="<?php echo $folio;?>"/></td>
                <td width="87">&nbsp;</td>
                <td width="221">&nbsp;</td>
              </tr>
              <tr>
                <td nowrap=""><div align="right">Folio SAP:</div></td>
                <td><input name="folio_sap" type="text" class="campo" id="folio_sap" size="15" maxlength="15" value="<?php echo $folio_sap?>"/></td>
                <td><div align="right">Folio Garant&iacute;a:</div></td>
                <td><input name="folio_gar" type="text" class="campo" id="folio_gar" size="15" maxlength="15" value="<?php echo $folio_gar;?>"/></td>
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
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" >
            <tr class="texto">
            <td nowrap="nowrap" colspan="12">
				<?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                      $condicion = "WHERE (    (dashboard.categoria = 15 OR (dashboard.categoria = 21 AND dashboard.subcategoria = 130)) 
                                            OR (dashboard.categoria = 19 AND (dashboard.subcategoria = 98 OR dashboard.subcategoria = 99))
                                            OR (dashboard.categoria = 25 AND (dashboard.subcategoria = 36 OR dashboard.subcategoria = 101))
                                           
                                         )";

					if (!empty($fecha)) {
						$fecha_desde = convierte_fecha(substr($fecha,0,10));
						$fecha_hasta = convierte_fecha(substr($fecha,13,10));
						$condicion .= " AND dashboard.fecha_pedido BETWEEN '$fecha_desde' AND '$fecha_hasta' ";
					 }

					 if ($folio) 
					 	$condicion .= " AND dashboard.folio_pos = '$folio' ";

					 if ($folio_sap) 
					 	$condicion .= " AND dashboard.folio_sap = '$folio_sap' ";	

					 if ($folio_gar) 
					 	$condicion .= " AND dashboard.folio_garantia = '$folio_gar' ";

					//  $condicion .= " AND pedido.estatus = 1 ";
					 $query = "SELECT * FROM dashboard $condicion";
                     $resultadotot = mysql_query($query,$conexion);
                     $totres = mysql_num_rows($resultadotot);
                     $totpags = ceil($totres/$ver);
                     if ($totres==0)
                         $numpag = 0;
						  
					 echo 'Total de Pólizas en la lista: <b>'.$totres.'</b>';
			 //  if ($_SESSION['usr_valido']==14)	 echo '<div style="color:#CCC">'.$query.'</div>';  // debug user garzard
			  ?>            
            </td>
            <td nowrap="nowrap" colspan="27" align="right">
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
        </table>
        <div style="overflow:scroll">
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" >

      
		 <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><b>Pedido POS</b></td>
            <td nowrap="nowrap"><strong>Pedido SAP</strong></td>
            <td nowrap="nowrap"><strong>Folio Garant&iacute;a</strong></td>
            <td nowrap="nowrap"><strong>Fecha Pedido</strong></td>
            <td nowrap="nowrap"><strong>Inicio Cobertura</strong></td>
            <td nowrap="nowrap"><strong>Fin  Cobertura</strong></td>
            <td align="center"><strong>Modelo Relacionado</strong></td>
            <td align="center"><strong>N&uacute;mero Serie</strong></td>            
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
            <td><div align="center"><strong>E-mail</strong></div></td>
            <td align="center"><strong>Fecha Compra Prod</strong></td>
            <td align="center"><strong>Autorizaci&oacute;n TC</strong></td>
            <td align="center"><strong>Autorizaci&oacute;n TD</strong></td>
            <td align="center"><strong>Autorizaci&oacute;n TE</strong></td>
            <td align="center"><strong>Estatus</strong></td>
          </tr>
          <?php

			 $query = "SELECT dashboard.*, pedido.nombre, 
			                  CONCAT(pedido.envio_calle,' ',pedido.envio_exterior,' ',pedido.envio_interior) AS direccion, pedido.envio_colonia, envio_ciudad_nombre, 
							         envio_estado, envio_cp, envio_telefono_casa,
			                  CONCAT(pedido.pers_calle,' ',pedido.pers_exterior) AS direccion_pers, pedido.pers_colonia, pers_ciudad, pers_estado, pers_cp, pers_telefono, pedido.fdp_tdc_folio, pedido.fdp_tdd_folio, pedido.fdp_te_folio
			 			FROM dashboard
			 			LEFT JOIN pedido ON dashboard.folio_pedido = pedido.folio
						$condicion $orden LIMIT $regini,$ver";
			 $gran_total = 0;
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
          <tr class="texto"  bgcolor="#FFFFFF" align="center">
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
            <td align="left"><?php echo $rowCTE['apellido_paterno'].' '.$rowCTE['apellido_materno'];?></td>
            <td align="left"><?php echo $direccion;?></td>
            <td align="left"><?php echo $colonia;?></td>
            <td align="left"><?php echo $ciudad;?></td>
            <td><?php echo $nombre_estado;?></td>
            <td align="left"><?php echo $cp;?></td>
            <td>México</td>
            <td><?php echo $telefono;?></td>
            <td><?php echo $celular;?></td>
            <td align="left"><?php echo $rowCTE['email'];?></td>
            <td><?php echo ($rowGAR['fecha_compra_producto'] != '0000-00-00') ? fecha_dash($rowGAR['fecha_compra_producto'],'novacio') : '';?></td>
            <td align="right"><?php echo $row['fdp_tdc_folio'];?></td>
            <td align="right"><?php echo $row['fdp_tdd_folio'];?></td>
            <td align="right"><?php echo $row['fdp_te_folio'];?></td>
            <td align="right" title="<?php echo $rowGAR['estatus_log']?>"><?php echo $rowGAR['estatus'];?></td>
		  </tr>
          <?php
                 } // WHILE
                 mysql_close();
              ?>
         
          
          
        </table>
        </div>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="right"><div align="center">
              <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar('xls');" value="Exportar a XLS" />
              <input name="exp_csv" type="button" class="boton" onclick="javascript:exportar('csv');" value="Exportar a CSV" />
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