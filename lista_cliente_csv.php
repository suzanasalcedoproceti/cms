<?php
if (!include('ctrl_acceso.php')) return;
include('funciones.php');

$modulo=8;
if (!op($modulo))  {
  return;
}

ini_set('max_execution_time','1000000');
ini_set('max_input_time','1000000');
ini_set("memory_limit", "2560M");


header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream; charset=utf-8");
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=WP Clientes.csv");


include('../conexion.php');
//include('lib.php');

$empresa = $_POST['empresa'];
$texto = $_POST['texto'];
$tipo = $_POST['tipo'];
$estatus = $_POST['estatus'];
if (!$estatus) $estatus = 'A';

$eflex = $_POST['eflex'];
if (!isset($_POST['eflex'])) $eflex= 'x';
$epep = $_POST['epep'];
if (!isset($_POST['epep'])) $epep= 'x';
$puntos = $_POST['puntos'];
if (!isset($_POST['puntos'])) $puntos= 'x';
$puntos_convenio = $_POST['puntos_convenio'];
if (!isset($_POST['puntos_convenio'])) $puntos_convenio= 'x';

 $condicion = "WHERE empresa.empresa_proyectos = 0 ";

 if (!empty($empresa))
  $condicion .= "AND empresa='$empresa'";

if (!empty($tipo)) {
          /*  if ($tipo=='I')
              $condicion .= "AND invitado = 1 ";
            else
              $condicion .= "AND invitado = 0 ";
          */
            $condicion .= " AND cliente.tipo = '$tipo' ";

          }
if ($estatus=='A') $condicion .= " AND activo = 1 ";
if ($estatus=='I') $condicion .= " AND activo = 0 ";

if ($eflex=='1') $condicion .= " AND puntos_flex > 0 ";
if ($eflex=='0') $condicion .= " AND puntos_flex <= 0 ";
if ($epep=='1') $condicion .= " AND puntos_pep > 0 ";
if ($epep=='0') $condicion .= " AND puntos_pep <= 0 ";
if ($puntos=='1') $condicion .= " AND cliente.puntos > 0 ";
if ($puntos=='0') $condicion .= " AND cliente.puntos <= 0 ";
if ($puntos_convenio=='1') $condicion .= " AND puntos_convenio > 0 ";
if ($puntos_convenio=='0') $condicion .= " AND puntos_convenio <= 0 ";


 if (!empty($texto)) {
  // identificar si sólo hay 1 palabra o más de 1
  $trozos=explode(" ",$texto);
  $numero_palabras=count($trozos);
  if (1 || $numero_palabras==1) {
    //SI SOLO HAY UNA PALABRA DE BUSQUEDA SE ESTABLECE UNA INSTRUCION CON LIKE
    $condicion .= "AND (cliente.nombre LIKE '%$texto%'  OR cliente.apellido_paterno LIKE '%$texto%' OR cliente.apellido_materno LIKE '%$texto%'  OR email LIKE '%$texto%' OR numero_empleado LIKE '%$texto%') ";
  } else  { // más de 1 palabras
    //SI HAY UNA FRASE SE UTILIZA EL ALGORTIMO DE BUSQUEDA AVANZADO DE MATCH AGAINST
    //busqueda de frases con mas de una palabra y un algoritmo especializado
    //$condicion .= " SELECT titulo, descripcion , MATCH ( titulo, descripcion ) AGAINST ( '$texto' ) AS Score FROM anuncio WHERE MATCH ( titulo, descripcion, ciudad, estado ) AGAINST ( '$texto' ) ORDER BY score DESC";
    $condicion .= " AND MATCH ( nombre_completo, email, numero_empleado ) AGAINST ( '$texto' IN BOOLEAN MODE ) ";
  }
 }

