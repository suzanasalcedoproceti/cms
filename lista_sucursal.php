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
    form.action='lista_sucursal.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_sucursal.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.sucursal.value = id;
    document.forma.action='borra_sucursales.php';
    document.forma.submit();
  }
  function exportar() {
    document.forma.target = '_self';
    document.forma.action='lista_sucursal_xls.php';
    document.forma.numpag.value=1;
    document.forma.submit();
    document.forma.target = '_self';
    document.forma.action='';
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Sucursales'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $estado = $_POST['estado'];
   $idestado = $_POST['idEstado'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='estado';

   if ($ord=='nombre') $orden='ORDER BY nombresucursal';
   if ($ord=='estado') $orden='ORDER BY cve_estado, nombresucursal';
   
   include('../conexion.php');
 
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar sucursal nueva" onClick="document.forma.action='abc_sucursal.php'; document.forma.submit();" /></td>
            <td><div align="left">
                  <input name="exp_xls" type="button" class="boton" onclick="javascript:exportar();" value="Descargar Registros" />
                </div></td>
          </tr>
          <tr>
            <td>Estado:
              <select name="estado" class="campo" id="estado" onchange="document.forma.submit();">
                <option value="">Cualquier estado...</option>
                  <?  
					$resultadoEDO = mysql_query("SELECT * FROM estados ORDER BY estado",$conexion);
					while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
					  echo '<option value="'.$rowEDO['idEstado'].'"';
					  if ($rowEDO['idEstado']==$estado) echo 'selected';
				  	  echo '>'.$rowEDO['estado'].'</option>';
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
                     $condicion = "WHERE 1 ";


					 if (!empty($estado))
					 	$condicion.= " AND estados.cve_estado='$estado'";



                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT 1 FROM sucursales $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de sucursales en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="sucursal" type="hidden" id="sucursal" />
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
        <table width="100%" border="0" align="center" cellpadding="5" cellspacing="3">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" ><div align="center"><strong>ID Sucursal</strong></div></td>
            
            <td align="center" nowrap="nowrap" bgcolor="<? if($ord=='nombre') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('sucursal_ocurre');" class="texto">Sucursal  <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>

            <td align="center" nowrap="nowrap" bgcolor="<? if($ord=='estado') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('estado');" class="texto">Direcci&oacute;n <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>

            <td nowrap="nowrap" ><div align="center"><strong>Planta</strong></div></td>
            <td nowrap="nowrap" ><div align="center"><strong>SL</strong></div></td>
         
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
			      $query = "SELECT sucursales.*, estados.estado AS nombre_estado FROM sucursales INNER JOIN estados ON sucursales.cve_estado = estados.cve_estado
						$condicion $orden LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			    	  $idsucursal = $row['idsuc'];
              $cve_mpio = $row['cve_mnpio'];
              $cve_edo = $row['cve_estado'];

            $resultadoMpo = mysql_query("SELECT mnpio FROM municipios WHERE cve_mnpio = $cve_mpio and cve_estado=$cve_edo",$conexion);
            $rowmpo = mysql_fetch_assoc($resultadoMpo);
            $nombrempio = $rowmpo['mnpio'];
            if($row['numext'] <> ""){$numerex= "  #".$row['numext']; }else{$numerex="  ";}
            if($row['numint']  <> ""){$numerin= "  #".$row['numint']. "(int)";}else{$numerin= "  ";}
            $direccionsuc=$row['calle']. "   ".$numerex.  "   " .$numerin. "  CP. ". $row['cp'].  "  Col.". $row['colonia'] ." , ". $nombrempio . ", ". $row['nombre_estado'];
			    /*$res = mysql_query("SELECT 1 FROM direccion_envio WHERE sucursal_ocurre=$cve_sucursal_ocurre",$conexion);
			    $rel = mysql_num_rows($res);
				*/

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF" align="center"><?= $row['idsuc']; ?></td>            
            <td bgcolor="#FFFFFF"><?= $row['nombresucursal']; ?></td>
            <td align="left" nowrap="nowrap" bgcolor="#FFFFFF"><?= $direccionsuc ;?></td>
                          <td bgcolor="#FFFFFF" align="center"><?= $row['planta']; ?></td> 
              <td bgcolor="#FFFFFF" align="center"><?= $row['SL']; ?></td> 
           
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><a href="abc_sucursal.php?sucursal=<?= $row['idSucursal']; ?>"><img src="images/editar.png" alt="Editar Sucursal" width="14" height="16" border="0" align="absmiddle" /></a>
       	  		<? if ($rel<=0) { ?><a onclick="return confirm('\u00bfEst\u00e1s seguro que deseas\n Borrar el registro?')" href="javascript:borra('<?= $row['idsuc']; ?>');"><img src="images/borrar.png" alt="Borrar Sucursal" width="14" height="15" border="0" align="absmiddle" /></a><? } else  { ?><img src="images/borrar_off.png" width="14" height="15" align="absmiddle" /><? } ?>            </td>

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
