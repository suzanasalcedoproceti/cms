<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=9;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<link href="css/thickbox.css" rel="stylesheet" type="text/css" >
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/thickbox.js"></script>

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_ciudad.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_ciudad.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.ciudad.value = id;
    document.forma.action='borra_ciudad.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Ciudades'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='ciudad';

   if ($ord=='ciudad') $orden='ORDER BY estado.nombre, ciudad.nombre';
   
   include('../conexion.php');
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="button" class="boton_agregar" id="button" value="Agregar ciudad nueva" onClick="document.forma.action='abc_ciudad.php'; document.forma.submit();" /></td>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td>Estado:
              <select name="estado" class="campo" id="estado" onchange="document.forma.submit();">
                <option value="">Cualquier estado...</option>
                  <?  
					$resultadoEDO = mysql_query("SELECT * FROM estado ORDER BY clave",$conexion);
					while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
					  echo '<option value="'.$rowEDO['clave'].'"';
					  if ($rowEDO['clave']==$estado) echo 'selected';
				  	  echo '>'.$rowEDO['nombre'].'</option>';
				    }
				  ?>
              </select></td>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE 1 ";


					 if (!empty($estado))
					 	$condicion.= " AND estado='$estado'";

                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT 1 FROM ciudad $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de ciudades en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="ciudad" type="hidden" id="ciudad" />
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
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
          <tr class="texto" >
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td align="center" >&nbsp;</td>
            <td colspan="5" align="center" bgcolor="#F4F4F2"><strong>Productos RM</strong></td>
            <td align="center" >&nbsp;</td>
            <td colspan="5" align="center" bgcolor="#F4F4F2"><strong>Productos RS</strong></td>
            <td align="center" >&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
          </tr>
          <tr class="texto" >
            <td nowrap="nowrap" bgcolor="#F4F4F2"><b>Estado</b></td>
            <td nowrap="nowrap" bgcolor="#F4F4F2"><b>Ciudad</b></td>
            <td align="center" bgcolor="#CCCCCC">&nbsp;</td>
            <td align="center" bgcolor="#F4F4F2"><strong>Entrega<br />
            Domicilio</strong></td>
            <td align="center" bgcolor="#F4F4F2"><strong>Entrega <br />
            Ocurre</strong></td>
            <td align="center" bgcolor="#F4F4F2"><strong>SKU<br />
            Domicilio</strong></td>
            <td align="center" bgcolor="#F4F4F2"><strong>SKU<br />
            Ocurre</strong></td>
            <td align="center" bgcolor="#F4F4F2"><strong>Sucursal<br />
            Ocurre</strong></td>
            <td align="center" bgcolor="#CCCCCC">&nbsp;</td>
            <td align="center" bgcolor="#F4F4F2"><strong>Entrega<br />
              Domicilio</strong></td>
            <td align="center" bgcolor="#F4F4F2"><strong>Entrega <br />
              Ocurre</strong></td>
            <td align="center" bgcolor="#F4F4F2"><strong>SKU<br />
              Domicilio</strong></td>
            <td align="center" bgcolor="#F4F4F2"><strong>SKU<br />
              Ocurre</strong></td>
            <td align="center" bgcolor="#F4F4F2"><strong>Sucursal<br />
              Ocurre</strong></td>
            <td align="center" bgcolor="#CCCCCC">&nbsp;</td>
            <td bgcolor="#F4F4F2"><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
			 $query = "SELECT ciudad.*, estado.nombre AS nombre_estado
			 			FROM ciudad 
			 			LEFT JOIN estado ON ciudad.estado = estado.clave
						$condicion $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			 	$cve_ciudad = $row['clave'];
			    $res = mysql_query("SELECT 1 FROM direccion_envio WHERE ciudad=$cve_ciudad",$conexion);
			    $rel = mysql_num_rows($res);

				$res = mysql_query("SELECT nombre FROM sucursal_ocurre WHERE clave = {$row['sucursal_rm_ocu']}",$conexion);
				$rowSORM = mysql_fetch_array($res);
				
				$res = mysql_query("SELECT nombre FROM sucursal_ocurre WHERE clave = {$row['sucursal_rs_ocu']}",$conexion);
				$rowSORS = mysql_fetch_array($res);
				

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['nombre_estado']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['nombre']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#CCCCCC">&nbsp;</td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= ($row['entrega_rm_dom']) ? 'SI' : 'NO'; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= ($row['entrega_rm_ocu']) ? 'SI' : 'NO'; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= $row['sku_rm_dom']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= $row['sku_rm_ocu']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= $rowSORM['nombre']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#CCCCCC">&nbsp;</td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= ($row['entrega_rs_dom']) ? 'SI' : 'NO'; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= ($row['entrega_rs_ocu']) ? 'SI' : 'NO'; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= $row['sku_rs_dom']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= $row['sku_rs_ocu']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= $rowSORS['nombre']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#CCCCCC">&nbsp;</td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><a href="abc_ciudad.php?ciudad=<?= $row['clave']; ?>"><img src="images/editar.png" alt="Editar Ciudad" width="14" height="16" border="0" align="absmiddle" /></a>
       	  		<? if ($rel<=0 AND op_aut($modulo)) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar la ciudad?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar Ciudad" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>            </td>
		  </tr>
          <?
                 } // WHILE
                 mysql_close();
              ?>
        </table>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
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
					 echo '<input type="text" name="pagina2" value="'.$numpag.'" size="2" onchange="javascript:ir(document.forma,this.value);" style="text-align:center"/>';
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
      </form>    
    </div>
</div>
</body>
</html>
