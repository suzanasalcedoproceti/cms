<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include("lib.php");
	$modulo=11;
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
    form.action='lista_stage.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_stage.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.stage.value = id;
    document.forma.action='borra_stage.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Stages'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   if ($ord=='nombre') $orden='ORDER BY nombre';
   
   include('../conexion.php');
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar stage nuevo" onClick="document.forma.action='abc_stage.php'; document.forma.submit();" /></td>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE (estatus=1 OR estatus=2) ";


                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT * FROM stage $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de stages en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB"><input name="stage" type="hidden" id="stage" />
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
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="P&aacute;gina anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "Página ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p&aacute;gina"></a>&nbsp;';
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
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" bgcolor="<? if($ord=='nombre') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('nombre');" class="texto">Nombre <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td><strong>Producto</strong></td>
            <td><strong>Combo</strong></td>
            <td><strong>Video</strong></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='orden') echo '#DDDDDD'; ?>"><strong>Empresa</strong></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='orden') echo '#DDDDDD'; ?>"><div align="center"><strong>Mobile</strong></div></td>
            <td><div align="center"><strong>Vigencia</strong></div></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='orden') echo '#DDDDDD'; ?>"><div align="center"><strong>URL</strong></div></td>
            <td nowrap="nowrap" bgcolor="<? if($ord=='orden') echo '#DDDDDD'; ?>"><div align="center"><b><a href="javascript:ordena('orden');" class="texto">Stage</a></b></div></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

             $resultado= mysql_query("SELECT * FROM stage $condicion $orden LIMIT $regini,$ver",$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

				 $producto=$row['producto'];
				 $resPRO= mysql_query("SELECT * FROM producto WHERE clave=$producto",$conexion);
				 $rowPRO= mysql_fetch_array($resPRO);
				
				 $combo=$row['combo'];
				 $resCOM= mysql_query("SELECT * FROM combo WHERE clave=$combo",$conexion);
				 $rowCOM= mysql_fetch_array($resCOM);
				
				 $empresa = $row['empresa'];
			     $resEMP= mysql_query("SELECT nombre FROM empresa WHERE clave=$empresa",$conexion);
			     $rowEMP= mysql_fetch_array($resEMP);
				 $nombre_empresa = ($rowEMP['nombre']) ? $rowEMP['nombre'] : 'Gen&eacute;rico';
				 

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowPRO['modelo']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowCOM['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['video']; ?></td>
            <td bgcolor="#FFFFFF"><?=$nombre_empresa; ?></td>
            <td align="center" bgcolor="#FFFFFF"><?= ($row['mobile']) ? 'SI' : ''; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
              <? echo fecha($row['inicio_vigencia']);
			     if (fecha($row['fin_vigencia'])) echo ' - '.fecha($row['fin_vigencia']);   ?>            </td>
            <td bgcolor="#FFFFFF"><? if ($row['url']) if ($row['externa']) echo 'Ext'; else echo 'Interna'; else echo '&nbsp';?></td>
            <td bgcolor="#FFFFFF"><div align="center">
    			<? if (file_exists('./images/cms/stages/'.$row['clave'].'.jpg')) echo '<a href="images/cms/stages/'.$row['clave'].'.jpg?'.md5(time()).'" rel="shadowbox"><img src="images/foto.png" width="14" height="15" align="absmiddle" alt="Ver stage" /></a>';
				   else echo '&nbsp;'; ?>
            </div></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
               	<? if ($row['estatus']==1 && !$row['accesorios']) { ?><a href="abc_stage.php?stage=<?= $row['clave']; ?>"><img src="images/editar.png" alt="Editar Stage" width="14" height="16" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/editar_off.png" width="14" height="16" align="absmiddle" /><? } ?>
       	  		<? if ($rel<=0 AND op_aut($modulo) AND $row['estatus']==1) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar el Stage?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar Promo" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>            </td>
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
