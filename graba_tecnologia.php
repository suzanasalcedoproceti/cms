<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=18;
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
	<? $tit='Administrar Tecnologías'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_tecnologia.php?numpag='.$_POST['numpag'];

		$usuario=$_SESSION['usr_valido'];
		include('../conexion.php');
		$error=FALSE;
		
		// extrae variables del formulario
		$tecnologia=$_POST['tecnologia'];
		if (empty($tecnologia)) $tecnologia=$_GET['tecnologia'];
		$tecnologia+=0;
		$nombre=$_POST['nombre'];
		$descripcion=$_POST['descripcion'];

		// otras variables		
		$imagen_tmp = $_POST['imagen_tmp'];
		
		


			if (!empty($tecnologia)) { 
				$original=$tecnologia;

				$resultado= mysql_query("UPDATE tecnologia SET nombre='$nombre',
														   descripcion='$descripcion',
															 act=1-act
															 WHERE clave=$tecnologia",$conexion);

				$reg= mysql_affected_rows();
				if   ($reg>0) $mensaje='Se actualizó el registro...';
				else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

			} else {  // si no es registro editado 
				
				  $resultado= mysql_query("INSERT tecnologia (nombre,
				  											descripcion)
												  VALUES ('$nombre',
												  		  '$descripcion')",$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  if     ($reg>0 AND empty($tecnologia)) $mensaje='Se agregó un nuevo registro...';
				  elseif ($reg>0 AND !empty($tecnologia)) $mensaje='Se actualizó el registro...';
				  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'; $link='javascript:history.go(-1);'; }
				  


			}  
				


				
			$id_jpg = ($new_id) ? ($new_id) : ($tecnologia);
			
			// mover imagen de carpeta uploads 
			$archivo_original = './uploads/'.$imagen_tmp.'.jpg';
			if (file_exists($archivo_original)) {
				$archivo_destino = 'images/cms/tecnologias/'.$id_jpg.'.jpg';
				copy($archivo_original,$archivo_destino);
				@unlink($archivo_original);
			} // si existe el archivo
		  


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