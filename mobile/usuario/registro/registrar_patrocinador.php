<?php
include_once ("../../funciones/f_html.php");
include_once ("../../funciones/f_general.php");
include_once ("../../../class/mysql.php");
session_start();
$pagina = $_SESSION['pagina'];
$_SESSION['tipo_pago'] = limpiaTexto($_POST['tipo_pago']);
if ( $pagina != 'index' && $pagina != 'ver_liga' && $pagina != 'registrar_patrocinador' ){
	header ("Location: ../cerrar_sesion.php");
}
else {
	$_SESSION['pagina']  = 'registrar_patrocinador';
}
$db = new MySQL('unicas');//UNICAS
$consulta = $db->consulta("SELECT Name,Code FROM paises WHERE Code='ESP' ");
cabecera_inicio();
incluir_general(1,1);
?>
<link rel="stylesheet" type="text/css" href="../../css/estilo_info_texto.css" />
<link rel="stylesheet" type="text/css" href="../../css/bpopup.css" />
<link rel="stylesheet" type="text/css" href="css/registrar_usuario.css" />
<script src="../../javascript/localizacion.js" type="text/javascript"></script>
<script src="../../javascript/info_texto.js" type="text/javascript"></script>
<script src="../../javascript/jquery.bpopup.min.js" type="text/javascript"></script>
<script src="javascript/registrar_patrocinador.js" type="text/javascript"></script>
<?php
cabecera_fin();
?>
<div class="principal">
	<!-- POPUP -->
	<div id="content_popup">
    	<div class="poptitulo"><h2>Condiciones y Pol&iacute;tica de datos</h2></div>
        <div class="popcentro">
        	<?php echo condiciones_generales(); ?>
        </div>
        <div class="poppie">
        	<span class="button b-close"><span>ENTENDIDO</span></span>
        </div>
	</div>
    <!-- FIN POPUP -->
	<!--<div class="izquierdo">&nbsp;</div>-->
    <div class="contenido">
    	<div class="paso">
        	<div class="atras"><a href="javascript:window.history.back();"><span class="botonAtras">ATRAS</span></a></div>
        	<div class="num_pasos"><span style="color:#009;">NUEVO</span></div>
            <div class="num_pasos"><span style="color:#009;">&nbsp;PATROCINADOR</span></div>
            <div class="traductor"><div id="google_translate_element"></div></div>
        </div>
        <div class="cuadro">
        	<div id="flotante"></div>
            <form action="gestion_datos_patrocinador.php" hidden="" style="display:none;" method="post" id="" name="form1" onsubmit=" return formulario.obtenTotal();">
            <!--<form action="#" hidden="" style="display:none;" method="post" id="" name="form1" onsubmit=" alert(formulario.obtenTotal());">-->
        </div>
        <div class="columna1">
            <div class="label"><div class="alinearD">Nombre:</div></div>
            <div class="label"><div class="alinearD">Empresa:</div></div>
            <div class="label"><div class="alinearD" onMouseOver="showdiv(event,'Introduce tu email habitual, lo necesitar&aacute;s para el acceso y gesti&oacute;n del panel de patrocinador.');" onMouseOut="hiddenDiv()" style='display:table;'>Email:</div></div>
            <div class="label"><div class="alinearD" onMouseOver="showdiv(event,'Es muy importante indicar un m&oacute;vil v&aacute;lido.');" onMouseOut="hiddenDiv()" style='display:table;'>Tel&eacute;fono:</div></div>
            <div class="label"><div class="alinearD">Contrase&ntilde;a:</div></div>
            <div class="label"><div class="alinearD">Repetir:</div></div>
            <div class="label"><div class="alinearD" onMouseOver="showdiv(event,'."'Es muy importante introducir tu Nif correctamente para poder recibir pagos online con total confianza.'".');" onMouseOut="hiddenDiv()" style="display:table;">Cif:</div></div>
            <div class="label"><div class="alinearD">Direcci&oacute;n:</div></div>
            <!--<div class="label"><div class="alinearD">C&oacute;digo postal:</div></div>-->
            <div class="label"><div class="alinearD">Pa&iacute;s:</div></div>
            <div class="label"><div class="alinearD">Provincia:</div></div>
            <div class="label"><div class="alinearD">Ciudad:</div></div>
        </div>
        <div class="columna2">
        	<!--<form action="" method="" id="" name="" onsubmit="alert(formulario.obtenTotal());"> -->
            <div class="input">
            	<input type="text" name="nombre" id="nombre" class="inputText" onkeypress="return soloLetras(event)" onblur="limpiaLetras('nombre',0)" maxlength="20" >
            </div>
            <div class="input"><input type="text" name="empresa" id="empresa" class="inputText" onkeypress="return tecla_direccion(event)" onblur="limpiaDireccion('empresa',1)" maxlength="100" >
            </div>
            <div class="input"><input type="text" name="email" id="email" class="inputText" onkeypress="return tecla_email(event)" onblur="limpiaEmail('email',2)" maxlength="50" >
            </div>
            <div class="input"><input type="text" name="telefono" id="telefono" class="inputText" onkeypress="return numeros(event)" onblur="limpiaNumeros('telefono',3,0)" maxlength="9" >
            </div>
            <div class="input"><input type="password" name="password" id="password" class="inputText" onkeypress="return tecla_password(event)" onblur="limpiaPassword('password',4)" maxlength="15" ></div>
            <div class="input"><input type="password" name="repassword" id="repassword" class="inputText" onkeypress="return tecla_password(event)" onblur="compara('password','repassword',5)" maxlength="15" ></div>
            <div class="input"><input type="text" name="cif" id="cif" class="inputText" onkeypress="return tecla_dni(event)" onblur="limpiadni('."'cif'".',6)"  maxlength="9"></div>
            
            <div class="input"><input type="text" name="direccion" id="direccion" class="inputText"  onkeypress="return tecla_direccion(event)" onblur="limpiaDireccion('direccion',8,<?php echo $_SESSION['tipo_pago'] ?>)" maxlength="50" ></div>
            <!--<div class="input"><input type="text" name="cp" id="cp" class="inputText"  onkeypress="return numeros(event)" onblur="limpiaNumeros('cp',9,<?php //echo $_SESSION['tipo_pago'] ?>)" ></div>-->
            <div class="input"><select name="pais" id="pais" class="inputText" onchange="lista('pais',10)">
            	<option value="">--Pais--</option>
				<?php
				if($consulta->num_rows>0){
				  while($resultados = $consulta->fetch_array(MYSQLI_ASSOC)){
					 echo '<option  value="'.$resultados['Code'].'">'.$resultados['Name'].'</option>';
				  }
				}
				$db->cerrar_conexion();// Desconectarse de la base de datos ?>
                </select>
 			</div>
            <div class="input"><select name="provincia" id="provincia" class="inputText" onchange="lista('provincia',11)"></select></div>
            <div class="input"><select name="ciudad" id="ciudad" class="inputText" onchange="lista('ciudad',12)"></select></div>
        </div>
        <div class="columna3">
