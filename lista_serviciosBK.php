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
    form.action='lista_servicios.php';
    form.submit();
  }
 
  function borra(id) {
    document.forma.servicio.value = id; 
    document.forma.action='borra_servicio.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Servicios'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
 
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   if ($ord=='nombre') $orden='ORDER BY idservicio,tipo_servicio';
  
   include('../conexion.php');
 
?>
  <div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar Servicio" onClick="document.forma.action='abc_servicios.php'; document.forma.submit();" /></td>
            <td align="right">&nbsp;</td>
          </tr>
         
          <tr>
            <td bgcolor="#BBBBBB"><? 
                     // construir la condición de búsqueda
                     $condicion = "WHERE 1 ";
                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT 1 FROM servicios $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
              
           echo 'Total de servicios en la lista: <b>'.$totres.'</b>';
      
        ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="servicio" type="hidden" id="servicio" />
                <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
                <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
                <? $regini = ($numpag * $ver) - $ver;
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
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="&Uacute;ltima p&aacute;gina"></a>';
                     }
              ?>            </td>
          </tr>
        </table>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" ><div align="center"><strong>Tipo de servicio</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Descripci&oacute;n</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Condition Type (F&iacute;sica)</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Condition Type (Moral)</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>Material</strong></div></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
             $query = "SELECT * from servicios  $condicion $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
              $idservicio = $row['idservicio'];
              $descripcion = $row['descripcion'];
              $tipo_servicio = $row['tipo_servicio'];          
              $condition_type_fisica = $row['condition_type_fisica'];          
              $condition_type_moral = $row['condition_type_moral'];          
          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF" align="left"><?= $row['tipo_servicio']; ?></td>            
            <td bgcolor="#FFFFFF"><?= $row['descripcion']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['condition_type_fisica']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['condition_type_moral']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['material']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><a href="abc_servicios.php?servicio=<?= $row['idservicio']; ?>"><img src="images/editar.png" alt="Editar Sucursal" width="14" height="16" border="0" align="absmiddle" /></a>
              <? if ($rel<=0) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar el servicio?')" href="javascript:borra('<?= $row['idservicio']; ?>');"><img src="images/borrar.png" alt="Borrar Sucursal" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>            
            </td>

      </tr>
          <? } // WHILE 
          mysql_close(); ?>
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
