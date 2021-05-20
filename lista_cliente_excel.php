<?
   session_cache_limiter('private, must-revalidate');
   session_start();
   if (empty($_SESSION['usr_valido'])) {
     include('logout.php');
         exit;
   }

	include('funciones.php');
	$modulo=8;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}

   $hoy = date('d/m/Y');

   header("Content-type: application/vnd.ms-excel");  
   header("Content-Disposition: attachment; filename=clientes_whirlpool_".$hoy.".xls");  

   include('../conexion.php');
   
   $empresa = $_POST['empresa'];
   $texto = $_POST['texto'];
   $tipo = $_POST['tipo'];
   $estatus = $_POST['estatus'];
   if (!$estatus) $estatus = 'A';

   $eflex = $_POST['eflex'];
   if (!isset($_POST['eflex'])) $eflex= 'x';
   $epep = $_POST['epep'];
   if (!isset($_POST['epep'])) $epep= 'x';
   
	 $condicion = "WHERE 1=1 ";

	 if (!empty($empresa)) 
		$condicion .= "AND empresa='$empresa'";

	 if (!empty($tipo)) {
		if ($tipo=='I') 
			$condicion .= "AND invitado = 1 ";
		else
			$condicion .= "AND invitado = 0 ";
	}
	if ($estatus=='A') $condicion .= " AND activo = 1 ";
	if ($estatus=='I') $condicion .= " AND activo = 0 ";

	if ($eflex=='1') $condicion .= " AND puntos_flex > 0 ";
	if ($eflex=='0') $condicion .= " AND puntos_flex <= 0 ";
	if ($epep=='1') $condicion .= " AND puntos_pep > 0 ";
	if ($epep=='0') $condicion .= " AND puntos_pep <= 0 ";
	
	 
	 if (!empty($texto)) {
		// identificar si sólo hay 1 palabra o más de 1
		$trozos=explode(" ",$texto);
		$numero_palabras=count($trozos);
		if (1 || $numero_palabras==1) {
			//SI SOLO HAY UNA PALABRA DE BUSQUEDA SE ESTABLECE UNA INSTRUCION CON LIKE
			$condicion .= "AND (nombre LIKE '%$texto%'  OR apellido_paterno LIKE '%$texto%' OR apellido_materno LIKE '%$texto%'  OR email LIKE '%$texto%' OR numero_empleado LIKE '%$texto%') ";	
		} else  { // más de 1 palabras
			//SI HAY UNA FRASE SE UTILIZA EL ALGORTIMO DE BUSQUEDA AVANZADO DE MATCH AGAINST
			//busqueda de frases con mas de una palabra y un algoritmo especializado
			//$condicion .= " SELECT titulo, descripcion , MATCH ( titulo, descripcion ) AGAINST ( '$texto' ) AS Score FROM anuncio WHERE MATCH ( titulo, descripcion, ciudad, estado ) AGAINST ( '$texto' ) ORDER BY score DESC";
			$condicion .= " AND MATCH ( nombre, email, numero_empleado ) AGAINST ( '$texto' IN BOOLEAN MODE ) ";
		} 
	 }
      

?>
  <table>
    <tr>
      <td><strong>Clave</strong></td>
      <td><strong>Nombre</strong></td>
      <td><strong>Tipo</strong></td>
      <td><strong>Cliente SAP</strong></td>
      <td><strong># Empleado</strong></td>
      <td><strong>E-mail</strong></td>
      <td><strong>Tel&eacute;fono</strong></td>
      <td><strong>Celular</strong></td>
      <td><strong>Empresa</strong></td>
      <td><strong>Público G.</strong></td>
      <td><strong>Registro</strong></td>
      <td><strong>Puntos</strong></td>
      <td><strong>Puntos Flex</strong></td>
      <td><strong>Puntos PEP</strong></td>
      <td><strong>Recepción de Promociones</strong></td>
      <td><strong>Estudio de Mercado</strong></td>      
      <td><strong>Activo</strong></td>
    </tr>
    <?
		 $resCLI= mysql_query("SELECT *, CONCAT(cliente.nombre,' ',cliente.apellido_paterno,' ',cliente.apellido_materno) AS nombre FROM cliente $condicion ORDER BY empresa, nombre",$conexion);
		 while ($rowCLI = mysql_fetch_array($resCLI)) { 

			$empresa=$rowCLI['empresa'];
			$resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
			$rowEMP= mysql_fetch_array($resEMP); 

    ?>
    <tr>
      <td><?= $rowCLI['clave']; ?></td>
      <td><?= $rowCLI['nombre']; ?></td>
      <td><? if ($rowCLI['invitado']==1) echo 'Invitado'; else echo 'Empleado'; ?></td>
      <td><?= $rowEMP['cliente_sap']; ?></td>
      <td align="left"><?= $rowCLI['numero_empleado']; ?></td>
      <td><?= $rowCLI['email']; ?></td>
      <td><?= $rowCLI['pers_telefono']; ?></td>
      <td><?= $rowCLI['pers_celular']; ?></td>     
      <td><?= $rowEMP['nombre']; ?></td>
      <td><? if ($rowEMP['empresa_publica']==1) echo 'SI'; else echo '&nbsp;'; ?></td>
      <td><?= date('d/m/Y',strtotime($rowCLI['fecha'])); ?></td>
      <td align="right"><? if ($rowCLI['puntos']>0) echo $rowCLI['puntos']; ?></td>
      <td align="right"><? if ($rowCLI['puntos_flex']>0) echo $rowCLI['puntos_flex']; ?></td>
      <td align="right"><? if ($rowCLI['puntos_pep']>0) echo $rowCLI['puntos_pep']; ?></td>
      <td><?= $rowCLI['recibir_informacion']; ?></td>
      <td><?= $rowCLI['participar_estudios']; ?></td>
      <td><? if ($rowCLI['activo']==1) echo 'SI'; else echo 'NO'; ?></td>
    </tr>
    <?
          } // WHILE
		  mysql_close();
    ?>
</table>
