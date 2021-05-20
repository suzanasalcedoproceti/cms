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
	$fcategoria = $_POST['fcategoria'];
	$fsubcategoria = $_POST['fsubcategoria'];
	$fmarca = $_POST['fmarca'];
	$texto = $_POST['texto'];

   include('../conexion.php');
	
	if ($_POST['accion']=='borrar') {
		$producto =$_POST['producto']+0;
        $resultado= mysql_query("DELETE FROM comp_producto WHERE clave = $producto LIMIT 1" ,$conexion);
		@unlink("images/cms/productos_comp/".$producto.".jpg");
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
<script type="text/javascript">
	Shadowbox.init({
		language: 'es',
		players:  ['img', 'html', 'iframe', 'qt', 'wmp', 'swf', 'flv']
	});
</script>


<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_producto_comp.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_producto_comp.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.producto.value = id;
	document.forma.accion.value = 'borrar';
    document.forma.action='lista_producto_comp.php';
    document.forma.submit();
  }
  function edita(id) {
  	document.forma.producto.value = id;
	document.forma.action='abc_producto_comp.php';
	document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Productos de Competencia'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   if     ($ord=='codigo') $orden='ORDER BY modelo';
   elseif ($ord=='nombre') $orden='ORDER BY nombre';
   
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar producto nuevo" onClick="document.forma.action='abc_producto_comp.php'; document.forma.submit();" /></td>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><table width="500" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td width="156"><div align="right">Categoría:</div></td>
                <td width="594"><select name="fcategoria" class="campo" id="fcategoria" onchange="document.forma.submit();">
                  <option value="">Cualquier categoría...</option>
                  <?  $resCAT= mysql_query("SELECT * FROM categoria ORDER BY orden, nombre",$conexion);
                      while ($rowCAT = mysql_fetch_array($resCAT)) {
					  	 echo '<option value="'.$rowCAT['clave'].'"';
						 if ($fcategoria==$rowCAT['clave']) echo 'selected';
						 echo '>'.$rowCAT['nombre'].'</option>';
					  } ?>
                </select></td>
              </tr>
              <tr>
                <td><div align="right">Subcategor&iacute;a:</div></td>
                <td><select name="fsubcategoria" class="campo" id="fsubcategoria">
                    <option value="">Cualquier subcategor&iacute;a...</option>
                    <?  $resCAT= mysql_query("SELECT * FROM subcategoria WHERE categoria = $fcategoria ORDER BY orden, nombre",$conexion);
                      while ($rowCAT = mysql_fetch_array($resCAT)) {
					  	 echo '<option value="'.$rowCAT['clave'].'"';
						 if ($fsubcategoria==$rowCAT['clave']) echo 'selected';
						 echo '>'.$rowCAT['nombre'].'</option>';
					  } ?>
                </select></td>
              </tr>
              <tr>
                <td><div align="right">Marca:</div></td>
                <td><select name="fmarca" class="campo" id="fmarca">
                    <option value="">Cualquier marca...</option>
                    <?  $resMAR= mysql_query("SELECT * FROM comp_marca ORDER BY nombre",$conexion);
                      while ($rowMAR = mysql_fetch_array($resMAR)) {
					  	 echo '<option value="'.$rowMAR['clave'].'"';
						 if ($fmarca==$rowMAR['clave']) echo 'selected';
						 echo '>'.$rowMAR['nombre'].'</option>';
					  } ?>
                </select></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input name="Submit" type="submit" class="boton" onclick="document.forma.numpag.value=1" value="Buscar" /></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE 1 ";


                     if (!empty($fcategoria))
					 	$condicion .= " AND categoria=$fcategoria ";

                     if (!empty($fsubcategoria))
					 	$condicion .= " AND subcategoria=$fsubcategoria ";

                     if (!empty($fmarca))
					 	$condicion .= " AND marca=$fmarca ";


                     // construir la condición de búsqueda
                       $resultadotot= mysql_query("SELECT * FROM comp_producto $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de productos en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="producto" type="hidden" id="producto" />
              <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
                <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
                <input name="accion" type="hidden" id="accion" />
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
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" class="texto"><strong>SKU</strong></td>
            <td nowrap="nowrap"><strong>Nombre</strong></td>
            <td><strong>Categoría</strong></td>
            <td><strong>Subcategor&iacute;a</strong></td>
            <td><strong>Marca</strong></td>
            <td><strong>Foto</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

			 $query = "SELECT * FROM comp_producto $condicion $orden LIMIT $regini,$ver";
			 
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

				 $cata=$row['categoria'];
			     $resCATA= mysql_query("SELECT * FROM categoria WHERE clave=$cata",$conexion);
			     $rowCATA= mysql_fetch_array($resCATA);

				 $cate=$row['subcategoria'];
			     $resCATE= mysql_query("SELECT * FROM subcategoria WHERE clave=$cate",$conexion);
			     $rowCATE= mysql_fetch_array($resCATE);

				 $marca=$row['marca'];
			     $resMAR= mysql_query("SELECT * FROM comp_marca WHERE clave=$marca",$conexion);
			     $rowMAR= mysql_fetch_array($resMAR);

				 // revisar si está en matriz
				 $producto = $row['clave'];
				 $resultadoCOM = mysql_query("SELECT 1 FROM comp_matriz WHERE producto_comp = $producto LIMIT 1");
				 $en_matriz = mysql_num_rows($resultadoCOM);
				 $rel = 0;
				 if ($en_matriz>0) $rel++;
				 

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['sku']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowCATA['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowCATE['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowMAR['nombre']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
            <? if (file_exists("images/cms/productos_comp/".$row['clave'].".jpg")) { ?>
				  <a href="images/cms/productos_comp/<?=$row['clave'];?>.jpg" rel="shadowbox"><img src="images/foto.png" width="14" height="15" align="absmiddle" alt="Ver foto" /></a>
            <? } ?>
            </td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><a href="javascript:edita(<?= $row['clave']; ?>)"><img src="images/editar.png" alt="Editar Producto" width="14" height="16" border="0" align="absmiddle" /></a>
       	  		<? if ($rel<=0) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar el Producto de Competencia?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar Producto de Competencia" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>       	  	</td>
		  </tr>
          <?
                 } // WHILE
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
