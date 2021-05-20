<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=20;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	$accion = $_POST['accion'];
	$cp = $_POST['cp'];
	$cp_buscar = $_POST['cp_buscar'];
	
	include("../conexion.php");
	if ($accion == 'borrar') {
		$query = "DELETE FROM cp WHERE cp = '$cp' LIMIT 1";
		echo $query;
		mysql_query($query,$conexion);
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
    form.action='lista_cp.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_cp.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.cp.value = id;
	document.forma.accion.value="borrar";
    document.forma.action='lista_cp.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Códigos Postales'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='cp';

   if ($ord=='cp') $orden='ORDER BY cp';
   
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td width="29%" valign="bottom"><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar C&oacute;digo Postal" onClick="document.forma.action='abc_cp.php?nuevo=1'; document.forma.submit();" /></td>
            <td width="71%" align="right"><table width="500" border="0" align="left" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td width="156"><div align="right">CP:</div></td>
                <td width="594"><input name="cp_buscar" type="text" class="campo" id="cp_buscar" value="<?= $cp_buscar;?>" size="6" maxlength="5" /></td>
              </tr>
              <tr>
                <td><div align="right">Estado:</div></td>
                <td><select name="estado" class="campo" id="estado">
                    <option value="">Cualquier estado...</option>
                    <?  
					$resultadoEDO = mysql_query("SELECT * FROM estado ORDER BY clave",$conexion);
					while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
					  echo '<option value="'.$rowEDO['clave'].'"';
					  if ($rowEDO['clave']==$estado) echo 'selected';
				  	  echo '>'.$rowEDO['nombre'].'</option>';
				    }
				  ?>
                </select></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input name="Submit2" type="submit" class="boton" onclick="document.forma.numpag.value=1" value="Buscar" /></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2">*Si no lleva SKU se asume que no hay costo en el env&iacute;o.</td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE 1 ";


					 if (!empty($estado))
					 	$condicion.= " AND cp.estado='$estado'";
					 if (!empty($cp_buscar))
					 	$condicion.= " AND cp.cp='$cp_buscar'";

                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT 1 FROM cp $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de Códigos Postales en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="cp" type="hidden" id="cp" />
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
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><div align="center"><strong>C.P.</strong></div></td>
            <td nowrap="nowrap"><div align="center"><strong>Estado </strong></div></td>
            <td nowrap="nowrap"><div align="center"><strong>Ciudad</strong></div></td>
            <td><strong>Trans Zone</strong></td>
            <td><div align="center"><strong>Entrega<br />
            Low Dom</strong></div></td>
            <td><div align="center"><strong>Entrega<br />
              Low Ocurre</strong></div></td>
            <td><div align="center"><strong>Entrega<br />
            LTL Dom</strong></div></td>
            <td><div align="center"><strong>Entrega<br />
            LTL Ocurre</strong></div></td>
            <td><div align="center"><strong>SKU<br />
            Low</strong>*</div></td>
            <td><div align="center"><strong>SKU<br />
            LTL</strong>*</div></td>
            <td><div align="center"><strong>Cedis Origen<br />
            LTL</strong></div></td>
            <td><div align="center"><strong>Sucursal<br />
              Almex</strong></div></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
			 $query = "SELECT cp.*, estado.nombre AS nombre_estado, sucursal_ocurre.nombre AS nombre_sucursal FROM cp 
			 			LEFT JOIN estado ON cp.estado = estado.clave
						LEFT JOIN sucursal_ocurre ON cp.sucursal_ocurre = sucursal_ocurre.clave
						$condicion $orden LIMIT $regini,$ver";

             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			 	$cve_cp = $row['clave'];
				


          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF" align="center"><?= $row['cp']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['nombre_estado']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['ciudad']; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?=$row['trans_zone'];?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= ($row['low_dom']) ? 'SI' : 'NO' ;?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= ($row['low_ocu']) ? 'SI' : 'NO' ;?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= ($row['ltl_dom']) ? 'SI' : 'NO' ;?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?= ($row['ltl_ocu']) ? 'SI' : 'NO' ;?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?=$row['sku_low'];?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?=$row['sku_ltl'];?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?=$row['cedis_origen_ltl'];?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><?=substr($row['nombre_sucursal'],0,25);?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><a href="abc_cp.php?cp=<?= $row['cp']; ?>"><img src="images/editar.png" alt="Editar cp" width="14" height="16" border="0" align="absmiddle" /></a>
       	  		<? if ($rel<=0) { ?><a onclick="return confirm('¿Estás seguro que deseas\nBorrar el Código Postal?')" href="javascript:borra('<?= $row['cp']; ?>');"><img src="images/borrar.png" alt="Borrar cp" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>            </td>
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
