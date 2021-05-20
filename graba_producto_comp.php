<?

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=23;
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
<script type="text/javascript">
  function lista () {
  	document.forma.action = "lista_producto_comp.php";
	document.forma.submit();
  }
</script>
</head>

<body>
<form name="forma" id="forma" method="post">
 <input type="hidden" name="categoria" id="categoria" value="<?=$_POST['categoria'];?>" />
</form>
<div id="container">
	<? $tit='Administrar Productos de Competencia'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='javascript:lista()';

		$usuario=$_SESSION['usr_valido'];
		include('../conexion.php');
		$error=FALSE;
		
		// comenzar transaccción	
		// extrae variables del formulario
		$producto=$_POST['producto']+0;
		if (empty($producto)) $producto=$_GET['producto']+0;
		$categoria=$_POST['categoria'];
		$subcategoria=$_POST['subcategoria'];

		// campos comunes
		$sku=$_POST['sku'];
		$nombre=mysql_real_escape_string($_POST['nombre']);
		$marca=$_POST['marca'];
		$menor_3m = $_POST['menor_3m']+0;
		$precio_real = $_POST['precio_real']+0;
		$precio_promocion = $_POST['precio_promocion']+0;
		$archivo_tmp=$_POST['archivo_tmp'];
		

		//campos extra
		for ($ic = 1; $ic<=40; $ic++) {
			$nombrev = "campo_".$ic;
			$$nombrev = $_POST[$nombrev];
		}

		$imagen_tmp = $_POST['imagen_tmp'];
		
		// armar queries para campos extra
		$update_campos_extra = '';
		$insert_campos_extra_var = '';
		$insert_campos_extra_val = '';
		for ($ic = 1; $ic<=40; $ic++) {
			$nombrev = "campo_".$ic;
			$valor = $$nombrev;
			$update_campos_extra .= "campo_".$ic."='".$valor."', ";
			$insert_campos_extra_var .= "campo_".$ic.", ";			
			$insert_campos_extra_val .= "'".$valor."', ";
		}

        $resultado= mysql_query("SELECT * FROM comp_producto WHERE clave=$producto",$conexion);
        $row = mysql_fetch_array($resultado);
	    $fotos = $row['fotos'];

		// busca que no exista otro producto con el mismo sku
		
		if ($producto) // edicion
			$query = "SELECT * FROM comp_producto WHERE (sku='$sku' AND clave!=$producto)";
		else // alta
			$query = "SELECT * FROM comp_producto WHERE sku='$sku'";
		
		$resultado = mysql_query($query,$conexion);
		if (mysql_num_rows($resultado)>0) {  // si existe otro registro con ese sku
			
			$error=TRUE; $mensaje='ERROR<br>Ya existe un producto con ese SKU...'; $link='javascript:history.go(-1);';
			
		} else {   // si no existe el otro registro con el mismo código
		

			if (!empty($producto)) {   // Si es un registro editado autorizado

				//			 exclusivo_rm12=$exclusivo_rm12,


				$query = "UPDATE comp_producto SET sku='$sku',
							 categoria=$categoria,
							 subcategoria=$subcategoria,
							 nombre='$nombre',
							 marca='$marca',
							 $update_campos_extra
							 menor_3m = $menor_3m,
							 precio_real = $precio_real,
							 precio_promocion = $precio_promocion,
							 act=1-act
							 WHERE clave=$producto";
							 

				$resultado= mysql_query($query,$conexion);

				$reg= mysql_affected_rows();
				if   ($reg>0) $mensaje='Se actualizó el registro...';
				else { 
					$error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; 
				}

			} else {  // si no es registro editado autorizado
				

				  $query = "INSERT comp_producto (marca, 
				  							sku,
											categoria,
											subcategoria,
											nombre,
											$insert_campos_extra_var
											menor_3m,
											precio_real,
											precio_promocion
											)
								  VALUES ($marca, 
								   		  '$sku',
										  $categoria,
										  $subcategoria,
										  '$nombre',
										  $insert_campos_extra_val
									      $menor_3m,
										  $precio_real,
										  $precio_promocion
										  )";
				  $resultado= mysql_query($query,$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  $producto = $new_id;
				  if     ($reg>0) $mensaje='Se agregó un nuevo registro...';
				  else  { 
				  	$error=TRUE; $mensaje='ERROR<br>No se agregó el registro...<br>'.$query.'<br>'.mysql_error(); $link='javascript:history.go(-1);'; 
				  }
				  
		    }  // si no es registro editado autorizado
				

		  }  // si no existe otro registro con el mismo código
				
			// mover imagen de carpeta uploads 
			$archivo_original = './uploads/'.$archivo_tmp.'.jpg';
			if (file_exists($archivo_original)) {
				$archivo_destino = 'images/cms/productos_comp/'.$producto.'.jpg';
				copy($archivo_original,$archivo_destino);
				@unlink($archivo_original);
			} // si existe el archivo
//			echo "<br>orig: ".$archivo_original;
//			echo "<br>dest: ".$archivo_destino;
		  
			// subir los 5 pdfs

      ?>
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>