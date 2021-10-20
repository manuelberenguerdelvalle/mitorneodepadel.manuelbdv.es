<?php
include_once ("../../funciones/f_html.php");
include_once ("../../funciones/f_conexion.php");
include_once ("../../funciones/f_general.php");
include_once ("../../../class/mysql.php");
include_once ("../../../class/jugador.php");
session_start();
$pagina = $_SESSION['pagina'];
comprobar_pagina_jugador($pagina);
$_SESSION['pagina'] = 'gestion_puntos';
if($_SESSION['conexion_jugador'] != obten_ultimaConexion_jugador($_SESSION['id_jugador'])){//modificacion
	header ("Location: ../cerrar_sesion.php");
}
$_SESSION['opcion'] = $opcion;
if($opcion == 0){
	$carga_pagina = 'modificar_puntos.php';
}
$jugador = unserialize($_SESSION['jugador']);
$genero = $jugador->getValor('genero');
cabecera_inicio();
incluir_general(1,1);
?>
<link rel="stylesheet" type="text/css" href="../../css/panel_usuario.css" />
<script src="../../javascript/selects_principales.js" type="text/javascript"></script>
<script src="../../javascript/pace.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	cargar("#menuIzq","../menu_izq.php");
	cargar(".contenido","modificar_puntos.php");
});
</script>
<?php
cabecera_fin();
?>
<div class="principal">
	<div class="superior">
    	<div class="nombre_usuario">
        	<?php echo $jugador->getValor('nombre'); ?> <a href="../cerrar_sesion.php">(Desconectar)</a>
        </div>
        <div class="desplegable_liga">&nbsp;</div>
        <div class="desplegable_division">&nbsp;</div>
        <div class="cuenta">&nbsp;</div>
        <div class="cuenta"><a href="">&nbsp;</a></div>
        <div class="traductor"><div id="google_translate_element"></div></div>
    </div>
    <div id="menuIzq" class="menuIzq">

    </div>
    <div class="contenido">
		
    </div>
<?php
pie();
?>
</div>
<?php
cuerpo_fin();
?>
