<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=20;
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
	<? $tit='Administrar Sucursales Almex'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_sucursal_ocurre.php';

		include('../conexion.php');
	
		$error=FALSE;

		// extrae variables del formulario
		$sucursal=$_POST['sucursal'];
		if (empty($sucursal)) $sucursal=$_GET['sucursal'];
		$estado=$_POST['estado'];
		$nombre=$_POST['nombre'];
		$telefonos = $_POST['telefonos'];
		$fax = $_POST['fax'];
		$direccion = $_POST['direccion'];
		$calle = $_POST['calle'];
		$colonia = $_POST['colonia'];
		$ciudad = $_POST['ciudad'];
		$cp = $_POST['cp'];
		$encargado = $_POST['encargado'];
		$email = $_POST['email'];
		$trans_zone = $_POST['trans_zone'];
		
		

		if (!empty($sucursal)) {   
			$resultado= mysql_query("UPDATE sucursal_ocurre SET estado='$estado',
													   nombre='$nombre',
													   telefonos='$telefonos',
													   fax='$fax',
													   direccion='$direccion',
													   calle='$calle',
													   colonia='$colonia',
													   ciudad='$ciudad',
													   cp='$cp',
													   trans_zone = '$trans_zone',
													   encargado='$encargado',
													   email='$email',
													   act=1-act
														 WHERE clave=$sucursal",$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualizó el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

		} else { 
			
			$query = "INSERT sucursal_ocurre (estado,
											  nombre,
											  telefonos,
											  fax,
											  direccion,
											  calle,
											  colonia,
											  ciudad,
											  cp,
											  trans_zone,
											  encargado,
											  email)
									  VALUES ('$estado',
											  '$nombre',
											  '$telefonos',
											  '$fax',
											  '$direccion',
											  '$calle',
											  '$colonia',
											  '$ciudad',
											  '$cp',
											  '$trans_zone',
											  '$encargado',
											  '$email')";
			$resultado= mysql_query($query,$conexion); 

			  $reg= mysql_affected_rows();
			  $new_id= mysql_insert_id();
			  if     ($reg>0) $mensaje='Se agregó un nuevo registro...';
			  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'.$query; $link='javascript:history.go(-1);'; }
			  

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
