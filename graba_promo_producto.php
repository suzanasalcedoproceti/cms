<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=10;
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
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Iconos de Promos'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_promo_producto.php';

		include('../conexion.php');
		$error=FALSE;
		
		// extrae variables del formulario
		$promo=$_POST['promo']+0;
		if (empty($promo)) $promo=$_GET['promo']+0;
		$nombre=$_POST['nombre'];

		// otras variables		
		$imagen_tmp = $_POST['imagen_tmp'];
		
		if (!empty($promo)) { 

			$resultado= mysql_query("UPDATE promo_producto SET nombre='$nombre',
										 act=1-act
									 WHERE clave=$promo",$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualizó el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

		} else {  // si no es registro editado autorizado
				
			  $resultado= mysql_query("INSERT promo_producto (nombre)
										  VALUES ('$nombre')",$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  if     ($reg>0) $mensaje='Se agregó un nuevo registro...';
			      else { 
				  	$error=TRUE; $mensaje='ERROR<br>No se agregó el registro...';
				  	$link='javascript:history.go(-1);'; 
				  }

		} 
		
		if (!$error) {
				
			$id_gif = ($new_id) ? ($new_id) : ($promo);
			
			// mover imagen de carpeta uploads 
			$archivo_original = './uploads/'.$imagen_tmp.'.gif';
			if (file_exists($archivo_original)) {
				$archivo_destino = 'images/cms/promo_productos/'.$id_gif.'.gif';
				copy($archivo_original,$archivo_destino);
				@unlink($archivo_original);
			} // si existe el archivo
		  

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