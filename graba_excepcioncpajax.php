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
	<? $tit='Administrar Estados'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 

		$link='lista_estado.php';

		include('../conexion.php');

		$error=FALSE;
		global $mensajes_error;
		
		$mensajes_error = array();
		$idcp=$_POST['idcp'];
		$cpexcnuevo=$_POST['cpexcnuevo']; 
		$tipo_producto=$_POST['tipo_producto']; 
		 
            actualizaprecioservicio($idcp, $cpexcnuevo,$tipo_producto);			
			$mensaje= ' Se actualizaron los registros...';
			if($mensajes_error){
				$error = true;
				$mensaje = implode("<br>", $mensajes_error);
				$link='javascript:history.go(-1);';
	 
			
		}

 


function actualizaprecioservicio($idcp, $cpexcnuevo,$tipo_producto){
	global $mensajes_error;
	$error = null;
 
			$sql = "UPDATE excepciones_cp SET 
							 cp='$cpexcnuevo',
							 tipo_producto='$tipo_producto'
				WHERE idexcepciones_cp=$idcp";;
		   //echo $sql."<br>";
			$rs = mysql_query($sql);
		
			$error = mysql_error();
			if($error){
				array_push($mensajes_error, "Hubo un error al actualizar el estado");
			}
 

}
 
	echo $sql;			
 ?>
 
 </div>
</div>
</body>
</html>