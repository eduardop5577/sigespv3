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

var dataStoreDocumento="";
var formularioBusquedaDocumento="";
var gridDocumento="";
var ventanaDocumento="";

function creardataStoreDocumento()
{

	registroDocumento = Ext.data.Record.create([
								{name:'codemp'},			  
								{name:'codtipdoc'}, 
								{name:'dentipdoc'}, 
								{name:'estcon'},
								{name:'estpre'},
								{name:'tipodocanti'},
								{name:'tipdoctesnac'},
								{name:'tipdocdon'}
						]);							
	
		var objetoDocumento={"raiz":[{"codemp":"","codtipdoc":"","dentipdoc":"","estcon":"","estpre":"","tipdocanti":""}]};
		
		dataStoreDocumento =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoDocumento),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "codtipdoc"   
			}
			,
			registroDocumento  
			),
			data: objetoDocumento
	  	})	
		
		var myJSONObject ={
			"oper": 'catalogo'
		}
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_cxp_documento.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoDocumento = eval('(' + datos + ')');
			if(objetoDocumento!='')
			{
				dataStoreDocumento.loadData(objetoDocumento);
			}
		}	
	})
}

function actdataStoreDocumento(criterio,cadena)
{
	dataStoreDocumento.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaDocumento = new Ext.FormPanel({
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
							actdataStoreDocumento('codtipdoc',v);
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
										actdataStoreDocumento('dentipdoc',v);
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


function creargridDocumento()
{
	crearFormularioBusqueda();
	creardataStoreDocumento();
		 
	 gridDocumento = new Ext.grid.GridPanel({
	 width:770,
	 height:350,
	 tbar: formularioBusquedaDocumento,
	 autoScroll:true,
     border:true,
     ds: dataStoreDocumento,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codtipdoc'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'dentipdoc'}
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

function mostrar_catalogo()
{
				   creargridDocumento();
				   objetoConcepto='definicion';
                   ventanaDocumento = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Conceptos',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridDocumento],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = gridDocumento.getSelectionModel().getSelected();
                    	switch(objetoConcepto)   
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
                    	gridDocumento.destroy();
		      			ventanaDocumento.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridDocumento.destroy();
		      			ventanaDocumento.destroy();
                     }
                    }]
                    
                   });
                  ventanaDocumento.show();       
 

 }