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

var dataStoreControlNumero=null;
var formularioBusquedaControlNumero=null;
var gridControlNumero=null;
var ventanaControlNumero=null;

function creardataStoreControlNumero()
{

	registroControlNumero = Ext.data.Record.create([			  
	                            {name:'codemp'},
								{name:'id'}, 
								{name:'codsis'},
								{name:'procede'},
								{name:'prefijo'}, 
								{name:'nro_inicial'},
								{name:'nro_final'},
								{name:'maxlen'},
								{name:'nro_actual'}, 
								{name:'estcompscg'},
								{name:'estact'}]);
	
		var objetoControlNumero={"raiz":[{"codemp":"","id":"","codsis":"","prefijo":"","procede":"","nro_inicial":"","nro_final":"","maxlen":"","nro_actual":"","estcompscg":"","estact":""}]};
		
		dataStoreControlNumero =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoControlNumero),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
			registroControlNumero  
			),
			data: objetoControlNumero
	  	})	
		
		var myJSONObject ={
			"oper": 'catalogo'
		}
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_controlnumero.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoControl = eval('(' + datos + ')');
			if(objetoControl != null)
			{
				
				dataStoreControlNumero.loadData(objetoControl);
			}
		}	
	})
}

function actdataStoreControlNumero(criterio,cadena)
{
	dataStoreControlNumero.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaControlNumero = new Ext.form.FormPanel({
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
							actdataStoreControlNumero('id',v);
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


function creargridControlNumero()
{
	crearFormularioBusqueda();
	creardataStoreControlNumero();
		 
	 gridControlNumero = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formularioBusquedaControlNumero,
	 autoScroll:true,
     border:true,
     ds: dataStoreControlNumero,
     cm: new Ext.grid.ColumnModel([
          {id:'id',header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'id'},
          {id:'codsis',header: "Sistema", width: 20, sortable: true, dataIndex: 'codsis'},
		  {id:'procede',header: "Procedencia", width: 30, sortable: true,  dataIndex: 'procede'},
		  {id:'nro_actual',header: "N&#250;mero Actual", width: 20, sortable: true, dataIndex: 'nro_actual'}
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
	url : '../../controlador/cfg/sigesp_ctr_cfg_controlnumero.php',
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
				if(Ext.getCmp(pk[i].toString()) != null)
				{
					Ext.getCmp(pk[i].toString()).setDisabled(true);
				}
			}
		}
	}
	})
	Ext.getCmp('prefijo').setDisabled(true);
}

/***********************************************************************************
* @Función que carga los usuarios del Control Numerico
* @parámetros:  registro: variable tipo Record que continene la informacion del Control
*                         de numero a buscar
* @retorno: 
* @fecha de creación: 27/07/2009
* @autor: Ing. Arnaldo Suarez 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
function cargarUsuariosControlNumero(registro)
{
	var myJSONObject ={
			'oper': 'usuarios',
			'id':registro.get('id'),
			'codsis':registro.get('codsis'), 
			'procede':registro.get('procede'),					
			'prefijo':registro.get('prefijo')					
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
				var objetoUsuario = eval('(' + datos + ')');
				if(objetoUsuario != '')
				{
					gridUsuariosControlNumero.store.loadData(objetoUsuario);
				}
				else
				{
					Ext.MessageBox.alert('Error', datos.raiz[0].mensaje+' Al cargar los usuarios.');
				}
			}
		}
	});
}


function irBuscar()
{
				   creargridControlNumero();
				   objetoControlNumero='definicion';
                   ventanaControlNumero = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Control de N&#250;meros',
		    		autoScroll:true,
                    width:800,
                    height:500,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridControlNumero],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	control = gridControlNumero.getSelectionModel().getSelected();
                    	switch(objetoControlNumero)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
                    			PasDatosGridDef(control);
                    			cargarUsuariosControlNumero(control);
                    			bloquearCamposPrimarios();
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(control);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(control);
	                    	break;
 
                    		
                    	}          
                    	gridControlNumero.destroy();
		      			ventanaControlNumero.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridControlNumero.destroy();
		      			ventanaControlNumero.destroy();
                     }
                    }]
                    
                   });
                  ventanaControlNumero.show();       
 

 }