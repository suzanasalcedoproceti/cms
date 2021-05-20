<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=21;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este m&oacute;dulo';
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
    form.action='lista_combo.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_combo.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.combo.value = id;
    document.forma.accion.value = 'borra_combo';
    document.forma.action='lista_combo.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Combos'; include('top.php'); ?>
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

   if ($accion == 'borra_combo') {
   		$combo = $_POST['combo']+0;
   		$resultadoB = mysql_query("DELETE FROM combo WHERE clave = $combo LIMIT 1");
		$resultadoB = mysql_query("DELETE FROM combo_detalle WHERE combo = $combo");
		@unlink("images/cms/combos/".$combo.".jpg");
   }
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar combo nuevo" onClick="document.forma.action='abc_combo.php?nuevo=1'; document.forma.submit();" /></td>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE 1=1 ";


                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT * FROM combo $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de combos en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
            	<input name="combo" type="hidden" id="combo" />
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
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Última p&aacute;gina"></a>';
                     }
              ?>            </td>
          </tr>
        </table>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" bgcolor="<? if($ord=='nombre') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('nombre');" class="texto">Nombre <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td><strong>Productos</strong></td>
            <td align="center"><strong>Activo</strong></td>
            <td align="center"><strong>Imagen</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

             $resultado= mysql_query("SELECT * FROM combo $condicion $orden LIMIT $regini,$ver",$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			 
			 	$txt_productos = '<table width="100%" border="0" padding="1">';
				$combo = $row['clave'];
				$resultadoDC = mysql_query("SELECT producto.nombre AS nombre_producto, producto.modelo
											 FROM combo_detalle 
										  LEFT JOIN producto ON combo_detalle.producto = producto.clave
										 WHERE combo = $combo ORDER by orden");
				while ($rowDC = mysql_fetch_array($resultadoDC)) {
				
					$txt_productos .= '<tr><td width="1"><strong>'.$rowDC['modelo'].'</strong></td><td>'.$rowDC['nombre_producto'].'</td></tr>';
				}
				$txt_productos .= '</table>';



          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $txt_productos;?></td>
            <td align="center" bgcolor="#FFFFFF"><?= ($row['activo']) ? 'SI' : 'NO'; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
            <? if (file_exists("images/cms/combos/".$row['clave'].".jpg")) { ?>
            <a href="images/cms/combos/<?=$combo;?>.jpg?<?=$ht;?>" rel="shadowbox"><img src="images/cms/combos/<?=$combo;?>t.jpg?<?=$ht;?>" height="45" /></a>
            <? } ?>
            </td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
           	<a href="abc_combo.php?combo=<?= $row['clave']; ?>&editar=1"><img src="images/editar.png" alt="Editar Combo" width="14" height="16" border="0" align="absmiddle" /></a>
       	  		<? if ($rel<=0) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar el Combo?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar Combo" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>            </td>
		  </tr>
          <?
                 } // WHILE
                 mysql_close();
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
