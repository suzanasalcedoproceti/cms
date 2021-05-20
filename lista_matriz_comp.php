<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=23;
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
<link href="js/src/shadowbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/src/shadowbox.js"></script>
<script type="text/javascript">
	Shadowbox.init({
		language: 'es',
		players:  ['img', 'html', 'iframe', 'qt', 'wmp', 'swf', 'flv']
	});
</script>

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_matriz_comp.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_matriz_comp.php';
    document.forma.submit();
  }
  function borra(clave) {
    document.forma.producto_wp.value = clave;
    document.forma.accion.value = 'borra_sku';
    document.forma.action='lista_matriz_comp.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Matriz de Comparativo de Productos'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $accion = $_POST['accion'];
   
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   if ($ord=='nombre') $orden='ORDER BY nombre';
   
   include('../conexion.php');

   if ($accion == 'borra_sku') {
   		$producto_wp = $_POST['producto_wp']+0;
   		$resultadoB = mysql_query("DELETE FROM comp_matriz WHERE producto_wp = $producto_wp ");
   }
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar SKU Whirlpool" onClick="document.forma.action='abc_sku_comp.php?nuevo=1'; document.forma.submit();" /></td>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                       $resultadotot= mysql_query("SELECT * FROM comp_matriz GROUP BY producto_wp ",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de productos Whirlpool en la lista: <b>'.$totres.'</b>';
			
			  ?></td>
            <td align="right" bgcolor="#BBBBBB">
            	<input name="sku" type="hidden" id="sku" />
            	<input name="producto_wp" type="hidden" id="producto_wp" />
                <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
                <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
                <input name="accion" type="hidden" id="accion" value="" />
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
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><strong>Producto Whirlpool</strong></td>
            <td><strong>Productos Competencia</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

			 $query = "SELECT * FROM comp_matriz GROUP BY producto_wp LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			 
			 	$txt_productos = '<table width="100%" border="0" padding="1">';
				$producto_wp = $row['producto_wp'];
				$resultadoPROD = mysql_query("SELECT nombre, modelo FROM producto WHERE clave = $producto_wp");
				$rowPROD = mysql_fetch_array($resultadoPROD);

				$query = "SELECT producto.nombre AS nombre_producto_wp, comp_producto.nombre AS nombre_producto_comp 
							FROM comp_matriz
						    LEFT JOIN producto ON comp_matriz.producto_wp = producto.clave
						    LEFT JOIN comp_producto ON comp_matriz.producto_comp = comp_producto.clave
						   WHERE producto_wp = $producto_wp";
														 
				$resultadoDM = mysql_query($query);
				while ($rowDM = mysql_fetch_array($resultadoDM)) {
					$producto_comp = $rowDM['producto_comp'];
				
					$txt_productos .= '<tr><td width="1"><strong>'.$rowDM['producto_comp'].'</strong></td><td>'.$rowDM['nombre_producto_comp'].'</td></tr>';
				}
				$txt_productos .= '</table>';



          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $rowPROD['nombre'].'<br>'.$rowPROD['modelo']; ?></td>
            <td bgcolor="#FFFFFF"><?= $txt_productos;?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
           	<a href="abc_sku_comp.php?producto_wp=<?= $row['producto_wp']; ?>&editar=1"><img src="images/editar.png" alt="Editar Combo" width="14" height="16" border="0" align="absmiddle" /></a>
            <a onclick="return confirm('¿Estás seguro que deseas\nBorrar el SKU de Whirlpool de la matriz?')" href="javascript:borra('<?= $row['producto_wp']; ?>');"><img src="images/borrar.png" alt="Borrar producto" width="14" height="15" border="0" align="absmiddle" /></a></td>
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
