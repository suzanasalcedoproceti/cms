<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
  /*$modulo=9;
  if (!op($modulo))  {
    $aviso = 'Usuario sin permiso para acceder a este módulo';
    $aviso_link = 'principal.php';
    include('mensaje_sistema.php');
    return;
  }*/
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
	<? $tit='Administrar Tipo de Servicios'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_servicios.php';

		include('../conexion.php');

		$error=FALSE;

		// extrae variables del formulario
		$clave=strtoupper($_POST['clave']);
		$nombre=ucwords($_POST['nombre']);
		$condition_type=ucwords($_POST['condition_type']);
		$idservicio=ucwords($_POST['idservicio']);
		$material=ucwords($_POST['material']);
   
		if (!empty($idservicio)) {  
			     $query = "UPDATE servicios SET tipo_servicio='$clave',descripcion='$nombre',condition_type='$condition_type',material='$material' 
				        WHERE idservicio='$idservicio'";										 		
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>=0) $mensaje='Se actualiz&oacute; el registro...';
			else { $error=TRUE; $mensaje='ERROR<br>No se actualiz&oacute; el registro...';'javascript:history.go(-1);'; }


		}

		else { 

            $query = "INSERT servicios (tipo_servicio, descripcion, condition_type,material) VALUES ('$clave','$nombre','$condition_type','$material')";
			$resultado= mysql_query($query,$conexion);

			$reg= mysql_affected_rows();
			if   ($reg>=0) {
				$mensaje='Se insert&oacute; el registro...  javascript:history.go(-1);';
			} else { 
				$error=TRUE; $mensaje='ERROR<br>No se actualiz&oacute; el registro...'.$reg;'javascript:history.go(-1);'; 
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