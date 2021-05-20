<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=14;
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
	<? $tit='Configuración de Family & Friends'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='principal.php';

		include('../conexion.php');

		$error=FALSE;
		
        $resultado= mysql_query("SELECT * FROM config WHERE reg = 1 ",$conexion);
        $row = mysql_fetch_array($resultado);
		$limite_invitados  = $row['limite_invitados'];
		$empresa = $_POST['empresa']+0;
		
		if ($empresa > 0) $filtro_emp = " AND clave = $empresa "; else $filtro_emp = "";
		
		// actualizar invitados solo de empresas con invitaciones permitidas
		// recorrer em
		$resultadoE= mysql_query("SELECT clave, nombre FROM empresa WHERE estatus = 1 AND invita_amigos = 1 $filtro_emp",$conexion);
		$afectados = 0;
		while ($rowE = mysql_fetch_array($resultadoE)) {
			$empresa = $rowE['clave'];
			$query = "UPDATE cliente SET invitados_disponibles = $limite_invitados, act = 1-act WHERE empresa = $empresa AND invitado = 0";
			$resultado = mysql_query($query,$conexion);
			$reg = mysql_affected_rows();
			if   ($reg>=1) $mensaje.='Se actualizaron '.$reg.' empleados de la empresa '.$rowE['nombre']."<br>";
			else { 
				$error=TRUE; 
				$mensaje.='No se actualizaron empleados de la empresa '.$rowE['nombre'].'<br>'; 
				$link='javascript:history.go(-1);'; 
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
