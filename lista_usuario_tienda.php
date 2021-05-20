<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=17;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
   include('../conexion.php');
   $usuario=$_POST['usuario'];
   $tienda=$_POST['tienda']+0;
   if ($_POST['accion'] == 'borrar') {
   		$resultado = mysql_query("DELETE FROM usuario_tienda WHERE clave = $usuario LIMIT 1",$conexion);
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
    form.action='lista_usuario_tienda.php';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='lista_usuario_tienda.php';
    document.forma.submit();
  }
  function borra(id) {
    document.forma.usuario.value = id;
	document.forma.accion.value='borrar';
    document.forma.action='lista_usuario_tienda.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
  <? $tit='Listado de Usuarios'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   if     ($ord=='nombre') $orden='ORDER BY nombre';
   
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td valign="bottom"><input name="button" type="submit" class="boton_agregar" id="button" value="Agregar usuario nuevo" onClick="document.forma.action='abc_usuario_tienda.php'; document.forma.submit();" /></td>
            <td><table width="500" border="0" align="center" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td width="156"><div align="right">Tienda:</div></td>
                <td width="594"><span class="row1">
                  <select name="tienda" class="campo" id="tienda">
                    <option value="" selected="selected">Cualquiera...</option>
                    <?
                    $condicion_tienda = ($_SESSION['usr_service']==1) ? 'WHERE tienda_service=1 ' : '';
					$resultadoTIE = mysql_query("SELECT * FROM tienda $condicion_tienda ORDER BY nombre",$conexion);
					while ($rowTIE = mysql_fetch_array($resultadoTIE)) {
					  echo '<option value="'.$rowTIE['clave'].'"';
					  if ($rowTIE['clave']==$tienda) echo 'selected';
				  	  echo '>'.$rowTIE['nombre'].'</option>';
				    }
			  ?>
                  </select>
                </span></td>
              </tr>

              <tr>
                <td align="left">&nbsp;</td>
                <td><input name="Submit" type="submit" class="boton" onclick="document.forma.numpag.value=1" value="Buscar" /></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
            $condicion = ($_SESSION['usr_service']==1) ? 'WHERE tienda_service=1 ' : 'WHERE 1=1 ';
					 
					 if ($tienda) $condicion .= ' AND usuario_tienda.tienda = '.$tienda;

                     // construir la condición de búsqueda
					   
					   $query = "SELECT * 
                    FROM usuario_tienda 
                    LEFT JOIN tienda ON usuario_tienda.tienda = tienda.clave 
                    $condicion";
					   
                       $resultadotot= mysql_query($query,$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de usuarios en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB"><input name="usuario" type="hidden" id="usuario" />
                <input type="hidden" name="accion" value="" />
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
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap" bgcolor="<? if($ord=='nombre') echo '#DDDDDD'; ?>"><b><a href="javascript:ordena('nombre');" class="texto">Nombre <img src="images/orden.png" width="14" height="15" border="0" align="absmiddle" /></a></b></td>
            <td nowrap="nowrap"><strong>Tienda</strong></td>
            <td nowrap="nowrap"><b>Login</b></td>
            <td nowrap="nowrap"><b>Contrase&ntilde;a</b></td>
            <td><div align="center"><strong>Estatus</strong></div></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?

             $resultado= mysql_query("SELECT usuario_tienda.*, tienda.nombre AS nombre_tienda 
			 							FROM usuario_tienda 
										LEFT JOIN tienda ON usuario_tienda.tienda = tienda.clave 
										$condicion $orden LIMIT $regini,$ver",$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?= $row['nombre']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['nombre_tienda']; ?></td>
            <td bgcolor="#FFFFFF"><?= $row['login']; ?></td>
            <td bgcolor="#FFFFFF">***********</td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF"><? if ($row['activo']==1) echo 'ACTIVO'; else echo 'INACTIVO'; ?></td>
            <td align="center" nowrap="nowrap" bgcolor="#FFFFFF">
               	<a href="abc_usuario_tienda.php?usuario=<?= $row['clave']; ?>"><img src="images/editar.png" alt="Editar Usuario" width="14" height="16" border="0" align="absmiddle" /></a>
	       	   <a onclick="return confirm('¿Estás seguro que deseas\nBorrar el Usuario?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" alt="Borrar Usuario" width="14" height="15" border="0" align="absmiddle" /></a>			 </td>
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
