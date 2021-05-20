<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=18;
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
	<? $tit='Administrar Tiendas de Marca'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_tienda_marca.php';

		$usuario=$_SESSION['usr_valido'];
		$autorizado=op_aut($modulo);
		include('../conexion.php');

		$error=FALSE;

		// extrae variables del formulario
		$tienda_marca=$_POST['tienda_marca'];
		if (empty($tienda_marca)) $tienda_marca=$_GET['tienda_marca'];
		$nombre=$_POST['nombre'];
		$activa=$_POST['activa']+0;
		$login=$_POST['login'];
		$correo_contacto=$_POST['correo_contacto'];

		if (!empty($tienda_marca)) { 
			$estatus=1;
			$original=$tienda_marca;

			$query = "UPDATE tienda_marca SET nombre='$nombre',
										 login='$login',
										 activa=$activa,
										 correo_contacto='$correo_contacto',
										 act=1-act
										 WHERE clave=$tienda_marca";
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) {
				$mensaje='Se actualizó el registro...';
			} else { 
				$error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'.$query; $link='javascript:history.go(-1);'; 
			}

		} else {  // si no es registro editado autorizado
		
			// checar que no exista login
				
			$resultado= mysql_query("SELECT * FROM tienda_marca WHERE clave!='$clave' AND login='$login'",$conexion);
			$totres = mysql_num_rows ($resultado);
		
			if ($totres>0) {
				  $mensaje.='<b>ERROR</b><br>Ya existe una tienda con el login: '.$login;
				  $link='javascript:history.go(-1);';
				  $rotulo='Regresar';
				  $error = true;
			} else {
				  $query = "INSERT tienda_marca (nombre,
										   login,
										   activa)
								  VALUES ('$nombre',
										  '$login',
										  $activa)";
				  $resultado= mysql_query($query,$conexion); 
	
				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
				  $tienda_marca = $new_id;
				  if     ($reg>0) $mensaje='Se agregó un nuevo registro...';
				  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'; $link='javascript:history.go(-1);'; }
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