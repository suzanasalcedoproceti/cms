<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');
	$permiso_bugs = op(28);

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

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_bugs.php';
    form.submit();
  }
  function seguimiento(folio) {
  	document.forma.folio.value=folio;
    document.forma.action='seguimiento_bugs.php';
    document.forma.submit();
  }

</script>
</head>

<body>
<div id="container">
	<? $tit='Seguimiento de errores y recomendaciones'; include('top.php'); ?>
  <?
  
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $tipo = $_POST['tipo'];
   $aplicacion = $_POST['aplicacion'];
   $estatus = $_POST['estatus'];
  
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='fecha';

   if     ($ord=='fecha') $orden='ORDER BY comentario_pedido.fecha, pedido';
   elseif ($ord=='fecha') $orden='ORDER BY comentario_pedido.fecha';
   
   include('../conexion.php');
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td colspan="2"><table width="550" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
                <tr>
                  <td><div align="right">Tipo:</div></td>
                  <td><select name="tipo" class="campo" id="tipo">
                    <option value="" selected="selected">Cualquiera</option>
                    <? $resultadoT = mysql_query("SELECT * FROM bug_tipo ORDER BY nombre",$conexion);
			      while ($rowT = mysql_fetch_array($resultadoT)) {
				     echo '<option value="'.$rowT['clave'].'"';
					 if ($rowT['clave']==$tipo) echo 'selected';
					 echo '>'.$rowT['nombre'].'</option>';
				  }
			    ?>
                  </select></td>
                  <td width="87"><div align="right">Estatus:</div></td>
                  <td width="221"><select name="estatus" class="campo" id="estatus">
                    <option value="" <? if ($estatus=='') echo 'selected ';?>>Cualquiera</option>
                    <option value="A" <? if ($estatus=='A') echo 'selected ';?>>Abierto</option>
                    <option value="R" <? if ($estatus=='R') echo 'selected ';?>>En revisión</option>
                    <option value="C" <? if ($estatus=='C') echo 'selected ';?>>Completado</option>
                    <option value="X" <? if ($estatus=='X') echo 'selected ';?>>Cancelado</option>
                  </select></td>
              </tr>

                <tr>
                  <td width="58"><div align="right">Aplicaci&oacute;n:</div></td>
                  <td width="168">
                    <select name="aplicacion" class="campo" id="aplicacion">
                      <option value="" <? if ($aplicacion=='') echo 'selected ';?>>Cualquiera</option>
                      <option value="CMS" <? if ($aplicacion=='CMS') echo 'selected ';?>>CMS</option>
                      <option value="POS" <? if ($aplicacion=='POS') echo 'selected ';?>>POS</option>
                    </select>
                  </td>
                  <td nowrap="nowrap">&nbsp;</td>
                  <td nowrap="nowrap">&nbsp;</td>
              </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td><input name="Submit" type="submit" class="boton" onclick="document.forma.numpag.value=1" value="Buscar" /></td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td width="250" bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE 1=1 ";

					 if (!$permiso_bugs) $condicion .= " AND bug.usuario = ".$_SESSION['usr_valido']." ";
					 
                     if (!empty($tipo))
					 	$condicion .= " AND bug.tipo=$tipo ";

                     if (!empty($estatus))
					 	$condicion .= " AND bug.estatus='$estatus' ";

                     if (!empty($aplicacion))
					 	$condicion .= " AND bug.aplicacion='$aplicacion' ";

                     // construir la condición de búsqueda
                       $resultadotot= mysql_query("SELECT * FROM bug  $condicion ",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de registros en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="folio" type="hidden" id="folio" />
            	<input name="accion" type="hidden" id="accion">
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
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td align="center" nowrap="nowrap"><b>Ultima<br />
            Actualizaci&oacute;n</b></td>
            <td nowrap="nowrap"><b>Tipo</b></td>
            <td nowrap="nowrap"><div align="center"><strong>Aplicaci&oacute;n</strong></div></td>
            <td nowrap="nowrap"><div align="center"><strong>Usuario</strong></div></td>
            <td nowrap="nowrap"><div align="center"><strong>Tienda</strong></div></td>
            <td><div align="center"><strong>Asunto</strong></div></td>
            <td nowrap="nowrap"><b>Estatus</b></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
		  	 $hoy=date('Y-m-d');
             $resultado= mysql_query("SELECT bug.*, bug_tipo.nombre FROM bug LEFT JOIN bug_tipo ON bug.tipo = bug_tipo.clave $condicion ORDER BY actualizado DESC LIMIT $regini,$ver",$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

				if ($row['aplicacion']=='CMS') {
					$usuario=$row['usuario'];
					$resUSU= mysql_query("SELECT nombre FROM usuario WHERE clave='$usuario'",$conexion);
					$rowUSU= mysql_fetch_array($resUSU);
					$nombre_tienda = 'N/A';
				}

				if ($row['aplicacion']=='POS') {
					$tienda=$row['tienda'];
					$usuario=$row['usuario'];
					$resUSU= mysql_query("SELECT nombre FROM usuario_tienda WHERE tienda=$tienda AND clave='$usuario'",$conexion);
					$rowUSU= mysql_fetch_array($resUSU);
					
					$tienda = $row['tienda'];
					$resTIE = mysql_query("SELECT nombre FROM tienda WHERE clave = $tienda");
					$rowTIE = mysql_fetch_array($resTIE);
					$nombre_tienda = $rowTIE['nombre'];

				}
				
          ?>
          <tr class="texto" valign="top">
            <td bgcolor="#FFFFFF"><div align="center"><?= fecha($row['actualizado']); ?></div></td>
            <td bgcolor="#FFFFFF"><?=$row['nombre'];?></td>
            <td bgcolor="#FFFFFF"><?= $row['aplicacion']; ?> </td>
            <td bgcolor="#FFFFFF"><?= $rowUSU['nombre']; ?> </td>
            <td bgcolor="#FFFFFF"><?= $nombre_tienda; ?> </td>
            <td bgcolor="#FFFFFF"><?=$row['asunto'];?></td>
            <td bgcolor="#FFFFFF"><? switch ($row['estatus']) {
										case 'A' : echo 'Abierto'; break;
										case 'R' : echo 'En Revisión'; break;
										case 'C' : echo 'Completado'; break;
										case 'X' : echo 'Cancelado'; break;
									 } ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
	       	  <a href="javascript:seguimiento(<?= $row['folio']; ?>);"><img src="images/foto.png" alt="Ver / contestar comentarios" title="Ver / contestar comentarios" width="14" height="15" border="0" align="absmiddle" /></a>          </tr>
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
