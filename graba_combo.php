<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=21;
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
	<? $tit='Grabar Combos'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_combo.php';

		include('../conexion.php');
		$error=FALSE;
		
		// extrae variables del formulario
		$combo=$_POST['combo']+0;
		$nombre=$_POST['nombre'];
		$descripcion=$_POST['descripcion'];
		$clasificacion=$_POST['clasificacion'];
		$activo=$_POST['activo']+0;
		$imagen_seleccionada=$_POST['imagen_seleccionada'];
		$imagen_tmp = date("Ymd")."_".session_id();

		if (!empty($combo)) {   // Si es un registro editado
				$resultado= mysql_query("UPDATE combo SET 
										  nombre='$nombre',
										  descripcion='$descripcion',
										  clasificacion='$clasificacion',
										  activo=$activo,
										  act=1-act
										 WHERE clave=$combo",$conexion);

				$reg= mysql_affected_rows();
				
				if ($reg>0) {
				
					// aqui eliminar detalle anterior
					$resultadoBD = mysql_query("DELETE FROM combo_detalle WHERE combo = $combo");
					
					$mensaje='Se actualizó el registro...';
					
				} else { 
				
					$error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; 
					$link='javascript:history.go(-1);'; 
				}

		} else {  // si no es registro editado autorizado
				

				  $resultado= mysql_query("INSERT combo (nombre, descripcion, clasificacion, activo)
												  VALUES ('$nombre', '$descripcion', '$clasificacion', $activo)",$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  if  ($reg>0) {
				  	$mensaje='Se agregó un nuevo registro...';
					
				  } else { 
				  	$error=TRUE; 
					$mensaje='ERROR<br>No se agregó el registro...'; 
					$link='javascript:history.go(-1);'; 
				  }
				  

		}  // edicion o alta

		if (!$error) {
		
			$id_combo = ($new_id) ? ($new_id) : ($combo);

			
			// agregar detalle de combo
            foreach($_SESSION['ss_combo'] as $i_prod => $item_prod) {
				$orden = $item_prod['orden']+0;
				$producto = $item_prod['producto'];
                $modelo = $item_prod['modelo'];
	            $lista_precios = $item_prod['lista_precios'];
				
				$resultadoID = mysql_query("INSERT combo_detalle (combo, orden, producto, modelo, lista_precios )
														VALUES ($id_combo, $orden, $producto, '$modelo', '$lista_precios')");
				$aff = mysql_affected_rows();
			
			}
		
			if ($imagen_seleccionada) {
			
					// mover imagen de carpeta uploads 
					$archivo_original = './uploads/'.$imagen_tmp.'.jpg';
					if (file_exists($archivo_original)) {
						  $archivo_destino = 'images/cms/combos/'.$id_combo.'.jpg';
						  copy($archivo_original,$archivo_destino);
						  @unlink($archivo_original);
						  $mensaje .= "<br>Se subió imagen del combo";

						  // procesa imágenes
						  $imagen_original='images/cms/combos/'.$id_combo.'.jpg';
						  $imagen_small='images/cms/combos/'.$id_combo.'t.jpg';
						  $img = imagecreatefromjpeg($imagen_original);
						  if ($img)  { //  si existe la imagen
						
							// CREA LA IMAGEN NORMAL (800 x ? max)
							$ancho = imagesx($img);
							$alto  = imagesy($img);
							$ancho_maximo = 800;
							
							if ($ancho > $ancho_maximo) {
								$ancho_nuevo = $ancho_maximo;
								$alto_nuevo  = $alto * ($ancho_nuevo/$ancho); 
							} else {
								$ancho_nuevo = $ancho;
								$alto_nuevo = $alto;
							}
										
							//crear imagen normal
							$imagen_nueva_normal = imagecreatetruecolor($ancho_nuevo,$alto_nuevo);
							//redimensionar y copiar imagen
							imagecopyresampled($imagen_nueva_normal, $img, 0,0,0,0, $ancho_nuevo, $alto_nuevo, $ancho, $alto);
							//graba la imagen
							imagejpeg($imagen_nueva_normal, $imagen_original);
						
						
							// CREA LA IMAGEN MINIATURA (85x67 max)
							$ancho_maximo = 108;
							$alto_maximo = 98;
							$ancho_nuevo = $ancho;
							$alto_nuevo  = $alto;
							$proporcion = $ancho_maximo/$alto_maximo;
										
							if (($ancho/$alto)>$proporcion) {
							  if ($ancho>$ancho_maximo) {
								$ancho_nuevo = $ancho_maximo;
								$alto_nuevo  = $alto * ($ancho_nuevo/$ancho); }}
							else
							  if ($alto>$alto_maximo) {
								$alto_nuevo = $alto_maximo;
								$ancho_nuevo  = $ancho * ($alto_nuevo/$alto); }
						
							//crear imagen small
							$imagen_nueva_small = imagecreatetruecolor($ancho_nuevo,$alto_nuevo);
							//redimensionar y copiar imagen
							imagecopyresampled($imagen_nueva_small, $img, 0,0,0,0, $ancho_nuevo, $alto_nuevo, $ancho, $alto);
							//graba la imagen
							imagejpeg($imagen_nueva_small, $imagen_small);
						} // if (img)
					} // si existe el archivo
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