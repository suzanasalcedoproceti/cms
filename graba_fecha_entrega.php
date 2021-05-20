<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=16;
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
	<? $tit='Especificar cálculo de tiempos de entrega'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='principal.php';

		include('../conexion.php');

		$error=FALSE;
		
		// extrae variables del formulario
		$tipo_entrega= $_POST['tipo_entrega'];
		$dias_entrega = $_POST['dias_entrega']+0;
		$label_entrega = $_POST['label_entrega'];
		$dias_entrega_ltl = $_POST['dias_entrega_ltl']+0;
		$label_entrega_ltl = $_POST['label_entrega_ltl'];
		$label_entrega_combo = $_POST['label_entrega_combo'];
		$dias_entrega_combo = $_POST['dias_entrega_combo']+0;
		$disponibilidad_venta = $_POST['disponibilidad_venta']+0;
		$puntos_global = $_POST['puntos_global']+0;

		$query = "UPDATE config SET 
						tipo_entrega='$tipo_entrega',
						dias_entrega=$dias_entrega,
						label_entrega='$label_entrega',
						dias_entrega_ltl=$dias_entrega_ltl,
						label_entrega_ltl='$label_entrega_ltl',
						dias_entrega_combo=$dias_entrega_combo,
						label_entrega_combo='$label_entrega_combo',
						disponibilidad_venta=$disponibilidad_venta,
						puntos_global=$puntos_global,
						act=1-act
					 WHERE reg = 1 ";
	
		$resultado= mysql_query($query,$conexion);
		$reg = mysql_affected_rows();
		if   ($reg>=1) $mensaje='Se actualizó el registro...';
		else { 
			$error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; 
		}
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
