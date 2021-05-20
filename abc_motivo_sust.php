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
   if (document.forma.descripcion.value == "") {
     alert("Falta indicar el motivo.");
	 document.forma.descripcion.focus();
     return;
   }
   document.forma.action='graba_motivo_sust.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_motivo_sust.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Motivos de Sustitución y Refacturación'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$clave=$_POST['clave'];
		if (empty($clave)) $clave=$_GET['clave'];
        
        if (!empty($clave)) {
          $resultado= mysql_query("SELECT * FROM motivo_sustitucion WHERE clave=$clave",$conexion);
          $row = mysql_fetch_array($resultado);
        }

        
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td><div align="right">Tipo:</div></td>
            <td><select name="tipo" id="tipo">
              <option value="R" <? if ($row['tipo']=='R') echo 'selected';?>>Refacturaci&oacute;n</option>
              <option value="S" <? if ($row['tipo']=='S') echo 'selected';?>>Sustituci&oacute;n</option>
            </select>
            </td>
          </tr>
          <tr>
            <td><div align="right">Motivo:</div></td>
            <td><input name="descripcion" type="text" class="campo" id="descripcion" value="<?= $row['descripcion']; ?>" size="115" maxlength="100" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
	            <input name="clave" type="hidden" id="clave" value="<?= $clave; ?>" />
            </td>
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
