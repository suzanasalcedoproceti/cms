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
</head>

<body>
<div id="container">
	<? $tit='Administrar Payment Terms'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_plazo.php';

		include('../conexion.php');

		$error=FALSE;

		// extrae variables del formulario
		$plazo=$_POST['plazo'];
		$interes=$_POST['interes']+0;
		$interes_b=$_POST['interes_b']+0;

		if (!empty($plazo)) { 

			$query = "UPDATE plazo SET interes=$interes, interes_b = $interes_b, act=1-act WHERE clave='$plazo'";
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) {
				$mensaje='Se actualizó el registro...';
			} else { 
				$error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'.$query; $link='javascript:history.go(-1);'; 
			}

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