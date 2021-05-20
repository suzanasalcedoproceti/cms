<?
    if (!include('ctrl_acceso.php')) return;
   	include('funciones.php');
	include_once('lib.php');
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
	<? $tit='Cambiar contraseña'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

				$link='principal.php';

                /// modificar contraseña
                include("../conexion.php");

			    $usuario = $_SESSION['usr_valido'];
				$pwd = $_POST['pwd'];
				$pwd1 = $_POST['pwd1'];
				$pwd2 = $_POST['pwd2'];

                $resultado= mysql_query("SELECT * FROM usuario WHERE clave='$usuario' LIMIT 1",$conexion);
                $row = mysql_fetch_array($resultado);

				$password_actual = genera_password($pwd, $row['password']);	
				
				if ($password_actual != $row['password']) {
                  $mensaje='Contraseña actual inválida,<br> no se modificó...';
				  $link='javascript:history.go(-1);';
				}
				else {

				  $password_strong = genera_password($pwd1);


                  $resultado= mysql_query("UPDATE usuario SET password='$password_strong',
														      act=1-act
															WHERE clave='$usuario'",$conexion);
                  $reg= mysql_affected_rows();
				  if ($reg > 0) {
				    $mensaje='Se actualizó la Contraseña...';
				  }
				  else { $mensaje='ERROR<br>No se actualizó la Contraseña...'; $link='javascript:history.go(-1);'; }
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
