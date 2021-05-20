<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=7;
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
	<? $tit='Generar Tarjetas'; include('top.php'); ?>
	<div class="main"></div>
</div>
<p>
<?

	function codigo_random() {  // genera codigo random (10 caracteres alfanuméricos)
	   srand((double)microtime()*1000000);
	   $codigo='';
	   for ($i=1; $i<=10; $i++) {
		 if (rand(0,1)==0) // letra
		   $codigo=$codigo.chr(rand(97,102));
		 else // numero
		   $codigo=$codigo.chr(rand(48,57));
	   }
	   return $codigo;
	}	

			    $empresa = $_POST['empresa'];
				$cantidad = 1;
				$ilimitada = 1;

                include('../conexion.php');
				
				// determina numero de siguiente lote
				
				$resTAR= mysql_query("SELECT * FROM tarjeta ORDER BY lote DESC LIMIT 1",$conexion);
				$rowTAR= mysql_fetch_array($resTAR);				
				$lote= $rowTAR['lote']+1;

				$total=0;						
			    for ($i=1; $i<=$cantidad; $i++) {
				
					$encontrado=TRUE;
					while ($encontrado) {
						$codigo = codigo_random();
					
					    // revisa que no exista otra tarjeta con ese codigo
						$resTAR= mysql_query("SELECT * FROM tarjeta WHERE codigo='$codigo'",$conexion);
						if (mysql_num_rows($resTAR)==0) $encontrado=$FALSE;
					}
					
					// graba la tarjeta
					 $resultado= mysql_query("INSERT tarjeta (lote,
										                      codigo,
															  empresa,
															  ilimitada)
													  VALUES ('$lote',
															  '$codigo',
															  '$empresa',
															  $ilimitada)",$conexion); 
					$total++;
				
					// echo '<br>LOTE: '.$lote. ' CODIGO: '.$codigo.'  PRECIO: '.$precio;

				
				
				}
	

				$mensaje= 'Se han generado <strong>'.$total.'</strong> Tarjetas.';


				$link='lista_tarjeta.php';

            ?>
</p><p>&nbsp;</p>
<? include('mensaje.php'); ?>
</body>
</html>
