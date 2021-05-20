<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=19;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	
	if ($_POST['accion']=='resetea_flex') {
		include("../conexion.php");
		$resultado = mysql_query("UPDATE cliente SET puntos_flex = 0 WHERE empresa IN (SELECT clave FROM empresa WHERE empresa_whirlpool = 1) OR empresa = 178",$conexion);
		$afe = mysql_affected_rows();
		$mensaje = 'Se han eliminando puntos Flex de '.$afe.' empleados';
		$log = 'Usuario: '.$_SESSION['ss_nombre'].': '.chr(10).$mensaje.' en CMS';
		$error = mysql_error();
		$hoy = date("Y-m-d H:i");
		$resultado = mysql_query("INSERT INTO log_puntos (fecha_hora, mensaje, error) VALUES ('$hoy', '$log', '$error')",$conexion);
		
	}
	if ($_POST['accion']=='resetea_pep') {
		include("../conexion.php");
		$resultado = mysql_query("UPDATE cliente SET puntos_pep = 0 WHERE empresa IN (SELECT clave FROM empresa WHERE empresa_whirlpool = 1) OR empresa = 178",$conexion);
		$afe = mysql_affected_rows();
		$mensaje = 'Se han eliminando puntos PEP de '.$afe.' empleados';
		$log = 'Usuario: '.$_SESSION['ss_nombre'].': '.chr(10).$mensaje.' en CMS';
		$error = mysql_error();
		$hoy = date("Y-m-d H:i");
		$resultado = mysql_query("INSERT INTO log_puntos (fecha_hora, mensaje, error) VALUES ('$hoy', '$log', '$error')",$conexion);
		
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
<script src="js/jquery.js" type="text/javascript" language="javascript1.2"></script>

<script language="JavaScript">
  function valida_flex() {
   res = window.confirm('Se eliminarán TODOS los puntos FLEX de los empleados Whirlpool, deseas continuar?');
   if (res==false) return;
   
   document.forma.accion.value='resetea_flex';
   document.forma.action='resetea_puntos_flex.php';
   document.forma.submit();
  }
  function valida_pep() {
   res = window.confirm('Se eliminarán TODOS los puntos PEP de los empleados Whirlpool, deseas continuar?');
   if (res==false) return;
   
   document.forma.accion.value='resetea_pep';
   document.forma.action='resetea_puntos_flex.php';
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
	<? $tit='Resetear Puntos Flex/PEP'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input type="hidden" name="accion" id="accion"  />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td><p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p></td>
          </tr>
          <? if (!$_POST['accion']) { ?>
          <tr>
            <td><div id="message" align="center" class="rojo">Con este proceso se eliminar&aacute;n TODOS los puntos FLEX o PEP que tengan actualmente los empleados Whirlpool</div>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="center">
                <input name="eliminar" type="button" class="boton" onclick="valida_flex();" value="ELIMINAR PUNTOS Flex" />
                &nbsp;
                <input name="eliminar" type="button" class="boton" onclick="valida_pep();" value="ELIMINAR PUNTOS PEP" />
                <br /><br />
	            <input name="descartar" type="button" class="boton" onclick="descarta();" value="REGRESAR" />
            </td>
          </tr>
          <?php  } else { ?>
          <tr>
            <td><div id="message" align="center" class="rojo"><?php echo $mensaje;?></div>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="center">
	            <input name="descartar" type="button" class="boton" onclick="descarta();" value="OK" />
            </td>
          </tr>
          <? } ?>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
