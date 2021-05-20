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
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
</head>

<body>
<div id="container">
	<? $tit='Administrar noticias y documentos compartidos'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_noticia.php';

		$usuario=$_SESSION['usr_valido'];
		include('../conexion.php');
		$error=FALSE;
		
		// extrae variables del formulario
		$noticia=$_POST['noticia'];
		if (empty($noticia)) $noticia=$_GET['noticia'];
		$noticia+=0;
		$orden=$_POST['orden']+0;
		$titulo=$_POST['titulo'];
		$descripcion=$_POST['descripcion'];

		// otras variables		
		$imagen_tmp = $_POST['imagen_tmp'];
		$nombre_archivo = $_POST['nombre_archivo'];
		
		

			if (!empty($noticia)) {  
				$resultado= mysql_query("UPDATE noticia SET titulo='$titulo',
														    descripcion='$descripcion',
															nombre_archivo = '$nombre_archivo',
															 act=1-act
															 WHERE clave=$noticia",$conexion);

				$reg= mysql_affected_rows();
				if   ($reg>0) $mensaje='Se actualizó el registro...';
				else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

			} else { 
				

			  $resultado= mysql_query("INSERT noticia (titulo,
														descripcion,
														nombre_archivo)
											  VALUES ('$titulo',
													  '$descripcion',
													  '$nombre_archivo')",$conexion); 

			  $reg= mysql_affected_rows();
			  $new_id= mysql_insert_id();
			  if     ($reg>0) $mensaje='Se agregó un nuevo registro...';
			  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'; $link='javascript:history.go(-1);'; }
			  


			}  // si no es proceso de autorización
				
			
			if (!$error) {
			
				//$id_jpg = ($new_id) ? ($new_id) : ($noticia);
				
				// mover imagen de carpeta uploads 
				$archivo_original = './uploads/'.$nombre_archivo;
				$archivo_original = str_replace('%20','+',$archivo_original);
				if (file_exists($archivo_original)) {
					$archivo_destino = 'images/cms/noticias/'.$nombre_archivo;
					copy($archivo_original,$archivo_destino);
					@unlink($archivo_original);
				} // si existe el archivo
				else echo "<br>No existe";
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