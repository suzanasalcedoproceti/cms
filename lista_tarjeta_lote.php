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
<script type="text/javascript" src="js/menu.js"></script>

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='lista_tarjeta_lote.php';
    form.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Listado de Tarjetas'; include('top.php'); ?>
  <?
  
   $lote = $_GET['lote'];
   if (empty($lote)) $lote = $_POST['lote'];
   
   $estado = $_POST['estado']+0;
   $codigo = $_POST['codigo'];
   
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];

   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='clave';

   if     ($ord=='clave') $orden='ORDER BY clave';

   include('../conexion.php');


	 $resTAR= mysql_query("SELECT * FROM tarjeta WHERE lote='$lote' LIMIT 1",$conexion);
	 $rowTAR= mysql_fetch_array($resTAR);
	
	$empresa=$rowTAR['empresa'];
	$resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
	$rowEMP= mysql_fetch_array($resEMP);

?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><table width="100%" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td><div align="right">Empresa:</div></td>
                <td><strong><?= $rowEMP['nombre']; ?></strong></td>
              </tr>
              <tr>
                <td><div align="right">Estado de las tarjetas:</div></td>
                <td><select name="estado" class="campo" id="estado">
                    <option value="">Cualquier estado...</option>
                    <option value="1" <? if ($estado==1) echo 'selected'; ?>>Tarjetas sin usar</option>
                    <option value="2" <? if ($estado==2) echo 'selected'; ?>>Tarjetas usadas</option>
                </select></td>
              </tr>
              <tr>
                <td><div align="right">C&oacute;digo:</div></td>
                <td><input name="codigo" type="text" class="campo" id="codigo" value="<?= $codigo; ?>" size="20" maxlength="10" /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input name="Submit" type="submit" class="boton" onclick="document.forma.numpag.value=1" value="Buscar" />
                <input name="lote" type="hidden" id="lote" value="<?= $lote; ?>" /></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE lote='$lote' ";
					 
					 if (!empty($estado)) {
					 
					 	if ($estado==1)
					 		$condicion .= "AND cliente='' ";
					 	elseif ($estado==2)
					 		$condicion .= "AND cliente!='' ";
				     }

					 if (!empty($codigo))
					 	$condicion .= "AND codigo LIKE '%$codigo%' ";
					 
                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT * FROM tarjeta $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de tarjetas en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB"><input name="cliente" type="hidden" id="cliente" />
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
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td><div align="center"><b>#</b></div></td>
            <td><div align="center"><b>C&oacute;digo</b></div></td>
            <td><b>Empresa</b></td>
            <td><div align="left"><b>Cliente</b></div></td>
            <td><div align="center"><strong>Fecha activaci&oacute;n</strong></div></td>
          </tr>
          <?

			 $num=($numpag-1)*20;

             $resultado= mysql_query("SELECT * FROM tarjeta $condicion $orden LIMIT $regini,$ver",$conexion);
             while ($row = mysql_fetch_array($resultado)) {
			 	$num++; 

				$empresa=$row['empresa'];
                $resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
                $rowEMP= mysql_fetch_array($resEMP);

				$cliente=$row['cliente'];
				$resCLI= mysql_query("SELECT *, CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) AS nombre FROM cliente WHERE clave='$cliente'",$conexion);
                $rowCLI= mysql_fetch_array($resCLI);



          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><div align="center">
              <?= $num; ?>
            </div></td>
            <td bgcolor="#FFFFFF"><div align="center">
              <?= $row['codigo']; ?>
            </div></td>
            <td bgcolor="#FFFFFF">
              <?= $rowEMP['nombre']; ?>            </td>
            <td bgcolor="#FFFFFF">
              <div align="left">
                <? if (!$row['ilimitada']) echo $rowCLI['nombre']; ?>
              </div></td>
            <td align="center" bgcolor="#FFFFFF"><? if ($row['fecha']!='0000-00-00 00:00:00' && !$row['ilimitada']) echo date('d/m/Y H:i:s',strtotime($row['fecha'])); else echo '&nbsp;'; ?></td>
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
