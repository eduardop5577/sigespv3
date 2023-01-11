/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var dataStoreCargo="";
var formBusquedaCargo="";
var gridCargo="";
var ventanaCatalogoCargo="";


function creardataStoreCargo(tipocargo)
{

	var registroCargo = Ext.data.Record.create([
							{name: 'codcar'},    
							{name: 'dencar'},
							{name: 'porcar'},
							{name: 'tipo_iva'}
						]);
	
	var objetoCargo={"raiz":[{"codcar":'',"dencar":'',"porcar":'',"tipo_iva":''}]};
		
	dataStoreCargo =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoCargo),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroCargo  
			),
			data: objetoCargo
	});	
	
	var JSONObject = null;
		
	switch (tipocargo)
	{
		case 'N':
			JSONObject = {"oper": 'catalogo'}
			break;
		
		case 'G':
			JSONObject = {"oper": 'catalogo_general'}
			break;
			
		case 'A':
			JSONObject = {"oper": 'catalogo_adicional'}
			break;
	}
		
	var ObjSon = JSON.stringify(JSONObject);
	var	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_cargos.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoCargo = eval('(' + datos + ')');
			if(objetoCargo!='')
			{
				dataStoreCargo.loadData(objetoCargo);
			}
		}	
	});
}

function actdataStoreCargo(criterio,cadena)
{
	dataStoreCargo.filter(criterio,cadena,true,false);
}


function crearFormBusquedaCargo()
{
		formBusquedaCargo = new Ext.FormPanel({
        labelWidth: 90, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'c&#243;digo',
				id:'codigo',
				width: 100,
				changeCheck: function(){
							var v = this.getValue();
							actdataStoreCargo('codcar',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							},							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}               
      			},{
			                fieldLabel: 'Denominaci&#243;n',
			                name: 'denominaci&#243;n',
			                id:'denominacion',
			                width: 400,
							changeCheck: function()
							{
										var v = this.getValue();
										actdataStoreCargo('dencar',v);
										if(String(v) !== String(this.startValue))
										{
											this.fireEvent('change', this, v, this.startValue);
										} 
										},							 
										initEvents : function()
										{
											AgregarKeyPress(this);
										}
			            }]
					});				  

}


function creargridCargo(tipocargo)
{
	crearFormBusquedaCargo();
	creardataStoreCargo(tipocargo);
		 
	 gridCargo = new Ext.grid.GridPanel({
	 width:770,
	 height:350,
	 tbar: formBusquedaCargo,
	 autoScroll:true,
     border:true,
     ds: dataStoreCargo,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codcar'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'dencar'},
          {header: "Porcentaje", width: 50, sortable: true, dataIndex: 'porcar'}
       ]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}
      });            
}

function pasarDatosGridCargo(registro)
{
	Ext.getCmp('codcar').setValue(registro.get('codcar'));
	Ext.getCmp('dencar').setValue(registro.get('dencar'));
	Ext.getCmp('porcar').setValue(registro.get('porcar'));
		
	Actualizar=true;			
}

function pasarDatosGridCargoServicio(datos)
{
	detalleCargo = new registroDetalle
	({
		'codcar':'',
		'dencar':'',
		'porcar':''
	});
	gridDetalles.store.insert(0,detalleCargo);
	detalleCargo.set('codcar',datos.get('codcar'));
	detalleCargo.set('dencar',datos.get('dencar'));
	detalleCargo.set('porcar',datos.get('porcar'));
}

function pasarDatosGridCargoConcepto(datos)
{
	detalleCargo = new registroDetalle
	({
		'codcar':'',
		'dencar':'',
		'porcar':''
	});
	gridDetalles.store.insert(0,detalleCargo);
	detalleCargo.set('codcar',datos.get('codcar'));
	detalleCargo.set('dencar',datos.get('dencar'));
	detalleCargo.set('porcar',datos.get('porcar'));
}

function mostrarCatalogoCargo(ObjetoFuente)
{
				   creargridCargo('N');
                   ventanaCatalogoCargo = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo de cargos',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridCargo],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridCargo.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
	                    		pasarDatosGridCargo(registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(registro);
	                    	break;
                    		case 'servicio':
	                    		pasarDatosGridCargoServicio(registro);
	                    	break;
                    		case 'concepto':
	                    		pasarDatosGridCargoConcepto(registro);
	                    	break;
                    	}          
                    	gridCargo.destroy();
		      			ventanaCatalogoCargo.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridCargo.destroy();
		      			ventanaCatalogoCargo.destroy();
                     }
                    }]
                    
                   });
                  ventanaCatalogoCargo.show();       
 

 }

 //Todo dependiendo del tipo enviar a los campos que corresponde
function pasarDatosCargo(registro,tipo){
	
	if(tipo!='A'){
		Ext.getCmp('codcaradi').setValue('');
		Ext.getCmp('dencaradi').setValue('');
		Ext.getCmp('porcaradi').setValue('');
		Ext.getCmp('codcar').setValue(registro.get('codcar'));
		Ext.getCmp('dencar').setValue(registro.get('dencar'));
		Ext.getCmp('porcar').setValue(registro.get('porcar'));
		if(parseInt(registro.get('tipo_iva'))==1){
			Ext.getCmp('botcatcargoadi').setDisabled(false);
		}
		else{
			Ext.getCmp('botcatcargoadi').setDisabled(true);
		}
	}
	else{
		Ext.getCmp('codcaradi').setValue(registro.get('codcar'));
		Ext.getCmp('dencaradi').setValue(registro.get('dencar'));
		Ext.getCmp('porcaradi').setValue(registro.get('porcar'));
	}
	Actualizar=true;			
 }

 function catalogoCargo(tipocargo)
 {
	 creargridCargo(tipocargo);
	 var ventanaCatalogoCargo = new Ext.Window({
		 	title: 'Cat&#225;logo de cargos',
		 	autoScroll:true,
		 	width:800,
		 	height:400,
		 	modal: true,
		 	closable:false,
		 	plain: false,
		 	items:[gridCargo],
		 	buttons: [{
		 				text:'Aceptar',  
		 				handler: function(){ 
		 							var registro = gridCargo.getSelectionModel().getSelected();
		 							pasarDatosCargo(registro,tipocargo)
		 							gridCargo.destroy();
		 							ventanaCatalogoCargo.destroy();                      
		 				}
		 			  },
		 			  {
		 				text: 'Salir',
		 				handler: function(){
		 							gridCargo.destroy();
		 							ventanaCatalogoCargo.destroy();
		 				}
		 			  }]
     });
    
	 ventanaCatalogoCargo.show();
	 
 }