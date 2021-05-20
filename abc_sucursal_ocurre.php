<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=20;
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
  function isEmail(string) {
    if (string.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)
        return true;
    else
        return false;
  }

  function valida() {
   if (document.forma.estado.value == "") {
     alert("Falta estado.");
	 document.forma.estado.focus();
     return;
   }
   if (document.forma.nombre.value == "") {
     alert("Falta nombre de la sucursal.");
	 document.forma.nombre.focus();
     return;
   }
   if (document.forma.telefonos.value == "") {
     alert("Falta indicar los teléfonos.");
	 document.forma.telefonos.focus();
     return;
   }
   if (document.forma.direccion.value == "") {
     alert("Falta la dirección");
	 document.forma.direccion.focus();
     return;
   }
   if (document.forma.email.value == "") {
     alert("Falta el correo electrónico.");
	 document.forma.email.focus();
     return;
   }
   if (!isEmail(document.forma.email.value)) {
     alert("Correo electrónico inválido.");
	 document.forma.email.focus();
     return;
   }
   document.forma.action='graba_sucursal_ocurre.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_sucursal_ocurre.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_sucursal_ocurre.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Sucursales Almex'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$sucursal=$_POST['sucursal'];
		if (empty($sucursal)) $sucursal=$_GET['sucursal'];
        
        if (!empty($sucursal)) {
          $resultado= mysql_query("SELECT * FROM sucursal_ocurre WHERE clave='$sucursal'",$conexion);
          $row = mysql_fetch_array($resultado);
		  $estado=$row['estado'];
        }
        
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Sucursal:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="115" maxlength="100" />            </td>
          </tr>
          <tr>
            <td><div align="right">Tel&eacute;fonos:</div></td>
            <td><label>
              <input name="telefonos" type="text" class="campo" id="telefonos" value="<?=$row['telefonos'];?>" size="90" maxlength="50" />
            </label></td>
          </tr>
          <tr>
            <td><div align="right">Fax:</div></td>
            <td><input name="fax" type="text" class="campo" id="fax" value="<?=$row['fax'];?>" size="90" maxlength="50" /></td>
          </tr>
          <tr>
            <td><div align="right">Direcci&oacute;n completa<br />
            (Para mostrar al cliente)</div></td>
            <td><textarea name="direccion" cols="90" rows="3" class="campo" id="direccion"><?=$row['direccion'];?></textarea></td>
          </tr>
          <tr>
            <td><div align="right">Calle y N&uacute;mero:</div></td>
            <td><input name="calle" type="text" class="campo" id="calle" value="<?= $row['calle']; ?>" size="50" maxlength="35" /></td>
          </tr>
          <tr>
            <td><div align="right">Colonia:</div></td>
            <td><input name="colonia" type="text" class="campo" id="colonia" value="<?= $row['colonia']; ?>" size="50" maxlength="35" /></td>
          </tr>
          <tr>
            <td><div align="right">Ciudad:</div></td>
            <td><input name="ciudad" type="text" class="campo" id="ciudad" value="<?= $row['ciudad']; ?>" size="50" maxlength="35" /></td>
          </tr>
          <tr>
            <td><div align="right">Estado:</div></td>
            <td><select name="estado" class="campo" id="estado">
                <option value="" selected="selected">Selecciona estado...</option>
                <?
					$resultadoEDO = mysql_query("SELECT * FROM estado ORDER BY clave",$conexion);
					while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
					  echo '<option value="'.$rowEDO['clave'].'"';
					  if ($rowEDO['clave']==$estado) echo ' selected ';
				  	  echo '>'.$rowEDO['nombre'].'</option>';
				    }
			  ?>
            </select></td>
          </tr>
          <tr>
            <td><div align="right">C.P.:</div></td>
            <td><input name="cp" type="text" class="campo" id="cp" value="<?= $row['cp']; ?>" size="7" maxlength="5" /></td>
          </tr>
          <tr>
            <td><div align="right">Trans Zone:</div></td>
            <td><input name="trans_zone" type="text" class="campo" id="trans_zone" value="<?= $row['trans_zone']; ?>" size="12" maxlength="10" /></td>
          </tr>
          <tr>
            <td><div align="right">Nombre del encargado:</div></td>
            <td><input name="encargado" type="text" class="campo" id="encargado" value="<?= $row['encargado']; ?>" size="50" maxlength="35" /></td>
          </tr>
          <tr>
            <td><div align="right">Correo electr&oacute;nico:</div></td>
            <td><input name="email" type="text" class="campo" id="email" value="<?= $row['email']; ?>" size="50" maxlength="50" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="sucursal" type="hidden" id="sucursal" value="<?= $sucursal; ?>" />            </td>
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
