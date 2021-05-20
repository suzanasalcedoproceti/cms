<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=7;
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

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_tarjeta.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_tarjeta.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Tarjetas'; include('top.php'); ?>
  <?
   $texto = $_POST['texto'];
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='lote';

   if ($ord=='lote') $orden='ORDER BY lote';
   
   include('../conexion.php');
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
<table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
  <tr>
    <td><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar lote nuevo" onclick="document.forma.action='genera_tarjetas.php'; document.forma.submit();" />
      <input name="button2" type="submit" class="boton_agregar" id="button2" value="Agregar tarjeta ilimitada" onclick="document.forma.action='genera_tarjeta_unica.php'; document.forma.submit();" /></td>
    <td><table width="500" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
      <tr>
        <td width="156" valign="top"><div align="right">Buscar:</div></td>
        <td width="594"><div align="left">
            <input name="texto" type="text" class="campo" id="texto" value="<?= $texto; ?>" size="50" />
            <br />
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="left">
            <input name="Submit" type="submit" class="boton" onclick="document.forma.numpag.value=1" value="Buscar" />
        </div></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condici&oacute;n de b&uacute;squeda
                     $condicion = "WHERE 1=1 ";
					 
					  if ($texto) $condicion.= " AND empresa.nombre LIKE '%$texto%' ";
					 
                     // construir la condici&oacute;n de b&uacute;squeda

                       $resultadotot= mysql_query("SELECT count(*) AS total FROM tarjeta LEFT JOIN empresa ON tarjeta.empresa = empresa.clave $condicion GROUP BY lote",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de lotes en la lista: <b>'.$totres.'</b>';
			
			  ?>    </td>
    <td align="right" bgcolor="#BBBBBB"><input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
        <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
        <?


                     $regini = ($numpag * $ver) - $ver;
					 if ($regini<0) $regini=0;

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
              ?>    </td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td><div align="center"><b>Lote</b></div></td>
            <td><strong>Empresa</strong></td>
            <td><div align="center"><b>Tarjetas</b></div></td>
            <td><div align="center"><b>Sin usar</b></div></td>
            <td><div align="center"><b>Usadas</b></div></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

             $resultado= mysql_query("SELECT *, COUNT(*) AS total FROM tarjeta  LEFT JOIN empresa ON tarjeta.empresa = empresa.clave $condicion GROUP BY lote $orden LIMIT $regini,$ver",$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

				$lote=$row['lote'];
				$tarjeta = $row['clave'];
				
				$empresa=$row['empresa'];
                $resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
                $rowEMP= mysql_fetch_array($resEMP);

                $resTAR= mysql_query("SELECT COUNT(*) AS total_sinusar FROM tarjeta WHERE cliente=0 AND lote='$lote'",$conexion);
                $rowTAR= mysql_fetch_array($resTAR);
				 
                $resTAR2= mysql_query("SELECT COUNT(*) AS total_usadas FROM tarjeta WHERE cliente>0 AND lote='$lote'",$conexion);
                $rowTAR2= mysql_fetch_array($resTAR2);
				
                $resTAR3= mysql_query("SELECT COUNT(*) AS total_usadas FROM cliente WHERE tarjeta = $tarjeta",$conexion);
                $rowTAR3= mysql_fetch_array($resTAR3);


          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><div align="center">
                <?= $row['lote']; ?>
            </div></td>
            <td bgcolor="#FFFFFF"><?= $rowEMP['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><div align="center">
                <? 
				  if ($row['ilimitada']) {
						echo $row['codigo'];
				  } else 
					  echo $row['total']; 
				  ?>
            </div></td>
            <td bgcolor="#FFFFFF"><div align="center">
                <? if (!$row['ilimitada']) echo $rowTAR['total_sinusar']; ?>
            </div></td>
            <td bgcolor="#FFFFFF"><div align="center">
                <? if (!$row['ilimitada']) echo $rowTAR2['total_usadas']; // else echo $rowTAR3['total_usadas']; ?>
            </div></td>
            <td align="left" nowrap="nowrap" bgcolor="#FFFFFF" style="padding: 4px 10px 0 20px;" width="1">
            <? if (!$row['ilimitada']) { ?>
            <a href="lista_tarjeta_lote.php?lote=<?= $row['lote']; ?>"><img src="images/foto.png" width="14" height="15" border="0" alt="Ver detalles de las Tarjetas" /></a> <a href="lista_tarjeta_excel.php?lote=<?= $row['lote']; ?>"><img src="images/icon_excel.gif" width="25" height="16" border="0" alt="Exportar c&oacute;digos" /></a>
            <? } ?> </td>
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
            <td align="right" bgcolor="#BBBBBB"><?


                     $regini = ($numpag * $ver) - $ver;
					 if ($regini<0) $regini=0;

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
              ?>            </td>
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