// en un solo renglón
$array_enc = array('Clave',
                    'Nombre',
                    'Apellido Paterno',
                    'Apellido Materno',
                    'Tipo',
                    'Cliente SAP',
                    '# Empleado',
                    'E-mail',
                    'Teléfono',
                    'Celular',
                    'Empresa',
                    'pe_disponibles',
                    'Puntos',
                    'Puntos Flex',
                    'Puntos PEP',
                    'Puntos Convenio',
                    'Activo',
                    'Correo',
                    'Calle',
                    'Numero',
                    'Colonia',
                    'Ciudad',
                    'Estado',
                    'Recepción de Promociones',
                    'Estudio de Mercado',
                    'Ultima Fecha Compra',
                    'Cantidad de Articulos Comprado',
                    'Fecha Registro',
                    'Fecha Ultima Actualizacion');

foreach ($array_enc AS $enc) {
    $var_enc .= utf8_decode($enc).',';
}
$var_enc = substr($var_enc,0,-1);

echo $var_enc;

$query = "SELECT cliente.*, CONCAT(cliente.nombre,' ',cliente.apellido_paterno,' ',cliente.apellido_materno) AS nombre_completo, empresa.nombre AS nombre_empresa , empresa.cliente_sap
      FROM cliente
      LEFT JOIN empresa ON cliente.empresa = empresa.clave
      $condicion
      ORDER BY empresa.nombre, cliente.nombre, cliente.apellido_paterno, cliente.apellido_materno";

//echo $query;
$i = 0;
$resCLI= mysql_query($query,$conexion);

while ($rowCLI = mysql_fetch_array($resCLI)) {

  $i++;
  if ($rowCLI['invitado']==1) $tipo = 'Invitado'; else $tipo = 'Empleado';
  if ($rowCLI['activo']==1) $activo = 'SI'; else $activo = 'NO'

    // dejar un renglón en blanco fuera del php para que brinque de renglón el CSV
?>

<?php
// no brincar de renglón
$cve_cliente=$rowCLI['clave'];
$resPED= mysql_query("SELECT COUNT(*) as compras, MAX(fecha) as fecha FROM pedido p INNER JOIN detalle_pedido dp on p.folio=dp.pedido WHERE p.cliente='$cve_cliente'",$conexion);
$rowCom = mysql_fetch_array($resPED);
/*echo "<pre>";
print_r($rowCLI);
echo "</pre>";*/
$array_val = array($rowCLI['clave'],
                    $rowCLI['nombre'],
                    $rowCLI['apellido_paterno'],
                    $rowCLI['apellido_materno'],
                    $tipo,
                    $rowCLI['cliente_sap'],
                    $rowCLI['numero_empleado'],
                    $rowCLI['email'],
                    $rowCLI['pers_telefono'],
                    $rowCLI['pers_celular'],
                    $rowCLI['nombre_empresa'],
                    $rowCLI['pe_disponibles'],
                    $rowCLI['puntos'],
                    $rowCLI['puntos_flex'],
                    $rowCLI['puntos_pep'],
                    $rowCLI['puntos_convenio'],
                    $activo,
                    $rowCLI['email'],
                    $rowCLI['pers_calle'],
                    $rowCLI['pers_interior'],
                    $rowCLI['pers_colonia'],
                    $rowCLI['pers_ciudad'],
                    $rowCLI['pers_estado'],
                    ($rowCLI['recibir_informacion']==1) ? 'SI' : (($rowCLI['recibir_informacion']=='') ? '' : 'NO'),
                    ($rowCLI['participar_estudios']==1) ? 'SI' : (($rowCLI['participar_estudios']=='') ? '' : 'NO'),
                    $rowCom['fecha'],
                    $rowCom['compras'],
                    substr($rowCLI['fecha_registro'],0,10),
                    substr($rowCLI['fecha_actualizacion'],0,10)
                    );
$var_val = '';
foreach ($array_val AS $val) {
    $xval = str_replace(',', '', $val);
    $var_val .= trim($xval).',';
}
$var_val = substr($var_val,0,-1);

echo $var_val;

}
?>