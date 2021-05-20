<?
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Content-type: application/vnd.ms-excel");  
	header("Content-Disposition: attachment; filename=WP Detalle Productos.xls");  

    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include('../conexion.php');

	$fcategoria = $_POST['fcategoria']+0;
	$fsubcategoria = $_POST['fsubcategoria']+0;
	$fmarca = $_POST['fmarca'];
	$hay_subcategorias = 1;
	$filtrosub = "";
	if ($fcategoria) {
		$resultado = mysql_query("SELECT 1 FROM subcategoria WHERE categoria = $fcategoria",$conexion);
		$hay_subcategorias = (mysql_num_rows($resultado)>0) ? 1 : 0;
	}
	if ($hay_subcategorias)  	$filtrosub = " AND subcategoria = $fsubcategoria";
	 // construir la condición de búsqueda
	 $condicion = "WHERE (estatus=1 OR estatus=2)";
	
	 if ($fcategoria>0)
		$condicion .= " AND categoria=$fcategoria ";
	
	 if (!empty($fsubcategoria))
		$condicion .= " AND subcategoria=$fsubcategoria ";
	
	 if (!empty($fmarca))
		$condicion .= " AND marca=$fmarca ";
?>

<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr class="texto" bgcolor="#F4F4F2">
    <td nowrap="nowrap"><b>Modelo</b></td>
    <td nowrap="nowrap"><b>Nombre</b></td>
    <td><strong>Marca</strong></td>
    <td><strong>Categoría TW</strong></td>
    <td><strong>Subcategor&iacute;a TW</strong></td>
    <td><div align="center"><strong>Segmento SM</strong></div></td>
    <td><div align="center"><strong>Categor&iacute;a SM</strong></div></td>
    <td><div align="center"><strong>Subcategor&iacute;a SM</strong></div></td>
    <td align="center"><strong>Color</strong></td>
    <td align="center" nowrap="nowrap"><strong>Descripci&oacute;n<br />
Stage</strong></td>
    <td align="center"><strong>Descripci&oacute;n <br />
    Larga</strong></td>
    <td align="center"><strong>T&eacute;rminos de Garant&iacute;a</strong></td>
    <td align="center"><strong>Garant&iacute;a Extendida</strong></td>
    <td align="center"><strong>Otras Caracter&iacute;sticas</strong></td>
    <td align="center"><strong>Vol Reb</strong></td>
    <td>&nbsp;</td>
    <? // campos adicionales 
	  $query = "SELECT campo.* FROM campo 
				   WHERE categoria = $fcategoria $filtrosub 
				   ORDER BY campo.orden";
	  $resultadoCA = mysql_query($query,$conexion);
	  while ($rowCA = mysql_fetch_array($resultadoCA)) {
	?>
    <td><div align="center"><strong><?= $rowCA['nombre_campo'];?></strong></div></td>
    <? 
	  } // while CA
	?>
  </tr>
  <?

     $query = "SELECT * FROM producto $condicion ORDER BY modelo";
     
     $resultado= mysql_query($query,$conexion);

     while ($row = mysql_fetch_array($resultado)){ 

         $producto = $row['clave'];
         
         $cata=$row['categoria'];
         $resCATA= mysql_query("SELECT * FROM categoria WHERE clave=$cata",$conexion);
         $rowCATA= mysql_fetch_array($resCATA);

         $cate=$row['subcategoria'];
         $resCATE= mysql_query("SELECT * FROM subcategoria WHERE clave=$cate",$conexion);
         $rowCATE= mysql_fetch_array($resCATE);

         $marca=$row['marca'];
         $resMAR= mysql_query("SELECT * FROM marca WHERE clave=$marca",$conexion);
         $rowMAR= mysql_fetch_array($resMAR);
         
         // obtener de acuerdo a la marca, la categorización de sitio de marca
         // obtener la tienda de marca de esta marca
         $resTM = mysql_query("SELECT clave FROM tienda_marca WHERE marca = $marca",$conexion);
         $rowTM = mysql_fetch_assoc($resTM);
         $tienda_marca = $rowTM['clave'];
         
         // obtener seg, cat y scat
         $resSEG = mysql_query("SELECT m_segmento.nombre AS nombre_segmento, m_categoria.nombre AS nombre_categoria, m_subcategoria.nombre AS nombre_subcategoria
                                  FROM m_producto 
                                  LEFT JOIN m_segmento ON m_producto.segmento = m_segmento.clave
                                  LEFT JOIN m_categoria ON m_producto.categoria = m_categoria.clave
                                  LEFT JOIN m_subcategoria ON m_producto.subcategoria = m_subcategoria.clave
                                 WHERE m_producto.tienda = $tienda_marca 
                                   AND producto = $producto",$conexion);
         $rowSEG = mysql_fetch_assoc($resSEG);

  ?>
  <tr class="texto" bgcolor="#FFFFFF">
    <td><?= $row['modelo']; ?></td>
    <td><?= $row['nombre']; ?></td>
    <td><?= $rowMAR['nombre']; ?></td>
    <td><?= $rowCATA['nombre']; ?></td>
    <td><?= $rowCATE['nombre']; ?></td>
    <td><?= $rowSEG['nombre_segmento'];?></td>
    <td><?= $rowSEG['nombre_categoria'];?></td>
    <td><?= $rowSEG['nombre_subcategoria'];?></td>
    <td><?= $row['color']; ?></td>
    <td><?= $row['descripcion_stage']; ?></td>
    <td><?= $row['descripcion_larga']; ?></td>
    <td><?= $row['terminos_garantia']; ?></td>
    <td><?= $row['garantia_extendida']; ?></td>
    <td><?= $row['otras_caracteristicas']; ?></td>
    <td><?= $row['vol_reb']; ?></td>
    <td>&nbsp;</td>
    <? // campos adicionales 
	  $query = "SELECT campo.* FROM campo 
				   WHERE categoria = $fcategoria $filtrosub 
				   ORDER BY campo.orden";
	  $resultadoCA = mysql_query($query,$conexion);
	  while ($rowCA = mysql_fetch_array($resultadoCA)) {
		$ic = substr($rowCA['campo_tabla'],6,3);
		$nombrecampo = "campo_".$ic;
	?>
    <td><?= $row[$nombrecampo];?></td>
    <? 
	  } // while CA
	?>    
  </tr>
  <?
         } // WHILE
      ?>
</table>