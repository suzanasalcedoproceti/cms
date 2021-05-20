<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=8;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	
	if ($_POST['accion']=='resetea') {
		include("../conexion.php");
		/*
		$ano = date("Y");
		$resultado = mysql_query("DELETE FROM precios_especiales WHERE ano = '$ano'",$conexion);
		$afe = mysql_affected_rows();
		$mensaje = 'Se ha eliminando registro de precios especiales de '.$afe.' empleados';
		$log = 'Usuario: '.$_SESSION['ss_nombre'].': '.chr(10).$mensaje.' en CMS';
		$error = mysql_error();
		$hoy = date("Y-m-d H:i");
//		$resultado = mysql_query("INSERT INTO log_puntos (fecha_hora, mensaje, error) VALUES ('$hoy', '$log', '$error')",$conexion);
		*/
	    // obtener datos de configuracion
	    $resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
	    $rowCFG = mysql_fetch_array($resultadoCFG);
	
	    $limite_precios_especiales = $rowCFG['limite_precios_especiales']+0;
				
		$resultado = mysql_query("UPDATE cliente SET pe_disponibles = $limite_precios_especiales, act=1-act 
									WHERE empresa IN (SELECT clave FROM empresa WHERE empresa_whirlpool = 1)
									  AND tipo = 'E'",$conexion);
		$afe = mysql_affected_rows();
		if ($afe>0) {
			$mensaje = 'Se ha reseteado precios especiales de '.$afe.' empleados';

			$resultado = mysql_query("UPDATE cliente SET pe_disponibles = 0, act=1-act 
										WHERE empresa = 178 OR tipo = 'I'",$conexion);
			$afe = mysql_affected_rows();
			if ($afe>0) {
				$mensaje .= '<br>Se ha reseteado a 0 los precios especiales de '.$afe.' invitados';
			}
		}
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
  function valida() {
   res = window.confirm('Se resetearán los precios especiales disponibles de TODOS los empleados, y sus invitados, y no será reversible, deseas continuar?');
   if (res==false) return;
   
   document.forma.accion.value='resetea';
   document.forma.action='resetea_precios_especiales.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_cliente.php';
   document.forma.submit();
  }
</script>
</head>
<body>
<div id="container">
	<? $tit='Resetear Precios Especiales'; include('top.php'); ?>
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
          <? if ($_POST['accion']!='resetea') { ?>
          <tr>
            <td><div id="message" align="center" class="rojo">Con este proceso se resetearán los precios especiales disponibles de TODOS los empleados Whirlpool, y sus invitados, y no será reversible, deseas continuar</div>
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
                <input name="eliminar" type="button" class="boton" onclick="valida();" value="RESETEAR" />
                &nbsp;
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