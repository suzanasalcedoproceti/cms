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
	<? $tit='Administrar determina planta '; include('top.php'); ?>
	<div class="main">
      <p>
        <? 
		$link='lista_determinapta.php';
		include('../conexion.php');
	
		$error=FALSE; 
		$idDeterminacion=$_POST['idDeterminacion'];
		$cluster = $_POST['cluster'];
		$idservicio = $_POST['idservicio'];
		$tipo_producto = $_POST['tipo_producto']; 
	    $cedis1=$_POST['cedis1']; 
		$cedis2=$_POST['cedis2']; 
		$cedis3=$_POST['cedis3']; 
		$cedis4=$_POST['cedis4']; 
		$cedis5=$_POST['cedis5']; 
		$cedis6=$_POST['cedis6']; 
		$cedis7=$_POST['cedis7']; 
		$cedis8=$_POST['cedis8']; 
		if (empty($idDeterminacion)) $idDeterminacion=$_GET['idDeterminacion'];
	 	
	 	if($cedis1 =='' && $cedis2 =='' && $cedis3 =='' && $cedis4 =='' && $cedis5 =='' && $cedis6 =='' && $cedis7 =='' && $cedis8 ==''){
	 		$error=TRUE; $mensaje='ERROR<br>No se insertó ningun Cedis...'.$query; $link='javascript:history.go(-1);'; 
	 	}else{

	 		$query = "INSERT INTO determina_planta (cluster,tipo_producto,idServicio,cedis1,cedis2,cedis3,cedis4,cedis5,cedis6,cedis7,cedis8)  VALUES ($cluster,'$tipo_producto','$idservicio','$cedis1','$cedis2','$cedis3','$cedis4','$cedis5','$cedis6','$cedis7','$cedis8')";
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