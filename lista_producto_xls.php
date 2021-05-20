<?
    if (!include('ctrl_acceso.php')) return;
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Content-type: application/vnd.ms-excel");  
	header("Content-Disposition: attachment; filename=WP Productos.xls");  

	include('funciones.php');
	include("../conexion.php");
	$modulo=4;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	$fcategoria = $_POST['fcategoria'];
	$fsubcategoria = $_POST['fsubcategoria'];
	$fmarca = $_POST['fmarca'];
	$texto = $_POST['texto'];
	$fpromo = $_POST['fpromo'];

	 // construir la condición de búsqueda
	 $condicion = "WHERE (estatus=1 OR estatus=2) AND solo_para_servicio=0 ";

	 if (!empty($fcategoria))
		$condicion .= " AND categoria=$fcategoria ";

	 if (!empty($fsubcategoria))
		$condicion .= " AND subcategoria=$fsubcategoria ";

	 if (!empty($fmarca))
		$condicion .= " AND marca=$fmarca ";

	 if ($fpromo=='N')
		$condicion .= " AND es_nuevo = 1";
	 if ($fpromo=='PS')
		$condicion .= " AND es_promocion = 1";
	 if ($fpromo=='PE')
		$condicion .= " AND es_promocion_especial >0 ";

	 if (!empty($texto)) {
		// identificar si sólo hay 1 palabra o más de 1
		$trozos=explode(" ",$texto);
		$numero_palabras=count($trozos);
		if ($numero_palabras==1 || 1) {
			//SI SOLO HAY UNA PALABRA DE BUSQUEDA SE ESTABLECE UNA INSTRUCION CON LIKE
			$condicion .= "AND (modelo LIKE '%$texto%' OR
								nombre LIKE '%$texto%' OR
								descripcion_larga LIKE '%$texto%') ";	
		} else  { // más de 1 palabras
			//SI HAY UNA FRASE SE UTILIZA EL ALGORTIMO DE BUSQUEDA AVANZADO DE MATCH AGAINST
			$condicion .= " AND MATCH ( modelo, nombre, descripcion_larga ) AGAINST ( '$texto' IN BOOLEAN MODE ) ";
		} 
	 }

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Panel de Control</title>
</head>

<body>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">

          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><b>Modelo  </b></td>
            <td nowrap="nowrap"><b>Nombre  </b></td>
            <td><strong>Categoría</strong></td>
            <td><strong>Subcategor&iacute;a</strong></td>
            <td><strong>Marca</strong></td>
            <td><div align="center"><strong>Promo Semana</strong></div></td>
            <td><div align="center"><strong>Lo m&aacute;s <br />
            Nuevo</strong></div></td>
            <td><div align="center"><strong>En Combo</strong></div></td>
            <td><div align="center"><strong>Promo<br />
            Especial</strong></div></td>
          </tr>
          <?

			 $query = "SELECT * FROM producto $condicion ORDER BY modelo";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 

				 $cata=$row['categoria'];
			     $resCATA= mysql_query("SELECT * FROM categoria WHERE clave=$cata",$conexion);
			     $rowCATA= mysql_fetch_array($resCATA);

				 $cate=$row['subcategoria'];
			     $resCATE= mysql_query("SELECT * FROM subcategoria WHERE clave=$cate",$conexion);
			     $rowCATE= mysql_fetch_array($resCATE);

				 $marca=$row['marca'];
			     $resMAR= mysql_query("SELECT * FROM marca WHERE clave=$marca",$conexion);
			     $rowMAR= mysql_fetch_array($resMAR);

                 $CR=chr(13).chr(10);
			     $fotos = $row['fotos'];
                 $foto = explode($CR,$fotos);
		         if (strlen($fotos)>0) $total_fotos=count($foto);
                 else $total_fotos=0;
				 
				 // revisar si está en combo
				 $producto = $row['clave'];
				 $resultadoCOM = mysql_query("SELECT 1 FROM combo_detalle WHERE producto = $producto LIMIT 1");
				 $en_combo = mysql_num_rows($resultadoCOM);
				 
				 $promo_especial = '';
				 $promo_esp = $row['es_promocion_especial'];
				 if ($promo_esp>0) {
					$resPE = mysql_query("SELECT nombre FROM promo_producto WHERE clave = $promo_esp");
					$rowPE = mysql_fetch_array($resPE);
					if ($rowPE['nombre']!='') 
						$promo_especial = $rowPE['nombre'];
					else
						$promo_especial = '';
				 } 

          ?>
          <tr class="texto" valign="top">
            <td><?= $row['modelo']; ?></td>
            <td><?= $row['nombre']; ?></td>
            <td><?= $rowCATA['nombre']; ?></td>
            <td><?= $rowCATE['nombre']; ?></td>
            <td><?= $rowMAR['nombre']; ?></td>
            <td align="center" nowrap="nowrap"><? if ($row['es_promocion']==1) echo 'SI'; else echo '&nbsp;'; ?></td>
            <td align="center" nowrap="nowrap"><? if ($row['es_nuevo']==1) echo 'SI'; else echo '&nbsp;'; ?></td>
            <td align="center" nowrap="nowrap"><? if ($en_combo>0) echo 'SI'; else echo '&nbsp;'; ?></td>
            <td align="center" nowrap="nowrap"><? echo $promo_especial;?>
            </td>
          </tr>
          <?
                 } // WHILE
          ?>
        </table>
        
</form>    
    </div>
</div>
</body>
</html>
