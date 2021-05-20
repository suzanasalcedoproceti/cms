<?

include("../conexion.php");
include("lib.php");
include("../libprod.php");
require('../phpmailer/class.phpmailer.php');
	
ini_set('display_errors',1);


$sql = "UPDATE orden_compra a 
   JOIN pedido b ON a.folio = b.folio  
   SET a.estatus = 9
   WHERE a.estatus in (0,1,2) and DATEDIFF(curdate(),b.fecha)>=10;";

$resultado = mysql_query($sql,$conexion);

?>