<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=2;
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
	<? $tit='Administrar Subcategorías'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_subcategoria.php';

		$usuario=$_SESSION['usr_valido'];
		$autorizado=op_aut($modulo);
		include('../conexion.php');
	
		$error=FALSE;

		// comenzar transaccción	
		$resultado = mysql_query("SET AUTOCOMMIT = 0");
		$resultado = mysql_query("START TRANSACTION");
		
		// extrae variables del formulario
		$subcategoria=$_POST['subcategoria'];
		if (empty($subcategoria)) $subcategoria=$_GET['subcategoria'];
		$nombre=$_POST['nombre'];
		$categoria=$_POST['categoria']+0;
		$orden=$_POST['orden']+0;
		$autorizar = $_POST['autorizar'];
		$solo_para_mas=$_POST['solo_para_mas']+0;
		$mostrar_en_mas=$_POST['mostrar_en_mas']+0;
		$tipo_producto=$_POST['tipo_producto'];
		$subtipo_producto=$_POST['subtipo_producto'];
		$cedis=$_POST['cedis'];
		$loc=$_POST['loc'];
		$override=$_POST['override']+0;

		 if (empty($autorizar))  { // si no es proceso de autorización


			if (!empty($subcategoria) AND $autorizado) {   // Si es un registro editado autorizado
				$estatus=1;
				$original=$subcategoria;

//															 solo_para_mas=$solo_para_mas,
				$resultado= mysql_query("UPDATE subcategoria SET nombre='$nombre',
															 categoria=$categoria,
															 tipo_producto='$tipo_producto',
															 subtipo_producto='$subtipo_producto',
															 orden=$orden,
															 mostrar_en_mas=$mostrar_en_mas,
				                                             estatus=1,
															 original=$original,
															 editor=$usuario,
															 act=1-act,
															 cedis='$cedis',
															 loc='$loc',
															 override=$override
															 WHERE clave=$subcategoria",$conexion);

				$reg= mysql_affected_rows();
				if   ($reg>0) $mensaje='Se actualizó el registro...';
				else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

			} else {  // si no es registro editado autorizado
				
				if (empty($subcategoria) AND $autorizado) {  // Si es un registro nuevo autorizado
				   $estatus=1;
				   $original=0;
				}

				elseif (empty($subcategoria) AND !($autorizado)) {  // Si es un registro nuevo por autorizar
				   $estatus=3;
				   $original=0;
				}

				elseif (!empty($subcategoria) AND !($autorizado)) {   // Si es un registro editado por autorizar
					$estatus=3;
					$original=$subcategoria;
				}

				  $resultado= mysql_query("INSERT subcategoria (nombre,
				  											categoria,
															tipo_producto,
															subtipo_producto,
															orden,
															mostrar_en_mas,
															estatus,
															original,
															editor,
															cedis,
															loc,
															override
															)
												  VALUES ('$nombre',
												  		  $categoria,
														  '$tipo_producto',
														  '$subtipo_producto',
														  $orden,
														  $mostrar_en_mas,
														  $estatus,
														  $original,
														  $usuario,
														  '$cedis',
														  '$loc',
														  $override

														)",$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  if     ($reg>0 AND empty($subcategoria)) $mensaje='Se agregó un nuevo registro...';
				  elseif ($reg>0 AND !empty($subcategoria)) $mensaje='Se actualizó el registro...';
				  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó o modificó el registro...'; $link='javascript:history.go(-1);'; }
				  
				  if (!$autorizado && !$error) $mensaje.='<br><br>Se publicará cuando sea autorizado.';

				  if (!empty($subcategoria) && !$error) {  // Marca el registro original como bloqueado para editar
					  $resultado= mysql_query("UPDATE subcategoria SET estatus=2 WHERE clave=$subcategoria",$conexion);
				  }
	  
				  
			   }  // si no es registro editado autorizado

			}  // si no es proceso de autorización
				
			elseif ($autorizar==1) {  // si es proceso de autorización

					$res= mysql_query("SELECT * FROM subcategoria WHERE clave='$subcategoria'",$conexion);
					$row= mysql_fetch_array($res);

					$original=$row['original'];
					
					// borra registro original
	                $resultado= mysql_query("DELETE FROM subcategoria WHERE clave = '$original'" ,$conexion);

					if (empty($original)) $original=$subcategoria;

                    $resultado= mysql_query("UPDATE subcategoria SET clave='$original',
				                                                 nombre='$nombre',
																 categoria=$categoria,
																 tipo_producto='$tipo_producto',
																 subtipo_producto='$subtipo_producto',
																 orden=$orden,
																 mostrar_en_mas=$mostrar_en_mas,
																 estatus=1,
																 act=1-act,
																 cedis='$cedis',
															 	 loc='$loc',
															 	 override=$override
													 	   WHERE clave=$subcategoria",$conexion);

				    $reg= mysql_affected_rows();
				    if   ($reg>0) { $mensaje='Se autorizó el registro...'; $link='lista_autorizar.php'; }
				    else { $error=TRUE; $mensaje='ERROR<br>No se autorizó el registro...'; $link='javascript:history.go(-1);'; }


			}  // si es proceso de autorización

		   // revisar si hubo error o no  
		   if ($error) mysql_query("ROLLBACK"); 
		   else mysql_query("COMMIT");
				
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
