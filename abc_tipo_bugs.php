<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=28;
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
    if (document.forma.nombre.value == "") {
     alert("Falta descripción");
	 document.forma.nombre.focus();
     return;
     }
   document.forma.action='graba_tipo_bugs.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_tipo_bugs.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Tipos de Bug'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$tipo_bug=$_POST['clave'];
		if (empty($tipo_bug)) $tipo_bug=$_GET['clave'];
        
        if (!empty($tipo_bug)) {
          $resultado= mysql_query("SELECT * FROM bug_tipo WHERE clave=$tipo_bug",$conexion);
          $row = mysql_fetch_array($resultado);
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
            <td><div align="right">Tipo de Bug:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="115" maxlength="100" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="tipo_bug" type="hidden" id="tipo_bug" value="<?= $tipo_bug; ?>" />            </td>
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
