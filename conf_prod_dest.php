<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=4;
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
<script src="js/jquery.js" type="text/javascript" language="javascript1.2"></script>

<script language="JavaScript">
  function valida() {
   document.forma.action='graba_conf_prod_dest.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='principal.php';
   document.forma.submit();
  }
</script></head>

<body>
<div id="container">
	<? $tit='Configuración de Productos en Promoción Especial'; include('top.php'); ?>
	<?
        include('../conexion.php');
		
        $resultado= mysql_query("SELECT * FROM config WHERE reg = 1 ",$conexion);
        $row = mysql_fetch_array($resultado);

      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma"  enctype="multipart/form-data">
      
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td colspan="3"><strong>Los productos marcados como &quot;Promoci&oacute;n Especial&quot; tendr&aacute;n el siguiente &iacute;cono y mensaje en la tienda:</strong></td>
          </tr>
          <tr>
            <td width="15%">&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Icono:</div></td>
            <td width="13%"><input name="archivo" type="file" class="campo" id="archivo" size="5" />
            </td>
            <td width="72%">
			   <? if (file_exists("images/btns/icon_promo_especial.gif")) { ?>
                 <img src="images/btns/icon_promo_especial.gif?<?=date("U");?>" />
               <? } ?>
            
            </td>
          </tr>
          <tr>
            <td><div align="right">Mensaje:</div></td>
            <td colspan="2"><input name="mensaje_promocion_especial" type="text" class="campo" id="mensaje_promocion_especial" value="<?=$row['mensaje_promocion_especial'];?>" size="40" maxlength="40" /> 
              (Texto de burbuja sobre el &iacute;cono)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2"><input name="grabar2" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc2" type="button" class="boton" onclick="descarta();" value="DESCARTAR" id="desc2" />            </td>
          </tr>
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
