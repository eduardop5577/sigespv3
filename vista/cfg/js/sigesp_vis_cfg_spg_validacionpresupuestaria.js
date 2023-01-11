/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de validaciones presupuestarias 
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var Actualizar=null
ruta ='../../controlador/cfg/sigesp_ctr_cfg_spg_validacionpresupuestaria.php'; //ruta del controlador
barraherramienta    = true;

var Campos =new Array(
	        ['estvalspg','novacio|'],
	        ['ctaspgced','novacio|'],
			['ctaspgrec','novacio|'])

Ext.onReady(function()
{	
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';	
	var Xpos = ((screen.width/2)-(740/2));
	var Ypos = 150;
	var formulario = new Ext.FormPanel({
	applyTo: 'formulario_validacionpresupuestaria',
	width: 700,
	height: 150,
	title: 'Definici&#243;n Validaciones Presupuestarias',
	frame:true,
	labelWidth:200,
	defaults: {width: 100},
    defaultType: 'textfield',
	style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
	items: [{
				xtype: "checkbox",
            	fieldLabel: "Activar Validaci&#243;n",
				inputValue:1,
            	id: 'estvalspg'
        	},
			{
				xtype: 'textfield',
				fieldLabel: 'Cuentas Cedentes',
				id: 'ctaspgced',
				readOnly: true,
				width: 250
			},
			{
				xtype:'button',
				iconCls: 'menubuscar',
				style:'position:absolute;left:460px;top:25px;',
				handler: function ()
						 {
								if(Ext.getCmp('estvalspg').checked)
								{
									mostrarCatalogoCtascedentesreceptoras('C');
								}
								else
								{
									Ext.MessageBox.alert('Advertencia','Debe activar la validacion para asignar cuentas cedentes')
								}
						 }
			},
			{
				xtype: 'textfield',
				fieldLabel: 'Cuentas Receptoras',
				id: 'ctaspgrec',
				readOnly: true,
				width: 250
			},
			{
				xtype:'button',
				iconCls: 'menubuscar',
				style:'position:absolute;left:460px;top:52px;',
				handler: function (){
								if(Ext.getCmp('estvalspg').checked)
								{
									mostrarCatalogoCtascedentesreceptoras('R');
								}
								else
								{
									Ext.MessageBox.alert('Advertencia','Debe activar la validacion para asignar cuentas receptoras')
								}
							}
			}] 
	});
	 irBuscar();
});

function irBuscar()
{
	var myJSONObject ={
			"oper":"catalogo" 
		};
		
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: ruta,
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
            var datos = result.responseText;
			var	resultado = datos.split("|");
			if(resultado[0]==1)
			{
				Ext.getCmp('estvalspg').setValue(true);
			}
			Ext.getCmp('ctaspgced').setValue(resultado[1]);
			Ext.getCmp('ctaspgrec').setValue(resultado[2]);
		},
		failure: function ( result, request){ 
		Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});
}

function irGuardar()
{
	obtenerMensaje('procesar','','Guardando Datos');
	var arregloJson = "{'oper':'incluir','codmenu':"+codmenu+",'estvalspg':'"+Ext.getCmp('estvalspg').getValue()+"','ctaspgced':'"+Ext.getCmp('ctaspgced').getValue()+"','ctaspgrec':'"+Ext.getCmp('ctaspgrec').getValue()+"'}";
	validacion= eval('(' + arregloJson + ')');
	ObjSon=Ext.util.JSON.encode(validacion);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : ruta,
	params : parametros,
	method: 'POST',
	success: function ( resultado, request )
	{ 
		Ext.Msg.hide();
		datos = resultado.responseText;
		registro = datos.split("|");
		codresultado = registro[1];
		switch(codresultado)
		{
			case '0': 
					Ext.Msg.show({
									title:'Mensaje',
									msg: 'Ha ocurrido un error, vuelva a intentar',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
				    break;
			case '1': 
					Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro actualizado con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
					});
				    break;
				    
			case '2': 
					Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro incluido con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
					});
				    break;
		
		}
	  },
	  failure: function ( result, request)
	  { 
			Ext.Msg.hide();
			Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
	  } 
});

}
