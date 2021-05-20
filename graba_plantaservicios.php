<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=20;
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
	<? $tit='Administrar Precios servicios '; include('top.php'); ?>
	<div class="main">
      <p>
        <? 
		$link='lista_determinaptaserv.php';
		include('../conexion.php');
	
		$error=FALSE;
		$idplantaservicio = $_POST['idplantaservicio'];
		$cluster = $_POST['cluster'];
		$idservicio = $_POST['idservicio'];
		$tipo_producto = $_POST['tipo_producto']; 
		$cedis=$_POST['cedis'];
		if (empty($idplantaservicio)) $idplantaservicio=$_GET['idplantaservicio'];
	 

		if (!empty($idplantaservicio)) {   
             $queryup="UPDATE determina_plantaservicio SET 
							 Cedis='$cedis'
				WHERE idplantaservicio=$idplantaservicio";
			$resultado= mysql_query($queryup,$conexion);
		    $reg += mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualizó el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'; 
			       $link='javascript:history.go(-1);'; }

		}  
				else
		{
			$query = "INSERT INTO determina_plantaservicio (cluster,tipo_producto,idServicio,Cedis)  VALUES ($cluster,'$tipo_producto','$idservicio','$cedis')";
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) {
				$mensaje='Se insertó el registro...';
			} else { 
				$error=TRUE; $mensaje='ERROR<br>No se insertó el registro...'.$query; $link='javascript:history.go(-1);'; 
			}
		}
 			
      ?>
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>