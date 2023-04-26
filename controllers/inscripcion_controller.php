<?php
	require_once("../models/config.php");
	require_once("../models/cls_inscripcion.php");
	    
	if(isset($_POST['ope'])){
		switch($_POST['ope']){
			case "Registrar":
				fn_Registrar();
			break;

			case "Actualizar":
				fn_Actualizar();
			break;
		}
	}

	function fn_Registrar(){
		$model_s = new cls_inscripcion();
		
		$model_s->setDatos($_POST);
		$mensaje = $model_s->create();

		header("Location: ".constant("URL")."inscripcion/index/$mensaje");	
	}

	function fn_Actualizar(){
		$model_s = new cls_inscripcion();
				
		$model_s->setDatos($_POST);
		$mensaje = $model_s->update();

		header("Location: ".constant("URL")."inscripcion/index/$mensaje");	
	}
?>