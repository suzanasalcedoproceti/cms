<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('lib.php');

	$modulo=11;
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
	<? $tit='Administrar Stages'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_stage.php';

		$usuario=$_SESSION['usr_valido'];
		$autorizado=op_aut($modulo);
		include('../conexion.php');
		$error=FALSE;
		
		// comenzar transaccción	
		$resultado = mysql_query("SET AUTOCOMMIT = 0");
		$resultado = mysql_query("START TRANSACTION");
		
		// extrae variables del formulario
		$stage=$_POST['stage'];
		if (empty($stage)) $stage=$_GET['stage'];
		$empresa=$_POST['empresa'];
		if (empty($empresa)) $empresa=$_GET['empresa'];
		$empresa+=0;
		$stage+=0;
		$nombre=mysql_real_escape_string($_POST['nombre']);
		$producto=$_POST['producto']+0;
		$combo=$_POST['combo']+0;
		$video=$_POST['video'];
		$omitidas=$_POST['omitidas'];
		$mobile=$_POST['mobile']+0;
		$solo_proyectos=$_POST['solo_proyectos']+0;
		$desde = convierte_fecha(substr($_POST['fechas'],0,10));
	 	$hasta = convierte_fecha(substr($_POST['fechas'],13,10));
		if (!$desde) $desde = '0000-00-00';
		if (!$hasta) $hasta= '0000-00-00';
		$url = $_POST['url'];
		$externa = $_POST['externa']+0;

		// otras variables		
		$autorizar = $_POST['autorizar'];
		$imagen_tmp = $_POST['imagen_tmp'];
		
		

		 if (empty($autorizar))  { // si no es proceso de autorización


			if (!empty($stage) AND $autorizado) {   // Si es un registro editado autorizado
				$estatus=1;
				$original=$stage;

				$resultado= mysql_query("UPDATE stage SET nombre='$nombre',
														  producto=$producto,
														  combo=$combo,
														  video='$video',
														  empresa=$empresa,
														  empresas_omitidas='$omitidas',
														  mobile=$mobile,
														  solo_proyectos=$solo_proyectos,
														  inicio_vigencia='$desde',
														  fin_vigencia='$hasta',
														  url='$url',
														  externa=$externa,
															 estatus=1,
															 original=$original,
															 editor=$usuario,
															 act=1-act
															 WHERE clave=$stage",$conexion);

				$reg= mysql_affected_rows();
				if   ($reg>0) $mensaje='Se actualizó el registro...';
				else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; $link='javascript:history.go(-1);'; }

			} else {  // si no es registro editado autorizado
				
				if (empty($stage) AND $autorizado) {  // Si es un registro nuevo autorizado
				   $estatus=1;
				   $original=0;
				}

				elseif (empty($stage) AND !($autorizado)) {  // Si es un registro nuevo por autorizar
				   $estatus=3;
				   $original=0;
				}

				elseif (!empty($stage) AND !($autorizado)) {   // Si es un registro editado por autorizar
					$estatus=3;
					$original=$stage;
				}

				  $query = "INSERT stage (nombre,
											 producto,
											 combo,
											 video,
											 empresa,
											 empresas_omitidas,
											 mobile,
											 solo_proyectos,
											 inicio_vigencia,
											 fin_vigencia,
											 url,
											 externa,
											 estatus,
											original,
											editor)
								  VALUES ('$nombre',
										  $producto,
										  $combo,
										  '$video',
										  $empresa,
										  '$omitidas',
										  $mobile,
										  $solo_proyectos,
										  '$desde',
										  '$hasta',
										  '$url',
										  $externa,
										  $estatus,
										  $original,
										  $usuario)";
				  $resultado= mysql_query($query,$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  if     ($reg>0 AND empty($stage)) $mensaje='Se agregó un nuevo registro...';
				  elseif ($reg>0 AND !empty($stage)) $mensaje='Se actualizó el registro...';
				  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'; $link='javascript:history.go(-1);'; }
				  
				  if (!$autorizado && !$error) $mensaje.='<br><br>Se publicará cuando sea autorizado.';

				  if (!empty($stage)) {  // Marca el registro original como bloqueado para editar
					  $resultado= mysql_query("UPDATE stage SET estatus=2 WHERE clave=$stage",$conexion);
				  }
				  
				  
			   }  // si no es registro editado autorizado

			}  // si no es proceso de autorización
				
			elseif ($autorizar==1) {  // si es proceso de autorización

					$res= mysql_query("SELECT * FROM stage WHERE clave='$stage'",$conexion);
					$row= mysql_fetch_array($res);

					$original=$row['original'];
					
					// borra registro original
	                $resultado= mysql_query("DELETE FROM stage WHERE clave = '$original'" ,$conexion);

					if (empty($original)) $original=$stage;

                    $resultado= mysql_query("UPDATE stage SET clave='$original',
				                                                 nombre='$nombre',
																 producto=$producto,
																 combo=$combo,
																 empresa=$empresa,
																 empresas_omitidas='$omitidas',
																 mobile=$mobile,
																 solo_proyectos=$solo_proyectos,
	 														     inicio_vigencia='$desde',
														  		 fin_vigencia='$hasta',
																 url='$url',
																 externa=$externa,
																 video='$video',
																 estatus=1,
																 act=1-act
													 	   WHERE clave=$stage",$conexion);

				    $reg= mysql_affected_rows();
				    if   ($reg>0) { $mensaje='Se autorizó el registro...'; $link='lista_autorizar.php'; }
				    else { $error=TRUE; $mensaje='ERROR<br>No se autorizó el registro...'; $link='javascript:history.go(-1);'; }


			}  // si es proceso de autorización



				
			$id_jpg = ($new_id) ? ($new_id) : ($stage);

		
			// mover imagen de carpeta uploads 
			$archivo_original = './uploads/'.$imagen_tmp.'.jpg';
			if (file_exists($archivo_original)) {
				$archivo_destino = 'images/cms/stages/'.$id_jpg.'.jpg';
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