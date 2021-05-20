<?
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

	$empresa = $_POST['empresa']+0;
	$cliente = $_POST['cliente']+0;
	$estatus = $_POST['estatus'];
	$origen = $_POST['origen'];
	$folio = $_POST['folio']+0;
	$forma_pago = $_POST['forma_pago'];
	$puntos = $_POST['puntos'];
	$puntos_flex = $_POST['puntos_flex'];
	$puntos_pep = $_POST['puntos_pep'];
	if (!isset($estatus)) $estatus = 'x';
	if (!isset($origen)) $origen = 'x';
	$fecha = $_POST['fecha'];
	if (!$fecha) $fecha = "01/".date("m/Y")." - ".date("d/m/Y");
	$buscar = $_POST['buscar'];
    $ver = $_POST['ver'];
    $numpag = $_POST['numpag'];
    $ord = $_POST['ord'];
    if (empty($ver)) $ver='15';
    if (empty($numpag)) $numpag='1';
    if (empty($ord)) $ord='folio';

    if     ($ord=='folio') $orden='ORDER BY folio DESC';
    elseif ($ord=='empresa') $orden='ORDER BY nombre_empresa, folio';
   
	
// include("_checa_vars.php");


  // obtener el total de registros que coinciden...
  // y establecer algunas variables
    if ($buscar) {	   

	 // construir la condición de búsqueda
	 $condicion = "WHERE 1 ";

	 if (!empty($fecha)) {
		$fecha_desde = convierte_fecha(substr($fecha,0,10));
		$fecha_hasta = convierte_fecha(substr($fecha,13,10));
		$condicion .= " AND pedido.fecha BETWEEN '$fecha_desde' AND '$fecha_hasta' ";
	 }

	 if ($cliente>0)
		$condicion .= " AND pedido.cliente=$cliente";

	 if ($empresa>0)
		$condicion .= " AND pedido.empresa=$empresa";

	 if ($estatus != 'x')
		$condicion .= " AND pedido.estatus = '$estatus' ";

	 if ($origen == 'web') 
		$condicion .= " AND pedido.origen = 'web' AND mobile = 0 ";
	 
	 if ($origen == 'pos') 
		$condicion .= " AND pedido.origen = 'pos' ";
	 
	 if ($origen == 'mobile') 
		$condicion .= " AND pedido.origen = 'web' AND mobile = 1 ";

	 if ($origen == 'mas') 
		$condicion .= " AND pedido.empresa = 404";

	 if ($origen == 'proy') 
		$condicion .= " AND pedido.tipo_venta = 'PR'";

		
	 if ($forma_pago=='tdc') $condicion .= " AND pedido.fdp_tdc > 0 ";
	 if ($forma_pago=='tdd') $condicion .= " AND pedido.fdp_tdd > 0  ";
	 if ($forma_pago=='cheque') $condicion .= " AND pedido.fdp_cheque > 0  ";
	 if ($forma_pago=='cep') $condicion .= " AND pedido.fdp_cep > 0  ";
	 if ($forma_pago=='credito') $condicion .= " AND pedido.fdp_credito > 0  ";
	 if ($forma_pago=='dep') $condicion .= " AND pedido.fdp_deposito > 0  ";

	 if ($puntos=='1') $condicion .= " AND pedido.fdp_puntos > 0 ";
	 if ($puntos_flex=='1') $condicion .= " AND pedido.fdp_puntos_flex > 0 ";
	 if ($puntos_pep=='1') $condicion .= " AND pedido.fdp_puntos_pep > 0 ";

	 if ($folio)  
		$condicion .= " AND CONCAT(REPLACE(pedido.fecha,'-',''),pedido.folio,'_L') LIKE '%$folio%' ";
								
	// substr(str_replace('-','',$row['fecha']).$row['folio'].'_L',2,50);						

	   $resultadotot= mysql_query("SELECT * FROM pedido $condicion",$conexion);
	   $totres = mysql_num_rows ($resultadotot);
	   $totpags = ceil($totres/$ver);
	   if ($totres==0)
		  $numpag = 0;		
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<link href="js/src/shadowbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/src/shadowbox.js"></script>
<link href="css/jquery.datepick.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.datepick.js" type="text/javascript" language="javascript1.2"></script>
<script src="js/jquery.datepick-es.js" type="text/javascript" language="javascript1.2"></script>
<script type="text/javascript">
	Shadowbox.init({
		language: 'es',
		players:  ['img', 'html', 'iframe', 'qt', 'wmp', 'swf', 'flv']
	});

	$(document).ready(function() {

		$('#tabla_datos table td:nth-child(16)').hide(); 	
	}
	
</script>
<script type="text/javascript">
$(document).ready(function() {
  $('#fecha').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/calendario.gif', rangeSelect: true, numberOfMonths: 2, minDate: '01/01/2011', maxDate: '+1m' } );
});
</script>


<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_pedidos.php';
	form.buscar.value = 1;
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
	document.forma.buscar.value = 1;
    document.forma.action='lista_pedidos.php';
    document.forma.submit();
  }
  function ver_detalle(folio) {
    document.forma.pedido.value = folio;
    document.forma.action='detalle_pedido.php';
    document.forma.submit();
  }
  function registra_credito(folio) {
    document.forma.pedido.value = folio;
    document.forma.action='registra_pago_tdc.php';
    document.forma.submit();
  }
  function registra_debito(folio) {
    document.forma.pedido.value = folio;
    document.forma.action='registra_pago_tdd.php';
    document.forma.submit();
  }
  function registra_cep(folio) {
    document.forma.pedido.value = folio;
    document.forma.action='registra_pago_cep.php';
    document.forma.submit();
  }
  function exportar() {
  	document.forma.target = '_blank';
    document.forma.action='lista_pedidos_xls.php';
    document.forma.submit();
	document.forma.target = '_self';
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Pedidos'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td colspan="2"><table width="550" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td align="right">Fechas:</td>
                <td><input name="fecha" type="text" class="fLeft fechas" id="fecha" value="<?php echo $fecha;?>" readonly="readonly" /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><div align="right">Empresa:</div></td>
                <td><select name="empresa" class="campo" id="empresa" onchange="ir(document.forma,1);">
                  <option value="">Cualquier empresa...</option>
                  <?  $resEMP= mysql_query("SELECT * FROM empresa ORDER BY nombre",$conexion);
                      while ($rowEMP = mysql_fetch_array($resEMP)) {
					  	 echo '<option value="'.$rowEMP['clave'].'"';
						 if ($empresa==$rowEMP['clave']) echo 'selected';
						 echo '>'.$rowEMP['nombre'].'</option>';
					  } ?>
                </select></td>
                <td width="87"><div align="right">Cliente:</div></td>
                <td width="221"><select name="cliente" class="campo" id="cliente">
                    <option value="">Cualquier cliente...</option>
                    <?  $resEMP= mysql_query("SELECT * FROM cliente WHERE empresa = $empresa AND nombre != '' ORDER BY nombre",$conexion);
                      while ($rowEMP = mysql_fetch_array($resEMP)) {
					  	 echo '<option value="'.$rowEMP['clave'].'"';
						 if ($cliente==$rowEMP['clave']) echo 'selected';
						 echo '>'.$rowEMP['nombre']." ".$rowEMP['apellido_paterno']." ".$rowEMP['apellido_materno'].'</option>';
					  } ?>
                </select></td>
              </tr>
              <tr>
                <td><div align="right">Origen:</div></td>
                <td><select name="origen" class="campo" id="origen">
                    <option value="x" selected="selected" <? if ($origen=='x') echo 'selected';?>>Cualquiera</option>
                    <option value="web" <? if ($origen=='web') echo 'selected';?>>Web</option>
                    <option value="mobile" <? if ($origen=='mobile') echo 'selected';?>>Mobile</option>
                    <option value="pos" <? if ($origen=='pos') echo 'selected';?>>POS</option>
                    <option value="mas" <? if ($origen=='mas') echo 'selected';?>>MAS</option>
                    <option value="proy" <? if ($origen=='proy') echo 'selected';?>>Proyectos</option>
                   </select>                </td>
                <td><div align="right">Estatus:</div></td>
                <td><select name="estatus" class="campo" id="estatus">
                    <option value="x" <? if ($estatus=='x') echo 'selected';?>>Cualquiera</option>
                    <option value="0" <? if ($estatus=='0') echo 'selected';?>>Pendiente</option>
                    <option value="1" <? if ($estatus=='1') echo 'selected';?>>Pagado</option>
                    <option value="2" <? if ($estatus=='2') echo 'selected';?>>Rechazado</option>
                    <option value="3" <? if ($estatus=='3') echo 'selected';?>>Revisión TDD</option>
                    <option value="4" <? if ($estatus=='4') echo 'selected';?>>Revisión CEP</option>
                    <option value="9" <? if ($estatus=='9') echo 'selected';?>>Cancelado</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td width="58"><div align="right">Folio:</div></td>
                <td width="168"><label>
                  <input name="folio" type="text" class="campo" id="folio" size="15" maxlength="15" value="<?=$folio;?>"/>
                </label></td>
                <td nowrap="nowrap"><div align="right">Forma de pago:</div></td>
                <td nowrap="nowrap">
                  <select name="forma_pago" class="campo" id="forma_pago">
                    <option value="" <? if ($forma_pago=='') echo 'selected';?>>Cualquiera</option>
                    <option value="tdc" <? if ($forma_pago=='tdc') echo 'selected';?>>T.D.C.</option>
                    <option value="tdd" <? if ($forma_pago=='tdd') echo 'selected';?>>Débito Clabe</option>
                    <option value="cheque" <? if ($forma_pago=='cheque') echo 'selected';?>>Cheque</option>
                    <option value="credito" <? if ($forma_pago=='credito') echo 'selected';?>>Crédito (Proyectos)</option>
                    <option value="dep" <? if ($forma_pago=='dep') echo 'selected';?>>Depósito</option>
                    <option value="cep" <? if ($forma_pago=='cep') echo 'selected';?>>Pago Directo</option>
                  </select>
                  <label><input name="puntos" type="checkbox" id="puntos" value="1" <? if ($puntos) echo 'checked';?>/>Puntos</label>
                  <label><input name="puntos_flex" type="checkbox" id="puntos_flex" value="1" <? if ($puntos_flex) echo 'checked';?>/>Puntos Flex</label>
                  <label><input name="puntos_pep" type="checkbox" id="puntos_pep" value="1" <? if ($puntos_pep) echo 'checked';?>/>Puntos PEP</label>
                </td>
              </tr> 
              <tr>
                <td>&nbsp;</td>
                <td><input name="Submit" type="button" class="boton" onclick="javascript:ir(document.forma,1)" value="Buscar" />
                	<input type="hidden" name="buscar" value="0" />
                    <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
	                <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
                    <input name="pedido" type="hidden" id="pedido" />

                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
          
          
        </table>
        <? if ($buscar) { ?>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
        <tr>
            <td bgcolor="#BBBBBB"><? echo 'Total de pedidos en la lista: <b>'.$totres.'</b>'; ?></td>
            <td align="right" bgcolor="#BBBBBB">
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
              ?>            </td>
          </tr>
        </table>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3" id="tabla_datos">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="11" align="center" bgcolor="#f4f4f2"><strong>Formas de pago</strong></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" bgcolor="<? if($ord=='folio') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('folio');" class="texto">Folio   <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td nowrap="nowrap"><b>Fecha</b></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='empresa') echo '#DDDDDD'; ?>"><div align="center"><strong>Origen</strong></div></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='empresa') echo '#DDDDDD'; ?>"><div align="center"><strong>Tienda</strong></div></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='empresa') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('empresa');" class="texto">Empresa  <img src="images/orden.png" alt="" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td><strong>Cliente</strong></td>
            <td><strong>Entrega</strong></td>
            <td><div align="center"><strong>Productos</strong></div></td>
            <td align="center">EFVO</td>
            <td align="center">TDC</td>
            <td align="center">TDD</td>
            <td align="center">CHE</td>
            <td align="center">CEP</td>
            <td align="center">ODC</td>
            <td align="center"><label title="Crédito Empresas Proyectos">CRE</label></td>
            <td align="center">DEP</td>
            <td align="center">PTS</td>
            <td align="center">FLEX</td>
            <td align="center">PEP</td>
            <td><div align="center"><strong>Total</strong></div></td>
            <td><div align="center"><strong>Estatus</strong></div></td>
            <!--td><div align="center"><strong>Dashboard</strong></div></td-->
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?


			 $query = "SELECT pedido.*, empresa.nombre AS nombre_empresa, CONCAT(cliente.nombre,' ',cliente.apellido_paterno,' ',cliente.apellido_materno) AS nombre_cliente , tienda.nombre AS nombre_tienda, usuario_tienda.nombre AS usuario_cancelacion_nombre
			 			FROM pedido 
			 			LEFT JOIN empresa ON pedido.empresa = empresa.clave
			 			LEFT JOIN cliente ON pedido.cliente = cliente.clave
						LEFT JOIN tienda  ON pedido.tienda = tienda.clave
            LEFT JOIN usuario_tienda ON usuario_tienda.clave = pedido.usuario_cancelacion
						       $condicion $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			    if ($row['pagado_cms']) $pagado_cms = '*'; else $pagado_cms = '';

				$pedido = $row['folio'];		
                $query = "SELECT detalle_pedido.modelo FROM detalle_pedido 
							 WHERE pedido = $pedido ORDER BY partida";
			    $resultadoDP = mysql_query($query,$conexion);
				$detalle_pedido = '';
				while ($rowDP = mysql_fetch_array($resultadoDP))
				 	$detalle_pedido .= $rowDP['modelo'].'<br>';
					
				// obtener datos de dashboard si los hay
			
            	$resultadoDASH = mysql_query("SELECT semaforo, avance_pedido FROM dashboard WHERE folio_pedido = $pedido");
				$rowDASH = mysql_fetch_array($resultadoDASH);

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td valign="top" bgcolor="#FFFFFF"><?= substr(str_replace('-','',$row['fecha']).$row['folio'].'_L',2,50); ?></td>
            <td valign="top" bgcolor="#FFFFFF"><?= fecha($row['fecha']); ?></td>
            <td valign="top" bgcolor="#FFFFFF"><div align="center">
              <? echo strtoupper($row['origen']);
			  	 if ($row['mobile']==1) echo ' iphone';
			  	 if ($row['mobile']==2) echo ' bberry';
			   ?>
            </div></td>
            <td valign="top" bgcolor="#FFFFFF">
			  <? 
			     if ($row['nombre_tienda']) 
				 	echo $row['nombre_tienda']; 
				  else {
					  switch ($row['tipo_venta']) {
						  case 'PR' : echo 'Sitio Web Proyectos'; break;
						  case 'MAS' : echo 'Sitio Web MAS'; break;
						  default : echo 'Tienda en Línea'; break;
					  }
				  } ?>
            </td>
            <td valign="top" bgcolor="#FFFFFF"><?= $row['nombre_empresa']; ?></td>
            <td valign="top" bgcolor="#FFFFFF"><?= $row['numero_empleado']." - ".$row['nombre_cliente']; ?></td>
            <td valign="top" bgcolor="#FFFFFF"><?=$row['envio_ciudad_nombre'].", ".substr($row['envio_estado'],0,3);?></td>
            <td nowrap="nowrap" bgcolor="#FFFFFF"><?=$detalle_pedido;?></td>
            <td align="right" bgcolor="#FFFFFF"><?=nocero($row['fdp_efectivo']);?></td>
            <td align="right" valign="top" bgcolor="#FFFFFF"><? echo nocero($row['fdp_tdc']); if ($row['pago_msi']>0) echo '<br> '.$row['pago_msi'].' msi'; ?></td>
            <td align="right" valign="top" bgcolor="#FFFFFF"><?=nocero($row['fdp_tdd']);?></td>
            <td align="right" valign="top" bgcolor="#FFFFFF"><?=nocero($row['fdp_cheque']);?></td>
            <td align="right" valign="top" bgcolor="#FFFFFF"><?=nocero($row['fdp_cep']);?></td>
            <td align="right" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_credito_nomina']);?></td>
            <td align="right" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_credito']);?></td>
            <td align="right" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_deposito']);?></td>
            <td align="right" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_puntos']);?></td>
            <td align="right" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_puntos_flex']);?></td>
            <td align="right" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><?=nocero($row['fdp_puntos_pep']);?></td>
            <td align="right" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><?= number_format($row['total'],2); ?></td>
            <td align="center" valign="top" nowrap="nowrap" bgcolor="#FFFFFF">
			<? switch ($row['estatus']) {
				case '0' : echo 'Pendiente'; break;
				case '1' : echo 'Pagado'.$pagado_cms; break;
				case '2' : echo 'Rechazado'; break;
				case '3' : echo 'Revisión TDD'; break;
				case '4' : echo 'Revisión CEP'; break;
				case '9' : echo 'Cancelado'; break;
			   }
			 ?>
           <? // Pago pendiente de autorizar en TDD (pasó el pago, pero falta conciliación
          if ($row['estatus']=='9') { ?>
            <div style="text-align: left;font-size: .9em;">
            <b>Usuario:</b> <?php echo $row['usuario_cancelacion_nombre']?><br>
            <b>Motivo:</b> <?php echo $row['motivo_cancelacion']?><br>
            <b>Fecha:</b> <?php echo date_format(date_create($row['fecha_cancelacion']),'Y-m-d')?><br>
            </div>
           <? } ?>

             <? // Pago pendiente de autorizar en TDD (pasó el pago, pero falta conciliación
			    if ($row['estatus']=='0' && $row['fdp_tdc']>0 && op(15)) { ?>
	             <a href="javascript:registra_credito(<?= $row['folio']; ?>);"><img src="images/tick.png" alt="Registrar Pago" title="Registrar Pago" width="16" height="16" /></a>
    	     <? } ?>

             <? // Pago pendiente de autorizar en TDD (pasó el pago, pero falta conciliación
			    if ($row['estatus']=='3' && $row['fdp_tdd']>0 && op(15)) { ?>
	             <a href="javascript:registra_debito(<?= $row['folio']; ?>);"><img src="images/tick.png" alt="Registrar Pago" title="Registrar Pago" width="16" height="16" /></a>
    	     <? } ?>
             <? // Pago pendiente de autorizar en CEP (7 eleven)
			    if ($row['estatus']=='4' && $row['fdp_cep']>0 && op(15)) { ?>
	             <a href="javascript:registra_cep(<?= $row['folio']; ?>);"><img src="images/tick.png" alt="Registrar Pago" title="Registrar Pago" width="16" height="16" /></a>
    	     <? } ?>            </td>
            <!--td align="center" valign="top" nowrap="nowrap" 
				<? switch ($rowDASH['semaforo']) {
                     case 'amarillo' : echo ' bgcolor="#FFFF99" '; break;
                     case 'rojo' 	 : echo ' bgcolor="#FF0033" '; break;
                     case 'verde'	 : echo ' bgcolor="#66CC33" '; break;
                     default		 : echo ' bgcolor="#FFFFFF" '; break;
                   }
				     ?>>
            <? 
					if ($rowDASH['avance_pedido']==1) echo '100%';
					elseif ($rowDASH['avance_pedido']==0) echo '0%'; 
					else  echo number_format($rowDASH['avance_pedido']*100,2).'%';?>            
             </td-->
            <td align="center" valign="top" nowrap="nowrap" bgcolor="#FFFFFF"><a href="javascript:ver_detalle(<?= $row['folio']; ?>);"><img src="images/foto.png" alt="Ver detalle de pedido"  border="0" align="absmiddle" /></a>
            </td>
		  </tr>
          <?
                 } // WHILE
                 mysql_close();
              ?>
        </table>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="right"><div align="left">* Estatus <strong>Pagado</strong> establecido en CMS</div>
            <div align="center">
              <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Exportar a XLS" />
            </div>
            </td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB" align="right"><?

                     // poner flechitas anterior, primero, &uacute;ltimo, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="P&aacute;gina anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "P&aacute;gina ";
					 echo '<input type="text" name="paginab" value="'.$numpag.'" size="2" onchange="javascript:ir(document.forma,this.value);" style="text-align:center"/>';
					 echo " de ".$totpags;
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
        <? } // buscar ?>
      </form>    
    </div>
</div>
</body>
</html>
