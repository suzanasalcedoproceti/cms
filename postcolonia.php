<?php  
$html = ''; 
   $base="whirlpoolqa2";  
   //$conexion=mysql_connect("mty-mysqlq01","qa2_user","C@lidad1");
   require_once("../conexion.php");
   mysql_select_db($base,$conexion);
   
   mysql_query("set names 'utf8'",$conexion);
   mysql_set_charset('latin1', $conexion);
  $id_cp = $_POST['id_cp'];
  $id_scr = $_POST['id_scr'];
  $colscr = $_POST['colscr'];

 $query = "SELECT * FROM cp_sepomex 
    WHERE codigo = ".$id_cp." ORDER BY asenta ASC";

             $resultado= mysql_query($query,$conexion);  
             $html .= '<tr><td><div align="right">Colonia:</div></td>
            <td >   
             <select name="colonia" id="colonia" class="form-control">';

             while ($row = mysql_fetch_array($resultado)){ 
             	// $html .= '<option value="'.$row['asenta'].'" >'.$row['asenta'].'</option>';
          $resultadoquery=$row['asenta'];
				  if ($row['asenta']==$colscr){$sel= 'selected';}else {$sel= ' ';} ;
				  $html .= '<option value="'.utf8_encode($row['asenta']).'" '.$sel.'  >'.utf8_encode($row['asenta']).'  </option>';
             	 
                  }
             $html .= ' </select></td></tr>';


            $resultado2= mysql_query($query,$conexion);
            $row2 = mysql_fetch_array($resultado2); 
 			$_POST['cve_estado'] =$row2['cve_estado'];
 			$estadoin=$_POST['cve_estado'];
 			$_POST['cve_mnpio'] =$row2['cve_mnpio'];
 			$mnpioin=$_POST['cve_mnpio'];

 			$querympo = "SELECT   DISTINCT (cve_mnpio),mnpio FROM cp_sepomex 
    			WHERE cve_mnpio =$mnpioin AND cve_estado =$estadoin";
             $resultado4= mysql_query($querympo,$conexion);
         
             $html .= '<tr>
            <td><div align="right">Municipio:</div></td>
            <td ><select name="municipios" id="municipios" class="form-control">';
             while ($row4 = mysql_fetch_array($resultado4)){ 
             	 $html .= '<option value="'.$row4['cve_mnpio'].'">'.utf8_encode($row4['mnpio']).'</option>';

             }
             $html .= ' </select></td></tr>';

            $queryest = "SELECT  DISTINCT (cve_estado),estado FROM cp_sepomex 
    			WHERE cve_estado =$estadoin ";
             $resultado3= mysql_query($queryest,$conexion);
         
             $html .= '<tr>
            <td><div align="right">Estado:</div></td>
            <td > <select name="estados" id="estados" class="form-control">';
             while ($row3 = mysql_fetch_array($resultado3)){ 
             	 $html .= '<option value="'.$row3['cve_estado'].'">'.utf8_encode($row3['estado']).'</option>';

             }
             $html .= ' </select> </td></tr>';
 
             echo  $html; 
?>
