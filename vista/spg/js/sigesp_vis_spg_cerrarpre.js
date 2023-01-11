/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var fromRevCiePreGas = null; //varibale para almacenar la instacia de objeto de formulario 
var gridOrdSer = null;
barraherramienta = true;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	
	//Creando formulario principal 
	var Xpos = ((screen.width/2)-(375));
	var Ypos = ((screen.height/2)-(650/2));
	fromRevCiePreGas = new Ext.FormPanel({
		title: "<H1 align='center'>REVERSO/CIERRE DE PRESUPUESTO DE GASTO</H1>",
		applyTo: 'formRevCiePreGas',
		width: 700,
		height: 150,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		frame: true,
		autoScroll:true,
		items: [{
				xtype:"fieldset", 
				title:'',
				style: 'position:absolute;left:17px;top:15px',
				border:true,
				width: 650,
				cls :'fondo',
				height: 70,
				items:[{
						layout:"column",
						defaults: {border: false},
						style: 'position:absolute;left:200px;top:20px',
						border:false,
						items:[{
								layout:"form",
								border:false,
								items:[{
										xtype: 'button',
										labelSeparator :'',
										fieldLabel: '',
										id: 'btbuscar',
										text: 'CERRAR/REVERSAR PRESUPUESTO DE GASTO',
										handler: function()
										{
											var operacion = 1;
											verificarEstatus();
											function respuesta(btn){
												if(btn=='yes'){
													obtenerMensaje('procesar','','Procesando Información');
				
													//creacion del objeto json
													var JSONObject = {
															'operacion' : 'cerrarPresupuesto',
															'codsis'    : sistema,
															'nomven'    : vista,
															'cierev'    : operacion,
													}
				
													var ObjSon = JSON.stringify(JSONObject);
													var parametros = 'ObjSon='+ObjSon; 
													Ext.Ajax.request({
														url : '../../controlador/spg/sigesp_ctr_spg_cerrarpre.php',
														params : parametros,
														method: 'POST',
														success: function ( resultado, request){
															datos = resultado.responseText;
															Ext.Msg.hide();
															var datajson = eval('(' + datos + ')');
															if(datajson.raiz.valido==true)
															{	
																Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
																irCancelar();
																verificarEstatus();
															}
															else
															{
																Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
															}
														}//fin de success	
													});//fin de ajax request
												}
											}
											if(Ext.getCmp('estciespg').getValue()==0){
												Ext.MessageBox.confirm('Confirmar', '¿Esta seguro de hacer el Cierre de Presupuesto de Gasto?', respuesta);
											}
											else{
												operacion = 0;
												Ext.MessageBox.confirm('Confirmar', '¿Esta seguro de reversar el Cierre de Presupuesto de Gasto?', respuesta);
											}
								        }
									}]
								}]
						},
						{
						xtype: 'hidden',
						id: 'estciespg',
						binding:true,
						defaultvalue:''
						}]
				}]
	});
	verificarEstatus();
});//fin creando formulario principal con parametros de busqueda y grid de modificaciones

	//-------------------------------------------------------------------------------------------------------------------------		 

function irCancelar(){
	limpiarFormulario(fromRevCiePreGas);
}

function verificarEstatus()
{
	var myJSONObject = {
		"operacion":"buscarEstatus" 
	};
			
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/spg/sigesp_ctr_spg_cerrarpre.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
			var ciespg = result.responseText;
			if (ciespg != "")
			{
				Ext.getCmp('estciespg').setValue(ciespg);
			}
		},
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});	
}