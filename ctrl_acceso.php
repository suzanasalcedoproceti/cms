<?
   session_cache_limiter('private, must-revalidate');
   session_start();

// con esta línea evitamos que nadie entre
//include("logout.php"); return;

   if (empty($_SESSION['usr_valido'])) {
     include('logout.php');
         exit;
   }
?>