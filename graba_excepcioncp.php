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
	<? $tit='Administrar Excepciones '; include('top.php'); ?>
	<div class="main">
      <p>
        <? 
		$link='lista_excepcioncp.php';

		include('../conexion.php');
	
		$error=FALSE;
		// extrae variables del formulario
		$cp = $_POST['cp'];
		$tipo_producto=$_POST['tipo_producto'];
		$tipo_servicio=$_POST['tipo_servicio'];
		 

		if (!empty($cpexcep)) {   

             $queryup="SELECT idexcepciones_cp FROM excepciones_cp 							 
				WHERE cp=$cp";
			$resultado= mysql_query($queryup,$conexion);
		    $reg += mysql_affected_rows();
			if   ($reg>0) $mensaje='CP existente en listado...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualizó el registro...'.$resultadod; ; $link='javascript:history.go(-1);'; }

		} else { 
			 $queryval="SELECT * FROM excepciones_cp  WHERE cp='$cp' AND tipo_producto='$tipo_producto'";
			 $resultadoval= mysql_query($queryval,$conexion);
		     $rowno=mysql_num_rows($resultadoval);
		     if   ($rowno>0) 
		    	{ $mensaje='Ya existe un  registro...';
		    	}
		     else{	 
				$query = "INSERT excepciones_cp (cp,
												  tipo_producto, 
												  idservicio)
										  VALUES (
												  '$cp',
												  '$tipo_producto',
												  '$tipo_servicio'
												   )";
				$resultado= mysql_query($query,$conexion); 

				  $reg= mysql_affected_rows();
				  $new_id= mysql_insert_id();
			 }
			  if     ($reg>0) $mensaje='Se agreg&oacute; un nuevo registro...';
			  else   { $error=TRUE; $mensaje='ERROR<br>No se agregó el registro...'.$query; $link='javascript:history.go(-1);'; }	
			    if     ($rowno>0) $mensaje='Ya existe un  registro para ese CP...';		  

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