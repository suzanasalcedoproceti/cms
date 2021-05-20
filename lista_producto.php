<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=4;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este m&oacute;dulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	$fcategoria = $_POST['fcategoria'];
	$fsubcategoria = $_POST['fsubcategoria'];
	$fmarca = $_POST['fmarca'];
	$texto = $_POST['texto'];
	$fpromo = $_POST['fpromo'];
	
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
	form.target = '_self';
    form.numpag.value = Pag;
    form.action='lista_producto.php';
    form.submit();
  }
  function ordena(orden) {
	document.forma.target = '_self';
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_producto.php';
    document.forma.submit();
  }
  function borra(id) {
	document.forma.target = '_self';
    document.forma.producto.value = id;
    document.forma.action='borra_producto.php';
    document.forma.submit();
  }
  function edita(id) {
	document.forma.target = '_self';
  	document.forma.producto.value = id;
	document.forma.action='abc_producto.php';
	document.forma.submit();
  }
  function exportar() {
  	document.forma.target = '_blank';
    document.forma.action='lista_producto_xls.php';
    document.forma.submit();
	document.forma.target = '_self';
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Productos'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='codigo';

   if     ($ord=='codigo') $orden='ORDER BY modelo';
   elseif ($ord=='nombre') $orden='ORDER BY nombre';
   
   include('../conexion.php');
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
	    <input name="producto" type="hidden" id="producto" />
        <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
        <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar producto nuevo" onClick="document.forma.action='abc_producto.php'; document.forma.submit();" /></td>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><table width="500" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td><div align="right">Categor&iacute;a:</div></td>
                <td><select name="fcategoria" class="campo" id="fcategoria" onchange="document.forma.submit();">
                  <option value="">Cualquier categor&iacute;a...</option>
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
                    <?
					  $fcategoria+=0;
					  $resCAT= mysql_query("SELECT * FROM subcategoria WHERE categoria = $fcategoria ORDER BY orden, nombre",$conexion);
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
                <td><div align="right">Promoci&oacute;n:</div></td>
                <td><select name="fpromo" class="campo" id="fpromo">
                    <option value="" <? if ($fpromo=='') echo 'selected';?>>Indistinto...</option>
                    <option value="PS" <? if ($fpromo=='PS') echo 'selected';?>>Promoción de la Semana...</option>
                    <option value="N" <? if ($fpromo=='N') echo 'selected';?>>Lo más nuevo...</option>
                    <option value="PE" <? if ($fpromo=='PE') echo 'selected';?>>Promoción Especial...</option>
                </select></td>
              </tr>
              <tr>
                <td width="156" valign="top"><div align="right">Buscar:</div></td>
                <td width="594"><input name="texto" type="text" class="campo" id="texto" value="<?= $texto; ?>" size="50" />
                    <br />
                  (modelo, nombre, descripci&oacute;n) </td>
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
                     $condicion = "WHERE (estatus=1 OR estatus=2) ";


                     if (!empty($fcategoria))
					 	$condicion .= " AND categoria=$fcategoria ";

                     if (!empty($fsubcategoria))
					 	$condicion .= " AND subcategoria=$fsubcategoria ";

                     if (!empty($fmarca))
					 	$condicion .= " AND marca=$fmarca ";

                     if ($fpromo=='N')
					 	$condicion .= " AND es_nuevo = 1";
                     if ($fpromo=='PS')
					 	$condicion .= " AND es_promocion = 1";
                     if ($fpromo=='PE')
					 	$condicion .= " AND es_promocion_especial >0 ";

                     /*if (!empty($subcategoria))
					 	$condicion .= " AND subcategoria=$subcategoria";*/

 					 if (!empty($texto)) {
						// identificar si sólo hay 1 palabra o más de 1
						$trozos=explode(" ",$texto);
						$numero_palabras=count($trozos);
						if ($numero_palabras==1 || 1) {
							//SI SOLO HAY UNA PALABRA DE BUSQUEDA SE ESTABLECE UNA INSTRUCION CON LIKE
							$condicion .= "AND (modelo LIKE '%$texto%' OR
												nombre LIKE '%$texto%' OR
												descripcion_larga LIKE '%$texto%') ";	
						} else  { // más de 1 palabras
							//SI HAY UNA FRASE SE UTILIZA EL ALGORTIMO DE BUSQUEDA AVANZADO DE MATCH AGAINST
							$condicion .= " AND MATCH ( modelo, nombre, descripcion_larga ) AGAINST ( '$texto' IN BOOLEAN MODE ) ";
						} 
					 }
                     // construir la condición de búsqueda
                       $resultadotot= mysql_query("SELECT * FROM producto $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de productos en la lista: <b>'.$totres.'</b>';
			
			  ?>            
            </td>
            <td align="right" bgcolor="#BBBBBB">
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
          <tr>
            <td bgcolor="#BBBBBB"><strong>PS</strong>=Promoci&oacute;n de la semana, <strong>N</strong>=Lo m&aacute;s nuevo, <strong>PE</strong>=Promoci&oacute;n Especial, <strong>C</strong>=En Combo</td>
            <td align="right" bgcolor="#BBBBBB">&nbsp;</td>
          </tr>
        </table>
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="2">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" bgcolor="<? if($ord=='codigo') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('codigo');" class="texto">Modelo  <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='nombre') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('nombre');" class="texto">Nombre  <img src="images/orden.png" alt="" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td><strong>Categoría</strong></td>
            <td><strong>Subcategor&iacute;a</strong></td>
            <td><strong>Marca</strong></td>
            <td><strong>ST</strong></td>
            <td><div align="center"><strong>PS</strong></div></td>
            <td><div align="center"><strong>N</strong></div></td>
            <td><div align="center"><strong>PE</strong></div></td>
            <td><div align="center"><strong>C</strong></div></td>
            <td><div align="center"><strong>Ficha</strong></div></td>
            <td><div align="center"><strong>Fotos</strong></div></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

			 $query = "SELECT * FROM producto $condicion $orden LIMIT $regini,$ver";
			 
	//echo $query;
			 
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

				 $cata=$row['categoria'];
			     $resCATA= mysql_query("SELECT * FROM categoria WHERE clave=$cata",$conexion);
			     $rowCATA= mysql_fetch_array($resCATA);

				 $cate=$row['subcategoria'];
			     $resCATE= mysql_query("SELECT * FROM subcategoria WHERE clave=$cate",$conexion);
			     $rowCATE= mysql_fetch_array($resCATE);

				 $marca=$row['marca'];
			     $resMAR= mysql_query("SELECT * FROM marca WHERE clave=$marca",$conexion);
			     $rowMAR= mysql_fetch_array($resMAR);

                 $CR=chr(13).chr(10);
			     $fotos = $row['fotos'];
                 $foto = explode($CR,$fotos);
		         if (strlen($fotos)>0) $total_fotos=count($foto);
                 else $total_fotos=0;
				 
				 // revisar si está en combo
				 $producto = $row['clave'];
				 $resultadoCOM = mysql_query("SELECT 1 FROM combo_detalle WHERE producto = $producto LIMIT 1");
				 $en_combo = mysql_num_rows($resultadoCOM);
				 
				 $rel=0;
				 // revisar que no se pueda eliminar el producto si está en combo_detalle, o en m_producto,
				 if ($en_combo>0) $rel++;
				 $resultadoREL = mysql_query("SELECT 1 FROM m_producto WHERE producto = $producto");
				 $enc = mysql_num_rows($resultadoREL);
				 if ($enc>0) $rel++;
				 
				 $estatus_inv = mysql_query("SELECT estatus FROM existencia WHERE producto = '{$row['modelo']}' LIMIT 1",$conexion);
				 $estatus_inv = mysql_fetch_assoc($estatus_inv);
				 $estatus_inv = $estatus_inv['estatus'];
				 

          ?>
          <tr class="texto" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['modelo']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowCATA['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowCATE['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowMAR['nombre']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?=$estatus_inv;?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? if ($row['es_promocion']==1) echo 'SI'; else echo '&nbsp;'; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? if ($row['es_nuevo']==1) echo 'SI'; else echo '&nbsp;'; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? if ($row['es_promocion_especial']>0) echo '<img src="images/cms/promo_productos/'.$row['es_promocion_especial'].'.gif" width="15" align="absmiddle">'; else echo '&nbsp;'; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? if ($en_combo>0) echo 'SI'; else echo '&nbsp;'; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
			  <? $pdf = 'images/cms/productos/pdf/'.$row['clave'].'.pdf';
			     if (file_exists($pdf)) {
					echo '<a href="'.$pdf.'?'.md5(time()).'" target="_blank" title="'.$row['nombre'].'"><img src="images/pdf.gif" align="absmiddle" alt="Ver PDF" /></a>';
			    }				
				 ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
			<? if ($total_fotos>0) {
				echo $total_fotos.' <a href="images/cms/productos/'.$foto[0].'?'.md5(time()).'" rel="shadowbox['.$row['clave'].']"><img src="images/foto.png" width="14" height="15" align="absmiddle" alt="Ver fotos" /></a>';
				for ($i=1; $i<count($foto); $i++)
					echo '<a href="images/cms/productos/'.$foto[$i].'?'.md5(time()).'" rel="shadowbox['.$row['clave'].']">';
			    }
				
				 ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
				<? if (op_aut($modulo) AND $row['estatus']==1) { ?>
                <a href="photo_mgr.php?producto=<?= $row['clave']; ?>"><img src="images/camara.png" width="16" height="16" align="absmiddle" alt="Actualizar fotos" /></a>
				<? } else  { ?>
                <img src="images/camara_off.png" width="16" height="16" align="absmiddle"/>
                <? } ?>
               	<? if ($row['estatus']==1) { ?><a href="javascript:edita(<?= $row['clave']; ?>)"><img src="images/editar.png" alt="Editar Producto" width="14" height="16" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/editar_off.png" width="14" height="16" align="absmiddle" /><? } ?>
       	  		<? if ($rel<=0 AND op_aut($modulo) AND $row['estatus']==1) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar el Producto?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar Producto" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>
       	  		<? if ($row['bitacora']) { ?>
                <a href="bitacora_producto.php?clave=<?=$row['clave'];?>" rel="shadowbox; width=540;height=440"><img src="images/txt_icon.gif" alt="Ver Bitácora" title="Ver Bitácora" border="0" align="absmiddle" /></a>
                <? } ?>
				</td>
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
      </form>    
    </div>
</div>
</body>
</html>
