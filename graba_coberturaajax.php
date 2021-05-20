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
	<? $tit='Administrar Cobertura'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_ciudad.php';

		include('../conexion.php');

		$error=FALSE;
		global $mensajes_error;
		
		$mensajes_error = array(); 
	    $idcobertura = $_POST['idcobertura'];
		$sucursal=$_POST['sucursal'];
		$cobertura=$_POST['cobertura'];
		$idsucursal= $_POST['idsucursal'];
		 
            actualizasucursal_cobertura($idcobertura,$cobertura,$idsucursal);			
			$mensaje= ' Se actualizaron los registros...';
			if($mensajes_error){
				$error = true;
				$mensaje = implode("<br>", $mensajes_error);
				$link='javascript:history.go(-1);';
	 
			
		}

 


function actualizasucursal_cobertura($idcobertura,$cobertura,$idsucursal){
	global $mensajes_error;
	$error = null;
 
    $sql = " UPDATE cobertura SET 
							 cobertura='$cobertura',  
							 idSucursal='$idsucursal'							 
				WHERE idCobertura=$idcobertura";
	 
			$rs = mysql_query($sql);		
			$error = mysql_error();
			if($error){
				array_push($mensajes_error, "Hubo un error al actualizar");
			}
			 

echo($sql);
}
 

				
 ?>
 
 </div>
</div>
</body>
</html>