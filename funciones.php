<?

	function op($opcion) {
		//   global $opcs;
	   $opcs = $_SESSION['ss_opciones'];
	   if (strpos($opcs,' '.$opcion.',')===FALSE) return FALSE;
	   else return TRUE;
	}

	function op_aut($opcion) {
		//   global $opcs;
	   $opcs = $_SESSION['ss_autorizar'];
	   if (strpos($opcs,' '.$opcion.',')===FALSE) return FALSE;
	   else return TRUE;
	}
	function nocero($valor) {
		if ($valor!=0) return $valor; else return '';
	}

	function limpia_comillas($string) {
		$xval = str_replace('"','&#8221',$string);
		$xval = str_replace("'",'&#8217;',$xval);
		return $xval;
	}

?>
