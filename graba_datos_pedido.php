<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=14;
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
</head>

<body>
<div id="container">
	<? $tit='Cargar datos fijos para exportar pedidos y enviar correo'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='principal.php';

		include('../conexion.php');

		$error=FALSE;
		
		// extrae variables del formulario
		extract ($_POST, EXTR_OVERWRITE);
		$limite_kad = $_POST['limite_kad']+0;
		$limite_precios_especiales = $_POST['limite_precios_especiales']+0;
		$minimo_venta_tw = $_POST['minimo_venta_tw']+0;
		$mensaje_minimo = mysql_real_escape_string($_POST['mensaje_minimo']);
		$correo_seguimiento_bugs = mysql_real_escape_string($_POST['correo_seguimiento_bugs']);

		$query = "UPDATE datos_pedido SET 
									sales_org='$sales_org',
									distr_chan='$distr_chan',
									division='$division',
									sales_grp='$sales_grp',
									sales_off='$sales_off',
									po_method='$po_method',
									pmnttrms='$pmnttrms',
									purch_no_c='$purch_no_c',
									ship_cond='$ship_cond',
									partn_role='$partn_role',
									country='$country',
									partn_role2='$partn_role2',
									country2='$country2',
									partn_role3='$partn_role3',
									partn_numb3='$partn_numb3',
									country3='$country3',
									text_id='$text_id',
									langu='$langu',
									text_id2='$text_id2',
									langu2='$langu2',
									text_line2='$text_line2',
									itm_number='$itm_number',
									ctyp='$ctyp',
									currency='$currency',
									condcoinhd='$condcoinhd',
									acctassggr='$acctassggr',
									parvw='$parvw',
									act=1-act
							 WHERE 1";
		$resultado= mysql_query($query,$conexion);
		$reg += mysql_affected_rows();
		$query = "UPDATE mail SET 
						nombre_contacto='$nombre_contacto',
						email_contacto='$email_contacto',
						nombre_contacto2='$nombre_contacto2',
						email_contacto2='$email_contacto2',
						nombre_logs='$nombre_logs',
						email_logs='$email_logs',
						act = 1-act
				 WHERE 1";
		$resultado= mysql_query($query,$conexion);
		$reg += mysql_affected_rows();

		$query = "UPDATE config SET limite_kad=$limite_kad, limite_precios_especiales=$limite_precios_especiales, minimo_venta_tw=$minimo_venta_tw, mensaje_minimo = '$mensaje_minimo', correo_seguimiento_bugs='$correo_seguimiento_bugs' WHERE reg = 1";
		$resultado= mysql_query($query,$conexion);

		$reg += mysql_affected_rows();

		if   ($reg>1) $mensaje='Se actualizó el registro...';
		else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }
		mysql_close();
				
      ?>
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>
