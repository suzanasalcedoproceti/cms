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
	<? $tit='Administrar'; include('top.php'); ?>
	<div class="main">
      <p>
        <? 
		include('../conexion.php');
		$error=FALSE;
		global $mensajes_error;		
		$mensajes_error = array(); 
	    $idcobertura = $_POST['idcobertura'];
		$sucursal=$_POST['sucursal'];
		$cobertura=$_POST['cobertura'];
		$idsucursal= $_POST['idsucursal'];	 
		$idcob= explode(',', $_POST['idsucursal']);
 
        $sqledt = "UPDATE cobertura SET cobertura='$cobertura' 	 
				    WHERE idCobertura=$idcobertura";
  	    $resultadoedt= mysql_query($sqledt,$conexion);
  	    $regdedt= mysql_affected_rows(); 
         					 

 
	    $queryval="SELECT * FROM cobertura_sucursal WHERE idCobertura=$idcobertura";
		$resultadoval= mysql_query($queryval,$conexion);
		$rowno=mysql_num_rows($resultadoval);
		    if ($rowno>0) 
		    { $sqldel = "DELETE FROM cobertura_sucursal  
  							WHERE  idCobertura=$idcobertura";
  							$resultadodel= mysql_query($sqldel,$conexion);
         					$regdel= mysql_affected_rows(); 
  			}
foreach($idcob as $valorSelectMultiple){    
         $sql = "INSERT  cobertura_sucursal (idCobertura,idsuc)  
                  VALUES($idcobertura,$valorSelectMultiple)"; 
         $resultadoedt= mysql_query($sql,$conexion);
         $regsuc= mysql_affected_rows();  
  
}
 echo   $sqledt."<br>";//
 echo $regdedt;
?>

</p>
</div>