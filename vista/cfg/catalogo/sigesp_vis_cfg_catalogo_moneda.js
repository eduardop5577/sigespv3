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

var dataStoreMoneda="";
var formularioBusquedaMoneda="";
var gridMoneda="";
var ventanaMoneda="";

function creardataStoreMoneda()
{

	registroMoneda = Ext.data.Record.create([			  
								{name:'codmon'},
								{name:'denmon'},
								{name:'desmon'},
								{name:'codpai'},
								{name:'denpai'}, 
								{name:'estatuspri'},
								{name:'abrmon'}
						]);							
	
		var objetoMoneda={"raiz":[{"codmon":"","denmon":"","codpai":"","denpai":"","estmonpri":"","abrmon":""}]};
		
		dataStoreMoneda =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoMoneda),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "codmon"   
			}
			,
		    registroMoneda  
			),
			data: objetoMoneda
	  	})	
		
		var myJSONObject ={
			"oper": 'catalogo'
		}
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_moneda.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoMoneda = eval('(' + datos + ')');
			if(objetoMoneda!='')
			{
				dataStoreMoneda.loadData(objetoMoneda);
			}
		}	
	})
}

function actdataStoreMoneda(criterio,cadena)
{
	dataStoreMoneda.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaMoneda = new Ext.FormPanel({
        labelWidth: 75,
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'codigo',
				id:'codigo',
				changeCheck: function(){
							var v = this.getValue();
							actdataStoreMoneda('codmon',v);
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
			                name: 'denominacion',
			                id:'denominacion',
							changeCheck: function()
							{
										var v = this.getValue();
										actdataStoreMoneda('denmon',v);
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


function creargridMoneda()
{
	crearFormularioBusqueda();
	creardataStoreMoneda();
		 
	 gridMoneda = new Ext.grid.GridPanel({
	 width:770,
	 height:350,
	 tbar: formularioBusquedaMoneda,
	 autoScroll:true,
     border:true,
     ds: dataStoreMoneda,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codmon'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denmon'},
          {header: "Abreviatura", width: 50, sortable: true, dataIndex: 'abrmon'},
          {header: "Pa&#237;s", width: 50, sortable: true, dataIndex: 'denpai'}
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
	url : '../../controlador/cfg/sigesp_ctr_cfg_concepto.php',
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
	})
}

/***********************************************************************************
* @Funci�n que carga los detalles de la Moneda
* @par�metros:  registro: variable tipo Record que continene la informacion del Detalle
* 						  de la Moneda
* @retorno: 
* @fecha de creaci�n: 12/08/2009
* @autor: Ing. Arnaldo Suarez 
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/	
function cargarDetalleMoneda(registro)
{
	var myJSONObject ={
			'oper': 'detalles',
			'codmon':registro.get('codmon')					
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
					gridDetalles.store.loadData(objetoDetalle);
				}
				else
				{
					Ext.MessageBox.alert('Error', datos.raiz[0].mensaje+' Al cargar los detalles.');
				}
			}
		}
	});
}

function CatalogoMoneda()
{
		creargridMoneda();
		objetoMoneda='definicion';
                   ventanaMoneda = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Monedas',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridMoneda],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	var registro = gridMoneda.getSelectionModel().getSelected();
                    	switch(objetoMoneda)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
                    			PasDatosGridDef(registro);
                    			Ext.getCmp('despai').setText(registro.get('denpai'));
								Ext.getCmp('estatuspri').setValue(false);
								if (registro.get('estatuspri')==1)
								{
									Ext.getCmp('estatuspri').setValue(true);
								}
                    			cargarDetalleMoneda(registro)
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(registro);
	                    	break;
 
                    		
                    	}          
                    	gridMoneda.destroy();
		      			ventanaMoneda.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                      	gridMoneda.destroy();
		      			ventanaMoneda.destroy();
                     }
                    }]
                    
                   });
                  ventanaMoneda.show();       
 

 }
 
 function CatalogoMonedaObjeto()
{
                   creargridMoneda();
		   objetoMoneda='objeto';
                   ventanaMoneda = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Monedas',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridMoneda],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	var registro = gridMoneda.getSelectionModel().getSelected();
                    	switch(objetoMoneda)   
                    	{
                    		case 'objeto':
	                    		pasarDatosGridMoneda(registro);
	                    	break;
 
                    		
                    	}          
                    	gridMoneda.destroy();
		      			ventanaMoneda.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                      	gridMoneda.destroy();
		      			ventanaMoneda.destroy();
                     }
                    }]
                    
                   });
                  ventanaMoneda.show();       
 

 }
 
 function pasarDatosGridMoneda(registro)
{
	
	Ext.getCmp('codmon').setValue(registro.get('codmon'));
	Ext.getCmp('denmon').setValue(registro.get('denmon'));		
} 
