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
	<? $tit='Administrar Direcciones de Clientes de Proyectos'; include('top.php'); ?>
	<div class="main">
      <p>
 <? 


	$usuario=$_SESSION['usr_valido'];
	$cliente = $_POST['cliente']+0;
	include('../conexion.php');

	$error=FALSE;
	
	// extrae variables del formulario
	
	$alias = $_POST['alias'];
	$ship_to = $_POST['ship_to'];
	$cedis = $_POST['cedis'];
	$nombre = $_POST['nombre'];
	$estado = $_POST['estado'];
	$ciudad = $_POST['ciudad'];
	$colonia = $_POST['colonia'];
	$calle = $_POST['calle'];
	$exterior = $_POST['exterior'];
	$interior = $_POST['interior'];
	$cp = $_POST['cp'];
	$referencias = $_POST['referencias'];
	$telefono_casa = $_POST['telefono_casa'];
	$telefono_oficina = $_POST['telefono_oficina'];
	$telefono_celular = $_POST['telefono_celular'];
	$contacto = $_POST['contacto'];
	$observaciones = $_POST['observaciones'];		

	$dir_envio = $_POST['direccion'];

	if (!$dir_envio) { // NUEVA DIRECCION
		
		$query = "INSERT direccion_envio
										(cliente,
										 ship_to,
										 cedis,
										 alias,
										 nombre,
										 estado,
										 ciudad,
										 colonia,
										 calle,
										 exterior,
										 interior,
										 cp,
										 referencias,
										 telefono_casa,
										 telefono_oficina,
										 telefono_celular,
										 contacto,
										 observaciones)
									  VALUES (
									  	 $cliente,
										 '$ship_to',
										 '$cedis',
										 '$alias',
										 '$nombre',
										 '$estado',
										 $ciudad,
										 '$colonia',
										 '$calle',
										 '$exterior',
										 '$interior',
										 '$cp',
										 '$referencias',
										 '$telefono_casa',
										 '$telefono_oficina',
										 '$telefono_celular',
										 '$contacto',
										 '$observaciones')";
	  	$resultado=mysql_query($query,$conexion);
		  $reg= mysql_affected_rows();
		  $dir_envio = mysql_insert_id();
		  if     ($reg>0) $mensaje='Se agregó correctamente el nuevo domicilio de entrega...';
		  else   { $error=TRUE; $mensaje='ERROR<br>No se pudo agregar el nuevo domicilio de entrega...'; }

	  } else {   // UPDATE

			$query = "UPDATE direccion_envio SET 
										 ship_to = '$ship_to',
										 cedis = '$cedis',
										 alias = '$alias',
										 nombre = '$nombre',
										 estado = '$estado',
										 ciudad = $ciudad,
										 colonia = '$colonia',
										 calle = '$calle',
										 exterior = '$exterior',
										 interior = '$interior',
										 cp = '$cp',
										 referencias = '$referencias',
										 telefono_casa = '$telefono_casa',
										 telefono_oficina = '$telefono_oficina',
										 telefono_celular = '$telefono_celular',
										 contacto = '$contacto',
										 observaciones = '$observaciones',
										 act=1-act
										 WHERE clave=$dir_envio";
			$resultado= mysql_query($query,$conexion);
			$reg= mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualizó el domicilio de entrega...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el domicilio de entrega...'; }

		} 
        mysql_close();
				
      ?>
      </p>
      <p>&nbsp;</p>
      <? 
	  	$link='direcciones_cliente_proyectos.php?cliente='.$cliente;
	     include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>