<?php
include_once ("../../funciones/f_general.php");
include_once ("../../funciones/f_desplegables.php");
include_once ("../../../class/mysql.php");
session_start();
$pagina = $_SESSION['pagina'];
$opcion = $_SESSION['opcion'];
if ( $pagina != 'gestion_publicidad' || $opcion != 1){
	header ("Location: ../cerrar_sesion.php");
}
header("Content-Type: text/html;charset=ISO-8859-1");
$provincias = array();
$provincias = obten_localizacionGratisDistintasBds(numero_de_BDligas(),'provincia','liga','pais','ESP');
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>www.mitorneodepadel.es</title>
<link rel="stylesheet" type="text/css" href="css/insertar_publicidad.css" />
<link rel="stylesheet" type="text/css" href="../../../sweetalert-master/lib/sweet-alert.css" />
<script src="../../javascript/jquery-1.11.1.js" type="text/javascript"></script>
<script src="../../javascript/validaciones.js" type="text/javascript"></script>
<script src="../../../sweetalert-master/lib/sweet-alert.min.js" type="text/javascript"></script>
<script src="javascript/insertar_publicidad.js" type="text/javascript"></script>
</head>
<body>
<div class="horizontal">&nbsp;</div>
<div class="horizontal"><span class="titulo">Publicite su Negocio en Todos los Torneos para la Ciudad seleccionada.</span></div>
<div class="horizontal"><span class="titulo"><b>Importante:</b>Si finalizan los torneos para la ciudad seleccionada, tendr&aacute; 2 opciones cambiar a una ciudad cercana o continuar y el tiempo de espera ser abonado autom&aacute;ticamente en cuanto su negocio vuelva a anunciarse.</span></div>
<?php 
	if(count($provincias) > 0){
?>
<div class="columna1">
    <div class="cuadroTexto">Provincia:</div>
    <div class="cuadroTexto">Ciudad:</div>
    <div class="cuadroTexto">Suscripci&oacute;n:</div>
    <div class="cuadroTexto">Precio (&euro;):</div>
    <div class="cuadroTexto">Url:</div>
</div>
<div id="flotante"></div>
<div class="columna2">
	<span class="cuadroInputs">
		<select name="provincia" id="provincia" class="input_select_liga" onChange="lista('provincia',0)" >
             <option value="">--Provincia--</option>
             <?php
             $db2 = new MySQL('unicas');//UNICAS
                for($i=0; $i<count($provincias); $i++){
                    $consulta2 = $db2->consulta("SELECT provincia FROM provincias WHERE id = '$provincias[$i]'; ");
                    $resultados2 = $consulta2->fetch_array(MYSQLI_ASSOC);
                    echo '<option  value="'.$provincias[$i].'">'.$resultados2['provincia'].'</option>';
             }
          	 $db2->cerrar_conexion();// Desconectarse de la base de datos 
			 ?>
          </select>
     </span>
     <span class="cuadroInputs"><select name="ciudad" id="ciudad" class="input_select_liga" onChange="lista('ciudad',1)" ></select></span>
     <span class="cuadroInputs">
          <select name="suscripcion" id="suscripcion" class="input_select_liga" onChange="lista('suscripcion',2)" >
          	<option value="">--Suscripci&oacute;n--</option>
          	<option value="1">90 d&iacute;as</option>
            <option value="2">180 d&iacute;as (30% Descuento)</option>
            <option value="3">365 d&iacute;as (50% Descuento)</option>
          </select>
     </span>
     <span class="cuadroInputs"><input type="text" name="precio" id="precio" value="" class="input_select_liga" disabled ></span>
     <span class="cuadroInputs"><input type="text" name="url" id="url" class="input_url" value="Copiar y pegar el enlace" onFocus="if(this.value=='Copiar y pegar el enlace')this.value=''" maxlength="200"  onchange="limpiaDireccionWeb('url',3,'')" ></span>
</div> 
<div class="columna3">
    <div class="cuadroComentario"><span id="provinciaCom">* Intruduzca una provincia</span></div>
    <div class="cuadroComentario"><span id="ciudadCom">* Introduzca una ciudad</span></div>
    <div class="cuadroComentario"><span id="suscripcionCom">* Introduzca un tipo de suscripcin</span></div>
    <div class="cuadroComentario"><span id="precioCom">&nbsp;</span></div>
    <div class="cuadroComentario"><span id="urlCom">Introduzca una url vlida o djela vaca</span></div>
</div>
<div class="columna4">
    <div class="fondo_vista_previa"><img id="vista_previa" src="../../../images/publicidad_libre.jpg" class="imagen_redondeada"></div>
    <div class="horizontal">&nbsp;</div>
    <div class="horizontal"><div class="titulo"><form enctype="multipart/form-data" id="formulario" action="#" method="post" name="formulario"><input type="file" name="nueva_publi" id="nueva_publi" class="nueva_publi" ></form></div></div>
    <div class="cuadroComentario"><span id="nueva_publiCom">* El formato es diferente a |.jpg| |.jpeg| |.png| |.bmp| o el tama&ntilde;o de la imagen es superior a 10 Mb.</span></div>
</div>
<div class="horizontal"><input type="button" id="btn_enviar" value="Insertar" class="boton" /></form></div>
<?php
	}//fin no hay provincias para insertar
	else{
		echo '<div class="horizontal">&nbsp;</div><div class="horizontal">&nbsp;</div>';
		echo '<div class="horizontal"><span class="titulo">Actualmente no existen Torneos en ninguna ciudad para patrocinar</span></div>';
	}
?>
<div id="respuesta" class="horizontal"></div>
</body>
</html>