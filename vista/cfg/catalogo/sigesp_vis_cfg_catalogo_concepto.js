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

var dataStoreConcepto="";
var formularioBusquedaConcepto="";
var gridConcepto="";

function creardataStoreConcepto()
{

	registroConcepto = Ext.data.Record.create([
								{name:'codemp'},			  
								{name:'codcla'}, 
								{name:'dencla'}, 
								{name:'sc_cuenta'},
								{name:'usado'}
						]);							
	
		var objetoConcepto={"raiz":[{"codemp":"","codcla":"","dencla":"","sc_cuenta":""}]};
		
		dataStoreConcepto =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoConcepto),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "codcla"   
			}
			,
		    registroConcepto  
			),
			data: objetoConcepto
	  	})	
		
		var myJSONObject ={
			"oper": 'catalogo'
		}
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_cxp_concepto.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoConcepto = eval('(' + datos + ')');
			if(objetoConcepto!='')
			{
				dataStoreConcepto.loadData(objetoConcepto);
			}
		}	
	})
}

function actdataStoreConcepto(criterio,cadena)
{
	dataStoreConcepto.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaConcepto = new Ext.FormPanel({
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
							actdataStoreConcepto('codcla',v);
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
										actdataStoreConcepto('dencla',v);
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


function creargridConcepto()
{
	crearFormularioBusqueda();
	creardataStoreConcepto();
		 
	 gridConcepto = new Ext.grid.GridPanel({
	 width:770,
	 height:300,
	 tbar: formularioBusquedaConcepto,
	 autoScroll:true,
     border:true,
     ds: dataStoreConcepto,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codcla'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'dencla'}
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

function mostrar_catalogo(){
	creargridConcepto();
	var objetoConcepto  = 'definicion';
    var ventanaConcepto = new Ext.Window({
                    title: 'Cat&#225;logo de Conceptos',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridConcepto],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	var registro = gridConcepto.getSelectionModel().getSelected();
                    	switch(objetoConcepto)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
                    			pasarDatos(registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(registro);
	                    	break;
 
                    		
                    	}          
                    	gridConcepto.destroy();
		      			ventanaConcepto.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridConcepto.destroy();
		      			ventanaConcepto.destroy();
                     }
                    }]
    	});
    ventanaConcepto.show();       
}

function pasarDatos(registro)
{
	
	Ext.getCmp('codemp').setValue(registro.get('codemp'));
	Ext.getCmp('codcla').setValue(registro.get('codcla'));
	Ext.getCmp('dencla').setValue(registro.get('dencla'));
	Ext.getCmp('sc_cuenta').setValue(registro.get('sc_cuenta'));
	Usado=registro.get('usado');
	Actualizar=true;			
} 

