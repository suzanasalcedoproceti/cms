<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=10;
   include('../conexion.php');
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este m?dulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	if ($_POST['accion']=='borrar'){
		$id_promo = $_POST['promo']+0;
		$resultado = mysql_query("DELETE FROM promo_producto WHERE clave = $id_promo LIMIT 1");
		$afe = mysql_affected_rows();
		if ($afe>0)
			@unlink("./images/cms/promo_productos/".$id_promo.'.gif');
	
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

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_promo_producto.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_promo_producto.php';
    document.forma.submit();
  }
  function borra(id) {
	document.forma.action = "lista_promo_producto.php";
    document.forma.accion.value="borrar";
    document.forma.promo.value = id;
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Iconos de Promociones especiales de productos'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   if ($ord=='orden') $orden='ORDER BY orden';
   if ($ord=='nombre') $orden='ORDER BY nombre';
   
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
        <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
        <input name="promo" type="hidden" id="promo" value="" />
        <input name="accion" type="hidden" id="accion" value="" />
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar icono nuevo" onClick="document.forma.action='abc_promo_producto.php'; document.forma.submit();" /></td>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condici?n de b?squeda
                     $condicion = "WHERE 1";


                     // construir la condici?n de b?squeda

                       $resultadotot= mysql_query("SELECT * FROM promo_producto $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de iconos en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">

                <?


                     $regini = ($numpag * $ver) - $ver;
					 if ($regini<0) $regini=0;

                     // poner flechitas anterior, primero, ?ltimo, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera p?gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="P?gina anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "P?gina ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p?gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="?ltima p?gina"></a>';
                     }
              ?>            </td>
          </tr>
        </table>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><div align="center"><b>Icono</a></b></div></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='nombre') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('nombre');" class="texto">Nombre   <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

             $resultado= mysql_query("SELECT * FROM promo_producto $condicion $orden LIMIT $regini,$ver",$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
				
				$clave_promo = $row['clave'];
				
				$res = mysql_query("SELECT 1 FROM producto WHERE es_promocion_especial = $clave_promo LIMIT 1");
				$rel = mysql_num_rows($res);

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><div align="center">
              <? if (file_exists('./images/cms/promo_productos/'.$row['clave'].'.gif')) echo '<img src="images/cms/promo_productos/'.$row['clave'].'.gif" />'; ?></div></td>
            <td bgcolor="#FFFFFF"><?= $row['nombre']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
               	<a href="abc_promo_producto.php?promo=<?= $row['clave']; ?>"><img src="images/editar.png" alt="Editar Promo" width="14" height="16" border="0" align="absmiddle" /></a>
       	  		<? if ($rel<=0) { ?>
                  <a onclick="return confirm('?Est?s seguro que deseas\nBorrar el icono?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar icono" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>            </td>
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
