<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=3;
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
	<? $tit='Administrar Categor&iacute;as'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_categoria.php';

		$usuario=$_SESSION['usr_valido'];
		$autorizado=op_aut($modulo);
		include('../conexion.php');

		$error=FALSE;
		// comenzar transaccción	
		$resultado = mysql_query("SET AUTOCOMMIT = 0");
		$resultado = mysql_query("START TRANSACTION");
		
		
		// extrae variables del formulario
		$categoria=$_POST['categoria'];
		if (empty($categoria)) $categoria=$_GET['categoria'];
		$nombre=$_POST['nombre'];
		$orden=$_POST['orden']+0;
		$orden_mas=$_POST['orden_mas']+0;
		//$minimo=$_POST['minimo']+0;
		//$tipo_inventario=$_POST['tipo_inventario']+0;
		//$planta=$_POST['planta'];
		$autorizar = $_POST['autorizar'];
		$minimo_venta = $_POST['minimo_venta']+0;
		$solo_para_mas=$_POST['solo_para_mas']+0;
		$mostrar_en_mas=$_POST['mostrar_en_mas']+0;
		$tipo_producto = $_POST['tipo_producto'];
		
		 if (empty($autorizar))  { // si no es proceso de autorización


			if (!empty($categoria) AND $autorizado) {   // Si es un registro editado autorizado
				$estatus=1;
				$original=$categoria;
				
//	solo_para_mas=$solo_para_mas,
//  tipo_inventario=$tipo_inventario,
//  minimo=$minimo,
				$resultado= mysql_query("UPDATE categoria SET nombre='$nombre',
															 orden=$orden,
															 orden_mas=$orden_mas,
															 mostrar_en_mas=$mostrar_en_mas,
															 minimo_venta=$minimo_venta,
															 tipo_producto='$tipo_producto',
															 estatus=1,
															 original=$original,
															 editor=$usuario,
															 act=1-act
															 WHERE clave=$categoria",$conexion);

				$reg= mysql_affected_rows();
				if   ($reg>0) $mensaje='Se actualiz&oacute; el registro...';
				else { $error=TRUE; $mensaje='ERROR<br>No se actualiz&oacute; el registro...'; $link='javascript:history.go(-1);'; }

			} else {  // si no es registro editado autorizado
				
				if (empty($categoria) AND $autorizado) {  // Si es un registro nuevo autorizado
				   $estatus=1;
				   $original=0;
				}

				elseif (empty($categoria) AND !($autorizado)) {  // Si es un registro nuevo por autorizar
				   $estatus=3;
				   $original=0;
				}

				elseif (!empty($categoria) AND !($autorizado)) {   // Si es un registro editado por autorizar
					$estatus=3;
					$original=$categoria;
				}


				  $resultado= mysql_query("INSERT categoria (nombre,
				  											 orden,
				  											 orden_mas,
															 minimo_venta,
															 mostrar_en_mas,
															 tipo_producto,
															 estatus,
															original,
															editor)
												  VALUES ('$nombre',
												  		  $orden,
												  		  $orden_mas,
														  $minimo_venta,
														  $mostrar_en_mas,
														  '$tipo_producto',
														  $estatus,
														  $original,
														  $usuario)",$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  if     ($reg>0 AND empty($categoria)) $mensaje='Se agreg&oacute; un nuevo registro...';
				  elseif ($reg>0 AND !empty($categoria)) $mensaje='Se actualiz&oacute; el registro...';
				  else   { $error=TRUE; $mensaje='ERROR<br>No se agreg&oacute; el registro...'; $link='javascript:history.go(-1);'; }
				  
				  if (!$autorizado && !$error) $mensaje.='<br><br>Se publicar&aacute; cuando sea autorizado.';

				  if (!empty($categoria)) {  // Marca el registro original como bloqueado para editar
					  $resultado= mysql_query("UPDATE categoria SET estatus=2 WHERE clave=$categoria",$conexion);
				  }
				  
				  
			   }  // si no es registro editado autorizado

			}  // si no es proceso de autorización
				
			elseif ($autorizar==1) {  // si es proceso de autorización

					$res= mysql_query("SELECT * FROM categoria WHERE clave='$categoria'",$conexion);
					$row= mysql_fetch_array($res);

					$original=$row['original'];
					
					// borra registro original
	                $resultado= mysql_query("DELETE FROM categoria WHERE clave = '$original'" ,$conexion);

					if (empty($original)) $original=$categoria;

                    $resultado= mysql_query("UPDATE categoria SET clave='$original',
				                                                 nombre='$nombre',
																 orden=$orden,
																 minimo_venta=$minimo_venta,
																 mostrar_en_mas=$mostrar_en_mas,
																 tipo_producto='$tipo_producto',
																 estatus=1,
																 act=1-act
													 	   WHERE clave=$categoria",$conexion);

				    $reg= mysql_affected_rows();
				    if   ($reg>0) { $mensaje='Se autoriz&oacute; el registro...'; $link='lista_autorizar.php'; }
				    else { $error=TRUE; $mensaje='ERROR<br>No se autoriz&oacute;  el registro...'; $link='javascript:history.go(-1);'; }


			}  // si es proceso de autorización

			if (!$error) {
				// actualizar criterio de inventario
				if (0 && $_POST['actualiza_criterio']==1) {
				
					// recorrer productos de la categor{ia
					$query = "SELECT * FROM producto WHERE categoria = $categoria ORDER BY modelo";
					$resultadoAC = mysql_query($query,$conexion);
					$total_upd = 0;
					
					while ($rowAC = mysql_fetch_array($resultadoAC)) {
					
						// obtener criterio de disponibilidad de su categoria
						$producto = $rowAC['modelo'];
						
						include("disponibilidad_prod.php"); // requiere $categoria y $producto, devuelve $mostrar y $ocultar
					
						// if ($estatus == 30) $resurtible = 1; else $resurtible = 0;
						// M1 = Linea      M4 = Promocionado     M3 = Obsoleto
						if ($vol_reb == 'M3') $resurtible = 0; else $resurtible = 1; // viene en disponibilidad_prod
					
						
						// actualizar producto
						$query_upd = "UPDATE producto SET mostrar = $mostrar, ocultar = $ocultar, act=1-act WHERE modelo = '$producto' LIMIT 1";
						$res = mysql_query($query_upd,$conexion);
						$upd = mysql_affected_rows();
						if ($upd >0) {
							$total_upd ++;
						}
						//$txt_msg .= '<br>'.$query_upd;
					} // while
					
					$mensaje .= "<br>Se actualiz&oacute; el criterio de inventarios de ".$total_upd." productos <br> de esta categor&iacute;a.";
				} // actualiza criterio
		   }  // !error 
		   
		   
		   // revisar si hubo error o no
		   if ($error) mysql_query("ROLLBACK"); 
		   else mysql_query("COMMIT");

				
      ?>
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>
