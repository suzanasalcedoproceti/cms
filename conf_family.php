<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=14;
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
   if (document.forma.dias_vigencia_invitados.checked) {
  	alert ("Indica los días de vigencia para que los invitados accedan a la tienda");
	document.forma.dias_vigencia_invitados.focus();
	return;
  }
  if (document.forma.limite_invitados.value == '') {
  	alert ("Ingresa el límite de personas que puede invitar un empleado");
	document.forma.limite_invitados.focus();
	return;
  }
   document.forma.action='graba_conf_family.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='principal.php';
   document.forma.submit();
  }
  function resetea() {
    continuar = window.confirm("Deseas resetear los empleados disponibles de todas la(s) empresa seleccionada?");
    if (!continuar) {
           return;
    }
   
   document.forma.action='conf_reset_invitados.php';
   document.forma.submit();
  }
</script></head>

<body>
<div id="container">
	<? $tit='Configuración de Family & Friends'; include('top.php'); ?>
	<?
        include('../conexion.php');
		
        $resultado= mysql_query("SELECT * FROM config WHERE reg = 1 ",$conexion);
        $row = mysql_fetch_array($resultado);

      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td colspan="2"><strong>Datos de configuraci&oacute;n</strong></td>
          </tr>
          <tr>
            <td width="15%">&nbsp;</td>
            <td width="85%">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Vigencia:</div></td>
            <td>            <input name="dias_vigencia_invitados" type="text" class="campo" id="dias_vigencia_invitados" value="<?=$row['dias_vigencia_invitados'];?>" size="3" maxlength="3" onblur="this.value = this.value.replace(/[^0-9]+/g,'');" />
              d&iacute;as para que el invitado ingrese a la tienda, a partir de la fecha en que fue invitado.</td>
          </tr>
          <tr>
            <td><div align="right">L&iacute;mite:</div></td>
            <td><input name="limite_invitados" type="text" class="campo" id="limite_invitados" value="<?=$row['limite_invitados'];?>" size="3" maxlength="3" onblur="this.value = this.value.replace(/[^0-9]+/g,'');" /> 
              personas invitadas por empleado en el periodo.</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar2" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc2" type="button" class="boton" onclick="descarta();" value="DESCARTAR" id="desc2" />            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><strong>Acciones</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><p><span class="row1">
              <select name="empresa" class="campo" id="empresa">
                <option value="" selected="selected">De todas las empresas</option>
                <?
				$resEMP = mysql_query("SELECT clave, nombre FROM empresa WHERE invita_amigos = 1 ORDER BY nombre",$conexion);
				while ($rowEMP = mysql_fetch_array($resEMP)) {
				  echo '<option value="'.$rowEMP['clave'].'"';
				  if ($rowEMP['clave']==$empresa) echo ' selected';
				  echo '>'.$rowEMP['nombre'].'</option>';
				}
			  ?>
              </select>
              </span>
              <input name="grabar2" type="button" class="boton" onclick="resetea();" value="Resetear invitados restantes" />
              <br />
              Aplica para  los empleados de la empresa seleccionada, restablece el valor al l&iacute;mite definido (
              <?=$row['limite_invitados'];?> 
            invitados).</p>
            </td></tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
