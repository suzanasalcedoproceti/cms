<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
/*	$modulo=4;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	*/
	include('../conexion.php');

	$fcategoria = $_POST['fcategoria']+0;
	$fsubcategoria = $_POST['fsubcategoria']+0;
	$fmarca = $_POST['fmarca'];
	$hay_subcategorias = 1;
	if ($fcategoria) {
		$resultado = mysql_query("SELECT 1 FROM subcategoria WHERE categoria = $fcategoria",$conexion);
		$hay_subcategorias = (mysql_num_rows($resultado)>0) ? 1 : 0;
	}

	$ver = $_POST['ver'];
	$numpag = $_POST['numpag'];
	$ord = $_POST['ord'];
	if (empty($ver)) $ver='20';
	if (empty($numpag)) $numpag='1';
	
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
<script language="JavaScript">
  function ir(form, Pag) {
	form.target = '_self';
    form.numpag.value = Pag;
	form.buscar.value = 1;
    form.action='reporte_detalles_productos.php';
    form.submit();
  }
  function exportar() {
  	document.forma.target = '_blank';
    document.forma.action='reporte_detalles_productos_xls.php';
    document.forma.submit();
	document.forma.target = '_self';
  }
  function consulta() {
	if (document.forma.fcategoria.value=="") {
		alert("Selecciona la categoría a buscar");
		docuemnt.forma.fcategoria.focus();
		return;	
	}
	var hay_subcategorias = <?=$hay_subcategorias;?>;
	if (hay_subcategorias == 1 && document.forma.fsubcategoria.value == '') {
		alert("Debes seleccionar la subcategoría");
		return;	
	}
    document.forma.action='reporte_detalles_productos.php'
	document.forma.buscar.value=1; 
	document.forma.numpag.value=1
	document.forma.submit();
  } 
</script>
</head>

<body>
<div id="container">
  <? $tit='Detalles de Productos'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
	    <input name="buscar" type="hidden" id="buscar" />
        <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
        <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><table width="500" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td><div align="right">Categoría:</div></td>
                <td><select name="fcategoria" class="campo" id="fcategoria" onchange="document.forma.submit();">
                  <option value="">Selecciona categoría...</option>
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
                    <option value="">Selecciona subcategor&iacute;a...</option>
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
                    <?  $resMAR= mysql_query("SELECT * FROM marca ORDER BY nombre",$conexion);
                      while ($rowMAR = mysql_fetch_array($resMAR)) {
					  	 echo '<option value="'.$rowMAR['clave'].'"';
						 if ($fmarca==$rowMAR['clave']) echo 'selected';
						 echo '>'.$rowMAR['nombre'].'</option>';
					  } ?>
                </select></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input name="Submit" type="button" class="boton" onclick="consulta();" value="Buscar" /></td>
              </tr>
            </table></td>
          </tr>
        </table>
        <? if ($_POST['buscar']==1) { ?>
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
          <tr class="texto">
            <td height="30" colspan="8" nowrap="nowrap" bgcolor="#BBBBBB">
            <?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE (estatus=1 OR estatus=2)";


                     if ($fcategoria>0)
					 	$condicion .= " AND categoria=$fcategoria ";

                     if (!empty($fsubcategoria))
					 	$condicion .= " AND subcategoria=$fsubcategoria ";

                     if (!empty($fmarca))
					 	$condicion .= " AND marca=$fmarca ";

                     // construir la condición de búsqueda
                       $resultadotot= mysql_query("SELECT * FROM producto $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de productos en la lista: <b>'.$totres.'</b>';
			
			  ?>&nbsp;
              <div style="float:right; margin-right:5px">
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
              ?>
              </div></td>
          </tr>
          <tr class="texto">
            <td colspan="8" nowrap="nowrap">&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><b>Modelo</b></td>
            <td nowrap="nowrap"><b>Nombre</b></td>
            <td><strong>Marca</strong></td>
            <td><strong>Categoría TW</strong></td>
            <td><strong>Subcategor&iacute;a TW</strong></td>
            <td><div align="center"><strong>Segmento SM</strong></div></td>
            <td><div align="center"><strong>Categor&iacute;a SM</strong></div></td>
            <td><div align="center"><strong>Subcategor&iacute;a SM</strong></div></td>
          </tr>
          <?

			 $query = "SELECT * FROM producto $condicion ORDER BY modelo LIMIT $regini,$ver";
			 
	//echo $query;
			 
             $resultado= mysql_query($query,$conexion);

             while ($row = mysql_fetch_array($resultado)){ 

				 $producto = $row['clave'];
				 
				 $cata=$row['categoria'];
			     $resCATA= mysql_query("SELECT * FROM categoria WHERE clave=$cata",$conexion);
			     $rowCATA= mysql_fetch_array($resCATA);

				 $cate=$row['subcategoria'];
			     $resCATE= mysql_query("SELECT * FROM subcategoria WHERE clave=$cate",$conexion);
			     $rowCATE= mysql_fetch_array($resCATE);

				 $marca=$row['marca'];
			     $resMAR= mysql_query("SELECT * FROM marca WHERE clave=$marca",$conexion);
			     $rowMAR= mysql_fetch_array($resMAR);
				 
				 // obtener de acuerdo a la marca, la categorización de sitio de marca
				 // obtener la tienda de marca de esta marca
				 $resTM = mysql_query("SELECT clave FROM tienda_marca WHERE marca = $marca",$conexion);
				 $rowTM = mysql_fetch_assoc($resTM);
				 $tienda_marca = $rowTM['clave'];
				 
				 // obtener seg, cat y scat
				 $resSEG = mysql_query("SELECT m_segmento.nombre AS nombre_segmento, m_categoria.nombre AS nombre_categoria, m_subcategoria.nombre AS nombre_subcategoria
				 						  FROM m_producto 
										  LEFT JOIN m_segmento ON m_producto.segmento = m_segmento.clave
										  LEFT JOIN m_categoria ON m_producto.categoria = m_categoria.clave
										  LEFT JOIN m_subcategoria ON m_producto.subcategoria = m_subcategoria.clave
										 WHERE m_producto.tienda = $tienda_marca 
										   AND producto = $producto",$conexion);
				 $rowSEG = mysql_fetch_assoc($resSEG);

          ?>
          <tr class="texto" bgcolor="#FFFFFF">
            <td><?= $row['modelo']; ?></td>
            <td><?= $row['nombre']; ?></td>
            <td><?= $rowMAR['nombre']; ?></td>
            <td><?= $rowCATA['nombre']; ?></td>
            <td><?= $rowCATE['nombre']; ?></td>
            <td><?= $rowSEG['nombre_segmento'];?></td>
            <td><?= $rowSEG['nombre_categoria'];?></td>
            <td><?= $rowSEG['nombre_subcategoria'];?></td>
          </tr>
          <?
                 } // WHILE
              ?>
        </table>
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="right"><div align="center">
              <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Exportar a XLS" />
            </div></td>
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
        <? } ?>
      </form>    
    </div>
</div>
</body>
</html>
