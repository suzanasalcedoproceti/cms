<?php
// Control de Cambios
// Julio 21 2016 Bitmore
// Destacar productos para Proyectos

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
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript">
  function lista () {
  	document.forma.action = "lista_producto.php";
	document.forma.submit();
  }
</script>
</head>

<body>
<form name="forma" id="forma" method="post">
 <input type="hidden" name="categoria" id="categoria" value="<?=$_POST['categoria'];?>" />
</form>
<div id="container">
	<? $tit='Administrar Productos'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='javascript:lista()';

		$usuario=$_SESSION['usr_valido'];
		$autorizado=op_aut($modulo);
		include('../conexion.php');
		$error=FALSE;
		
		// comenzar transaccción	
		$resultado = mysql_query("SET AUTOCOMMIT = 0");
		$resultado = mysql_query("START TRANSACTION");
		
		// extrae variables del formulario
		$producto=$_POST['producto'];
		if (empty($producto)) $producto=$_GET['producto'];
		$producto+=0;
		$categoria=$_POST['categoria'];
		$subcategoria=$_POST['subcategoria'];

		// campos comunes
		$modelo=$_POST['modelo'];
		$nombre=mysql_real_escape_string($_POST['nombre']);
		$marca=$_POST['marca'];
		$color=$_POST['color'];
		$clasificacion = $_POST['clasificacion'];
		$descripcion_larga=mysql_real_escape_string($_POST['descripcion_larga']);
        $descripcion_stage=mysql_real_escape_string($_POST['descripcion_stage']);
		$link_compartir=$_POST['link_compartir'];
		$video=$_POST['video'];
		$notas_precio=$_POST['notas_precio'];
		
		$es_promocion=$_POST['es_promocion']+0;
		$es_promocion_especial=$_POST['es_promocion_especial']+0;
		$es_nuevo=$_POST['es_nuevo']+0;
		$es_moda=$_POST['es_moda']+0;
		$kad_mayor=$_POST['kad_mayor']+0;
		$es_promocion_mas=$_POST['es_promocion_mas']+0;
		$venta_sin_inventario=$_POST['venta_sin_inventario']+0;
		$sync_meli=$_POST['sync_meli']+0;
		$es_nuevo_mas=$_POST['es_nuevo_mas']+0;
		// Inicio Agregado 21/Jul/2016  Bitmore - Destacar productos en Proyectos . Se modificó query de INSERT y UPDATE con estos nuevos datos ///
		$es_promocion_proyectos=$_POST['es_promocion_proyectos']+0;
		$es_nuevo_proyectos=$_POST['es_nuevo_proyectos']+0;
		// Fin Agregado 21/Jul/2016 ////////////////
		//$exclusivo_rm12=$_POST['exclusivo_rm12']+0;
		$archivo_tmp=$_POST['archivo_tmp'];
		$genera_puntos=$_POST['genera_puntos']+0;
		$pct_puntos=$_POST['pct_puntos']+0;
		$solo_para_marcas=$_POST['solo_para_marcas']+0;
		$solo_para_web=$_POST['solo_para_web']+0;
		$solo_para_pos=$_POST['solo_para_pos']+0;
		$solo_para_mas=$_POST['solo_para_mas']+0;
		$mostrar_en_mas=$_POST['mostrar_en_mas']+0;

		//productos relacionados, y categorias para accesorios
		$relacionados=$_POST['relacionados'];
		$categorias_accesorios=$_POST['categorias'];
		$marcas_sitios_marca=$_POST['marcas_sitios_marca'];
		
		// precios 
		$precio_lista = $_POST['precio_lista']+0;
		$precio_web = $_POST['precio_web']+0;
		$precio_w1 = $_POST['precio_w1']+0;
		$precio_w2 = $_POST['precio_w2']+0;
		$precio_w3 = $_POST['precio_w3']+0;
		$precio_w4 = $_POST['precio_w4']+0;
		$precio_w5 = $_POST['precio_w5']+0;
		$precio_w6 = $_POST['precio_w6']+0;
		$precio_w7 = $_POST['precio_w7']+0;
		$precio_w8 = $_POST['precio_w8']+0;
		$precio_w9 = $_POST['precio_w9']+0;
		$precio_x0 = $_POST['precio_x0']+0;
		$precio_x1 = $_POST['precio_x1']+0;
		$precio_x2 = $_POST['precio_x2']+0;
		$precio_x3 = $_POST['precio_x3']+0;
		$precio_x4 = $_POST['precio_x4']+0;
		$precio_x5 = $_POST['precio_x5']+0;
		$precio_x6 = $_POST['precio_x6']+0;
		$precio_x7 = $_POST['precio_x7']+0;
		$precio_x8 = $_POST['precio_x8']+0;
		$precio_x9 = $_POST['precio_x9']+0;

		//campos extra
		for ($ic = 1; $ic<=40; $ic++) {
			$nombrev = "campo_".$ic;
			$$nombrev = $_POST[$nombrev];
		}

		// campos comunes finales
		$terminos_garantia=$_POST['terminos_garantia'];
		$garantia=$_POST['garantia'];
		$garantia_extendida=$_POST['garantia_extendida'];
		$otras_caracteristicas=$_POST['otras_caracteristicas'];
		
		$es_garantia = $_POST['es_garantia']+0;  /// 0=no  1=1año, 2=2años, 3=3años
		
		$pl_amecop = $_POST['pl_amecop'];
		$pl_comentarios = $_POST['pl_comentarios'];
		$pl_exclusivo = $_POST['pl_exclusivo']+0;
		$pl_aplica_para = $_POST['pl_aplica_para'];
		$pl_clave_cat = $_POST['pl_clave_cat'];
		
		// otras variables		
		$autorizar = $_POST['autorizar'];
		$imagen_tmp = $_POST['imagen_tmp'];
		
		// armar queries para campos extra
		$update_campos_extra = '';
		$insert_campos_extra_var = '';
		$insert_campos_extra_val = '';
		for ($ic = 1; $ic<=40; $ic++) {
			$nombrev = "campo_".$ic;
			$valor = $$nombrev;
			$valor = limpia_comillas($valor);
			$update_campos_extra .= "campo_".$ic."='".$valor."', ";
			$insert_campos_extra_var .= "campo_".$ic.", ";			
			$insert_campos_extra_val .= "'".$valor."', ";
		}

        $resultado= mysql_query("SELECT * FROM producto WHERE clave=$producto",$conexion);
        $row = mysql_fetch_array($resultado);
	    $fotos = $row['fotos'];

		$pdf_1_titulo = $_POST['pdf_1_titulo'];
		$pdf_2_titulo = $_POST['pdf_2_titulo'];
		$pdf_3_titulo = $_POST['pdf_3_titulo'];
		$pdf_4_titulo = $_POST['pdf_4_titulo'];
		$pdf_5_titulo = $_POST['pdf_5_titulo'];
		$pdf_1_resumen = $_POST['pdf_1_resumen'];
		$pdf_2_resumen = $_POST['pdf_2_resumen'];
		$pdf_3_resumen = $_POST['pdf_3_resumen'];
		$pdf_4_resumen = $_POST['pdf_4_resumen'];
		$pdf_5_resumen = $_POST['pdf_5_resumen'];
		

		$usuario = $_SESSION['usr_valido'];
		$txt_bitacora_nuevo = date("d/m/Y H:i:s")." Usr: ".$_SESSION['ss_nombre'].". Producto Creado.".chr(10);
		$txt_bitacora_act = date("d/m/Y H:i:s")." Usr: ".$_SESSION['ss_nombre'].". Modific&oacute Producto.".chr(10);
		
		// busca que no exista otro producto con el mismo modelo
		if (!empty($autorizar)) {
		  $original= $row['original']+0;
		}
		
		if (!empty($original))
			$query = "SELECT * FROM producto WHERE (modelo='$modelo' AND clave!=$producto AND clave!=$original)";
		else
			$query = "SELECT * FROM producto WHERE modelo='$modelo' AND clave!=$producto";
		
		$resultado = mysql_query($query,$conexion);
		if (mysql_num_rows($resultado)>0) {  // si existe otro registro con ese modelo
		$error=TRUE; $mensaje='ERROR<br>Ya existe un producto con ese modelo...'; $link='javascript:history.go(-1);';
		}
		
		else {   // si no existe el otro registro con el mismo código
		

		 if (empty($autorizar))  { // si no es proceso de autorización


			if (!empty($producto) AND $autorizado) {   // Si es un registro editado autorizado
				$estatus=1;
				$original=$producto;

				//			 exclusivo_rm12=$exclusivo_rm12,

//							 solo_para_mas=$solo_para_mas,

				$query = "UPDATE producto SET modelo='$modelo',
							 categoria=$categoria,
							 subcategoria=$subcategoria,
							 nombre='$nombre',
							 marca='$marca',
							 marcas_sitios_marca='$marcas_sitios_marca',
							 color='$color',
							 clasificacion='$clasificacion',
							 es_promocion=$es_promocion,
							 es_promocion_mas=$es_promocion_mas,
							 es_promocion_proyectos=$es_promocion_proyectos,
							 es_promocion_especial=$es_promocion_especial,
							 es_nuevo=$es_nuevo,
							 es_nuevo_mas=$es_nuevo_mas,
							 es_nuevo_proyectos=$es_nuevo_proyectos,
							 es_moda=$es_moda,
							 kad_mayor=$kad_mayor,
							 notas_precio='$notas_precio',
							 genera_puntos=$genera_puntos,
							 pct_puntos=$pct_puntos,
							 solo_para_marcas=$solo_para_marcas,
							 solo_para_web=$solo_para_web,
							 solo_para_pos=$solo_para_pos,
							 venta_sin_inventario=$venta_sin_inventario,
							 mostrar_en_mas=$mostrar_en_mas,
							 terminos_garantia='$terminos_garantia',
							 garantia_extendida='$garantia_extendida',
							 es_garantia='$es_garantia',
							 otras_caracteristicas='$otras_caracteristicas',
							 descripcion_larga='$descripcion_larga',
							 descripcion_stage='$descripcion_stage',
							 link_compartir='$link_compartir',
							 video='$video',
							 relacionados='$relacionados',
							 categorias_accesorios='$categorias_accesorios',
							 precio_lista = $precio_lista,
							 precio_web = $precio_web,
							 precio_w1 = $precio_w1,
							 precio_w2 = $precio_w2,
							 precio_w3 = $precio_w3,
							 precio_w4 = $precio_w4,
							 precio_w5 = $precio_w5,
							 precio_w6 = $precio_w6,
							 precio_w7 = $precio_w7,
							 precio_w8 = $precio_w8,
							 precio_w9 = $precio_w9,
							 precio_x0 = $precio_x0,
							 precio_x1 = $precio_x1,
							 precio_x2 = $precio_x2,
							 precio_x3 = $precio_x3,
							 precio_x4 = $precio_x4,
							 precio_x5 = $precio_x5,
							 precio_x6 = $precio_x6,
							 precio_x7 = $precio_x7,
							 precio_x8 = $precio_x8,
							 precio_x9 = $precio_x9,
							 pdf_1_titulo='$pdf_1_titulo',
							 pdf_2_titulo='$pdf_2_titulo',
							 pdf_3_titulo='$pdf_3_titulo',
							 pdf_4_titulo='$pdf_4_titulo',
							 pdf_5_titulo='$pdf_5_titulo',
							 pdf_1_resumen='$pdf_1_resumen',
							 pdf_2_resumen='$pdf_2_resumen',
							 pdf_3_resumen='$pdf_3_resumen',
							 pdf_4_resumen='$pdf_4_resumen',
							 pdf_5_resumen='$pdf_5_resumen',
							 pl_amecop = '$pl_amecop',
							 pl_comentarios = '$pl_comentarios',
							 pl_exclusivo = $pl_exclusivo,
							 pl_aplica_para = '$pl_aplica_para',
							 pl_clave_cat = '$pl_clave_cat',
							 $update_campos_extra
							 estatus=1,
							 original=$original,
							 editor=$usuario,
							 bitacora=CONCAT(bitacora,'$txt_bitacora_act'),
							 act=1-act,
							 sync_meli=$sync_meli
							 WHERE clave=$producto";
				$resultado= mysql_query($query,$conexion);

				$reg= mysql_affected_rows();
				if   ($reg>0) $mensaje='Se actualiz&oacute el registro...';
				else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'.mysql_error(); $link='javascript:history.go(-1);'; }

			} else {  // si no es registro editado autorizado
				
				if (empty($producto) AND $autorizado) {  // Si es un registro nuevo que nace autorizado
				   $estatus=1;
				   $original=0;
				}

				elseif (empty($producto) AND !($autorizado)) {  // Si es un registro nuevo por autorizar
				   $estatus=3;
				   $original=0;
				}

				elseif (!empty($producto) AND !($autorizado)) {   // Si es un registro editado por autorizar
					$estatus=3;
					$original=$producto;
				}

										//	exclusivo_rm12,
										//  $exclusivo_rm12,


				  $query = "INSERT producto (modelo,
											categoria,
											subcategoria,
											nombre,
											color,
											clasificacion,
											marca,
											marcas_sitios_marca,
											mostrar,
											es_promocion,
											es_promocion_mas,
											es_promocion_proyectos,
											es_promocion_especial,
											es_nuevo,
											es_nuevo_mas,
											es_nuevo_proyectos,
											es_moda,
											kad_mayor,
											notas_precio,
											genera_puntos,
									 		pct_puntos,
											solo_para_marcas,
											solo_para_web,
											solo_para_pos,
											mostrar_en_mas,
											fotos,
											terminos_garantia,
											garantia_extendida,
											es_garantia,
											otras_caracteristicas,
											descripcion_larga,
                                            descripcion_stage,
											link_compartir,
											video,
											relacionados,
											categorias_accesorios,
											precio_lista,
											precio_web,
											precio_w1,
											precio_w2, 
											precio_w3, 
											precio_w4, 
											precio_w5, 
											precio_w6, 
											precio_w7, 
											precio_w8, 
											precio_w9, 
											precio_x0, 
											precio_x1, 
											precio_x2, 
											precio_x3, 
											precio_x4, 
											precio_x5, 
											precio_x6, 
											precio_x7, 
											precio_x8, 
											precio_x9, 
											pdf_1_titulo,
											pdf_2_titulo,
											pdf_3_titulo,
											pdf_4_titulo,
											pdf_5_titulo,
											pdf_1_resumen,
											pdf_2_resumen,
											pdf_3_resumen,
											pdf_4_resumen,
											pdf_5_resumen,
											pl_amecop,
											pl_comentarios,
											pl_exclusivo,
											pl_aplica_para,
											pl_clave_cat,
											$insert_campos_extra_var
											estatus,
											original,
											editor,
											bitacora,
											venta_sin_inventario,
											sync_meli)
								  VALUES ('$modelo',
										  $categoria,
										  $subcategoria,
										  '$nombre',
										  '$color',
										  '$clasificacion',
										  '$marca',
										  '$marcas_sitios_marca',
										  0,
										  $es_promocion,
										  $es_promocion_mas,
										  $es_promocion_proyectos,
										  $es_promocion_especial,
										  $es_nuevo,
										  $es_nuevo_mas,
										  $es_nuevo_proyectos,
										  $es_moda,
										  $kad_mayor,
										  '$notas_precio',
										  $genera_puntos,
										  $pct_puntos,
										  $solo_para_marcas,
										  $solo_para_web,
										  $solo_para_pos,
										  $mostrar_en_mas,
										  '$fotos',
										  '$terminos_garantia',
										  '$garantia_extendida',
										  $es_garantia,
										  '$otras_caracteristicas',
										  '$descripcion_larga',
                                          '$descripcion_stage',
										  '$link_compartir',
										  '$video',
										  '$relacionados',
										  '$categorias_accesorios',
											$precio_lista,
											$precio_web,
											$precio_w1, 
											$precio_w2, 
											$precio_w3, 
											$precio_w4, 
											$precio_w5, 
											$precio_w6, 
											$precio_w7, 
											$precio_w8, 
											$precio_w9, 
											$precio_x0, 
											$precio_x1, 
											$precio_x2, 
											$precio_x3, 
											$precio_x4, 
											$precio_x5, 
											$precio_x6, 
											$precio_x7, 
											$precio_x8, 
											$precio_x9, 
										  '$pdf_1_titulo',
										  '$pdf_2_titulo',
										  '$pdf_3_titulo',
										  '$pdf_4_titulo',
										  '$pdf_5_titulo',
										  '$pdf_1_resumen',
										  '$pdf_2_resumen',
										  '$pdf_3_resumen',
										  '$pdf_4_resumen',
										  '$pdf_5_resumen',
										  '$pl_amecop',
										  '$pl_comentarios',
										   $pl_exclusivo,
										  '$pl_aplica_para',
										  '$pl_clave_cat',
										  $insert_campos_extra_val
										  $estatus,
										  $original,
										  $usuario,
										  '$txt_bitacora_nuevo',
										  $venta_sin_inventario,
										  $sync_meli)";
				  $resultado= mysql_query($query,$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  if     ($reg>0 AND empty($producto)) $mensaje='Se agregó un nuevo registro...';
				  elseif ($reg>0 AND !empty($producto)) $mensaje='Se actualizó el registro...';
				  else { 
				  	$error=TRUE; $mensaje='ERROR<br>No se agregó el registro.._'; 
					$link='javascript:history.go(-1);'; 
					$query_err = "INSERT INTO log_error (fecha, usuario, error) VALUES ('".date("Y-m-d H:i")."', '".$_SESSION['usr_valido']."', 'admin/graba_producto.php".chr(10).mysql_real_escape_string(mysql_error()).chr(10).mysql_real_escape_string($query)."')";
					mysql_query($query_err,$conexion);
					
				  }
				  
				  if (!$autorizado && !$error) $mensaje.='<br><br>Se publicará cuando sea autorizado.';

				  if (!empty($producto)) {  // Marca el registro original como bloqueado para editar
					  $resultado= mysql_query("UPDATE producto SET estatus=2 WHERE clave=$producto",$conexion);
				  }
				  
				  
			   }  // si no es registro editado autorizado

			}  // si no es proceso de autorización
				
			elseif ($autorizar==1) {  // si es proceso de autorización

					$res= mysql_query("SELECT * FROM producto WHERE clave='$producto'",$conexion);
					$row= mysql_fetch_array($res);

					$original=$row['original'];
				    $fotos = $row['fotos'];
					
					// borra registro original
	                $resultado= mysql_query("DELETE FROM producto WHERE clave = '$original'" ,$conexion);

					if (empty($original)) $original=$producto;

			//						exclusivo_rm12=$exclusivo_rm12,
					$sql = "UPDATE producto SET clave='$original',
												 modelo='$modelo',
												 categoria=$categoria,
												 subcategoria=$subcategoria,
												 nombre='$nombre',
												 marca='$marca',
												 marcas_sitios_marca='$marcas_sitios_marca',
												 color='$color',
												 clasificacion='$clasificacion',
												 es_promocion=$es_promocion,
												 es_promocion_mas=$es_promocion_mas,
												 es_promocion_proyectos=$es_promocion_proyectos,
												 es_promocion_especial=$es_promocion_especial,
												 es_nuevo=$es_nuevo,
												 es_nuevo_mas=$es_nuevo_mas,
												 es_nuevo_proyectos=$es_nuevo_proyectos,
												 es_moda=$es_moda,
												 kad_mayor=$kad_mayor,
												 notas_precio='$notas_precio',
												 genera_puntos=$genera_puntos,
												 pct_puntos=$pct_puntos,
												 solo_para_marcas=$solo_para_marcas,
												 solo_para_web=$solo_para_web,
												 solo_para_pos=$solo_para_pos,
												 mostrar_en_mas=$mostrar_en_mas,
												 venta_sin_inventario=$venta_sin_inventario,
												 sync_meli=$sync_meli,
												 fotos='$fotos',
												 terminos_garantia='$terminos_garantia',
												 garantia_extendida='$garantia_extendida',
												 es_garantia='$es_garantia',
												 otras_caracteristicas='$otras_caracteristicas',
												 descripcion_larga='$descripcion_larga',
												 descripcion_stage='$descripcion_stage',
												 link_compartir='$link_compartir',
												 video='$video',
												 relacionados='$relacionados',
												 categorias_accesorios='$categorias_accesorios',
												 precio_lista = $precio_lista,
												 precio_web = $precio_web,
												 precio_w1 = $precio_w1,
												 precio_w2 = $precio_w2,
												 precio_w3 = $precio_w3,
												 precio_w4 = $precio_w4,
												 precio_w5 = $precio_w5,
												 precio_w6 = $precio_w6,
												 precio_w7 = $precio_w7,
												 precio_w8 = $precio_w8,
												 precio_w9 = $precio_w9,
												 precio_x0 = $precio_x0,
												 precio_x1 = $precio_x1,
												 precio_x2 = $precio_x2,
												 precio_x3 = $precio_x3,
												 precio_x4 = $precio_x4,
												 precio_x5 = $precio_x5,
												 precio_x6 = $precio_x6,
												 precio_x7 = $precio_x7,
												 precio_x8 = $precio_x8,
												 precio_x9 = $precio_x9,
										  		 pdf_1_titulo='$pdf_1_titulo',
										  		 pdf_2_titulo='$pdf_2_titulo',
										  		 pdf_3_titulo='$pdf_3_titulo',
										  		 pdf_4_titulo='$pdf_4_titulo',
										  		 pdf_5_titulo='$pdf_5_titulo',
										  		 pdf_1_resumen='$pdf_1_resumen',
										  		 pdf_2_resumen='$pdf_2_resumen',
										  		 pdf_3_resumen='$pdf_3_resumen',
										  		 pdf_4_resumen='$pdf_4_resumen',
										  		 pdf_5_resumen='$pdf_5_resumen',
												 pl_amecop = '$pl_amecop',
												 pl_comentarios = '$pl_comentarios',
												 pl_exclusivo = $pl_exclusivo,
												 pl_aplica_para = '$pl_aplica_para',
												 pl_clave_cat = '$pl_clave_cat',
												 $update_campos_extra
												 estatus=1,
												 bitacora=CONCAT(bitacora,'$txt_bitacora_act'),
												 act=1-act
										   WHERE clave=$producto";
                    $resultado= mysql_query($sql,$conexion);
					

				    $reg= mysql_affected_rows();
					
					// si el registro a autorizar tiene pdf, eliminar el original y renombrar este por la original
					if (file_exists("../pdf/".$producto.".pdf")) {
						// eliminar imagen original
						if (file_exists("../pdf/".$original.".pdf"))
							unlink("../pdf/".$original.".pdf");
						// renombrar esta por la original
						copy("../pdf/".$producto.".pdf","../pdf/".$original.".pdf");
						if (file_exists("../pdf/".$producto.".pdf"))
							unlink("../pdf/".$producto.".pdf");
					}
					
				
				    if   ($reg>0) { $mensaje='Se autorizó el registro...'; $link='lista_autorizar.php'; }
				    else { $error=TRUE; $mensaje='ERROR<br>No se autorizó el registro...'; $link='javascript:history.go(-1);'; }


			}  // si es proceso de autorización



		  }  // si no existe otro registro con el mismo código
				
			$id_pdf = ($new_id) ? ($new_id) : ($producto);
			
			// mover imagen de carpeta uploads 
			$archivo_original = './uploads/'.$archivo_tmp.'.pdf';
			if (file_exists($archivo_original)) {
				$archivo_destino = 'images/cms/productos/pdf/'.$id_pdf.'.pdf';
				copy($archivo_original,$archivo_destino);
				@unlink($archivo_original);
			} // si existe el archivo
//			echo "<br>orig: ".$archivo_original;
//			echo "<br>dest: ".$archivo_destino;
		  

		   // revisar si hubo error o no
		   if ($error) mysql_query("ROLLBACK"); 
		   else mysql_query("COMMIT");

			// subir los 5 pdfs
			////////////////////////////////////////////////
			////  SUBIR IMAGENES
			////////////////////////////////////////////////
			for ($ii = 1; $ii<=5; $ii++) {
				  $doc_valor = 'documento'.$ii;
				  $id_doc = $id_pdf."_".$ii.".pdf";
				  include("sube_pdf.php");
			}
			if ($error) $mensaje .= "<br>".$error;
			////////////////////////////////////////////////					


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