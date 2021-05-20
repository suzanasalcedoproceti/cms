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


<script language="JavaScript">
  function valida() {
    if (document.forma.nombre.value == "") {
     alert("Falta marca.");
	 document.forma.nombre.focus();
     return;
     }
   document.forma.action='graba_marca.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_marca.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_marca.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Marcas'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$marca=$_POST['marca'];
		$autorizar=$_GET['autorizar'];
		
		if (empty($marca)) $marca=$_GET['marca'];
        
        if (!empty($marca)) {
          $resultado= mysql_query("SELECT * FROM marca WHERE clave='$marca'",$conexion);
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
            <td><div align="right">Marca:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="115" maxlength="100" />            </td>
          </tr>
          <tr>
            <td><div align="right">Orden en listado:</div></td>
            <td><input name="orden" type="text" class="campo" id="orden" value="<?= $row['orden']; ?>" size="3" maxlength="2" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="marca" type="hidden" id="marca" value="<?= $marca; ?>" />            </td>
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
