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
		$idDeterminacion=$_POST['idDeterminacion'];
		$cedis1=$_POST['cedis1']; 
		$cedis2=$_POST['cedis2']; 
		$cedis3=$_POST['cedis3']; 
		$cedis4=$_POST['cedis4']; 
		$cedis5=$_POST['cedis5']; 
		$cedis6=$_POST['cedis6']; 
		$cedis7=$_POST['cedis7']; 
		$cedis8=$_POST['cedis8']; 
		 
            actualizadeterminaplanta($idDeterminacion, $cedis1,$cedis2,$cedis3,$cedis4,$cedis5,$cedis6,$cedis7,$cedis8);			
			$mensaje= ' Se actualizaron los registros...';
			if($mensajes_error){
				$error = true;
				$mensaje = implode("<br>", $mensajes_error);
				$link='javascript:history.go(-1);';	 
			
		}


function actualizadeterminaplanta($idDeterminacion, $cedis1,$cedis2,$cedis3,$cedis4,$cedis5,$cedis6,$cedis7,$cedis8){
	global $mensajes_error;
	$error = null;
 
			$sql ="UPDATE determina_planta SET 
							 cedis1='$cedis1',
							 cedis2='$cedis2',
							 cedis3='$cedis3',
							 cedis4='$cedis4',
							 cedis5='$cedis5',
							 cedis6='$cedis6',
							 cedis7='$cedis7',
							 cedis8='$cedis8'
				WHERE idDeterminacion=$idDeterminacion";
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