<?

// Autor: Ricardo Rangel Navarro

function convierte_fecha($vfecha) {
	if (empty($vfecha) || $vfecha=="  /  /    " || $vfecha=="--" || $vfecha=="- -" || $vfecha=="//") {
		return "0000-00-00";
	} else {
		return substr($vfecha,6,4).'-'.substr($vfecha,3,2).'-'.substr($vfecha,0,2);
	}
}


function dia_semana($vfecha) {
	$dia=array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
	return $dia[date('w',strtotime($vfecha))];
}

function fecha($vfecha) {
	// 1 MAY 2009
	if ($vfecha=='0000-00-00' || $vfecha =='') return '';
	$mes=array('','ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC');
    return date('j ',strtotime($vfecha)).$mes[date('n',strtotime($vfecha))].date(' Y ',strtotime($vfecha));
}

function fechamy2mx($mifecha,$tipo="normal") {
	if ($tipo=="novacio")
		if ($mifecha=="0000-00-00" || $mifecha=="") return "";
	return substr($mifecha,8,2)."/".substr($mifecha,5,2)."/".substr($mifecha,0,4);
}

function genera_password($pwd, $salt = null) {
	if ($salt === null)
		$salt = substr(md5(uniqid(rand(), true)), 0, 9);
	else
		$salt = substr($salt, 0, 9);
	return $salt.md5($salt.$pwd);
}

function fecha_dash($mifecha,$tipo="normal") {  // formato  mm/dd/aaaa
	if ($tipo=="novacio")
	if ($mifecha=="0000-00-00" || $mifecha=="" || $mifecha =="0") return "";
	return substr($mifecha,5,2)."/".substr($mifecha,8,2)."/".substr($mifecha,0,4);
}




?>