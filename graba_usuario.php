<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include_once('lib.php');
	$modulo=1;
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
	<? $tit='Administrar Usuarios'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_usuario.php';

		/// agregar o modificar usuario
		include("../conexion.php");
		
		$nombre = $_POST['nombre'];
		$email = $_POST['email'];
		$login = $_POST['login'];
		$pwd = $_POST['pwd'];
		$usuario = $_POST['usuario'];
		$opciones = $_POST['opciones'];
		$autorizar = $_POST['autorizar'];
		$service = $_POST['service']+0;		
		$activo = $_POST['activo']+0;		
		
	    $password_strong = genera_password($pwd);

		// el pwd se cambia solo que se haya cambiado.. si es insert debe haber a fuerzas
	    if ($pwd) $update_pwd = "password='$password_strong', "; else $update_pwd = '';

		
		$resultado= mysql_query("SELECT * FROM usuario WHERE login='$login' AND clave!='$usuario'",$conexion);
		$totres = mysql_num_rows ($resultado);

		if ($totres>0) {
			$mensaje.='<b>ERROR</b><br>Ya existe un Usuario con el login: '.$login;
			$link='javascript:history.go(-1);';
			$rotulo='Regresar';
			$subido=FALSE; 
		}
		else {
				
                if (empty($usuario)) {
				
                      $resultado= mysql_query("INSERT usuario (nombre,
					  										   email,
														       login,
															   password,
															   opciones,
															   autorizar,
															   service,
															   activo)
											          VALUES ('$nombre',
													  		  '$email',
															  '$login',
															  '$password_strong',
															  '$opciones',
															  '$autorizar',
															  '$service',
															  '$activo')",$conexion); 
				      $reg= mysql_affected_rows();
					  $new_id= mysql_insert_id();
					  if ($reg > 0) $mensaje='Se agregó un nuevo Usuario...';
					  else { $mensaje='ERROR<br>No se agregó el Usuario...'; $link='javascript:history.go(-1);'; }

				}
                else {
				
				
                  $resultado= mysql_query("UPDATE usuario SET nombre='$nombre',
				  											  email='$email',
													          login='$login',
															  $update_pwd
															  opciones='$opciones',
															  autorizar='$autorizar',
															  activo='$activo',
															  service='$service',
				  										      act=1-act
													 WHERE clave=$usuario",$conexion);

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
