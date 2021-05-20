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
	<? $tit='Administrar Marcas'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_marca.php';

		$usuario=$_SESSION['usr_valido'];
		$autorizado=op_aut($modulo);
		include('../conexion.php');

		$error=FALSE;
		
		// extrae variables del formulario
		$marca=$_POST['marca'];
		if (empty($marca)) $marca=$_GET['marca'];
		$nombre=$_POST['nombre'];
		$orden=$_POST['orden']+0;
		if (!empty($marca)) {   // Si es un registro editado autorizado
			$resultado= mysql_query("UPDATE marca SET nombre='$nombre',
														 orden=$orden,
														 act=1-act
														 WHERE clave=$marca",$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualizó el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

		} else {  // si no es registro editado autorizado
				
			  $resultado= mysql_query("INSERT marca (nombre, orden) VALUES ('$nombre', $orden )",$conexion); 
	
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
