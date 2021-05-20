<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=2;
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
    form.action='lista_subcategoria.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_subcategoria.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.subcategoria.value = id;
    document.forma.action='borra_subcategoria.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Subcategor&iacute;as'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $categoria = $_POST['categoria'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='categoria';

   if ($ord=='nombre') $orden='ORDER BY nombre';
   if ($ord=='categoria') $orden='ORDER BY orden_categoria, orden, nombre';
   if ($ord=='orden') $orden='ORDER BY categoria, orden, nombre';
   
   include('../conexion.php');
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar subcategor&iacute;a nueva" onClick="document.forma.action='abc_subcategoria.php'; document.forma.submit();" /></td>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td>Categor&iacute;a:
              <select name="categoria" class="campo" id="categoria" onchange="document.forma.submit();">
                  <option value="">Cualquier categor&iacute;a...</option>
                  <?  $resCAT= mysql_query("SELECT * FROM categoria WHERE  NOT accesorios AND NOT garantias ORDER BY orden",$conexion);
					  while ($rowCAT = mysql_fetch_array($resCAT)) { 
						echo '<option value="'.$rowCAT['clave'].'"';
						if ($rowCAT['clave']==$categoria) echo ' selected';
						echo '>'.$rowCAT['nombre'].'</option>';
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
                     $condicion = "WHERE (subcategoria.estatus=1 OR subcategoria.estatus=2) ";


					 if (!empty($categoria))
					 	$condicion.= " AND categoria='$categoria'";

                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT 1 FROM subcategoria $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de subcategor&iacute;as en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="subcategoria" type="hidden" id="subcategoria" />
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
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Página anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "P&aacute;gina ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Última página"></a>';
                     }
              ?>            </td>
          </tr>
        </table>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" bgcolor="<? if($ord=='categoria') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('categoria');" class="texto">Categor&iacute;a <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='orden') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('orden');" class="texto">Orden <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='nombre') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('nombre');" class="texto">Subcategoría <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td align="center"><strong>Tipo Producto</strong></td>
            <td align="center"><strong>Subtipo Producto</strong></td>
            <td align="center"><strong>MAS</strong></td>
            <td align="center"><strong>Almacén</strong></td>
            <td align="center"><strong>SL</strong></td>
            <td align="center"><strong>Logítica<br>Override</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
			 $query = "SELECT subcategoria.*, categoria.nombre AS nombre_categoria, categoria.orden AS orden_categoria FROM subcategoria 
						 LEFT JOIN categoria ON subcategoria.categoria = categoria.clave
						 $condicion $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

				$subcategoria = $row['clave'];
			    $resPRO= mysql_query("SELECT 1 FROM producto WHERE subcategoria=$subcategoria",$conexion);
			    $rel = mysql_num_rows($resPRO);

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['nombre_categoria']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['orden']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['nombre']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?=  substr($row['tipo_producto'], 1);?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= $row['subtipo_producto'];?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? if ($row['mostrar_en_mas']) echo 'SI';?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? echo $row['cedis']?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? echo $row['loc']?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? echo ($row['override']) ? 'SI' : 'NO';?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? if ($row['estatus']==1) { ?><a href="abc_subcategoria.php?subcategoria=<?= $row['clave']; ?>"><img src="images/editar.png" alt="Editar Subcategoría" width="14" height="16" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/editar_off.png" width="14" height="16" align="absmiddle" /><? } ?>
       	  		<? if ($rel<=0 AND op_aut($modulo) AND $row['estatus']==1) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar la subcategoría?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar Categoría" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>            </td>
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
