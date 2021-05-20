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
	<? $tit='Administrar Motivos de Sustitución / Refacturación'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_motivo_sust.php';

		include('../conexion.php');

		$error=FALSE;
		
		// extrae variables del formulario
		$clave=$_POST['clave'];
		if (empty($clave)) $clave=$_GET['clave'];
		$descripcion=$_POST['descripcion'];
		$tipo=$_POST['tipo'];
		if (!empty($clave)) {   // Si es un registro editado autorizado
			$resultado= mysql_query("UPDATE motivo_sustitucion SET descripcion='$descripcion',
														 tipo='$tipo',
														 act=1-act
														 WHERE clave=$clave",$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualizó el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

		} else {  // si no es registro editado autorizado
				
			  $resultado= mysql_query("INSERT motivo_sustitucion (descripcion, tipo) VALUES ('$descripcion', '$tipo' )",$conexion); 
	
			  $reg= mysql_affected_rows();
			  $new_id= mysql_insert_id();
			  if     ($reg>0) $mensaje='Se agregó un nuevo registro...';
			  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'; $link='javascript:history.go(-1);'; }
				  

		}  // 

      ?>
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>
