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
</head>

<body>
<div id="container">
	<? $tit='Configuración de Productos en Promoción Especial'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='principal.php';

		include('../conexion.php');

		$error=FALSE;
		
		// extrae variables del formulario
		$mensaje_promocion_especial= $_POST['mensaje_promocion_especial'];

		$query = "UPDATE config SET 
						mensaje_promocion_especial='$mensaje_promocion_especial',
						act=1-act
					 WHERE reg = 1 ";
	
		$resultado= mysql_query($query,$conexion);
		$reg = mysql_affected_rows();
		if ($reg<=0) { 
			$error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; 
		} else  {
			$mensaje='Se actualizó el registro...';		

			// subir el icono
			////////////////////////////////////////////////
			////  SUBIR IMAGENES
			////////////////////////////////////////////////
		    $doc_valor = 'archivo';
			$id_doc = 'images/btns/icon_promo_especial.gif';
			include("sube_icono.php");

			if ($error) $mensaje .= "<br>".$error;
			
		}
		
				
      ?>
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>
