# cms
Sistema de administración de contenido para POS2, POS3 y parte de VTEX.

Nota:
El archivo de conexión se mantiene en una carpeta superior al proyecto llamado conexion.php

Esta carpeta pertenece al proyecto original de Tienda Whirlpool que actualmente se encuentra en desuso.


conexion.php
<? 
   $base="whirlpool";
   $conexion=mysql_connect("localhost","root","Calidad1");
   mysql_select_db($base,$conexion);
   mysql_set_charset('latin1', $conexion);
?>
