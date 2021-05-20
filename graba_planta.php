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
	<? $tit='Administrar Plantas'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_planta.php';

		include('../conexion.php');

		$error=FALSE;

		// extrae variables del formulario
		$clave=$_POST['clave'];
		$planta=$_POST['planta'];
		$loc=$_POST['loc'];

		if (!empty($clave)) { 

			$query = "UPDATE planta SET planta='$planta', loc = '$loc' WHERE clave='$clave'";
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>=0) {
				$mensaje='Se actualiz&oacute; el registro...';
			} else { 
				$error=TRUE; $mensaje='ERROR<br>No se actualiz&oacute; el registro...'.$link='javascript:history.go(-1);'; 
			}

		}
		else { 
			 $queryval="SELECT * FROM planta  WHERE planta='$planta' AND loc='$loc'";
			 $resultadoval= mysql_query($queryval,$conexion);
		     $rowno=mysql_num_rows($resultadoval);
		     if   ($rowno>0) 
		    	{ $mensaje='Ya existe un  registro...';
		    	}
		     else{	
		     	$query = "INSERT INTO planta (planta,loc) VALUES ('$planta','$loc')";
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>0) {
				$mensaje='Se insert&oacute; el registro...';
			} else { 
				$error=TRUE; $mensaje='ERROR<br>No se insert&oacute; el registro...'. $link='javascript:history.go(-1);'; 
			}
		}
 				 if     ($rowno>0) $mensaje='Ya existe un  registro para esa planta...';		  

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