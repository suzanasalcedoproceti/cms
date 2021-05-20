<?php 

$html = '';
   $base="wp_test";  
   $conexion=mysql_connect("127.0.0.1","root","");
   mysql_select_db($base,$conexion);
   mysql_query("SET character_set_results=utf8", $conexion);
   mysql_query("set names 'utf8'",$conexion);

$id_cp = $_POST['id_cp'];

 $query = "SELECT asenta FROM cp_sepomex 
    WHERE cp = $id_cp ORDER BY asenta ASC";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
             	 $html .= '<option value="'.$row['asenta'].'">'.$row['asenta'].'</option>';

             }
 

echo  $html;
?>