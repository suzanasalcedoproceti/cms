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
	<? $tit='Administrar Sucursales '; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_sucursal.php';

		include('../conexion.php');
	
		$error=FALSE;

		// extrae variables del formulario
		$idsuc = $_POST['idsuc'];
		$sucursal=$_POST['sucursal'];
		if (empty($sucursal)) $sucursal=$_GET['sucursal'];
		$estados=$_POST['estados'];
		$nombresucursal=$_POST['nombre'];
		$telefonos = $_POST['telefonos'];		
		$direccion = $_POST['direccion'];
		$calle = $_POST['calle'];
		$colonia = $_POST['colonia'];
		$municipios = $_POST['municipios'];
		$cp = $_POST['cp'];
		$encargado = $_POST['encargado'];
		$email = $_POST['email'];
		$numext = $_POST['numext'];
		$numint = $_POST['numint'];
		$cp = $_POST['cp'];
		$planta = $_POST['planta'];
		$slct = $_POST['slct'];

		if (!empty($sucursal)) {   
             $queryup="UPDATE sucursales SET idsuc='$idsuc',
							 nombresucursal='$nombresucursal',
							 calle='$calle',
							 numext='$numext',
							 numint='$numint',
							 cp='$cp',
							 colonia='$colonia',
							 cve_estado = '$estados',
							 cve_mnpio = '$municipios',
							 telefono='$telefonos', 
							 email='$email',  
							 planta='$planta', 
							 SL='$slct'
				WHERE idsuc=$idsuc";
			$resultado= mysql_query($queryup,$conexion);
		    $reg += mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualiz&oacute; el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualiz&oacute; el registro...'. $link='javascript:history.go(-1);'; }

		} else { 
			if($idsuc==''){$idsuc=0;}
			$query = "INSERT sucursales (nombresucursal,
											  idsuc, 
											  calle,
											  numext,
											  numint,
											  cp,
											  colonia,
											  cve_estado,
											  cve_mnpio,
											  telefono,
											  email,
											  planta,
											  SL)
									  VALUES (
											  '$nombresucursal',
											  '$idsuc',
											  '$calle',
											  '$numext',
											  '$numint',
											  '$cp',
											  '$colonia',
											  '$estados',
											  '$municipios',
											  '$telefonos', 
											  '$email',
											  '$planta',
											  '$slct')";
			  $resultado= mysql_query($query,$conexion); 
			  $reg= mysql_affected_rows();
			  $new_id= mysql_insert_id();

			  $queryupsuc="UPDATE sucursales SET idsuc=$new_id WHERE idSucursal=$new_id";
			  $resultadoupsuc= mysql_query($queryupsuc,$conexion);
			  $regidsuc= mysql_affected_rows();
			  if     ($reg>0) $mensaje='Se agreg&oacute; un nuevo registro...'.$new_id. $regidsuc. $queryupsuc;
			  else   { 
               
			  	$error=TRUE; 
			  	$mensaje='ERROR<br>No se agreg&oacute; el registro...'. $link='javascript:history.go(-1);'; 
			  	
			  }
			  

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
