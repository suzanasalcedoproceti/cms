<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=18;
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
    if (document.forma.nombre.value == "") {
     alert("Falta tienda.");
	 document.forma.nombre.focus();
     return;
    }
    if (!isEmail(document.forma.correo_contacto.value)) {
     alert("Falta correo electrónico para formulario de contacto");
	 document.forma.correo_contacto.focus();
     return;
    }

   document.forma.action='graba_tienda_marca.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_tienda_marca.php';
   document.forma.submit();
  }


</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Tiendas de Marca'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$tienda_marca=$_POST['tienda_marca'];
		$autorizar=$_GET['autorizar'];
		
		if (empty($tienda_marca)) $tienda_marca=$_GET['tienda_marca'];
        $tienda_marca+=0;
        if (!empty($tienda_marca)) {
          $resultado= mysql_query("SELECT * FROM tienda_marca WHERE clave='$tienda_marca'",$conexion);
          $row = mysql_fetch_array($resultado);
        }

        
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td><div align="right">Nombre:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="60" maxlength="50" />            </td>
          </tr>
          <tr>
            <td><div align="right">Login:</div></td>
            <td><input name="login" type="text" class="campo" id="login" value="<?= $row['login']; ?>" size="11" maxlength="10" readonly="readonly" />            </td>
          </tr>
          <tr>
            <td><div align="right">Correo de Contacto:</div></td>
            <td><span class="last">
              <input name="correo_contacto" type="text" class="campo" id="correo_contacto" value="<?=$row['correo_contacto'];?>" size="60" maxlength="100" />
            </span></td>
          </tr>
          <tr>
            <td><div align="right">Activa:</div></td>
            <td><input name="activa" type="checkbox" id="activa" value="1" <? if ($row['activa']==1 OR empty($tienda_marca)) echo 'checked'; ?> /></td>
          </tr>
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
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="tienda_marca" type="hidden" id="tienda_marca" value="<?= $tienda_marca; ?>" />            </td>
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
