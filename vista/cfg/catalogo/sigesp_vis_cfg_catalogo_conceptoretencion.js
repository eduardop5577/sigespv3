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

var dataStoreConceptoRetencion="";
var formBusquedaConceptoRetencion="";
var gridConceptoRetencion="";
var ventanaConceptoRetencion="";

function creardataStoreConceptoRetencion()
{

	registroConceptoRetencion = Ext.data.Record.create([
	                        {name: 'codemp'},                            
							{name: 'codconret'},    
							{name: 'desact'},
							{name: 'obsconret'}
						]);
	
	var objetoConceptoRetencion={"raiz":[{"codemp":'',"codconret":'',"desact":'',"obsconret":''}]};
		
		dataStoreConceptoRetencion =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoConceptoRetencion),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroConceptoRetencion  
			),
			data: objetoConceptoRetencion
	  	})	
		
		var JSONObject ={
			"operacion": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_conceptoretencion.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoConceptoRetencion = eval('(' + datos + ')');
			if(objetoConceptoRetencion!='')
			{
				dataStoreConceptoRetencion.loadData(objetoConceptoRetencion);
			}
		}	
	})
}

function pasarDatosConceptoRetencion(registro,codigo,denominacion)
{
	if(codigo != null)
	{
		codigo.setValue(String.trim(registro.get('codconret')));
	}
	
	if(denominacion != null)
	{
		if(denominacion.getXType() == 'label')
		{
			denominacion.setText(registro.get('desact'));
		}
		else
		{
			denominacion.setValue(registro.get('desact'));
		}
			
	}
}

function actdataStoreConceptoRetencion(criterio,cadena)
{
	dataStoreConceptoRetencion.filter(criterio,cadena,true,false);
}


function crearFormBusqueda()
{
		formBusquedaConceptoRetencion = new Ext.FormPanel({
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
                name: 'cod',
				id:'cod',
				changeCheck: function(){
							var v = this.getValue();
							actdataStoreConceptoRetencion('codconret',v);
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
			                name: 'den',
			                id:'den',
							changeCheck: function()
							{
										var v = this.getValue();
										actdataStoreConceptoRetencion('desact',v);
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


function crearGridConceptoRetencion()
{
	crearFormBusqueda();
	creardataStoreConceptoRetencion();
		 
	 gridConceptoRetencion = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaConceptoRetencion,
	 autoScroll:true,
     border:true,
     ds: dataStoreConceptoRetencion,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codconret'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'desact'}
       ]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}
      });            
} 

function mostrarCatalogoConceptoRetencion(codigo,denominacion)
{
				   crearGridConceptoRetencion();
                   ventanaConceptoRetencion = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo de conceptos de retenci&#243;n',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridConceptoRetencion],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridConceptoRetencion.getSelectionModel().getSelected();
                    	pasarDatosConceptoRetencion(registro,codigo,denominacion);
                    	gridConceptoRetencion.destroy();
		      			ventanaConceptoRetencion.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridConceptoRetencion.destroy();
		      			ventanaConceptoRetencion.destroy();
                     }
                    }]
                    
                   });
                  ventanaConceptoRetencion.show();       
 

 }