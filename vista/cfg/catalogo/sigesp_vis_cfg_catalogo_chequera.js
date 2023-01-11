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

var dataStoreChequera="";
var formularioBusquedaChequera="";
var gridChequera="";
var ventanaChequera="";


function crearDataStoreChequera()
{

		var registroChequera = Ext.data.Record.create([	
		                            {name:'codemp'},
									{name:'numchequera'},
									{name:'codban'},
									{name:'nomban'},
									{name:'ctaban'},
									{name:'dencta'},
									{name:'codtipcta'},
									{name:'nomtipcta'}
							]);							
	
		var objetoChequera={"raiz":[{"codemp":"","numchequera":"","codban":"","nomban":"","ctaban":"","dencta":"","codtipcta":"","nomtipcta":""}]};
		
		dataStoreChequera =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoChequera),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "codmon"   
			}
			,
		    registroChequera  
			),
			data: objetoChequera
	  	});	
		
		var objetoJson ={
			"operacion": 'catalogo'
		};
		
		ObjSon=Ext.util.JSON.encode(objetoJson);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scb_chequera.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoChequera = eval('(' + datos + ')');
			if(objetoChequera!='')
			{
				dataStoreChequera.loadData(objetoChequera);
			}
		}	
	});
}

function actdataStoreChequera(criterio,cadena)
{
	dataStoreChequera.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaChequera = new Ext.FormPanel({
        labelWidth: 125,
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 500,
		height:125,
        defaultType: 'textfield',
        items: [{
        	fieldLabel: 'N&#250;mero de Chequera',
        	name: 'codigo',
        	width:250,
        	id:'codigo',
        	changeCheck: function(){
        	var v = this.getValue();
        	actdataStoreChequera('numchequera',v);
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
			fieldLabel: 'Banco',
			name: 'nombanco',
			id:'nombanco',
			width:200,
			changeCheck: function()
			{
			var v = this.getValue();
			actdataStoreChequera('nomban',v);
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
			fieldLabel: 'Cuenta Bancaria',
			name: 'ctabanco',
			id:'ctabanco',
			width:300,
			changeCheck: function()
			{
			var v = this.getValue();
			actdataStoreChequera('ctaban',v);
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


function crearGridChequera()
{
	crearFormularioBusqueda();
	crearDataStoreChequera();
		 
	 gridChequera = new Ext.grid.GridPanel({
	 width:770,
	 height:350,
	 tbar: formularioBusquedaChequera,
	 autoScroll:true,
     border:true,
     ds: dataStoreChequera,
     cm: new Ext.grid.ColumnModel([
          {header: "N&#250;mero de Chequera", width: 75, sortable: true,   dataIndex: 'numchequera'},
          {header: "Cuenta Bancaria", width: 50, sortable: true, dataIndex: 'ctaban'},
          {header: "Banco", width: 50, sortable: true, dataIndex: 'nomban'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
}

function bloquearCamposPrimarios()
{
	var myJSONObject ={
		"oper":"claveprimaria"
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_scb_chequera.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var pk = eval('(' + datos + ')');
		if(pk.length>0)
		{
			for(i=0; i < pk.length; i++)
			{
				Ext.getCmp(pk[i].toString()).setDisabled(true);
			}
		}
	}	
	});
}

/***********************************************************************************
* @Funci�n que carga los detalles de la Chequera
* @par�metros:  registro: variable tipo Record que continene la informacion del Detalle
* 						  de la Chequera
* @retorno: 
* @fecha de creacion: 17/12/2009
* @autor: Ing. Arnaldo Suarez 
***********************************************************************************/	
function cargarDetalleChequera(gridcheques,registro)
{
	var myJSONObject ={
			'operacion': 'detalleschequera',
			'codban':registro.get('codban'),
			'ctaban':registro.get('ctaban'),
			'numchequera':registro.get('numchequera')					
	};
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function (resultado,request)
		{
			datos = resultado.responseText;
			if (datos!='')
			{
				var objetoDetalle = eval('(' + datos + ')');
				if(objetoDetalle != '')
				{
					gridcheques.getStore().loadData(objetoDetalle);
				}
				else
				{
					Ext.MessageBox.alert('Error', datos.raiz[0].mensaje+' Al cargar los detalles.');
				}
			}
		}
	});
}

function cargarDetalleUsuarios(gridusuarios,registro)
{
	var myJSONObject ={
			'operacion': 'detallesusuario',
			'codban':registro.get('codban'),
			'ctaban':registro.get('ctaban'),
			'numchequera':registro.get('numchequera')					
	};
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function (resultado,request)
		{
			datos = resultado.responseText;
			if (datos!='')
			{
				var objetoDetalle = eval('(' + datos + ')');
				if(objetoDetalle != '')
				{
					gridusuarios.getStore().loadData(objetoDetalle);
				}
				else
				{
					Ext.MessageBox.alert('Error', datos.raiz[0].mensaje+' Al cargar los detalles.');
				}
			}
		}
	});
}

function pasarDatosDefinicion(registro)
{
 Ext.getCmp('numchequera').setValue(registro.get('numchequera'));
 Ext.getCmp('codban').setValue(registro.get('codban'));
 Ext.getCmp('nomban').setValue(registro.get('nomban'));
 Ext.getCmp('ctaban').setValue(registro.get('ctaban'));
 Ext.getCmp('dencta').setValue(registro.get('dencta'));
 Ext.getCmp('codtipcta').setValue(registro.get('codtipcta'));
 Ext.getCmp('nomtipcta').setValue(registro.get('nomtipcta'));
 Ext.getCmp('numchequera').disable();
 Ext.getCmp('codban').disable();
 Ext.getCmp('nomban').disable();
 Ext.getCmp('ctaban').disable();
 Ext.getCmp('dencta').disable();
 Ext.getCmp('codtipcta').disable();
 Ext.getCmp('nomtipcta').disable();
 Ext.getCmp('btnbanco').disable();
 Ext.getCmp('btnctabanco').disable();
 Actualizar = true;
}

function mostrarCatalogoChequeraDefinicion(gridCheques,gridUsuarios)
{
				   crearGridChequera();
                   ventanaChequera = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Chequeras',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridChequera],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	var registro = gridChequera.getSelectionModel().getSelected();
                    		irNuevo();
                    	    pasarDatosDefinicion(registro);
                    	    cargarDetalleChequera(gridChequesChequera,registro);
                    	    cargarDetalleUsuarios(gridUsuariosChequera,registro);
                    	    gridChequera.destroy();
		      			    ventanaChequera.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                      	gridChequera.destroy();
		      			ventanaChequera.destroy();
                     }
                    }]
                    
                   });
                  ventanaChequera.show();       
 

 }