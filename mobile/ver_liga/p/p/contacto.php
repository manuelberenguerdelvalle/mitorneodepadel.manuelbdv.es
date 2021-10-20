<?php
include_once ("../../funciones/f_general.php");
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_ligas.php");
include_once ("../../funciones/f_html.php");
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
}
else{$dimensiones = ' 360x640';}
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
		//DATOS CONTACTO
		$id_premio = obten_consultaUnCampo('session','id_premio','premio','division',$_SESSION["id_division"],'','','','','','','');
		$id_regla = obten_consultaUnCampo('session','id_regla','regla','liga',$_SESSION["id_liga"],'','','','','','','');
		$estilo = obten_consultaUnCampo('session','estilo','liga','id_liga',$_SESSION["id_liga"],'','','','','','','');
cabecera_inicio();
?>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Cabin">
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Alegreya+SC">
<link rel="stylesheet" type="text/css" href="../../css/ligas<?php echo $estilo.$dimensiones; ?>.css" />
<link rel="stylesheet" type="text/css" href="../../css/pie.css" />
<link rel="stylesheet" type="text/css" href="../../../sweetalert-master/lib/sweet-alert.css" />
<link rel="stylesheet" type="text/css" href="../../css/bpopup.css" />
<script src="../../javascript/jquery-1.11.1.js" type="text/javascript"></script>
<script src="../../javascript/detect_paginas.js" type="text/javascript"></script>
<script src="../../javascript/validaciones.js" type="text/javascript"></script>
<script src="../../../sweetalert-master/lib/sweet-alert.min.js" type="text/javascript"></script>
<script src="../../javascript/jquery.bpopup.min.js" type="text/javascript"></script>
<script src="../javascript/ligas_pago.js" type="text/javascript"></script>

<?php
cabecera_fin();
?>
<div class="principal">
	<div id="oculto"></div>
	<div id="content_popup">
		<div class="poptitulo"><h2>Contactar con el Administrador</h2></div>
		<div class="popcentro">
        <h3>Para publicitarte en esta posici&oacute;n debes contactar con el administrador de este torneo.</h3>
		<form id="formulario_contacto" action="#" method="post" name="formulario_contacto" >
            <label class="caja_texto">Tu email:</label><label class="caja_input"><input name="contacto"  type="text" class="input_text_liga" ></label><label id="errorContacto" class="caja_error">*</label>
            <label class="caja_texto">Asunto:</label><label class="caja_input"><input name="asunto" type="text" class="input_text_liga" ></label><label id="errorAsunto" class="caja_error">*</label>
            <label class="caja_texto">Mensaje:</label><label class="caja_input_area"><textarea  rows="4" cols="25" name="mensaje" class="input_text_area" ></textarea></label><label id="errorTextarea" class="caja_error">*</label>
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
        		<div class="nombre"><?php echo  '<div>'.$_SESSION["nombre"].'</div>'.generar_input_divs($_SESSION['id_liga'],$_SESSION['id_division'],'contacto.php');?></div>
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
        	<div class="texto_titulo">CONTACTO</div>
        	<div class="publi_google">&nbsp;</div>
            <div class="contenedor_datos">
           		<form id="formulario_contacto2" action="#" method="post" name="formulario_contacto2" >
				<label class="caja_texto2">Tu Email:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                <label class="caja_input2"><input name="contacto" id="contacto"  type="text" class="input_text_liga2" onKeyPress="return tecla_email(event)" onBlur="limpiaEmail('contacto',0)" ></label>
                <label class="caja_error2">*<span  id="contactoCom"> Email err&oacute;neo</span></label>
				<label class="caja_texto2">Asunto:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                <label class="caja_input2"><input name="asunto" id="asunto" type="text" class="input_text_liga2" onKeyPress="return soloLetras(event)" onBlur="limpiaLetras('asunto',1)" ></label>
                <label class="caja_error2">*<span id="asuntoCom"> Asunto err&oacute;neo</span></label>
				<label class="caja_texto2">Mensaje:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                <label class="caja_input_area2"><textarea  rows="5" cols="14" name="mensaje" id="mensaje" class="input_text_area2" onKeyPress="return tecla_direccion(event)" onBlur="limpiaDireccion('mensaje',2,1)"  ></textarea></label>
                <label class="caja_error2">*<span id="mensajeCom"> Mensaje err&oacute;neo</span></label>
                <label id="errorTextarea" class="caja_error2">&nbsp;</label>
                <label id="errorTextarea" class="caja_error2">&nbsp;</label>
                <label id="errorTextarea" class="caja_error2">&nbsp;</label>
                <label id="errorTextarea" class="caja_error2">&nbsp;</label>
                <label id="errorTextarea" class="caja_error2">&nbsp;</label>
                <label class="caja_texto2">&nbsp;</label><label class="caja_input2"><span><a class="botonAtras2" onclick="enviar_general();" href="#">ENVIAR</a></span></label><label id="errorTextarea" class="caja_error2">&nbsp;</label>
                <input type="hidden" name="id_liga" value="<?php echo $_SESSION['id_liga'];?>">
                <input type="hidden" name="id_division" value="<?php echo $_SESSION['id_division'];?>">
                <input type="hidden" name="modo" value="3">
                <label class="caja_texto2">&nbsp;</label>
                <label class="caja_input2">&nbsp;</label>
                <label class="caja_error2">&nbsp;</label>
			</form>
            </div><!--fin contenedor datos -->
            <br />&nbsp;
    </div>
    </div><!-- FIN DE CENTRAL--> 
    <div class="derecho">
    	<?php mostrar_columna_pago($_SESSION['bd'],$_SESSION['id_liga'],$_SESSION['id_division'],$url_der,$posicion_publi_der,'D',$url_vacia);?>
    </div>   
<?php
	//pie();
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