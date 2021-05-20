<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
	$modulo=19;
    include('../conexion.php');
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
   if ($_POST['accion'] == 'cancelar') {
   	 $clave = mysql_real_escape_string($_POST['clave']);
	 $resultadoC = mysql_query("UPDATE puntos_flex SET estatus = '9' WHERE folio = '$clave' AND estatus = '0'");
   }
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='fecha';

   $fecha = $_POST['fecha'];
   $empresa = $_POST['empresa'];
   $texto = $_POST['texto'];
   $tipo = $_POST['tipo'];
   $estatus = $_POST['estatus'];
   if (!isset($estatus)) $estatus = '1';
   if (!$fecha) $fecha = date("d/m/Y")." - ".date("d/m/Y");
   if ($ord=='fecha') $orden='ORDER BY fecha';
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
<link href="js/src/shadowbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/src/shadowbox.js"></script>
<link href="css/jquery.datepick.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.datepick.js" type="text/javascript" language="javascript1.2"></script>
<script src="js/jquery.datepick-es.js" type="text/javascript" language="javascript1.2"></script>

<script type="text/javascript">
$(document).ready(function() {
  $('#fecha').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/calendario.gif', rangeSelect: true, numberOfMonths: 2, minDate: '01/01/2011', maxDate: '+10y' } );
// $('#fecha').datepick({ showTrigger: '<img src="images/calendario.png" alt="" class="cal"></img>', changeMonth: false, changeYear: false, monthsToShow: 2, dateFormat: 'dd/mm/yyyy', minDate: 0, onSelect: customRange });

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
  	document.forma.target = '_self';
    form.numpag.value = Pag;
    form.action='lista_puntos_flex.php';
    form.submit();
  }
  function ordena(orden) {
  	document.forma.target = '_self';
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_puntos_flex.php';
    document.forma.submit();
  }
  function cancela(id) {
  	document.forma.target = '_self';
    document.forma.clave.value = id;
	document.forma.accion.value = 'cancelar';
    document.forma.action='lista_puntos_flex.php';
    document.forma.submit();
  }
  function reporte() {
  	document.forma.target = '_blank';
  	document.forma.action = 'lista_puntos_flex_excel.php';
    document.forma.submit();
  	document.forma.target = '_self';
  }

</script>
</head>

<body>
<div id="container">
	<? $tit='Reporte de Puntos Flex / PEP'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
       <input type="hidden" name="clave" id="clave" value="" />
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><a onclick="reporte();"  class="texto"><img src="images/icon_excel.gif" alt="" width="25" height="16" align="absmiddle" />Exportar a Excel</a></td>
            <td align="right"><table width="500" border="0" align="left" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td width="156" valign="top"><div align="right">Fechas:</div></td>
                <td width="594"><div align="left">
                  <input name="fecha" type="text" class="fLeft fechas" id="fecha" value="<?=$fecha;?>" readonly="readonly" />
                </div></td>
              </tr>
              <tr>
                <td><div align="right">Tipo:</div></td>
                <td><select name="tipo" class="campo" id="tipo">
                  <option value="" <? if ($tipo=='') echo 'selected';?>>Cualquiera...</option>
                  <option value="F" <? if ($tipo=='F') echo 'selected';?>>Puntos Flex</option>
                  <option value="P" <? if ($tipo=='P') echo 'selected';?>>Puntos PEP</option>
                </select></td>
              </tr>
              <tr>
                <td><div align="right">Estatus:</div></td>
                <td><div align="left">
                  <select name="estatus" class="campo" id="estatus">
                    <option value="x" <? if ($estatus=='x') echo 'selected';?>>Cualquiera...</option>
                    <option value="0" <? if ($estatus=='0') echo 'selected';?>>Inactivos</option>
                    <option value="1" <? if ($estatus=='1') echo 'selected';?>>Activos</option>
                    <option value="9" <? if ($estatus=='9') echo 'selected';?>>Cancelados</option>
                  </select>
                </div></td>
              </tr>

              <tr>
                <td>&nbsp;</td>
                <td><div align="left">
                  <input name="Button" type="button" class="boton" onclick="ir(document.forma,1);" value="Buscar" />
                </div></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE 1=1 ";

					 if (!empty($fecha)) {
						$fecha_desde = convierte_fecha(substr($fecha,0,10));
						$fecha_hasta = convierte_fecha(substr($fecha,13,10));
						$condicion .= " AND puntos_flex.fecha BETWEEN '$fecha_desde' AND '$fecha_hasta' ";
					 }
					 if ($estatus=='1') $condicion .= " AND estatus = 1 ";
					 if ($estatus=='0') $condicion .= " AND estatus = 0 ";
					 if ($estatus=='9') $condicion .= " AND estatus = 9 ";
					
					 if ($tipo) $condicion .= " AND tipo = '$tipo' ";

					 
                    // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT * FROM puntos_flex $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de registros en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="cliente" type="hidden" id="cliente">
            	<input name="accion" type="hidden" id="accion">
                <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
                <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
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
                     echo "Página ".$numpag." de ".$totpags;
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
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" bgcolor="<? if($ord=='fecha') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('fecha');" class="texto">Folio  <img src="images/orden.png" alt="" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td nowrap="nowrap"><div align="center"><strong>Tipo</strong></div></td>
            <td nowrap="nowrap"><div align="center"><strong>Cantidad</strong></div></td>
            <td><div align="center"><b>User ID</b></div></td>
            <td align="center"><strong>No. Empleado</strong></td>
            <td align="center"><strong>Fecha Solicitud</strong></td>
            <td align="center"><strong>Estatus</strong></td>
            <td align="center"><strong>Canal</strong></td>
            <td align="center"><strong>Usuario Canje</strong></td>
            <td align="center"><strong>Fecha de Canje</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
			 $query = "SELECT * FROM puntos_flex
						       $condicion $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)) { 
			 	
				$usuario_canje = '';
				$vendedor = $row['vendedor']+0;
				if ($row['canal']=='POS') {
					$resultadoV = mysql_query("SELECT nombre FROM usuario_tienda WHERE clave = $vendedor");
					$rowV = mysql_fetch_array($resultadoV);
					$usuario_canje = $rowV['nombre'];
				} 
				if ($row['canal']=='TW') {
					$resultadoV = mysql_query("SELECT CONCAT(nombre,' ',apellido_paterno) AS nombre FROM cliente WHERE clave = $vendedor");
					$rowV = mysql_fetch_array($resultadoV);
					$usuario_canje = $rowV['nombre'];
				} 
				

          ?>
          <tr class="texto">
            <td  bgcolor="#FFFFFF"><?= $row['folio']; ?></td>
            <td  align="center"  bgcolor="#FFFFFF">
              <? 
			    if ($row['tipo']=='F') echo 'Flex';
			    if ($row['tipo']=='P') echo 'PEP';
			  ?>            </td>
            <td  align="center"  bgcolor="#FFFFFF"><?= $row['monto'];?></td>
            <td  bgcolor="#FFFFFF"><?= $row['usuario']; ?></td>
            <td  bgcolor="#FFFFFF"><?= $row['empleado']; ?></td>
            <td align="center"  bgcolor="#FFFFFF"><?= fecha($row['fecha']); ?></td>
            <td align="center"  bgcolor="#FFFFFF"><? switch ($row['estatus']) { 
						case '0' : echo 'Inactivo'; break;
						case '1' : echo 'Activo'; break;
						case '9' : echo 'Cancelado'; break;
					 } ?>            </td>
            <td align="center"  bgcolor="#FFFFFF"><? echo $row['canal'];  ?></td>
            <td align="center"  bgcolor="#FFFFFF"><?=$usuario_canje;?></td>
            <td align="center"  bgcolor="#FFFFFF"><? echo fecha($row['fecha_aplicacion'],'novacio'); ?></td>
            <td align="center"  nowrap="nowrap" bgcolor="#FFFFFF"><? if ($row['estatus']==0) { ?><a onclick="return confirm('¿Estás seguro que deseas cancelar el folio?')" href="javascript:cancela('<?= $row['folio']; ?>');"><img src="images/borrar.png" alt="Cancelar folio" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absbottom" /><? } ?>   			</td>
          </tr>
          <?
                 } // WHILE
              ?>
        </table>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="right">&nbsp;</td>
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
      </form>    
    </div>
</div>
</body>
</html>
