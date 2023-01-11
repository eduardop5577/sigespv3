/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var fromConfiguracion = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;

var	fromContBanco = new Ext.form.FieldSet({ 
		title:'Caja y Banco',
		style: 'position:absolute;left:10px;top:10px',
		border:true,
		width: 550,
		cls :'fondo',
		height: 70,
		items:[{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:80px;top:15px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 300,
						items: [{
								xtype: 'checkbox',
								labelSeparator :'',
								fieldLabel: 'Contabilizar los Depositos con la fecha del Sistema',
								id: 'configban',
								inputValue:1,
								allowBlank:true
							}]
						}]
				}] 
})

var	fromContObra = new Ext.form.FieldSet({ 
		title:'Obras',
		style: 'position:absolute;left:10px;top:90px',
		border:true,
		width: 550,
		cls :'fondo',
		height: 70,
		items:[{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:80px;top:15px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 300,
						items: [{
								xtype: 'checkbox',
								labelSeparator :'',
								fieldLabel: 'Contabilizar los Contratos con la fecha del Sistema',
								id: 'configobr',
								inputValue:1,
								allowBlank:true
							}]
						}]
				}] 
})

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(600/2));
	fromConfiguracion = new Ext.FormPanel({
		applyTo: 'formConfiguracion',
		width: 600,
		height: 225,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:65px;',
		title: "<H1 align='center'>Configuraci&#243;n</H1>",
		frame: true,
		autoScroll:true,
		items: [fromContBanco,fromContObra]
	});
	buscarConfiguracion("MIS","BANCO","FECHA_CONT_DEPOSITOS","0","C",'configban');
	buscarConfiguracion("MIS","OBRAS","FECHA_CONT_CONTRATOS","0","C",'configobr');
	fromConfiguracion.doLayout();
});

function irGuardar(){
	if(Ext.getCmp('configban').getValue()){
		guardar("MIS","BANCO","FECHA_CONT_DEPOSITOS","1","C",'Depositos');
	}
	if(!Ext.getCmp('configban').getValue()){
		guardar("MIS","BANCO","FECHA_CONT_DEPOSITOS","0","C",'Depositos');
	}
	if(Ext.getCmp('configobr').getValue()){
		guardar("MIS","OBRAS","FECHA_CONT_CONTRATOS","1","C",'Contratos');
	}
	if(!Ext.getCmp('configobr').getValue()){
		guardar("MIS","OBRAS","FECHA_CONT_CONTRATOS","0","C",'Contratos');
	}
}

function guardar(sistema,seccion,variable,valor,tipo,campo){
	
	var myJSONObject = {
			"operacion" : "insertar_config",
			"sistema"   : sistema,
			"seccion"   : seccion,
			"variable"  : variable,
			"valor"     : valor,
			"tipo"      : tipo,
			"nomven"    : vista,
	};
			
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/mis/sigesp_ctr_mis_configuracion.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
			var valido = result.responseText;
			if(valido)
			{
				Ext.MessageBox.alert('Exito', 'La configuraci&#243;n de los '+campo+' fue registrada ');
			}
		},
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error', 'La configuraci&#243;n no pudo ser registrada'); 
		}
	});
}

function buscarConfiguracion(sistema,seccion,variable,valor,tipo,campo){
	var myJSONObject = {
			"operacion" : "select_config",
			"sistema"   : sistema,
			"seccion"   : seccion,
			"variable"  : variable,
			"valor"     : valor,
			"tipo"      : tipo,
			"nomven"    : vista,
	};
			
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/mis/sigesp_ctr_mis_configuracion.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
			var config = result.responseText;
			if (config != 0)
			{
				Ext.getCmp(campo).setValue(true);
			}
		},
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});
}