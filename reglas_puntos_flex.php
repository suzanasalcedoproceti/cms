<?php
    if (!include('ctrl_acceso.php')) return;
	include('../conexion.php');
	include('funciones.php');
	include("funciones_ajax.php");
	$modulo=19;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}

	require ('xajax/xajax_core/xajax.inc.php');
	$xajax = new xajax(); 
	$xajax->register(XAJAX_FUNCTION, 'cambia_categoria_uni'); 
	$xajax->register(XAJAX_FUNCTION, 'cambia_categoria_rep'); 
	$xajax->register(XAJAX_FUNCTION, 'test'); 
	$xajax->processRequest(); 
	$xajax->configure('javascript URI','xajax/'); 


	
   $numpag = mysql_real_escape_string($_POST['numpag']);
   $ver = mysql_real_escape_string($_POST['ver']);
   $ord = $_POST['ord'];
   $accion = mysql_real_escape_string($_POST['accion']);

   if (empty($ver)) $ver='20';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   if ($ord=='nombre') $orden='ORDER BY nombre';
   
   include_once('lib.php');
   
   if ($accion == 'borrar') {
   	$regla = (int) mysql_real_escape_string($_POST['regla']);
	$resultado=mysql_query("DELETE FROM regla_puntos WHERE clave = $regla LIMIT 1");
	$resultado=mysql_query("DELETE FROM regla_puntos_detalle WHERE regla = $regla");
	
   }
   	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>

<link href="css/jquery-ui.css" rel="stylesheet">
<script src="js/jquery_1.10.js"></script>
<script src="js/jquery-ui.js"></script>

<?php 
 $xajax->printJavascript("xajax/"); 
?>
</head>

<body>
<div id="container">
	<? $tit='Administrar Reglas de Puntos Flex/PEP'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input type="hidden" name="accion" />
        <input type="hidden" name="regla" value="<?=$regla;?>" />
        <table width="40%" border="0" align="center" cellpadding="3" cellspacing="2">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><b>Categor&iacute;a </b></td>
            <td align="center" nowrap="nowrap"><b>Unidades disponibles Flex/PEP</b></td>
            <td align="center"><strong>Permitir repetir SKU</strong></td>
          </tr>

          <?php

		  	 $hoy=date('Y-m-d');

             $resultado= mysql_query("SELECT * FROM categoria ORDER BY nombre",$conexion);
             while ($row = mysql_fetch_array($resultado)) {

          ?>
		 <tr class="texto"  bgcolor="#FFFFFF">
            <td><?php echo $row['nombre']; ?></td>
            <td align="center"><input name="unidades_<?=$row['clave'];?>" type="text" class="campo numerico" id="unidades_<?=$row['clave'];?>" size="5" maxlength="3" value="<?=$row['unidades_disponibles_flex_pep'];?>" onchange="xajax_cambia_categoria_uni(<?=$row['clave'];?>,this.value);// alert('hola');" onblur="this.value = this.value.replace(/[^0-9]+/g,'');" /></td>
            <td align="center"><input type="checkbox" name="repetir_<?=$row['clave'];?>" id="repetir_<?=$row['clave'];?>" <? if ($row['repetir_sku_flex_pep']) echo 'checked';?> onchange="xajax_cambia_categoria_rep(<?=$row['clave'];?>,(this.checked) ? '1' : '0')"/></td>
          </tr>
          <?
                 } // WHILE
              ?>
        </table> 
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td align="right">&nbsp;</td>
          </tr>

          
        </table>
      </form>    
    </div>
</div>
</body>
</html>
