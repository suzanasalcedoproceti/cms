<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include_once('lib.php');
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Usuarios de Tiendas'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_usuario_tienda.php';

		/// agregar o modificar usuario
		include("../conexion.php");
		
		$nombre = $_POST['nombre'];
		$login = $_POST['login'];
		$tienda = $_POST['tienda']+0;
		$password = $_POST['pwd'];
		$email = $_POST['email'];

		
		$usuario = $_POST['usuario'];
		$opciones = $_POST['opciones'];
		$activo = $_POST['activo']+0;		

	    $password_strong = genera_password($password);
		
		// el pwd se cambia solo que se haya cambiado.. si es insert debe haber a fuerzas
	    if ($password) $update_pwd = "password='$password_strong', "; else $update_pwd = '';

		$resultado= mysql_query("SELECT * FROM usuario_tienda WHERE login='$login' AND clave!='$usuario' AND tienda = $tienda",$conexion);
		$totres = mysql_num_rows ($resultado);

		if ($totres>0) {
			$mensaje.='<b>ERROR</b><br>Ya existe un Usuario con el login: <strong>'.$login.'</strong> en la misma tienda';
			$link='javascript:history.go(-1);';
			$rotulo='Regresar';
			$subido=FALSE; 
		}
		else {
				

                if (empty($usuario)) {
					  
					  $query = "INSERT usuario_tienda (tienda,
					  										   nombre,
															   email,
														       login,
															   password,
															   opciones,
															   activo)
											          VALUES ($tienda, 
													   		  '$nombre',
															  '$email',
															  '$login',
															  '$password_strong',
															  '$opciones',
															  '$activo')";
                      $resultado= mysql_query($query,$conexion); 
				      $reg= mysql_affected_rows();
					  $new_id= mysql_insert_id();
					  if ($reg > 0) $mensaje='Se agregó un nuevo Usuario a la tienda...';
					  else { $mensaje='ERROR<br>No se agregó el Usuario...'; $link='javascript:history.go(-1);'; }

				}
                else {
				
				
				  $query = "UPDATE usuario_tienda SET 
									  tienda=$tienda,
									  nombre='$nombre',
									  email='$email',
									  login='$login',
									  $update_pwd
									  opciones='$opciones',
									  activo='$activo',
									  act=1-act
							 WHERE clave=$usuario";

                  $resultado= mysql_query($query,$conexion);

				  $reg= mysql_affected_rows();
				  if ($reg > 0) $mensaje='Se actualizó el Usuario...';
				  else { $mensaje='ERROR<br>No se actualizó el Usuario...'; $link='javascript:history.go(-1);'; }
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
