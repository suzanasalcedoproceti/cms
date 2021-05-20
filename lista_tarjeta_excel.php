<?
   session_cache_limiter('private, must-revalidate');
   session_start();
   if (empty($_SESSION['usr_valido'])) {
     include('logout.php');
         exit;
   }

	include('funciones.php');
	$modulo=7;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}

   $lote = $_GET['lote'];

   header("Content-type: application/vnd.ms-excel");  
   header("Content-Disposition: attachment; filename=tarjetas_whirlpool_lote_".$lote.".xls");  

   include('../conexion.php');


?>

  <table>
    <tr>
      <td><strong>Código</strong></td>
      <td><strong>Empresa</strong></td>
      <td><strong>Cliente</strong></td>
      <td><strong>Activaci&oacute;n</strong></td>
    </tr>
    <?
		 $resTAR= mysql_query("SELECT * FROM tarjeta WHERE lote='$lote' ORDER BY clave",$conexion);
		 while ($rowTAR = mysql_fetch_array($resTAR)) { 

				$empresa=$rowTAR['empresa'];
                $resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
                $rowEMP= mysql_fetch_array($resEMP);

				$cliente=$rowTAR['cliente'];
				$resCLI= mysql_query("SELECT *,CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) AS nombre  FROM cliente WHERE clave='$cliente'",$conexion);
                $rowCLI= mysql_fetch_array($resCLI);

    ?>
    <tr>
      <td><?= $rowTAR['codigo']; ?></td>
      <td><?= $rowEMP['nombre']; ?></td>
      <td><? if (!$rowTAR['ilimitada']) echo $rowCLI['nombre']; ?></td>
      <td><? if ($rowTAR['fecha']!='0000-00-00 00:00:00' && !$rowTAR['ilimitada']) echo date('d/m/Y H:i:s',strtotime($rowTAR['fecha'])); ?></td>
    </tr>
    <?
          } // WHILE
    ?>
</table>
