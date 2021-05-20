<?
   session_cache_limiter('private, must-revalidate');
   session_start();
   if (empty($_SESSION['usr_valido'])) {
     include('logout.php');
         exit;
   }

   include('../conexion.php');
	include('funciones.php');
	include('lib.php');
	$modulo=19;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}

   $hoy = date('d/m/Y');

   header("Content-type: application/vnd.ms-excel");  
   header("Content-Disposition: attachment; filename=puntos_flex_".$hoy.".xls");  

   
   $fecha = $_POST['fecha'];
   $empresa = $_POST['empresa'];
   $texto = $_POST['texto'];
   $tipo = $_POST['tipo'];
   $estatus = $_POST['estatus'];
   if (!isset($estatus)) $estatus = '1';

	 $condicion = "WHERE 1=1 ";

	 if (!empty($fecha)) {
		$fecha_desde = convierte_fecha(substr($fecha,0,10));
		$fecha_hasta = convierte_fecha(substr($fecha,13,10));
		$condicion .= " AND puntos_flex.fecha BETWEEN '$fecha_desde' AND '$fecha_hasta' ";
	 }
	 if ($estatus=='1') $condicion .= " AND estatus = 1 ";
	 if ($estatus=='0') $condicion .= " AND estatus = 0 ";
	 if ($estatus=='9') $condicion .= " AND estatus = 9 ";


?>
  <table>
    <tr>
        <td><b>Folio</b></td>
        <td><b>Tipo</b></td>
        <td><strong>Cantidad</strong></td>
        <td><b>User ID</b></td>
        <td><strong>No. Empleado</strong></td>
        <td><strong>Fecha Solicitud</strong></td>
        <td><strong>Estatus</strong></td>
        <td><strong>Canal</strong></td>
        <td align="center" bgcolor="#F4F4F2" class="texto"><strong>Usuario Canje</strong></td>
        <td><strong>Fecha de Canje</strong></td>
    </tr>
          <?
			 $query = "SELECT * FROM puntos_flex
						       $condicion $orden";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)) { 

				$usuario_canje = '';
				$vendedor = $row['vendedor']+0;
				if ($row['canal']=='POS') {
					$resultadoV = mysql_query("SELECT nombre FROM usuario_tienda WHERE clave = $vendedor");
					$rowV = mysql_fetch_array($resultadoV);
					$usuario_canje = $rowV['nombre'];
				} 
				if ($row['canal']=='TW') {
					$resultadoV = mysql_query("SELECT CONCAT(nombre,' ',apellido_paterno) AS nombre FROM cliente WHERE clave = $vendedor");
					$rowV = mysql_fetch_array($resultadoV);
					$usuario_canje = $rowV['nombre'];
				} 
          ?>
    <tr>
            <td><?= $row['folio']; ?></td>
            <td>
              <? 
			    if ($row['tipo']=='F') echo 'Flex';
			    if ($row['tipo']=='P') echo 'PEP';
			  ?>            </td>
            <td><?= $row['monto'];?></td>
            <td><?= $row['usuario']; ?></td>
            <td><?= $row['empleado']; ?></td>
            <td><?= fecha($row['fecha']); ?></td>
            <td><? switch ($row['estatus']) { 
						case '0' : echo 'Inactivo'; break;
						case '1' : echo 'Activo'; break;
						case '9' : echo 'Cancelado'; break;
					 } ?>            </td>
            <td><? echo $row['canal'];  ?></td>
            <td align="center"  bgcolor="#FFFFFF" class="texto"><?=$usuario_canje;?></td>
            <td><? echo fecha($row['fecha_aplicacion'],'novacio'); ?></td>
    </tr>
    <?
          } // WHILE
    ?>
</table>
