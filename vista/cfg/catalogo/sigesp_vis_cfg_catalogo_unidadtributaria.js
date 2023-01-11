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

var dataStoreUnidadTributaria=null;
var formularioBusquedaUnidadTributaria=null;
var gridUnidadTributaria=null;
var ventanaUnidadTributaria=null;


function crearDataStoreUnidadTributaria()
{

	registroUnidadTributaria = Ext.data.Record.create([			  
								{name:'codunitri'}, 
								{name:'anno'}, 
								{name:'fecentvig'}, 
								{name:'gacofi'}, 
								{name:'fecpubgac'}, 
								{name:'decnro'}, 
								{name:'fecdec'}, 
								{name:'valunitri'}]);							
	
		var objetoUnidadTributaria={"raiz":[{"codunitri":"","anno":"","fecentvig":"","gacofi":"","fecpubgac":"",
								   "decnro":"","fecdec":"","valunitri":"0.000"}]};
		
		dataStoreUnidadTributaria =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoUnidadTributaria),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "codunitri"   
			}
			,
			registroUnidadTributaria  
			),
			data: objetoUnidadTributaria
	  	})	
		
		var myJSONObject ={
			"oper": 'catalogo'
		}
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_unidadtributaria.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoUnidad = eval('(' + datos + ')');
			if(objetoUnidad != '')
			{
				
				dataStoreUnidadTributaria.loadData(objetoUnidad);
			}
		}	
	})
}

function actdataStoreUnidadTributaria(criterio,cadena)
{
	dataStoreUnidadTributaria.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaUnidadTributaria = new Ext.form.FormPanel({
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
							actdataStoreUnidadTributaria('codunitri',v);
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


function creargridUnidadTributaria()
{
	crearFormularioBusqueda();
	crearDataStoreUnidadTributaria();
		 
	 gridUnidadTributaria = new Ext.grid.GridPanel({
	 width:770,
	 height:350,
	 tbar: formularioBusquedaUnidadTributaria,
	 autoScroll:true,
     border:true,
     ds: dataStoreUnidadTributaria,
     cm: new Ext.grid.ColumnModel([
          {id:'codunitri',header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codunitri'},
          {id:'anno',header: "A&#241;o", width: 20, sortable: true, dataIndex: 'anno'},
		  {id:'fecentvig',header: "Fecha de Entrada en Vigencia", width: 30, sortable: true, dataIndex: 'fecentvig'},
		  {id:'gacofi',header: "Gaceta Oficial", width: 20, sortable: true, dataIndex: 'gacofi'},
		  {id:'fecpubgac',header: "Fecha de Publicaci&#243;n", width: 30, sortable: true,  dataIndex: 'fecpubgac'},
		  {id:'deccro',header: "Decreto Nro.", width: 20, sortable: true, dataIndex: 'decnro'},
		  {id:'fecdec',header: "Fecha del Decreto", width: 30, sortable: true,  dataIndex: 'fecdec'},
		  {id:'valunitri',header: "Valor", width: 20, sortable: true, dataIndex: 'valunitri'}
       ]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}
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
	url : '../../controlador/cfg/sigesp_ctr_cfg_unidadtributaria.php',
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


function irBuscar()
{
	creargridUnidadTributaria();
	objetoUnidadTributaria='definicion';
    ventanaUnidadTributaria = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Unidad Tributaria',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridUnidadTributaria],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = gridUnidadTributaria.getSelectionModel().getSelected();
                    	switch(objetoUnidadTributaria)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
                    			PasDatosGridDef(Registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
                    	}          
                    	gridUnidadTributaria.destroy();
		      			ventanaUnidadTributaria.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                      	gridUnidadTributaria.destroy();
		      			ventanaUnidadTributaria.destroy();
                     }
                    }]
    });
	ventanaUnidadTributaria.show();       
}