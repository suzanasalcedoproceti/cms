<?

// Autor: Ricardo Rangel Navarro
if (!function_exists('convierte_fecha')) {
function convierte_fecha($vfecha) {
	if (empty($vfecha) || $vfecha=="  /  /    " || $vfecha=="--" || $vfecha=="- -" || $vfecha=="//") {
		return "0000-00-00";
	} else {
		return substr($vfecha,6,4).'-'.substr($vfecha,3,2).'-'.substr($vfecha,0,2);
	}
}
}
if (!function_exists('dia_semana')) {
function dia_semana($vfecha) {
	$dia=array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
	return $dia[date('w',strtotime($vfecha))];
}
}
if (!function_exists('fecha')) {
function fecha($vfecha) {
	// 1 MAY 2009
	if ($vfecha=='0000-00-00' || $vfecha =='') return '';
	$mes=array('','ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC');
    return date('j ',strtotime($vfecha)).$mes[date('n',strtotime($vfecha))].date(' Y ',strtotime($vfecha));
}
}
if (!function_exists('fechamy2mx')) {
function fechamy2mx($mifecha,$tipo="normal") {
	if ($tipo=="novacio")
		if ($mifecha=="0000-00-00" || $mifecha=="") return "";
	return substr($mifecha,8,2)."/".substr($mifecha,5,2)."/".substr($mifecha,0,4);
}
}
if (!function_exists('genera_password')) {
function genera_password($pwd, $salt = null) {
	if ($salt === null)
		$salt = substr(md5(uniqid(rand(), true)), 0, 9);
	else
		$salt = substr($salt, 0, 9);
	return $salt.md5($salt.$pwd);
}
}
if (!function_exists('fecha_dash')) {
function fecha_dash($mifecha,$tipo="normal") {  // formato  mm/dd/aaaa
	if ($tipo=="novacio")
	if ($mifecha=="0000-00-00" || $mifecha=="" || $mifecha =="0") return "";
	return substr($mifecha,5,2)."/".substr($mifecha,8,2)."/".substr($mifecha,0,4);
}
}

if ($_GET['estado']) {
	$estado = $_GET['estado'];

	include_once("../conexion.php");
	$res = mysql_query("SELECT distinct mnpio,cve_mnpio FROM cp_sepomex where cve_estado=$estado order by mnpio asc");
	$data = "<option value=''>Cualquier municipio...</option>";
	while ($rowEDO = mysql_fetch_array($res)) {
	$data .= '<option value="'.$rowEDO['cve_mnpio'].'">'.$rowEDO['mnpio'].'</option>';
	}
	echo $data;
}

if ($_GET['clavecluster']) {
	$clavecluster = $_GET['clavecluster'];

	include_once("../conexion.php");
	$res = mysql_query("SELECT subtipo_producto FROM subtipo_producto where clave='$clavecluster' order by idsubtipo_producto asc");
	$data = "<option value=''>Cualquier subtipoproducto ...</option>";
	while ($rowEDO = mysql_fetch_array($res)) {
	$data .= '<option value="'.$rowEDO['subtipo_producto'].'">'.$rowEDO['subtipo_producto'].'</option>';
	}
	echo $data;
}
if ($_GET['tipoprod']) {
	$tipoprod = $_GET['tipoprod'];

	include_once("../conexion.php");
	$res = mysql_query("SELECT subtipo_producto FROM subtipo_producto where clave='$tipoprod' order by idsubtipo_producto asc");
	$data = "<option value=''>Seleccione ...</option>";
	while ($rowEDO = mysql_fetch_array($res)) {
	$data .= '<option value="'.$rowEDO['subtipo_producto'].'">'.$rowEDO['subtipo_producto'].'</option>';
	}
	echo $data;
}

if ($_GET['claveestado']) {
	$claveestado = $_GET['claveestado'];

	include_once("../conexion.php");
	$res = mysql_query("SELECT * FROM sucursales where cve_estado='$claveestado' order by idSucursal");
	$data = "<option value=''>Seleccione</option>";
	$data .= "<option value='0'>N/A</option>";
	while ($rowSUC = mysql_fetch_array($res)) {
	$data .= '<option value="'.$rowSUC['idsuc'].'">'.$rowSUC['nombresucursal'].'  '. " (ID: ". $rowSUC['idsuc']. ")" .' </option>';

	}
	echo $data;
}

if ($_GET['cluster']) {
	$cluster = $_GET['cluster'];

	include_once("../conexion.php"); 

	$res = mysql_query("SELECT distinct subcluster.idsubcluster,subcluster.subcluster, subcluster.cve_mnpio FROM subcluster  inner join cp_sepomex 
		on cp_sepomex.cve_mnpio=subcluster.cve_mnpio
		and  cp_sepomex.cve_estado=subcluster.cve_estado
		where cp_sepomex.cve_estado=$cluster and  subcluster.subcluster> 0  group by subcluster.subcluster");
	$data = "<option value=''>Subcluster...</option>";
	while ($rowEDO = mysql_fetch_array($res)) {
		$cve_mpio= $rowEDO['subcluster'];
		$rest="SELECT distinct mnpio FROM cp_sepomex WHERE cve_mnpio = $cve_mpio and cve_estado=$cluster";  
	$data .= '<option value="'.$rowEDO['subcluster'].'">'.$rowEDO['subcluster'].'</option>';
	}
	echo $data;
}



if ($_GET['cve_cluster']) {
	$cve_cluster = $_GET['cve_cluster'];

	include_once("../conexion.php");  

	$res = mysql_query("SELECT *  FROM subcluster   
		where cluster=$cve_cluster and  subcluster.subcluster> 0  group by subcluster.subcluster");
	$data = "<option value=''>Subcluster...</option>";
	while ($rowEDO = mysql_fetch_array($res)) { 
	$data .= '<option value="'.$rowEDO['subcluster'].'">'.$rowEDO['subcluster'].'</option>';
	}
	echo $data;
}



if ($_GET['cve_estado']) {
	$clave_estado = $_GET['cve_estado'];

	include_once("../conexion.php");  


    $resultadosrv = mysql_query("SELECT cluster FROM estados WHERE cve_estado = $clave_estado",$conexion);
    $rowsrv= mysql_fetch_assoc($resultadosrv);
    $idcluster=$rowsrv['cluster'];

    $ret="SELECT *  FROM subcluster   
		where cluster= $idcluster and cve_estado =$clave_estado  group by subcluster";

	$res = mysql_query("SELECT *  FROM subcluster   
		where cluster= $idcluster and cve_estado =$clave_estado  group by subcluster");
	$data = "<option value=''>Subcluster...</option>";
	while ($rowEDO = mysql_fetch_array($res)) { 
	$data .= '<option value="'.$rowEDO['subcluster'].'">'.$rowEDO['subcluster'].'</option>';
	}
	echo $data;
}

if ($_GET['cvecluster']) {
	$cvecluster = $_GET['cvecluster'];

	include_once("../conexion.php");  

	$res = mysql_query("SELECT *  FROM estados where cluster=$cvecluster");
	$data = "<option value=''>Estado...</option>";
	while ($rowEDO = mysql_fetch_array($res)) { 
	$data .= '<option value="'.$rowEDO['cve_estado'].'">'.$rowEDO['estado'].'</option>';
	}
	echo $data;
}


?>