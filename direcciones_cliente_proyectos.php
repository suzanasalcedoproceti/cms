<?php
// Cambios
// Enero 2016
//	  Administrar direcciones de envío de clientes

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=8;
    include('../conexion.php');
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
   if ($_POST['accion'] == 'borrar') {
   		$cliente = $_POST['cliente']+0;
		$direccion = $_POST['direccion']+0;
   		$resultado = mysql_query("DELETE FROM direccion_envio WHERE cliente = $cliente AND clave = $direccion LIMIT 1",$conexion);
   }
   // obtener datos de configuracion
   $resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
   $rowCFG = mysql_fetch_array($resultadoCFG);
   $cliente=$_GET['cliente']+0;
   if (empty($cliente)) $cliente=$_POST['cliente']+0;

   if ($cliente<=0) {
	   header('Location: lista_cliente_proyectos.php');
	   return;
   }
   $resultado= mysql_query("SELECT * FROM cliente WHERE clave=$cliente",$conexion);
   $row = mysql_fetch_array($resultado);
   $empresa=$row['empresa'];
   $resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
   $rowEMP= mysql_fetch_array($resEMP); 
   if ($rowEMP['empresa_proyectos']!=1) {
	   header('Location: lista_cliente_proyectos.php');
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
    form.action='lista_cliente_proyectos.php';
    form.submit();
  }
  function borra(direccion) {
    document.forma.direccion.value = direccion;
	document.forma.accion.value = 'borrar';
    document.forma.action='direcciones_cliente_proyectos.php';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Listado de Domicilios de entrega de '.$row['nombre']." ".$row['apellido_paterno']; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input name="cliente" type="hidden" id="cliente" value="<?=$cliente;?>">
        <input name="direccion" type="hidden" id="direccion">
        <input name="accion" type="hidden" id="accion">
        <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
        <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />


        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td nowrap="nowrap"><p>
              <input name="button" type="submit" class="boton_agregar" id="button" value="Agregar dirección nueva" onclick="document.forma.action='abc_dir_cliente_proyectos.php?cliente=<?=$cliente;?>'; document.forma.submit();" />
            </p>
            <p>
              <input name="breg" type="submit" class="boton" id="button2" value="Regresar" onclick="document.forma.action='lista_cliente_proyectos.php'; document.forma.submit();" />
            </p></td>
            <td align="right">&nbsp;</td>
          </tr>
          
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                       $resultadotot= mysql_query("SELECT * FROM direccion_envio WHERE cliente = $cliente",$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de clientes en la lista: <b>'.$totres.'</b>';
			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
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
                     echo "P&aacute;gina ";
					 echo '<input type="text" name="pagina" value="'.$numpag.'" size="2" onchange="javascript:ir(document.forma,this.value);" style="text-align:center"/>';
					 echo " de ".$totpags;
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
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><strong>Ship-to</strong></td>
            <td nowrap="nowrap"><b>Alias</b></td>
            <td nowrap="nowrap"><b>Nombre</b></td>
            <td><b>Estado</b></td>
            <td align="center"><strong>Ciudad</strong></td>
            <td align="center"><strong>Calle</strong></td>
            <td><div align="center"><b>Acci&oacute;n</b></div></td>
          </tr>
          <?
			 $query = "SELECT * FROM direccion_envio WHERE cliente = $cliente ORDER BY alias LIMIT $regini,$ver";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			 	
          ?>
          <tr class="texto" valign="top" bgcolor="#FFFFFF">
            <td><?= $row['ship_to'];?></td>
            <td><?= $row['alias']; ?></td>
            <td><?= $row['nombre']; ?></td>
            <td><?= $row['estado'] ?></td>
            <td><?= $row['ciudad'] ?></td>
            <td><?= $row['calle']." ".$row['exterior']; ?></td>
            <td align="center" nowrap="nowrap">
	            <a href="abc_dir_cliente_proyectos.php?direccion=<?=$row['clave'];?>"><img src="images/editar.png" title="Editar Dirección" width="14" height="16" border="0" align="absmiddle" /></a>
                <a onclick="return confirm('¿Estás seguro que deseas borrar la dirección de entrega?')" href="javascript:borra('<?= $row['clave']; ?>');"><img src="images/borrar.png" title="Borrar Dirección" width="14" height="15" border="0" align="absmiddle" /></a></td>
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
			 echo "P&aacute;gina ";
			 echo '<input type="text" name="pagina2" value="'.$numpag.'" size="2" onchange="javascript:ir(document.forma,this.value);" style="text-align:center"/>';
			 echo " de ".$totpags;
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