<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=7;
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
    if (document.forma.empresa.value == "") {
     alert("Falta empresa.");
	 document.forma.empresa.focus();
     return;
     }

   document.forma.action='genera_tarjeta_unica2.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Generar Tarjetas'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>          </tr>
          <tr>
            <td><div align="right">Empresa:</div></td>
            <td><select name="empresa" class="campo" id="empresa">
            <option value="">Selecciona la empresa...</option>
            <?  include('../conexion.php');
			    $resEMP= mysql_query("SELECT * FROM empresa WHERE (estatus=1 OR estatus=2) ORDER BY nombre",$conexion);
				while ($rowEMP = mysql_fetch_array($resEMP)) { 
					echo '<option value="'.$rowEMP['clave'].'">'.$rowEMP['nombre'].'</option>';
						  }
					  ?>
            </select></td>          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GENERAR TARJETA ILIMITADA" /></td>          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>          </tr>        </table>
    </form>    
    </div>
</div>
</body>
</html>
