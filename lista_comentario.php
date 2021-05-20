<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=12;
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
    form.action='lista_comentario.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_comentario.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.comentario.value = id;
    document.forma.action='borra_comentario.php';
    document.forma.submit();
  }
  function activa(id,accion) {
    document.forma.comentario.value = id;
	document.forma.accion.value = accion;
    document.forma.action='activa_comentario.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Listado de Comentarios a Productos'; include('top.php'); ?>
  <?
  
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   $inactivos = $_POST['inactivos'];
  
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='fecha';

   if     ($ord=='fecha') $orden='ORDER BY fecha, clave DESC';
   elseif ($ord=='fecha') $orden='ORDER BY fecha';
   
   include('../conexion.php');
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td width="250"><input type="checkbox" name="inactivos" id="inactivos" value="1" onclick="document.forma.numpag.value=1; document.forma.submit();" <? if ($inactivos==1) echo 'checked="checked"'; ?> />
              Mostrar s&oacute;lo comentarios inactivos.</td>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE 1=1 ";
					 
					 if (!empty($inactivos))
					 	$condicion .= " AND activo=0";

                     // construir la condición de búsqueda

                       $resultadotot= mysql_query("SELECT * FROM comentario $condicion",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de comentarios en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB"><input name="comentario" type="hidden" id="comentario" />
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
            <td align="center" nowrap="nowrap"><b>Fecha</b></td>
            <td nowrap="nowrap"><b>Cliente</b></td>
            <td nowrap="nowrap"><strong>Empresa</strong></td>
            <td nowrap="nowrap"><b>Producto</b></td>
            <td nowrap="nowrap"><strong>Comentario</strong></td>
            <td nowrap="nowrap"><div align="center"><strong>Calificaci&oacute;n</strong></div></td>
            <td nowrap="nowrap"><div align="center"><strong>Activo</strong></div></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
		  	 $hoy=date('Y-m-d');

             $resultado= mysql_query("SELECT * FROM comentario $condicion $orden LIMIT $regini,$ver",$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

				$cliente=$row['cliente'];
				$resCLI= mysql_query("SELECT *, CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) AS nombre FROM cliente WHERE clave='$cliente'",$conexion);
				$rowCLI= mysql_fetch_array($resCLI);

				$producto=$row['producto'];
				$resPRO= mysql_query("SELECT * FROM producto WHERE clave='$producto'",$conexion);
				$rowPRO= mysql_fetch_array($resPRO);
				
				$empresa = $rowCLI['empresa'];
				$resEMP = mysql_query("SELECT nombre FROM empresa WHERE clave = $empresa");
				$rowEMP = mysql_fetch_array($resEMP);

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><div align="center">
              <?= date('d/m/Y',strtotime($row['fecha'])); ?>
            </div></td>
            <td bgcolor="#FFFFFF"><?= $rowCLI['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowEMP['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= '<strong>'.$rowPRO['modelo'].'</strong><br> '.$rowPRO['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= str_replace(chr(10),'<br>',$row['comentario']); ?></td>
            <td bgcolor="#FFFFFF"><div align="center"><img src="images/icons/<?= $row['calificacion']; ?>star.gif" /></div></td>
            <td bgcolor="#FFFFFF"><div align="center">
              <? if ($row['activo']==1) echo '<input name="p'.$row['clave'].'" type="checkbox" value="1" checked="checked" onClick="activa(\''.$row['clave'].'\',0);">';
	     			else echo '<input name="p'.$row['clave'].'" type="checkbox" value="0" onClick="activa(\''.$row['clave'].'\',1);">'; ?>
            </div></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
	       	  <a onclick="return confirm('¿Estás seguro que deseas\nBorrar el Comentario?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar Comentario" width="14" height="15" border="0" align="absmiddle" /></a>          </tr>
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
