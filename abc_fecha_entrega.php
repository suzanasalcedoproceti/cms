<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=16;
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
  function valida() {
   if (document.forma.tipo_entrega_f.checked) {
   	  if (document.forma.dias_entrega.value == '') {
	  	alert ("Para fecha de entrega fija, indica los días de entrega deseados");
		document.forma.dias_entrega.focus();
		return;
	  }
   	  if (document.forma.label_entrega.value == '') {
	  	alert ("Para fecha de entrega fija, indica la etiqueta a mostrar al usuario (ej. 15 a 20 días)");
		document.forma.label_entrega.focus();
		return;
	  }
   }
   document.forma.action='graba_fecha_entrega.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='principal.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Especificar cálculo de tiempos de entrega y disponibilidad'; include('top.php'); ?>
	<?
        include('../conexion.php');
		
        $resultado= mysql_query("SELECT * FROM config WHERE reg = 1 ",$conexion);
        $row = mysql_fetch_array($resultado);

      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td colspan="4"><strong>C&aacute;lculo de fecha de entrega de PRODUCTOS</strong></td>
          </tr>
          <? if (0) { ?>
          <tr>
            <td width="15%"><div align="right">M&eacute;todo:</div></td>
            <td colspan="3"><input type="radio" name="tipo_entrega" id="tipo_entrega_v" value="V" <? if ($row['tipo_entrega'] == 'V') echo 'checked';?> />
            Calculado por existencias en CEDIS considerando domicilio de entrega</td>
          </tr>
          <? } ?>
          <tr>
            <td width="15%"><div align="right">M&eacute;todo:</div></td>
            <td width="15%"><input type="radio" name="tipo_entrega" id="tipo_entrega_f" value="F"  <? if ($row['tipo_entrega'] == 'F') echo 'checked';?> />
              Fijo, estableciendo            </td>
            <td width="10%">Para <strong>Low Ticket</strong> </td>
            <td width="60%"><input name="dias_entrega" type="text" class="campo" id="dias_entrega" value="<?=$row['dias_entrega'];?>" size="5" maxlength="3" onblur="this.value = this.value.replace(/[^0-9]+/g,'');" />
d&iacute;as, desplegando:
<input name="label_entrega" type="text" class="campo" id="label_entrega" value="<?=$row['label_entrega'];?>" size="40" maxlength="50" />
(ej. &quot;15 a 20 d&iacute;as&quot;)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Para <strong>LTL</strong></td>
            <td><input name="dias_entrega_ltl" type="text" class="campo" id="dias_entrega_ltl" value="<?=$row['dias_entrega_ltl'];?>" size="5" maxlength="3" onblur="this.value = this.value.replace(/[^0-9]+/g,'');" />
d&iacute;as, desplegando:
<input name="label_entrega_ltl" type="text" class="campo" id="label_entrega_ltl" value="<?=$row['label_entrega_ltl'];?>" size="40" maxlength="50" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4"><strong>C&aacute;lculo de fecha de entrega de COMBOS (Low y LTL)</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3"><input name="dias_entrega_combo" type="text" class="campo" id="dias_entrega_combo" value="<?=$row['dias_entrega_combo'];?>" size="5" maxlength="3" onblur="this.value = this.value.replace(/[^0-9]+/g,'');" />
              d&iacute;as, desplegando:
              <input name="label_entrega_combo" type="text" class="campo" id="label_entrega_combo" value="<?=$row['label_entrega_combo'];?>" size="40" maxlength="50" /></td>
          </tr>
          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4"><strong>C&aacute;lculo de disponibilidad de productos</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3"><p>Los productos podr&aacute;n venderse en TW cuando la suma de las existencias en todos los CEDIS sea mayor que
                <input name="disponibilidad_venta" type="text" class="campo" id="disponibilidad_venta" value="<?=$row['disponibilidad_venta'];?>" size="5" maxlength="3" onblur="this.value = this.value.replace(/[^0-9]+/g,'');" />
              <br />
              (AHORA SE CONFIGURA EN LAS CATEGOR&Iacute;AS DE PRODUCTOS. ESTE DATO SE IGNORA)
            </p>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="4"><strong>PORCENTAJE FIJO DE PUNTOS PARA EMPLEADOS WHIRLPOOL</strong></td>
          </tr>
          <tr>
            <td><div align="right">Puntos:</div></td>
            <td colspan="3"><input name="puntos_global" type="text" class="campo" id="puntos_global" value="<?= $row['puntos_global']; ?>" size="3" maxlength="3" />
 %          </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3"><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="DESCARTAR" id="desc" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
