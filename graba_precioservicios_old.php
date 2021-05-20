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

		$link='lista_preciosservicios.php';

		include('../conexion.php');
	
		$error=FALSE;

		// extrae variables del formulario
		$idPrecioserv = $_POST['idPrecioserv'];
        $cluster=$_POST['cluster'];
        $tipo_producto=$_POST['tipo_producto'];
        $subtipo_producto=$_POST['subtipo_producto'];
        $idservicio=$_POST['idservicio'];
        
		$precio=$_POST['precio'];
		$costo=$_POST['costo'];
		if (empty($idPrecioserv)) $idPrecioserv=$_GET['idPrecioserv'];
	 

		if (!empty($idPrecioserv)) {   
             $queryup="UPDATE precioservicios SET 
							 precio='$precio', costo='$costo'
				WHERE idPrecioservicio=$idPrecioserv";
			$resultado= mysql_query($queryup,$conexion);
		    $reg += mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualizó el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'.$resultadod; ; $link='javascript:history.go(-1);'; }

		}  
		else
		{

			 $queryval="SELECT * FROM precioservicios WHERE  cluster='$cluster'AND  tipo_producto='$tipo_producto' AND subtipo_producto= '$subtipo_producto'
			 AND idservicio='$idservicio' ";
			$resultadoval= mysql_query($queryval,$conexion);
		    $rowno=mysql_num_rows($resultadoval);
		    if   ($rowno>0) 
		    	{ $mensaje='Ya existe un  registro...';
		    	}
		    else{	

			$query = "INSERT INTO precioservicios (cluster,tipo_producto,subtipo_producto,idServicio,precio,costo)  VALUES ($cluster,'$tipo_producto','$subtipo_producto','$idservicio','$precio','$costo')";
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) {
				$mensaje='Se insertó el registro...';
			} else { 
				$error=TRUE; $mensaje='ERROR<br>No se insertó el registro...'.$query; $link='javascript:history.go(-1);'; 
			}
			 if     ($rowno>0) $mensaje='Ya existe un  registro para esa cobertura...';
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
