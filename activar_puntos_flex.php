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
	include("../conexion.php");
	
	if ($_POST['accion']=='activa_pflex') {
		$resultado = mysql_query("UPDATE config SET puntos_flex_activos = 1 WHERE reg = 1",$conexion);
		$afe = mysql_affected_rows();
		if ($afe>0) {
			$log = 'Usuario: '.$_SESSION['ss_nombre'].': '.chr(10).' Activó los puntos flex en CMS';
			$error = mysql_error();
			$hoy = date("Y-m-d H:i");
			$resultado = mysql_query("INSERT INTO log_puntos (fecha_hora, mensaje, error) VALUES ('$hoy', '$log', '$error')",$conexion);
		}
		
	}
	if ($_POST['accion']=='desactiva_pflex') {
		$resultado = mysql_query("UPDATE config SET puntos_flex_activos = 0 WHERE reg = 1",$conexion);
		$afe = mysql_affected_rows();
		if ($afe>0) {
			$log = 'Usuario: '.$_SESSION['ss_nombre'].': '.chr(10).' Desactivó los puntos flex en CMS';
			$error = mysql_error();
			$hoy = date("Y-m-d H:i");
			$resultado = mysql_query("INSERT INTO log_puntos (fecha_hora, mensaje, error) VALUES ('$hoy', '$log', '$error')",$conexion);
		}
		
	}	
	if ($_POST['accion']=='activa_ppep') {
		$resultado = mysql_query("UPDATE config SET puntos_pep_activos = 1 WHERE reg = 1",$conexion);
		$afe = mysql_affected_rows();
		if ($afe>0) {
			$log = 'Usuario: '.$_SESSION['ss_nombre'].': '.chr(10).' Activó los puntos PEP en CMS';
			$error = mysql_error();
			$hoy = date("Y-m-d H:i");
			$resultado = mysql_query("INSERT INTO log_puntos (fecha_hora, mensaje, error) VALUES ('$hoy', '$log', '$error')",$conexion);
		}
		
	}
	if ($_POST['accion']=='desactiva_ppep') {
		$resultado = mysql_query("UPDATE config SET puntos_pep_activos = 0 WHERE reg = 1",$conexion);
		$afe = mysql_affected_rows();
		if ($afe>0) {
			$log = 'Usuario: '.$_SESSION['ss_nombre'].': '.chr(10).' Desactivó los puntos PEP en CMS';
			$error = mysql_error();
			$hoy = date("Y-m-d H:i");
			$resultado = mysql_query("INSERT INTO log_puntos (fecha_hora, mensaje, error) VALUES ('$hoy', '$log', '$error')",$conexion);
		}
		
	}
	// obtener valor actual
	$resultado = mysql_query("SELECT puntos_flex_activos, puntos_pep_activos FROM config WHERE reg = 1",$conexion);
	$row = mysql_fetch_assoc($resultado);
	
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
  function procesar(accion,tipo) {

   
   var proceder = false;
   if (accion=='activar' && tipo =='pflex') {
	   res = window.confirm('Se activarán los puntos Flex en TW y POS, deseas continuar?');
	   if (res==false) return;
	   document.forma.accion.value='activa_pflex';
	   proceder = true;
   }
   if (accion=='desactivar' && tipo =='pflex') {
	   res = window.confirm('Se desactivarán los puntos Flex en TW y POS, deseas continuar?');
	   if (res==false) return;
	   document.forma.accion.value='desactiva_pflex';
	   proceder = true;
   }
   if (accion=='activar' && tipo =='ppep') {
	   res = window.confirm('Se activarán los puntos PEP en TW y POS, deseas continuar?');
	   if (res==false) return;
	   document.forma.accion.value='activa_ppep';
	   proceder = true;
   }
   if (accion=='desactivar' && tipo =='ppep') {
	   res = window.confirm('Se desactivarán los puntos PEP en TW y POS, deseas continuar?');
	   if (res==false) return;
	   document.forma.accion.value='desactiva_ppep';
	   proceder = true;
   }   
   if (proceder) {
	   document.forma.action='activar_puntos_flex.php';
	   document.forma.submit();
   }
  }
  function descarta() {
   document.forma.action='principal.php';
   document.forma.submit();
  }
</script>
</head>
<body>
<div id="container">
	<? $tit='Activar / Desactivar Puntos Flex - PEP'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input type="hidden" name="accion" id="accion"  />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td colspan="2">
            <p>&nbsp;</p>
            <p>&nbsp;</p></td>
          </tr>
          <tr>
            <td colspan="2"><div id="message" align="center"><strong>Estatus de forma de pago Puntos Flex / Puntos PEP<br> Aplica para TW y POS </strong></div>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td width="50%" align="right"> Puntos Flex est&aacute; actualmente: <strong> <?= ($row['puntos_flex_activos']) ? 'Activo' : '<span class="rojo">Inactivo</span>'; ?></strong></td>
            <td width="50%">
            <? if($row['puntos_flex_activos']) { ?>
            <input name="dpf" type="button" class="boton" style="width:110px" onclick="javascript:procesar('desactivar','pflex');" value="DESACTIVAR" />
            <? } else { ?>
            <input name="apf" type="button" class="boton" style="width:110px" onclick="javascript:procesar('activar','pflex');" value="ACTIVAR" />
            <? } ?>
            </td>
          </tr>
          <tr>
            <td align="right"> Puntos PEP est&aacute; actualmente: <strong> <?= ($row['puntos_pep_activos']) ? 'Activo' : '<span class="rojo">Inactivo</span>'; ?></strong></td>
            <td>
            <? if($row['puntos_pep_activos']) { ?>
            <input name="dpp" type="button" class="boton" style="width:110px" onclick="javascript:procesar('desactivar','ppep');" value="DESACTIVAR" />
            <? } else { ?>
            <input name="app" type="button" class="boton" style="width:110px" onclick="javascript:procesar('activar','ppep');" value="ACTIVAR" />
            <? } ?>            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" align="center">&nbsp;
	            <input name="descartar" type="button" class="boton" onclick="descarta();" value="REGRESAR" />
            </td>
          </tr>
          <tr>
            <td colspan="2"><div id="message" align="center" class="rojo"><?php echo $mensaje;?></div>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
