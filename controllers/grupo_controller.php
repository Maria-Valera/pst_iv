<?php
	require_once("../models/config.php");
	require_once("../models/cls_grupo.php");
	    
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

	if(isset($_GET['ope'])){
		switch($_GET['ope']){
			case "Get_grupo":
				fn_Consultar_grupo();
			break;

			case "Get_estu_grup":
				fn_Consultar_grupo_est();
			break;
		}
	}
	

	function fn_Registrar(){
		$model_g = new cls_grupo();
		
		$model_g->setDatos($_POST);
		$mensaje = $model_g->create();

		header("Location: ".constant("URL")."grupo/index/$mensaje");	
	}

	function fn_Actualizar(){
		$model_g = new cls_grupo();
				
		$model_g->setDatos($_POST);
		$mensaje = $model_g->update();

		header("Location: ".constant("URL")."grupo/index/$mensaje");	
	}

	function fn_Consultar_grupo(){
		$models_g = new cls_grupo();
		$result = $models_g->consulta($_GET['id_grupo']);

		print json_encode(["data" => $result]);
	}

	function fn_Consultar_grupo_est(){
		$models_g = new cls_grupo();
		$result = $models_g->Get_estudiasntes_grup($_GET['id_grupo']);

		print json_encode(["data" => $result]);
	}
?>