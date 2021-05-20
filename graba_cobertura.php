<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=9;
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
	<? $tit='Administrar Cobertura '; include('top.php'); ?>
	<div class="main">
      <p>
        <? 
		$link='lista_cobertura.php';

		include('../conexion.php');
	
		$error=FALSE;
		// extrae variables del formulario
		$idcobertura = $_POST['idcobertura'];
		$sucursal=$_POST['sucursal'];
		$cobertura=$_POST['cobertura'];
		$idSucursal= $_POST['sucursal'];
		$cve_estado= $_POST['estado'];
		$cve_mnpio= $_POST['municipio'];
		$idservicio= $_POST['idservicio'];
		$tipo_producto= $_POST['tipo_producto'];
		$cobertura_= $_POST['cobertura_'];
		if (empty($idSucursal)) $idSucursal=0; 
	
		if (empty($idcobertura)) $idcobertura=$_GET['idcobertura']; 



		if (!empty($idcobertura)) {   

             $queryup="UPDATE cobertura SET 
							 cobertura='$cobertura',  
							 idSucursal='$idSucursal'							 
				WHERE idCobertura=$idcobertura";
			$resultado= mysql_query($queryup,$conexion);
		    $reg += mysql_affected_rows();
			if   ($reg>0) $mensaje='Se actualizó el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'.$resultadod; ; $link='javascript:history.go(-1);'; }

		} else {  
            $queryval="SELECT * FROM cobertura WHERE cve_estado='$cve_estado' AND cve_mnpio= '$cve_mnpio' AND idServicio='$idservicio' AND tipo_producto ='$tipo_producto' ";
			$resultadoval= mysql_query($queryval,$conexion);
		    $rowno=mysql_num_rows($resultadoval);
		    if   ($rowno>0) 
		    	{ $mensaje='Ya existe un  registro...';
		    	}
		    else{	 
			$query = "INSERT cobertura (cve_estado,
											  cve_mnpio, 
											  idServicio, 
											  tipo_producto,
											  cobertura)
									  VALUES ('$cve_estado',
											  '$cve_mnpio',
											  '$idservicio', 
											  '$tipo_producto',
											  '$cobertura_')";
			$resultado= mysql_query($query,$conexion); 

			  $reg= mysql_affected_rows();
			  $new_id= mysql_insert_id();
			}

			foreach($_POST["sucursal"] as $valorSelectMultiple){           		
           		$querysuc = "INSERT cobertura_sucursal (idCobertura,idsuc)
							  VALUES ( '$new_id','$valorSelectMultiple')"; 
				$resultadosuc= mysql_query($querysuc,$conexion); 
			 	$regsuc= mysql_affected_rows();  
       		}
			  if     ($reg>0) $mensaje='Se agreg&oacute; un nuevo registro...';
			  else   { $error=TRUE; $mensaje='ERROR<br>No se agreg&oacute; el registro...'.$rowno; $link='javascript:history.go(-1);'; }	
			  if     ($rowno>0) $mensaje='Ya existe un  registro para esa cobertura...';
			   

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