<?php 
if($_SESSION['tipo_pago'] != 0){
	for($i=0; $i<13; $i++){
		if($i == 7){
			echo '<div>&nbsp;</div>';
		}
		else{	
    		echo '<div>*</div>';
		}
	}
}
else{
	echo '<div>*</div>
          <div>*</div>
          <div>*</div>
          <div>*</div>
		  <div>*</div>
		  <div>*</div>
		  <div>&nbsp;</div>
          <div>&nbsp;</div>
          <div>*</div>
		  <div>*</div>
          <div>*</div>';
}
?>     
        </div>
        <div class="columna4">
            <div class="comentario"><div class="alinearI"><span id="nombreCom">Introduzca solo letras.</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="empresaCom">Introduzca solo letras.</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="emailCom">Introduzca un email v&aacute;lido</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="telefonoCom">Introduzca 9 d&iacute;gitos num&eacute;ricos</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="passwordCom">Utilice entre 4-15 letras o n&uacute;meros.</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="repasswordCom">Contrase&ntilde;as incorrectas.</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="cifCom">Introduzca su Cif correctamente.</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="direccionCom">Introduzca su direcci&oacute;n correctamente.</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="cpCom">Introduzca su c&oacute;digo postal correctamente.</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="paisCom">Introduzca un pa&iacute;s.</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="provinciaCom">Introduzca una provincia.</span></div></div>
            <div class="comentario"><div class="alinearI"><span id="ciudadCom">Introduzca una ciudad.</span></div></div>
        </div>
        <!-- esto es un comentario -->
        <div class="cuadro2">
            <div class="cuadro2div1"><input type="checkbox" id="condiciones" class="radio">
            </div>
            <div class="cuadro2div2">&nbsp;Al hacer clic en Inscribir, Aceptas las Condiciones y <br />confirmas que has le&iacute;do nuestra Pol&iacute;tica de datos.</div>
        	
        </div>
        <div class="cuadro2">
        	<div class="cuadro2div2"><input type="submit" value="Registrar" class="inputRegistrar" /></div></form>
        </div>
    </div>
    <!--<div class="derecho">&nbsp;</div>-->
<?php
pie();
?>
</div>
<?php
cuerpo_fin();
?>

