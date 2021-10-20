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
if(!empty($_SESSION['ancho']) && !empty($_SESSION['alto'])){//comprobamos al resolucion para cargar css
	if($_SESSION['alto'] <= 480){$dimensiones = ' 320x480';}// ipad pro 320x480
	else if($_SESSION['alto'] > 480 && $_SESSION['alto'] <= 640){$dimensiones = ' 360x640';}// iphone 5s y android 4.7", 5" y 5.5"
	else if($_SESSION['alto'] > 640 && $_SESSION['alto'] <= 667){$dimensiones = ' 375x667';}// iphone 6s y 7s
	else if($_SESSION['alto'] > 667 && $_SESSION['alto'] <= 736){$dimensiones = ' 414x736';}// iphone 6s+ y 7s+
	else if($_SESSION['alto'] > 736 && $_SESSION['alto'] <= 960){$dimensiones = ' 360x640';}// android 7"
	else if($_SESSION['alto'] > 960 && $_SESSION['alto'] <= 1024){$dimensiones = ' 768x1024';}// android 10", ipad air, air2
	else {$dimensiones = ' 360x640';}
	//echo $dimensiones;
}
else{$dimensiones = ' 360x640';}
if( (!empty($_SESSION['id_liga']) && !empty($_SESSION['id_division']) && !empty($_SESSION['bd']) && $_SESSION['tipo_pago'] == 0) || !empty($_POST['a']) || !empty($_GET['a']) ){//verifico datos session o post
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
		if($bd == 0){$_SESSION['bd'] = 'admin_torneo';}
		else{$_SESSION['bd'] = 'admin_torneo'.$bd;}
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
		$cont = 0;
		$id_publi = array();
		$url = array();
		$contador = array();
		$liga = new Liga($_SESSION['id_liga'],'','','','','','','','','','','','','','','','');
		//echo 'ahora '.date('Y-m-d H:i:s').'<br>';
		$db = new MySQL('unicas');//LIGA PADEL
		$consulta = $db->consulta("SELECT id_publicidad_gratis,url,contador FROM publicidad_gratis WHERE provincia = '".$liga->getValor('provincia')."' AND ciudad = '".$liga->getValor('ciudad')."' AND pagado = 'S' AND estado = '0' AND fecha_fin >= '".date('Y-m-d H:i:s')."' ORDER BY ultima_rep,contador LIMIT 10; ");
		if($consulta->num_rows > 0){
			while($resultados = $consulta->fetch_array(MYSQLI_ASSOC)){
				//echo $resultados['ultima_rep'].'-'.$resultados['fecha_fin'].'<br>';
				$id_publi[$cont] = $resultados['id_publicidad_gratis'];
				$url[$cont] = $resultados['url'];
				$contador[$cont] = $resultados['contador'];
				$cont++;
			}//fin while
		}//fin num_rows
		$id_publi_izq = array();
		$id_publi_der = array();
		$url_izq = array();
		$url_der = array();
		$cont_izq = 0;
		$cont_der = 0;
		for($j=0; $j<10; $j++){//rellenamos array con publi izquierda y derecha
			if( ($j+1) <= count($id_publi) ){//entro si hay datos
				if($j <= 4){//para la columna izq
					$id_publi_izq[$cont_izq] = $id_publi[$j];
					$url_izq[$cont_izq] = $url[$j];
					$cont_izq++;
				}
				else{//para la columna der
					$id_publi_der[$cont_der] = $id_publi[$j];
					$url_der[$cont_der] = $url[$j];
					$cont_der++;
				}
			}//fin if con datos
			else{
				break;
			}
		}//fin for
		$url_publi_vacia = '../../usuario/registro/registrar_patrocinador.php';//URL PARA LA PUBLI VACIA, EN ESTE CASO ACCESO AL REGISTRO DE PUBLICIDAD
		//DATOS CALENDARIO
		$max_jornadas = obten_consultaUnCampo('session','MAX(jornada)','partido','division',$_SESSION['id_division'],'','','','','','','');
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
		//echo $max_jornadas;
		//las jornadas siempre son equipos-1, asi que se suma 1 para obtener el numero de equipos y dividir
			
		$id_premio = obten_consultaUnCampo('session','id_premio','premio','division',$_SESSION["id_division"],'','','','','','','');
		$id_regla = obten_consultaUnCampo('session','id_regla','regla','liga',$_SESSION["id_liga"],'','','','','','','');
		$estilo = obten_consultaUnCampo('session','estilo','liga','id_liga',$_SESSION["id_liga"],'','','','','','','');
		$auto_completar = obten_consultaUnCampo('session','auto_completar','liga','id_liga',$_SESSION["id_liga"],'','','','','','','');
cabecera_inicio();
?>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Cabin">
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Alegreya+SC">
<link rel="stylesheet" type="text/css" href="../../../sweetalert-master/lib/sweet-alert.css" />
<link rel="stylesheet" type="text/css" href="../../css/estilo_info_texto.css" />
<link rel="stylesheet" type="text/css" href="../../css/ligas<?php echo $estilo.$dimensiones; ?>.css" />
<link rel="stylesheet" type="text/css" href="../../css/pie.css" />
<script src="../../javascript/validaciones.js" type="text/javascript"></script>
<script src="../../javascript/jquery-1.11.1.js" type="text/javascript"></script>
<script src="../../javascript/detect_paginas.js" type="text/javascript"></script>
<script src="../../javascript/info_texto.js" type="text/javascript"></script>
<script src="../../../sweetalert-master/lib/sweet-alert.min.js" type="text/javascript"></script>
<script src="../javascript/modificar_calendario.js" type="text/javascript"></script>
<script src="https://apis.google.com/js/platform.js" async defer>
  {lang: 'es'}
</script>
<style>
.caja2 {
	width:99% !important;
	margin-top:4%;
	float:left;
}
.cuadro_elim {
	width:80% !important;
	margin-top:2%;
	border:1px #C1C1C1 solid;
	background-color:#FFF;
	box-shadow:2px 2px 3px rgba(0,0,0,0.3);
	float:left;
}
.cuadro_elim_gana {
	width:80% !important;
	margin-top:2%;
	border:1px #090 solid;
	background-color:#FFF;
	box-shadow:2px 2px 3px rgba(0,0,0,0.3);
	float:left;
}
.linea_elim {
	width:10% !important;
	border-bottom:1px #C1C1C1 solid;
	float:left;
}
.linea_elim_gana {
	width:10% !important;
	border-bottom:1px #090 solid;
	float:left;
}
.mostrar_equipo2 {
	width:85% !important;
	float:left;
}
.jugador1_clasif {
	width:99.9% !important;
	height:49% !important;
	text-align:center;
	color:#003;
	font-size:90%;
	/*border-right:1px #C1C1C1 solid;*/
	float:left;
}
.jugador2_clasif {
	width:99.9% !important;
	height:50% !important;
	text-align:center;
	color:#003;
	font-size:90%;
	float:left;
}
.resultados2 {
	width:13% !important;
	height:90% !important;
	margin-top:1%;
	padding-left:0.5%;
	float:left;
}
.resultados3 {
	width:13% !important;
	height:90% !important;
	margin-top:1%;
	padding-left:1%;
	float:left;
}

</style>
<?php
cabecera_fin();
?>
<div class="principal">
    <div class="izquierdo">
        <?php mostrar_columna($id_publi_izq,$url_izq,$url_publi_vacia); ?>
    </div>
    <div class="central">
    	<div class="cabecera">
        	<div class="logo">
            <?php 
				$logo = '../../../logos/'.$_SESSION['bd'].$_SESSION['id_liga'].'.jpg';
				if(file_exists($logo)){echo '<img src="'.$logo.'" alt="torneos de padel" />';}
				else{echo '<img src="../../../logos/0'.$estilo.'.jpg" alt="torneos de padel" />';}
			?>
            </div>
    		<div class="superior">
        		<div class="nombre"><?php echo  $_SESSION["nombre"].' - Divisi&oacute;n '.$_SESSION['num_division'];?></div>
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
        	<div class="texto_titulo">CALENDARIO</div>
        	<div class="publi_google"><!--REDES SOCIALES-->
            	<div class="red_social"><a href="http://facebook.com/sharer.php?u=http%3A%2F%2Fwww.mitorneodepadel.es" target="_blank"><img src="../../../images/facebook-logo.png" /></a></div>
               <div class="red_social"><a href="whatsapp://send?text= http://www.mitorneodepadel.es" data-action="share/whatsapp/share"><img src="../../../images/whatsapp-logo.png" /></a></div>
               <div class="red_social"><a href="https://plus.google.com/share?url=http%3A%2F%2Fwww.mitorneodepadel.es" target="_blank"><img src="../../../images/google-logo.png" /></a></div>
               <div class="red_social"><a href="http://twitter.com/home?status=<?php echo urlencode("Torneos de padel http://www.mitorneodepadel.es");?>" target="_blank"><img src="../../../images/twitter-logo.png" /></a></div>
            </div><!-- FIN REDES SOCIALES-->
            <div class="contenedor_datos">
            	<div id="flotante"></div>
            <?php
			//onMouseOver="showdiv(event,'."'Obligatorio'".');" onMouseOut="hiddenDiv()" style="display:table;"
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
							echo '<div class="eliminatoria"><a href="calendario.php?eliminatoria='.$i.'" class="seleccionado" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
						else{
							echo '<div class="eliminatoria"><a href="calendario.php?eliminatoria='.$i.'" class="normal" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
					}
					else if($partidos_finalizados == $i){//si la suma de todos los finalizados es igual a los partidos por jornada = jornada finalizada
						if($i == $eliminatoria && $_SESSION['seleccionado'] == 'E'){
							echo '<div class="eliminatoria_completa"><a href="calendario.php?eliminatoria='.$i.'" class="seleccionado" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
						else{
							echo '<div class="eliminatoria_completa"><a href="calendario.php?eliminatoria='.$i.'" class="normal" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
					}
					else{
						if($i == $eliminatoria && $_SESSION['seleccionado'] == 'E'){
							echo '<div class="eliminatoria_disputandose"><a href="calendario.php?eliminatoria='.$i.'" class="seleccionado" >'.obten_nombreEliminatoria($i).'</a></div>';
						}
						else{
							echo '<div class="eliminatoria_disputandose"><a href="calendario.php?eliminatoria='.$i.'" class="normal" >'.obten_nombreEliminatoria($i).'</a></div>';
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
				if($resultados['local'] == $resultados['ganador']){echo '<div class="cuadro_elim_gana">';}
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
							</div><!--fin alinear texto -->
						</div><!--fin jugador1 clasif -->
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
							</div><!--fin alinear texto -->
						</div><!--fin jugador2 clasif -->
					</div><!--fin mostrar_equipo2 -->
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
					</div><!--fin resultados -->
                </div><!-- fin cuadro_elim / cuadro_elim_gana-->
        <?php
		 	if($resultados['local'] == $resultados['ganador']){echo '<div class="linea_elim_gana">&nbsp;</div>';}
			else{echo '<div class="linea_elim">&nbsp;</div>';}
			//cuadro siguiente pareja
			if($resultados['visitante'] == $resultados['ganador']){echo '<div class="cuadro_elim_gana">';}
			else{echo '<div class="cuadro_elim">';}
		 ?>
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
							</div><!--fin alinear texto -->
						</div><!--fin jugador1_clasif -->
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
							</div><!--fin alinear texto -->
						</div><!--fin jugador2_clasif-->
					</div><!-- fin mostrar_jugador2 -->
                    <div class="resultados2">
						<?php
						if($resultados['local'] != 0 && $resultados['visitante'] != 0){
							for($i=0; $i<$sets; $i++){
								$campo = $campos_visitante[$i];
								echo '<div class="datos_planos">'.$resultados[$campo].'</div>';
							}
						}
						?>
					</div>
                </div><!-- fin cuadro_elim / cuadro_elim_gana-->
                 <?php
						if($resultados['visitante'] == $resultados['ganador']){echo '<div class="linea_elim_gana">&nbsp;</div>';}
						else{echo '<div class="linea_elim">&nbsp;</div>';}
				?>
			</div><!-- fin caja -->
			<?php
				}//fin del while
			?>   
            </div><!--fin contenedor datos -->
    </div>
    </div><!-- FIN DE CENTRAL--> 
    <div class="derecho">
    	<?php mostrar_columna($id_publi_der,$url_der,$url_publi_vacia); ?>
    </div>   
<?php
	//pie();
?>    
</div>
<?php
cuerpo_fin();
		//updatear la publicidad
		for($i=$cont; $i>=0; $i--){
		//for($i=0; $i<$cont; $i++){
			$aux = $contador[$i]+1;
			$update = 'contador='.$aux.',ultima_rep="'.date("Y-m-d H:i:s").'"';
			realiza_updateGeneral('unicas','publicidad_gratis',$update,'id_publicidad_gratis',$id_publi[$i],'','','','','','','','','');	
		}
	}//fin de continua
}//FIN DE IF INICIAL
?>