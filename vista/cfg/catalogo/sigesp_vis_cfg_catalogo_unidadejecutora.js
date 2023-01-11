/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* al catalgo de unidades ejecutoras  
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var dataStoreUnidadEjecutora="";
var formBusquedaUnidadEjecutora="";
var gridUnidadEjecutora="";
var ventanaUnidadEjecutora="";

function creardataStoreUnidadEjecutora()
{

		registroUnidadEjecutora = Ext.data.Record.create([			  
								{name:'coduniadm'},
								{name:'denuniadm'},
								{name:'estemireq'},
								{name:'coduniadmsig'},
								{name:'denuac'},
								{name:'resuniadm'}
						]);							
	
		var objetoUnidadEjecutora={"raiz":[{"coduniadm":"","denuniadm":"","estemireq":"","coduniadmsig":"","denuac":"","resuniadm":""}]};
		
		dataStoreUnidadEjecutora =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoUnidadEjecutora),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "coduniadm"   
			}
			,
		    registroUnidadEjecutora  
			),
			data: objetoUnidadEjecutora
	  	})	
		
		var myJSONObject ={
			"operacion": 'catalogo'
		}
		
		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_spg_unidadejecutora.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetodata = eval('(' + datos + ')');
			if(objetodata!='')
			{
				dataStoreUnidadEjecutora.loadData(objetodata);
			}
		}	
	})
}

function actdataStoreUnidadEjecutora(criterio,cadena)
{
	dataStoreUnidadEjecutora.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formBusquedaUnidadEjecutora = new Ext.FormPanel({
        labelWidth: 75,
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 400,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                id:'codunieje',
				changeCheck: function(){
								var v = this.getValue();
								actdataStoreUnidadEjecutora('coduniadm',v);
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
				id:'denunieje',
				changeCheck: function(){
							var v = this.getValue();
							actdataStoreUnidadEjecutora('denuniadm',v);
							if(String(v) !== String(this.startValue)){
								this.fireEvent('change', this, v, this.startValue);
							} 
				},							 
				initEvents : function(){
						AgregarKeyPress(this);
					}
				}]
		});				  

}

function mostrarEstatusUnidad(est)
{
	if (est=="1")
	{
		return 'Si';
	}
	else
	{
		return 'No';	
	}
}

function creargridUnidadEjecutora()
{
	crearFormularioBusqueda();
	creardataStoreUnidadEjecutora();
	gridUnidadEjecutora = new Ext.grid.GridPanel({
			width:650,
			height:350,
			tbar: formBusquedaUnidadEjecutora,
			autoScroll:true,
			border:true,
			ds: dataStoreUnidadEjecutora,
			cm: new Ext.grid.ColumnModel([
         			{header: "C&#243;digo", width: 15, sortable: true,   dataIndex: 'coduniadm'},
          			{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denuniadm'},
          			{header: "Emite Req.", width: 10, sortable: true, dataIndex: 'estemireq',renderer:mostrarEstatusUnidad}
			]),
       		stripeRows: true,
      		viewConfig: {forceFit:true}
	});            
}

function cargarDetalleUniEje(registro)
{
	var myJSONObject ={
			'operacion': 'detalles',
			'coduniadm':registro.get('coduniadm'),
			'cantnivel':cantnivel					
	};
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_spg_unidadejecutora.php',
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
					comliscatestructura.dataGridEditable.store.loadData(objetoDetalle);
				}
				else
				{
					Ext.MessageBox.alert('Error', datos.raiz[0].mensaje+' Al cargar los detalles.');
				}
			}
		}
	});
}

function mostrarCatalogoUnidadEjecutora()
{
	creargridUnidadEjecutora();
	ventanaUnidadEjecutora = new Ext.Window({
                    title: 'Cat&#225;logo de Unidad Ejecutora',
		    		autoScroll:true,
                    width:670,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridUnidadEjecutora],
                    buttons: [{
                    			text:'Aceptar',  
                    			handler: function()
								{ 
                    				var registro = gridUnidadEjecutora.getSelectionModel().getSelected();
                    				limpiarCampos();
                    				PasDatosGridDef(registro);
									cargarDetalleUniEje(registro)
	                    			gridUnidadEjecutora.destroy();
		      						ventanaUnidadEjecutora.destroy();                      
                    			}
                    		},{
                     			text: 'Salir',
                     			handler: function()
								{
 									gridUnidadEjecutora.destroy();
		      						ventanaUnidadEjecutora.destroy();
                     			}
                    		}]
    });
    ventanaUnidadEjecutora.show();       
}