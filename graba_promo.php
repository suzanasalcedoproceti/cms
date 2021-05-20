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
	<? $tit='Administrar Promos'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_promo.php';

		$usuario=$_SESSION['usr_valido'];
		$autorizado=op_aut($modulo);
		include('../conexion.php');
		$error=FALSE;
		
		// comenzar transaccción	
		$resultado = mysql_query("SET AUTOCOMMIT = 0");
		$resultado = mysql_query("START TRANSACTION");
		
		// extrae variables del formulario
		$promo=$_POST['promo'];
		if (empty($promo)) $promo=$_GET['promo'];
		$promo+=0;
		$orden=$_POST['orden']+0;
		$nombre=$_POST['nombre'];
		$url=$_POST['url'];
		$interna=$_POST['interna']+0;

		// otras variables		
		$autorizar = $_POST['autorizar'];
		$imagen_tmp = $_POST['imagen_tmp'];
		
		

		 if (empty($autorizar))  { // si no es proceso de autorización


			if (!empty($promo) AND $autorizado) {   // Si es un registro editado autorizado
				$estatus=1;
				$original=$promo;

				$resultado= mysql_query("UPDATE promo SET nombre='$nombre',
														  orden=$orden,
														  url='$url',
														  interna=$interna,
															 estatus=1,
															 original=$original,
															 editor=$usuario,
															 act=1-act
															 WHERE clave=$promo",$conexion);

				$reg= mysql_affected_rows();
				if   ($reg>0) $mensaje='Se actualizó el registro...';
				else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

			} else {  // si no es registro editado autorizado
				
				if (empty($promo) AND $autorizado) {  // Si es un registro nuevo autorizado
				   $estatus=1;
				   $original=0;
				}

				elseif (empty($promo) AND !($autorizado)) {  // Si es un registro nuevo por autorizar
				   $estatus=3;
				   $original=0;
				}

				elseif (!empty($promo) AND !($autorizado)) {   // Si es un registro editado por autorizar
					$estatus=3;
					$original=$promo;
				}


				  $resultado= mysql_query("INSERT promo (nombre,
				  											orden,
				  											 url,
															 interna,
															 estatus,
															original,
															editor)
												  VALUES ('$nombre',
												  		  $orden,
												  		  '$url',
														  $interna,
														  $estatus,
														  $original,
														  $usuario)",$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  if     ($reg>0 AND empty($promo)) $mensaje='Se agregó un nuevo registro...';
				  elseif ($reg>0 AND !empty($promo)) $mensaje='Se actualizó el registro...';
				  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'; $link='javascript:history.go(-1);'; }
				  
				  if (!$autorizado && !$error) $mensaje.='<br><br>Se publicará cuando sea autorizado.';

				  if (!empty($promo)) {  // Marca el registro original como bloqueado para editar
					  $resultado= mysql_query("UPDATE promo SET estatus=2 WHERE clave=$promo",$conexion);
				  }
				  
				  
			   }  // si no es registro editado autorizado

			}  // si no es proceso de autorización
				
			elseif ($autorizar==1) {  // si es proceso de autorización

					$res= mysql_query("SELECT * FROM promo WHERE clave='$promo'",$conexion);
					$row= mysql_fetch_array($res);

					$original=$row['original'];
					
					// borra registro original
	                $resultado= mysql_query("DELETE FROM promo WHERE clave = '$original'" ,$conexion);

					if (empty($original)) $original=$promo;

                    $resultado= mysql_query("UPDATE promo SET clave='$original',
				                                                 nombre='$nombre',
																 orden=$orden,
																 url='$url',
																 interna=$interna,
																 estatus=1,
																 act=1-act
													 	   WHERE clave=$promo",$conexion);

				    $reg= mysql_affected_rows();
				    if   ($reg>0) { $mensaje='Se autorizó el registro...'; $link='lista_autorizar.php'; }
				    else { $error=TRUE; $mensaje='ERROR<br>No se autorizó el registro...'; $link='javascript:history.go(-1);'; }


			}  // si es proceso de autorización



				
			$id_jpg = ($new_id) ? ($new_id) : ($promo);
			
			// mover imagen de carpeta uploads 
			$archivo_original = './uploads/'.$imagen_tmp.'.jpg';
			if (file_exists($archivo_original)) {
				$archivo_destino = 'images/cms/promos/'.$id_jpg.'.jpg';
				copy($archivo_original,$archivo_destino);
				@unlink($archivo_original);
			} // si existe el archivo
		  

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