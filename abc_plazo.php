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
   document.forma.action='graba_plazo.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_plazo.php';
   document.forma.submit();
  }


</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Payment Terms'; include('top.php'); ?>
	<?
        include('../conexion.php');
		$plazo=$_POST['plazo'];
		if (empty($plazo)) $plazo=$_GET['plazo'];

        if (!empty($plazo)) {
          $resultado= mysql_query("SELECT * FROM plazo WHERE clave='$plazo'",$conexion);
          $row = mysql_fetch_array($resultado);
        }
        
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td><div align="right">Código:</div></td>
            <td><input name="clave" type="text" class="campo" id="clave" value="<?= $row['clave']; ?>" size="8" maxlength="5" readonly="readonly" />            </td>
          </tr>
          <tr>
            <td><div align="right">Descripción:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="40" maxlength="35" readonly="readonly" />            </td>
          </tr>
          <tr>
            <td><div align="right">Interés A:</div></td>
            <td><span class="last">
              <input name="interes" type="text" class="campo" id="interes" value="<?=$row['interes'];?>" size="8" maxlength="6" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" /> 
              %
            </span></td>
          </tr>
          <tr>
            <td><div align="right">Inter&eacute;s B:</div></td>
            <td><span class="last">
              <input name="interes_b" type="text" class="campo" id="interes_b" value="<?=$row['interes_b'];?>" size="8" maxlength="6" onblur="this.value = this.value.replace(/[^0-9.]+/g,'');" />
              % </span></td>
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
            <input name="plazo" type="hidden" id="plazo" value="<?= $plazo; ?>" />            </td>
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
