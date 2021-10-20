<?php
include_once ("../../funciones/f_general.php");
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_ligas.php");
include_once ("../../funciones/f_html.php");
include_once ("../../funciones/f_partidos.php");
include_once ("../../funciones/f_inputs.php");
include_once ("../../funciones/f_fechasHoras.php");
include_once ("../../../class/mysql.php");
include_once ("../../../class/liga.php");
session_start();
$_SESSION['pagina'] = 'ver_liga';
if( (!empty($_SESSION['id_liga']) && !empty($_SESSION['id_division']) && !empty($_SESSION['bd']) && $_SESSION['tipo_pago'] > 0) || !empty($_POST['a']) || !empty($_GET['a']) ){//verifico datos session o post
	$continua = false;
	if(!empty($_POST['a']) || !empty($_GET['a'])){//si viene por post viene por acceso compartido, descodifico los datos encriptados
		if(!empty($_POST['a'])){//post
			$cadena = limpiaTexto(decodifica($_POST["a"]));
		}
		else{//get
			$cadena = limpiaTexto(decodifica($_GET["a"]));
		}
		$ini = 12;
		$pos = strpos($cadena,'F');
		$bd = substr($cadena, $ini, 1);
		if($bd == 0){$_SESSION['bd'] = 'admin_liga';}
		else{$_SESSION['bd'] = 'admin_liga'.$bd;}
		$_SESSION['id_division'] = substr($cadena, $ini+1, $pos-($ini+1));
		$_SESSION['id_liga'] = obten_consultaUnCampo('session','liga','division','id_division',$_SESSION["id_division"],'','','','','','','');
		$_SESSION['tipo_pago'] = obten_consultaUnCampo('session','tipo_pago','liga','id_liga',$_SESSION["id_liga"],'','','','','','','');
		if($_SESSION['id_liga'] != '' && $_SESSION['id_division'] != '' && $_SESSION['bd'] != ''){$continua = true;}//ninguna variable vacia
	}
	else{//entra desde el index
		$continua = true;
	}
//SI TODO VA BIEN CONTINUAMOS
	if($continua){//accede si todo va bien
		if(isset($_POST['id_division'])){
			$_SESSION['id_division'] = limpiaTexto($_POST['id_division']);
		}
		$cont_izq = 0;
		$cont_der = 0;
		$id_publi_izq = array();
		$id_publi_der = array();
		$url_izq = array();
		$url_der = array();
		$posicion_publi_izq = array();
		$posicion_publi_der = array();
		$contador_izq = array();
		$contador_der = array();
		//$liga = new Liga($_SESSION['id_liga'],'','','','','','','','','','','','','','','','');
		//echo 'ahora '.date('Y-m-d H:i:s').'<br>';
		$db = new MySQL('session');//LIGA PADEL
		$consulta = $db->consulta("SELECT id_publicidad,url,posicion_publi,contador FROM publicidad WHERE liga = '".$_SESSION['id_liga']."' AND division = '".$_SESSION['id_division']."' AND pagado = 'S' AND estado = '0' ORDER BY posicion_publi ; ");
		if($consulta->num_rows > 0){
			while($resultados = $consulta->fetch_array(MYSQLI_ASSOC)){
				//echo $resultados['id_publicidad'].'-'.$resultados['posicion_publi'].'<br>';
				if(strpos($resultados['posicion_publi'],'I') != false){//si es columna Izquierda
					$id_publi_izq[$cont_izq] = $resultados['id_publicidad'];
					$url_izq[$cont_izq] = $resultados['url'];
					$posicion_publi_izq[$cont_izq] = $resultados['posicion_publi'];
					$contador_izq[$cont_izq] = $resultados['contador'];
					$cont_izq++;
				}
				else{///si es columna Derecha
					$id_publi_der[$cont_der] = $resultados['id_publicidad'];
					$url_der[$cont_der] = $resultados['url'];
					$posicion_publi_der[$cont_der] = $resultados['posicion_publi'];
					$contador_der[$cont_der] = $resultados['contador'];
					$cont_der++;
				}
			}//fin while
		}//fin num_rows
		$url_publi_vacia = '#';//URL PARA LA PUBLI VACIA, EN ESTE CASO ACCESO AL REGISTRO DE PUBLICIDAD
		//DATOS CLASIFICACION GRUPOS
		$max_grupos = obten_consultaUnCampo('session','MAX(grupo)','partido','liga',$_SESSION["id_liga"],'division',$_SESSION["id_division"],'','','','','');//ganados
		if( isset($_GET['grupo']) && is_numeric($_GET['grupo']) ){//cuando recibimos el grupo por GET
			if($_GET['grupo'] > $max_grupos){$_SESSION['grupo'] = $max_grupos;}
			else if($_GET['grupo'] < 0){$_SESSION['grupo'] = 0;}
			else{	$_SESSION['grupo'] = $_GET['grupo'];}
			$grupo = $_SESSION['grupo'];
		}
		else if ( isset($_SESSION['grupo']) ){//cuando se actualiza la pagina
			$grupo = $_SESSION['grupo'];
		}
		else{//cuando entramos por primera vez
			$grupo = 0;
		}
		//DATOS CLASIFICACION ELIMINATORIA
		if( isset($_GET['eliminatoria']) && is_numeric($_GET['eliminatoria']) ){//cuando recibimos el eliminatoria por GET
			$_SESSION['eliminatoria'] = $_GET['eliminatoria'];
			$eliminatoria = $_SESSION['eliminatoria'];
			$_SESSION['seleccionado'] = 'E';
		}
		else if ( isset($_SESSION['eliminatoria']) ){//cuando se actualiza la pagina
			$eliminatoria = $_SESSION['eliminatoria'];
		}
		else{//cuando entramos por primera vez
			$eliminatoria = obten_consultaUnCampo('session','MIN(eliminatoria)','partido','division',$_SESSION['id_division'],'jornada',0,'','','','','');
			if($eliminatoria == ''){
				$_SESSION['eliminatoria'] = 0;
				$_SESSION['seleccionado'] = 'G';
			}
			else{
				$_SESSION['eliminatoria'] = $eliminatoria;
				$_SESSION['seleccionado'] = 'E';
			}
		}
		$id_equipo = array();
		$ganados = array();
		$sets_aux = array();
		$sets_favor = array();
		$sets_contra = array();
		$finalizados = array();
		$cont = 0;	
		if($max_grupos == 0 || $grupo == 0){
			$db = new MySQL('session');//LIGA PADEL
			$consulta = $db->consulta("SELECT id_equipo FROM equipo WHERE liga = '".$_SESSION["id_liga"]."' AND division = '".$_SESSION["id_division"]."' ; ");
			while($resultados = $consulta->fetch_array(MYSQLI_ASSOC)){
				$id_equipo[$cont] = $resultados['id_equipo'];
				$ganados[$cont] = obten_consultaUnCampo('session','COUNT(id_partido)','partido','ganador',$resultados['id_equipo'],'','','','','','','');//ganados
				$sets_aux = obten_sumaSets($resultados['id_equipo'],'local');//solicita a favor local, visitantes en contra
				$sets_favor[$cont] = $sets_aux[0];//local a favor
				$sets_contra[$cont] = $sets_aux[1];//visitante en contra
				$sets_aux = obten_sumaSets($resultados['id_equipo'],'visitante');//solicita a favor local, visitantes en contra
				$sets_favor[$cont] += $sets_aux[0];//local a favor
				$sets_contra[$cont] += $sets_aux[1];//visitante en contra
				$finalizados[$cont] = obten_datosPartidos($resultados['id_equipo'],1);
				$finalizados[$cont] += obten_datosPartidos($resultados['id_equipo'],2);
				$cont++;
			}
		}
		else{//clasificacion para el grupo seleccionado
			$resultados = array();
			$resultados = obten_equiposGrupo($_SESSION["id_liga"],$_SESSION["id_division"],$grupo);
			for($i=0; $i<count($resultados); $i++){
				$id_equipo[$cont] = $resultados[$i];
				$ganados[$cont] = obten_consultaUnCampo('session','COUNT(id_partido)','partido','ganador',$resultados[$i],'grupo',$grupo,'','','','','');//ganados
				$sets_aux = obten_sumaSetsGrupo($resultados[$i],'local',$grupo);//solicita a favor local, visitantes en contra
				$sets_favor[$cont] = $sets_aux[0];//local a favor
				$sets_contra[$cont] = $sets_aux[1];//visitante en contra
				$sets_aux = obten_sumaSetsGrupo($resultados[$i],'visitante',$grupo);//solicita a favor local, visitantes en contra
				$sets_favor[$cont] += $sets_aux[0];//local a favor
				$sets_contra[$cont] += $sets_aux[1];//visitante en contra
				$finalizados[$cont] = obten_datosPartidosGrupo($resultados[$i],1,$grupo);
				$finalizados[$cont] += obten_datosPartidosGrupo($resultados[$i],2,$grupo);
				$cont++;
			}
		}
		$aux_id = 0;
		$aux_ga = 0;
		$aux_sf = 0;
		$aux_sc = 0;
		$aux_fi = 0;
		for($i=0; $i<count($id_equipo); $i++){
			for($j=$i+1; $j<count($id_equipo); $j++){
				if($ganados[$j] > $ganados[$i]){//si el siguiente es mayor que el base, hago cambio
					//copio el base
					$aux_id = $id_equipo[$i];
					$aux_ga = $ganados[$i];
					$aux_sf = $sets_favor[$i];
					$aux_sc = $sets_contra[$i];
					$aux_fi = $finalizados[$i];
					//asigno el mayor a base
					$id_equipo[$i] = $id_equipo[$j];
					$ganados[$i] = $ganados[$j];
					$sets_favor[$i] = $sets_favor[$j];
					$sets_contra[$i] = $sets_contra[$j];
					$finalizados[$i] = $finalizados[$j];
					//asigno el menor
					$id_equipo[$j] = $aux_id;
					$ganados[$j] = $aux_ga;
					$sets_favor[$j] = $aux_sf;
					$sets_contra[$j] = $aux_sc;
					$finalizados[$j] = $aux_fi;
				}
				else if($ganados[$j] == $ganados[$i]){//si es igual miro partidos jugados/finalizados
					if($finalizados[$j] > $finalizados[$i]){
						//copio el base
						$aux_id = $id_equipo[$i];
						$aux_ga = $ganados[$i];
						$aux_sf = $sets_favor[$i];
						$aux_sc = $sets_contra[$i];
						$aux_fi = $finalizados[$i];
						//asigno el mayor a base
						$id_equipo[$i] = $id_equipo[$j];
						$ganados[$i] = $ganados[$j];
						$sets_favor[$i] = $sets_favor[$j];
						$sets_contra[$i] = $sets_contra[$j];
						$finalizados[$i] = $finalizados[$j];
						//asigno el menor
						$id_equipo[$j] = $aux_id;
						$ganados[$j] = $aux_ga;
						$sets_favor[$j] = $aux_sf;
						$sets_contra[$j] = $aux_sc;
						$finalizados[$j] = $aux_fi;
					}
					else if($finalizados[$j] == $finalizados[$i]){//si es igual miro sets favor contra
						if( ($sets_favor[$j] - $sets_contra[$j]) > ($sets_favor[$i] - $sets_contra[$i]) ){
							//copio el base
							$aux_id = $id_equipo[$i];
							$aux_ga = $ganados[$i];
							$aux_sf = $sets_favor[$i];
							$aux_sc = $sets_contra[$i];
							$aux_fi = $finalizados[$i];
							//asigno el mayor a base
							$id_equipo[$i] = $id_equipo[$j];
							$ganados[$i] = $ganados[$j];
							$sets_favor[$i] = $sets_favor[$j];
							$sets_contra[$i] = $sets_contra[$j];
							$finalizados[$i] = $finalizados[$j];
							//asigno el menor
							$id_equipo[$j] = $aux_id;
							$ganados[$j] = $aux_ga;
							$sets_favor[$j] = $aux_sf;
							$sets_contra[$j] = $aux_sc;
							$finalizados[$j] = $aux_fi;
						}
						else if( ($sets_favor[$j] - $sets_contra[$j]) == ($sets_favor[$i] - $sets_contra[$i]) ){//si es igual antes equipo con id menor
							if($id_equipo[$j] < $id_equipo[$i]){
								//copio el base
								$aux_id = $id_equipo[$i];
								$aux_ga = $ganados[$i];
								$aux_sf = $sets_favor[$i];
								$aux_sc = $sets_contra[$i];
								$aux_fi = $finalizados[$i];
								//asigno el mayor a base
								$id_equipo[$i] = $id_equipo[$j];
								$ganados[$i] = $ganados[$j];
								$sets_favor[$i] = $sets_favor[$j];
								$sets_contra[$i] = $sets_contra[$j];
								$finalizados[$i] = $finalizados[$j];
								//asigno el menor
								$id_equipo[$j] = $aux_id;
								$ganados[$j] = $aux_ga;
								$sets_favor[$j] = $aux_sf;
								$sets_contra[$j] = $aux_sc;
								$finalizados[$j] = $aux_fi;
							}//id_equipo menor
						}
						else{}//sets
					}
					else{}//finalizados
				}
				else{}//ganados
			}//fin for i
		}//fin for j
		$id_premio = obten_consultaUnCampo('session','id_premio','premio','division',$_SESSION["id_division"],'','','','','','','');
		$id_regla = obten_consultaUnCampo('session','id_regla','regla','liga',$_SESSION["id_liga"],'','','','','','','');
		$estilo = obten_consultaUnCampo('session','estilo','liga','id_liga',$_SESSION["id_liga"],'','','','','','','');
		$movimientos = obten_consultaUnCampo('session','movimientos','liga','id_liga',$_SESSION["id_liga"],'','','','','','','');
		$num_divisiones = obten_consultaUnCampo('session','COUNT(id_division)','division','liga',$_SESSION["id_liga"],'comienzo','S','bloqueo','N','','','');
		$num_division = obten_consultaUnCampo('session','num_division','division','id_division',$_SESSION["id_division"],'','','','','','','');
cabecera_inicio();
?>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Cabin">
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Alegreya+SC">
<link rel="stylesheet" type="text/css" href="../../css/bpopup.css" />
<link rel="stylesheet" type="text/css" href="../../css/ligas<?php echo $estilo; ?>.css" />
<link rel="stylesheet" type="text/css" href="../../css/pie.css" />
<script src="../../javascript/jquery-1.11.1.js" type="text/javascript"></script>
<script src="../../javascript/detect_paginas.js" type="text/javascript"></script>
<script src="../../javascript/jquery.bpopup.min.js" type="text/javascript"></script>
<script src="../javascript/ligas_pago.js" type="text/javascript"></script>
<script src="https://apis.google.com/js/platform.js" async defer>
  {lang: 'es'}
</script>
<?php
cabecera_fin();
?>
<div id="fb-root"></div>
<script>
(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v2.5";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
<style>
/*.contenido {
	border:1px black solid;
}
.contenedor_jornadas {
	border:1px black solid;
}
.caja2 {
	border:1px black solid;
}*/
</style>
<div class="principal">
	<div id="oculto"></div>
	<div id="content_popup">
		<div class="poptitulo"><h2>Contactar con el Administrador</h2></div>
		<div class="popcentro">
        <h3>Para publicitarte en esta posici&oacute;n debes contactar con el administrador de este torneo.</h3>
		<form id="formulario_contacto" action="#" method="post" name="formulario_contacto" >
            <label class="caja_texto">Tu email:</label><label class="caja_input"><input name="contacto"  type="text" class="input_text_liga" ></label><label id="errorContacto" class="caja_error">*</label>
            <label class="caja_texto">Asunto:</label><label class="caja_input"><input name="asunto" type="text" class="input_text_liga" ></label><label id="errorAsunto" class="caja_error">*</label>
            <label class="caja_texto">Mensaje:</label><label class="caja_input_area"><textarea  rows="11" cols="30" name="mensaje" class="input_text_area" ></textarea></label><label id="errorTextarea" class="caja_error">*</label>
            <input type="hidden" name="id_liga" value="<?php echo $_SESSION["id_liga"]; ?>">
            <input type="hidden" name="id_division" value="<?php echo $_SESSION["id_division"]; ?>">
            <input type="hidden" name="bd" value="<?php echo $_SESSION["bd"]; ?>">
            <input type="hidden" name="modo" value="1">
		</form>
		</div>
        <div class="poppie2">
            <span class="button b-close"><span><a class="env" href="#"  onclick="enviar();">ENVIAR</a></span></span>
        </div>
	</div><!-- FIN POPUP -->
    <div class="izquierdo">
        <?php mostrar_columna_pago($_SESSION['bd'],$_SESSION['id_liga'],$_SESSION['id_division'],$url_izq,$posicion_publi_izq,'I',$url_vacia);?>
    </div>
    <div class="central">
    	<div class="cabecera">
        	<div class="logo">
            <?php 
				$logo = '../../../logos/'.$_SESSION['bd'].$_SESSION['id_liga'].'.jpg';
				if(file_exists($logo)){echo '<img src="'.$logo.'" alt="ligas de padel" />';}
				else{echo '<img src="../../../logos/0'.$estilo.'.jpg" alt="ligas de padel" />';}
			?>
            </div>
    		<div class="superior">
        		<div class="nombre"><?php echo  '<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Torneo: '.$_SESSION["nombre"].' - Divisi&oacute;n&nbsp;</div>'.generar_input_divs($_SESSION['id_liga'],$_SESSION['id_division'],'clasificacion.php');?></div>
            	<div class="inicio"><a href="http://www.mitorneodepadel.es">Inicio</a></div>
            	<div class="idioma"><div id="google_translate_element"></div></div>
        	</div>
        	<div class="menu">
        		<ul class="block-menu">
                    <li><a href="calendario.php" class="three-d">
                        Calendario
                        <span aria-hidden="true" class="three-d-box">
                            <span class="front">Calendario</span>
                            <span class="back">Calendario</span>
                        </span>
                    </a></li>
                    <li><a href="clasificacion.php" class="three-d">
                        Clasificacion
                        <span aria-hidden="true" class="three-d-box">
                            <span class="front">Clasificacion</span>
                            <span class="back">Clasificacion</span>
                        </span>
                    </a></li>
                    <!-- more items here -->
                    <li><a href="noticia.php" class="three-d">
                        Noticias
                        <span aria-hidden="true" class="three-d-box">
                            <span class="front">Noticias</span>
                            <span class="back">Noticias</span>
                        </span>
                    </a></li>
<?php if( !empty($id_premio) || !empty($id_regla) ){ ?>
                    <li><a href="info.php" class="three-d">
                        Info
                        <span aria-hidden="true" class="three-d-box">
                            <span class="front">Info</span>
                            <span class="back">Info</span>
                        </span>
                    </a></li>
<?php } ?>
                    <li><a href="contacto.php" class="three-d">
                        Contacto
                        <span aria-hidden="true" class="three-d-box">
                            <span class="front">Contacto</span>
                            <span class="back">Contacto</span>
                        </span>
                    </a></li>
                </ul>
        	</div>
        </div><!--FIN CABECERA-->
        <div class="contenido">
        	<div class="texto_titulo">CLASIFICACION</div>
        	<div class="publi_google"><!--REDES SOCIALES-->
            	<div class="red_social"><div class="fb-share-button" data-href="http://www.mitorneodepadel.es/web/ver_liga/p/noticia.php" data-layout="button"></div></div>
                <div class="red_social"><a href="https://twitter.com/share" class="twitter-share-button" data-text="Ligas de Padel">Tweet</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script></div>
                <div class="red_social"><div class="g-plus" data-action="share" data-annotation="none" data-href="http://www.mitorneodepadel.es/web/ver_liga/p/noticia.php"></div></div>
            </div><!-- FIN REDES SOCIALES-->
            <?php
			if($max_grupos > 0){//liguilla y eliminatorias
				echo '<div class="contenedor_jornadas">';
				if($grupo == 0){
					echo '<div class="eliminatoria"><a href="clasificacion.php?grupo=0" class="seleccionado" >Clasificaci&oacute;n General</a></div>';
				}
				else{
					echo '<div class="eliminatoria"><a href="clasificacion.php?grupo=0" class="normal" >Clasificaci&oacute;n General</a></div>';
				}
				echo '</div>';
				echo '<div class="horizontal"><div class="titulo"><b>GRUPOS</b></div></div>';
				echo '<div class="contenedor_jornadas">';
				for($i=1; $i<=$max_grupos; $i++){
					$partidos_finalizados = grupoPartidosFinalizados($_SESSION['id_division'],$i);
					//$hay_partidos_descanso = hayPartidoDescanso($_SESSION['id_division'],$i);
					if($partidos_finalizados == 0 ){//si hay partidos de descanso minimo va a ver 1 partido finalizado si no 0 = jornada normal
						if($i == $grupo){
							echo '<div class="jornada"><a href="clasificacion.php?grupo='.$i.'" class="seleccionado" >'.$i.'</a></div>';
						}
						else{
							echo '<div class="jornada"><a href="clasificacion.php?grupo='.$i.'" class="normal" >'.$i.'</a></div>';
						}
					}
					else if($partidos_finalizados == 3){//si la suma de todos los finalizados es igual a los partidos por jornada = jornada finalizada
						if($i == $grupo){
							echo '<div class="jornada_completa"><a href="clasificacion.php?grupo='.$i.'" class="seleccionado" >'.$i.'</a></div>';
						}
						else{
							echo '<div class="jornada_completa"><a href="clasificacion.php?grupo='.$i.'" class="normal" >'.$i.'</a></div>';
						}
					}
					else{
						if($i == $grupo){//si es cualquier valor intermedio = jornada disputandose
							echo '<div class="jornada_disputandose"><a href="clasificacion.php?grupo='.$i.'" class="seleccionado" >'.$i.'</a></div>';
						}
						else{
							echo '<div class="jornada_disputandose"><a href="clasificacion.php?grupo='.$i.'" class="normal" >'.$i.'</a></div>';
						}
					}//fin else
				}//FIN DE FOR
				echo '</div>';	
			?>
            <div class="linea_datos">
					<div class="posicion_datos">POS</div>
					<div class="movimiento">&nbsp;</div>
					<div class="foto">&nbsp;</div>
					<div class="equipo_datos">EQUIPO</div>
					<div class="p_jugados">PJ</div>
					<div class="p_ganados">PG</div>
					<div class="p_perdidos">PP</div>
					<div class="sets_favor">SF</div>
					<div class="sets_contra">SC</div>
					<div class="sets_diferencia">DIF</div>
					<div class="puntos">PTOS</div>
			</div>
            <div class="contenedor_datos_clasif">
            <?php
			/*
			echo '<div class="linea_datos">';
					echo '<div class="posicion_datos">POS</div>';
					echo '<div class="movimiento">&nbsp;</div>';
					echo '<div class="foto">&nbsp;</div>';
					echo '<div class="equipo_datos">EQUIPO</div>';
					echo '<div class="p_jugados">PJ</div>';
					echo '<div class="p_ganados">PG</div>';
					echo '<div class="p_perdidos">PP</div>';
					echo '<div class="sets_favor">SF</div>';
					echo '<div class="sets_contra">SC</div>';
					echo '<div class="sets_diferencia">DIF</div>';
					echo '<div class="puntos">PTOS</div>';
			echo '</div>';
			*/
			/*--------------------BORRAR-----------------------*/
			//$num_divisiones = 2;
			//$num_division = 2;
			/*-------------------------------------------------------*/
			for($i=0; $i<count($id_equipo); $i++){
				$asc = false;
				$desc = false;
				if($grupo == 0){//general
					if($num_divisiones > 1){
						if($i < $movimientos){//ascensos
							if($num_division == 1){echo '<div class="linea_clasificacion" >';}//si es la division 1 normal
							else{echo '<div class="linea_clasificacion_asc" >';$asc=true;}//si es otra ascenso
						}
						else if ( $i >= (count($id_equipo) - $movimientos) ){//descensos
							echo '<div class="linea_clasificacion_desc" >';
							$desc=true;
						}
						else{//normal
							echo '<div class="linea_clasificacion" >';
						}
					}//fin de si hay varias divisiones
					else{
						echo '<div class="linea_clasificacion" >';
					}
				}
				else{
					if($i < 2){echo '<div class="linea_clasificacion_asc" >';$asc=true;}
					else{echo '<div class="linea_clasificacion" >';}
				}
				//REVISAR OTROS ESTADOS DE PARTIDOS, SANCIONADOS Y EXPULSADOS Y AÑADIRLOS A FINALIZADOS
				//finalizados + sancionados (comprobar bd)
				//para el tratamiento de expulsados de manera especial y comprobar bd
					if($i == 0){
						if($grupo > 0){//grupos
							echo '<div class="posicion"><div class="numero_pos">'.($i+1).'</div></div>';
						}
						else{//general
							echo '<div class="posicion"><img class="imagen_pos" src="../../../images/copa.png"></div>';
						}
					}
					else{
						echo '<div class="posicion"><div class="numero_pos">'.($i+1).'</div></div>';
					}
					//si no esta la foto se muestra blanco
					if($asc){echo '<div class="movimiento"><img class="foto_movi" src="../../../images/asc.png"></div>';}
					else if($desc){echo '<div class="movimiento"><img class="foto_movi" src="../../../images/desc.png"></div>';}
					else{echo '<div class="movimiento">&nbsp;</div>';}
					$id_jugador1 = obten_consultaUnCampo('session','jugador1','equipo','id_equipo',$id_equipo[$i],'','','','','','','');
					$origen = '../../../../../fotos_jugador/'.$id_jugador1.'.jpg';
					if(file_exists($origen)){
						$foto_temp = '../../../fotos_jugador/'.$id_jugador1.'.jpg';
						copy($origen, $foto_temp);
						echo '<div class="foto">';
							echo '<a href="#image-'.$id_jugador1.'"><img class="imagen_foto" src="'.$foto_temp.'"></a>
										<div class="lb-overlay" id="image-'.$id_jugador1.'">
											<img style="width:20%;" src="'.$foto_temp.'" alt="jugador1" />
											<a href="#page" class="lb-close">X</a>
										</div>';	
						echo '</div>';
					}
					else{
						echo '<div class="foto">&nbsp;</div>';
					}
					if($id_jugador1 == 0){//jugador temporal
						$inscripcion = obten_consultaUnCampo('session','seguro_jug1','equipo','id_equipo',$id_equipo[$i],'','','','','','','');
						echo '<div class="equipo">&nbsp;'.obten_consultaUnCampo('session','nombre1','inscripcion','id_inscripcion',$inscripcion,'','','','','','','').' '.obten_consultaUnCampo('session','apellidos1','inscripcion','id_inscripcion',$inscripcion,'','','','','','','').'</div>';
					}
					else{echo '<div class="equipo">&nbsp;'.obtenNombreJugadorMostrar($id_equipo[$i],'jugador1').'</div>';}//jugador registrado
					$id_jugador2 = obten_consultaUnCampo('session','jugador2','equipo','id_equipo',$id_equipo[$i],'','','','','','','');
					$origen = '../../../../../fotos_jugador/'.$id_jugador2.'.jpg';
					if(file_exists($origen)){
						$foto_temp = '../../../fotos_jugador/'.$id_jugador2.'.jpg';
						copy($origen, $foto_temp);
						echo '<div class="foto">';
							echo '<a href="#image-'.$id_jugador2.'"><img class="imagen_foto" src="'.$foto_temp.'"></a>
										<div class="lb-overlay" id="image-'.$id_jugador2.'">
											<img style="width:20%;" src="'.$foto_temp.'" alt="jugador2" />
											<a href="#page" class="lb-close">X</a>
										</div>';	
						echo '</div>';
					}
					else{
						echo '<div class="foto">&nbsp;</div>';
					}
					if($id_jugador2 == 0){//jugador temporal
						$inscripcion = obten_consultaUnCampo('session','seguro_jug2','equipo','id_equipo',$id_equipo[$i],'','','','','','','');
						echo '<div class="equipo">&nbsp;'.obten_consultaUnCampo('session','nombre2','inscripcion','id_inscripcion',$inscripcion,'','','','','','','').' '.obten_consultaUnCampo('session','apellidos2','inscripcion','id_inscripcion',$inscripcion,'','','','','','','').'</div>';
					}
					else{echo '<div class="equipo">&nbsp;'.obtenNombreJugadorMostrar($id_equipo[$i],'jugador2').'</div>';}//jugador registrado
					echo '<div class="p_jugados">'.$finalizados[$i].'</div>';
					echo '<div class="p_ganados">'.$ganados[$i].'</div>';
					echo '<div class="p_perdidos">'.abs($finalizados[$i]-$ganados[$i]).'</div>';
					echo '<div class="sets_favor">'.$sets_favor[$i].'</div>';
					echo '<div class="sets_contra">'.$sets_contra[$i].'</div>';
					echo '<div class="sets_diferencia">'.($sets_favor[$i]-$sets_contra[$i]).'</div>';
					echo '<div class="puntos"><b>'.($ganados[$i]*3).'</b></div>';
				echo '</div>';
			}//fin for
			?>     
            </div><!--fin contenedor datos -->
      <?php 
	  	}//fin grupos
		else{//eliminatoria
			echo '<div class="contenedor_datos">';
			$max_eliminatorias = obten_consultaUnCampo('session','MAX(eliminatoria)','partido','division',$_SESSION['id_division'],'jornada',0,'','','','','');
			$min_eliminatorias = obten_consultaUnCampo('session','MIN(eliminatoria)','partido','division',$_SESSION['id_division'],'jornada',0,'','','','','');
			if($max_eliminatorias > 0){
				$titulo = 'ELIMINATORIAS';
				echo '<div class="horizontal"><div class="titulo"><b>ELIMINATORIAS</b></div></div>
						<div class="contenedor_jornadas">';
				for($i=$max_eliminatorias; $i>=$min_eliminatorias; $i=$i/2){
					$partidos_finalizados = eliminatoriaPartidosFinalizados($_SESSION['id_division'],$i);
					if($partidos_finalizados == 0 ){//si hay partidos de descanso minimo va a ver 1 partido finalizado si no 0 = jornada normal
						if($i == $eliminatoria && $_SESSION['seleccionado'] == 'E'){
							echo '<div class="eliminatoria"><a href="clasificacion.php?eliminatoria='.$i.'" class="seleccionado" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
						else{
							echo '<div class="eliminatoria"><a href="clasificacion.php?eliminatoria='.$i.'" class="normal" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
					}
					else if($partidos_finalizados == $i){//si la suma de todos los finalizados es igual a los partidos por jornada = jornada finalizada
						if($i == $eliminatoria && $_SESSION['seleccionado'] == 'E'){
							echo '<div class="eliminatoria_completa"><a href="clasificacion.php?eliminatoria='.$i.'" class="seleccionado" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
						else{
							echo '<div class="eliminatoria_completa"><a href="clasificacion.php?eliminatoria='.$i.'" class="normal" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
					}
					else{
						if($i == $eliminatoria && $_SESSION['seleccionado'] == 'E'){
							echo '<div class="eliminatoria_disputandose"><a href="clasificacion.php?eliminatoria='.$i.'" class="seleccionado" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
						else{
							echo '<div class="eliminatoria_disputandose"><a href="clasificacion.php?eliminatoria='.$i.'" class="normal" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
					}
				}//FIN DE FOR
				echo '</div>';
			}//fin eliminatorias
			
			$campos_local = array('set1_local','set2_local','set3_local','set4_local','set5_local');
			$campos_visitante = array('set1_visitante','set2_visitante','set3_visitante','set4_visitante','set5_visitante');
			$db = new MySQL('session');//LIGA PADEL
			$consulta = $db->consulta("SELECT * FROM partido WHERE division = '".$_SESSION['id_division']."' AND eliminatoria = '$eliminatoria'; ");
			while($resultados = $consulta->fetch_array(MYSQLI_ASSOC)){
				if($resultados["enlace"] == ''){$enlace = 'Enlace ver partido';}
				else{$enlace = $resultados["enlace"];}
				$jugador1_loc = obten_consultaUnCampo('session','jugador1','equipo','id_equipo',$resultados['local'],'','','','','','','');
				$jugador2_loc = obten_consultaUnCampo('session','jugador2','equipo','id_equipo',$resultados['local'],'','','','','','','');
				$jugador1_vis = obten_consultaUnCampo('session','jugador1','equipo','id_equipo',$resultados['visitante'],'','','','','','','');
				$jugador2_vis = obten_consultaUnCampo('session','jugador2','equipo','id_equipo',$resultados['visitante'],'','','','','','','');
				if($jugador1_loc == 0 && $jugador2_loc == 0 && $jugador1_vis == 0 && $jugador2_vis == 0){$todosTemporales = true;}
				else{$todosTemporales = false;}
			?>
				<div class="caja2">
			<?php	
				if($resultados['local'] == $resultados['ganador'] && $eliminatoria == 1){echo '<div class="cuadro_elim_ganador_izq">';}
				else if($resultados['local'] == $resultados['ganador']){echo '<div class="cuadro_elim_gana">';}
				else{echo '<div class="cuadro_elim">';}
			?> 
            		<div class="mostrar_equipo2">
						<div class="jugador1_clasif">
							<div class="alinear_texto">
							<?php
							if($resultados['local'] == 0){echo 'Clasificado';}
							else{
								if($resultados['modificado'] == $jugador1_loc && $jugador1_loc > 0){
									echo '<span style="text-align:center" onMouseOver="'."showdiv(event,'Este jugador/a ha insertado el resultado del partido');".'" onMouseOut="hiddenDiv()" style="display:table;">';
									echo '<b><i>'.substr(obtenNombreJugadorMostrar($resultados['local'],'jugador1'),0,35).'</i></b></span>';
								}
								else{
									if($jugador1_loc == 0){//si es temporal
										$inscripcion_equipoLocal = obten_consultaUnCampo('session','seguro_jug1','equipo','id_equipo',$resultados['local'],'','','','','','','');
										echo substr(obten_consultaUnCampo('session','nombre1','inscripcion','id_inscripcion',$inscripcion_equipoLocal,'','','','','','','').' '.obten_consultaUnCampo('session','apellidos1','inscripcion','id_inscripcion',$inscripcion_equipoLocal,'','','','','','',''),0,35);
									}
									else{echo substr(obtenNombreJugador($resultados['local'],'jugador1'),0,35);}
								}
							}		 
							?>
							</div>
						</div>
						<div class="jugador2_clasif">
							<div class="alinear_texto">
							<?php
							if($resultados['local'] == 0){echo 'Clasificado';}
							else{
								if($resultados['modificado'] == $jugador1_loc && $jugador1_loc > 0){
									echo '<span style="text-align:center" onMouseOver="'."showdiv(event,'Este jugador/a ha insertado el resultado del partido');".'" onMouseOut="hiddenDiv()" style="display:table;">';
									echo '<b><i>'.substr(obtenNombreJugadorMostrar($resultados['local'],'jugador2'),0,35).'</i></b></span>';
								}
								else{
									if($jugador1_loc == 0){//si es temporal
										echo substr(obten_consultaUnCampo('session','nombre2','inscripcion','id_inscripcion',$inscripcion_equipoLocal,'','','','','','','').' '.obten_consultaUnCampo('session','apellidos2','inscripcion','id_inscripcion',$inscripcion_equipoLocal,'','','','','','',''),0,35);
									}
									else{echo substr(obtenNombreJugador($resultados['local'],'jugador2'),0,35);}
								}
							}	 
							?>
							</div>
						</div>
                    </div><!--fin mostrar_equipo2-->
					<div class="resultados2">
						<?php
						if($resultados['local'] != 0 && $resultados['visitante'] != 0){
							if($resultados['set5_local'] == -1){$sets = 3;}
							else{$sets = 5;}
							for($i=0; $i<$sets; $i++){
								$campo = $campos_local[$i];
								echo '<div class="datos_planos">'.$resultados[$campo].'</div>';
							}
						}
						?>  
                    </div><!-- fin resultados-->
                 </div><!-- fin cuadro_elim / cuadro_elim_gana-->
         <?php
		 	if($resultados['local'] == $resultados['ganador']){echo '<div class="linea_elim_gana">&nbsp;</div>';}
			else{echo '<div class="linea_elim">&nbsp;</div>';}
			if($resultados['visitante'] == $resultados['ganador']){echo '<div class="linea_elim_gana">&nbsp;</div>';}
			else{echo '<div class="linea_elim">&nbsp;</div>';}
		 	if($resultados['visitante'] == $resultados['ganador'] && $eliminatoria == 1){echo '<div class="cuadro_elim_ganador_der">';}
			else if($resultados['visitante'] == $resultados['ganador']){echo '<div class="cuadro_elim_gana">';}
			else{echo '<div class="cuadro_elim">';}
		 ?>
					<div class="resultados3">
						<?php
						if($resultados['local'] != 0 && $resultados['visitante'] != 0){
							for($i=0; $i<$sets; $i++){
								$campo = $campos_visitante[$i];
								echo '<div class="datos_planos">'.$resultados[$campo].'</div>';
							}
						}
						?>
					</div>
					<div class="mostrar_equipo2">
						<div class="jugador1_clasif">
							<div class="alinear_texto">
							<?php
							if($resultados['visitante'] == 0){echo 'Clasificado';}
							else{
								if($resultados['modificado'] == $jugador1_vis && $jugador1_vis > 0){
									echo '<span style="text-align:center" onMouseOver="'."showdiv(event,'Este jugador/a ha insertado el resultado del partido');".'" onMouseOut="hiddenDiv()" style="display:table;">';
									echo '<b><i>'.substr(obtenNombreJugadorMostrar($resultados['visitante'],'jugador1'),0,35).'</i></b></span>';
								}
								else{
									if($jugador1_vis == 0){//si es temporal
										$inscripcion_equipoVisit = obten_consultaUnCampo('session','seguro_jug1','equipo','id_equipo',$resultados['visitante'],'','','','','','','');
										echo substr(obten_consultaUnCampo('session','nombre1','inscripcion','id_inscripcion',$inscripcion_equipoVisit,'','','','','','','').' '.obten_consultaUnCampo('session','apellidos1','inscripcion','id_inscripcion',$inscripcion_equipoVisit,'','','','','','',''),0,35);
									}
									else{echo substr(obtenNombreJugador($resultados['visitante'],'jugador1'),0,35);}
								}
							}		 
							?>
							</div>
						</div>
						<div class="jugador2_clasif">
							<div class="alinear_texto">
							<?php
							if($resultados['visitante'] == 0){echo 'Clasificado';}
							else{
								if($resultados['modificado'] == $jugador1_vis && $jugador1_vis > 0){
									echo '<span style="text-align:center" onMouseOver="'."showdiv(event,'Este jugador/a ha insertado el resultado del partido');".'" onMouseOut="hiddenDiv()" style="display:table;">';
									echo '<b><i>'.substr(obtenNombreJugadorMostrar($resultados['visitante'],'jugador2'),0,35).'</i></b></span>';
								}
								else{
									if($jugador1_vis == 0){//si es temporal
										echo substr(obten_consultaUnCampo('session','nombre2','inscripcion','id_inscripcion',$inscripcion_equipoVisit,'','','','','','','').' '.obten_consultaUnCampo('session','apellidos2','inscripcion','id_inscripcion',$inscripcion_equipoVisit,'','','','','','',''),0,35);
									}
									else{echo substr(obtenNombreJugador($resultados['visitante'],'jugador2'),0,35);}
								}
							}		 
							?>
							</div>
						</div>
					</div>
                  </div><!-- fin cuadro_elim / cuadro_elim_gana--> 
			 </div><!-- fin caja2-->
			<?php
				}//fin del while
			echo '</div>';
		}//fin eliminatoria
	  ?>
    	</div><!-- fin contenido -->
    </div><!-- FIN DE CENTRAL--> 
    <div class="derecho">
    	<?php mostrar_columna_pago($_SESSION['bd'],$_SESSION['id_liga'],$_SESSION['id_division'],$url_der,$posicion_publi_der,'D',$url_vacia);?>
    </div>   
<?php
	pie();
?>    
</div>
<?php
cuerpo_fin();
		//updatear la publicidad izq
		for($i=0; $i<count($id_publi_izq); $i++){
		//for($i=0; $i<$cont; $i++){
			$aux = $contador_izq[$i]+1;
			$update = 'contador='.$aux.',ultima_rep="'.date("Y-m-d H:i:s").'"';
			realiza_updateGeneral('session','publicidad',$update,'id_publicidad',$id_publi_izq[$i],'','','','','','','','','');	
		}
		//updatear la publicidad der
		for($i=0; $i<count($id_publi_der); $i++){
		//for($i=0; $i<$cont; $i++){
			$aux = $contador_der[$i]+1;
			$update = 'contador='.$aux.',ultima_rep="'.date("Y-m-d H:i:s").'"';
			realiza_updateGeneral('session','publicidad',$update,'id_publicidad',$id_publi_der[$i],'','','','','','','','','');	
		}
	}//fin de continua
}//FIN DE IF INICIAL
?>