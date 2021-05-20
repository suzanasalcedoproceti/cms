<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
  include('../conexion.php');
  /*$modulo=9;
  if (!op($modulo))  {
    $aviso = 'Usuario sin permiso para acceder a este módulo';
    $aviso_link = 'principal.php';
    include('mensaje_sistema.php');
    return;
  }*/
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

     
   document.forma.action='graba_coberturainstalacion.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_coberturainstalacion.php';
   document.forma.submit();
  }

</script>
</head>
<body>
<div id="container">
	<? $tit='Administrar cobertura servicios'; include('top.php'); ?>
	<?
		$idcoberturainstalacion=$_POST['idcoberturainstalacion'];
		if (empty($idcoberturainstalacion)) $idcoberturainstalacion=$_GET['idcoberturainstalacion'];

        if (!empty($idcoberturainstalacion)) {
          $resultado= mysql_query("SELECT * FROM cobertura_instalacion WHERE idcoberturainstalacion='$idcoberturainstalacion'",$conexion);
          $row = mysql_fetch_array($resultado);
        }
        
      ?> 
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
  
          <tr>
            <td><div align="right">Material:</div></td>
            <td><input name="material" type="text" class="campo" required="required" id="material" style="text-transform:capitalize;" value="<?= $row['material']; ?>" size="40" maxlength="50"  />            </td>
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
            <td>
              <input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
              <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" /> 
      <input name="idcoberturainstalacion" type="hidden"  id="idcoberturainstalacion"  value="<?= $row['idcoberturainstalacion']; ?>" />  
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
