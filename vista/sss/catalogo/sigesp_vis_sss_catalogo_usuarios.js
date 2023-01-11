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

var dataStoreUsuarios=null;
var dataStoreUsuarios=null;
var gridUsuarios=null;
var formBusquedaUsuarios=null
var registroUsuarios= null;
var ventanaCatalogoUsuarios = null;
var arregloUsuario = null;

/******************************************************************************
* @Función para crear el Data Store que contendrá la data de un usuario 
* @fecha de creación: 28/07/2009
* @autor: Arnaldo Suárez
******************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
********************************************************************************/
function crearDataStoreUsuario(operacion)
{
	registroUsuario = Ext.data.Record.create([
						{name: 'codusu'},
						{name: 'cedusu'},
						{name: 'nomusu'},
						{name: 'apeusu'},
						{name: 'telusu'},
						{name: 'email'},
						{name: 'ultingusu'},
						{name: 'estatus'},
						{name: 'admusu'},
						{name: 'nota'},
						{name: 'fecnacusu'},
						{name: 'estblocon'}
					]);
	
	var objetoUsuario={"raiz":[{"codusu":'',"cedusu":'',"cedusu":'',"nomusu":'',"apeusu":'',"telusu":'',"email":'',"ultingusu":'',"estatus":'',"admusu":'',"nota":'',"fecnacusu":'',"estblocon":''}]};		
	dataStoreUsuarios =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoUsuario),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "codusu"   
			}
			,
			registroUsuario  
			),
			data: objetoUsuario
	  	})	
		

		var myJSONObject ={
		"oper": operacion,
		"sistema":'SSS',
		"vista":'sigesp_vis_sss_usuario.html'
		}
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'objdata='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/sss/sigesp_ctr_sss_usuario.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var respuesta = eval('(' + datos + ')');
			if(respuesta!=null)
			{
				dataStoreUsuarios.loadData(respuesta);
			}
		}	
	})
}

/******************************************************************************
* @Función para filtrar los datos en el grid
* @fecha de creación: 28/07/2009
* @autor: Arnaldo Suárez
******************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
********************************************************************************/
function filtrardataStoreUsuarios(criterio,cadena)
{
	dataStoreUsuarios.filter(criterio,cadena);
}

/******************************************************************************
* @Función para crear el formulario de Busqueda
* @fecha de creación: 28/07/2009
* @autor: Arnaldo Suárez
******************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
********************************************************************************/
function crearformBusquedaUsuarios()
{
		formBusquedaUsuarios = new Ext.FormPanel({
        labelWidth: 80,
        frame:true,
        title: 'Busqueda',
        bodyStyle:'padding:5px 5px 0',
        width: 650,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'codigo',
				id:'codigo',
				changeCheck: function(){
							var v = this.getValue();
							filtrardataStoreUsuarios('codusu',v);
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
			                fieldLabel: 'Nombre',
			                name: 'nombre',
			                id:'nombre',
			                width:500,
							changeCheck: function()
							{
										var v = this.getValue();
										filtrardataStoreUsuarios('nomusu',v);
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
/******************************************************************************
* @Función para crear el Grid que muestra la data de los usuarios 
* @fecha de creación: 28/07/2009
* @autor: Arnaldo Suárez
******************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
********************************************************************************/

function CreargridUsuarios(operacion)
{
		crearformBusquedaUsuarios();
		crearDataStoreUsuario(operacion);
     var modoSeleccionUsuario = new Ext.grid.CheckboxSelectionModel({});	 
	 gridUsuarios = new Ext.grid.GridPanel({
	 width:770,
	 height:350,
	 tbar: formBusquedaUsuarios,
	 autoScroll:true,
     border:true,
     ds: dataStoreUsuarios,
     cm: new Ext.grid.ColumnModel([
          modoSeleccionUsuario,
          {header: 'C&#243;digo', width: 50, sortable: true,   dataIndex: 'codusu'},
          {header: 'Nombre', width: 70, sortable: true, dataIndex: 'nomusu'},
	      {header: 'Apellido', width: 70, sortable: true, dataIndex: 'apeusu'}
       ]),
    stripeRows: true,
    viewConfig:{
      			forceFit:true
      		   },
    sm: new Ext.grid.CheckboxSelectionModel({})
      });            
}

/******************************************************************************
* @Función para insertar el registro seleccionado de la grid del catalgo 
* a la grid del formulario.
* @fecha de creación: 28/07/2009
* @autor: Arnaldo Suárez
******************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
********************************************************************************/
	function pasarDatosGridUsuario(grid,registro)
	{
		usuario = new registroGridUsuario
		({
		'codusu':'',
		'nomusu':'',
		'apeusu':''
		});
		grid.store.insert(0,usuario);
		usuario.set('codusu',registro.get('codusu'));
		usuario.set('nomusu',registro.get('nomusu'));
		usuario.set('apeusu',registro.get('apeusu'));
	}

/******************************************************************************
* @Función para mostrar el catalogo de usuarios
* @parametros: operacion: indica el evento a ejecutar en el controlador de usuario
*              objeto:    indica el objeto fuente de la peticion de mostrar el catalogo
* @fecha de creación: 28/07/2009
* @autor: Arnaldo Suárez
******************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
********************************************************************************/
function mostrarCatalogoUsuario(operacion,grid)
{
	
		CreargridUsuarios(operacion);
                   ventanaCatalogoUsuarios = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Usuarios',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridUsuarios],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function(){
            							arregloUsuario = gridUsuarios.getSelectionModel().getSelections();
			                    	    for (i=0; i<arregloUsuario.length; i++)
			                    	    {
			                    	    	
			                    	    	usuarioControl = arregloUsuario[i];
			                    	    	if(validarExistenciaRegistroGrid(usuarioControl,grid,'codusu','codusu',false))
			                    	    	{
			                    	    		pasarDatosGridUsuario(grid,arregloUsuario[i]);	
			                    	    	}
			                    	
			                    	    }
					                    gridUsuarios.destroy();
					                    ventanaCatalogoUsuarios.destroy(); 
                    				   }
                    
                    },
                   {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridUsuarios.destroy();
		      			ventanaCatalogoUsuarios.destroy();
                     }
                   }]
                    
                   });
                  ventanaCatalogoUsuarios.show();       
 }
