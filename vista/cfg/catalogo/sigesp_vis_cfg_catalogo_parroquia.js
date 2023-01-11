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

var dataStoreParroquia=""; //datastore del catalogo
var formBusquedaParroquia=""; // formulario de busqueda
var gridParroquia=""; // grid del catalogo
var ventanaParroquia=""; //ventana donde se ubica el catalogo


function crearDataStoreParroquia()
{

	registroParroquia = Ext.data.Record.create([
							{name: 'codpar'},
							{name: 'denpar'}
						]);
	
	var objetoParroquia={"raiz":[{"codpar":'',"denpar":''}]};
		
		dataStoreParroquia =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoParroquia),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroParroquia  
			),
			data: objetoParroquia
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo',
			"codpai": codpai,
			"codest": codest,
			"codmun": codmun
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_parroquia.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoParroquia = eval('(' + datos + ')');
			if(objetoParroquia.raiz == null)
			{
				obtenerMensaje('informacion',ventanaParroquia.destroy(),'Mensaje','No se encontro informacion, con los parametros de busqueda seleccionados')	
			}
			else
			{
			 if(objetoParroquia!='')
			 {
				dataStoreParroquia.loadData(objetoParroquia);
			 }
			}
		}	
	})
}

function actDataStoreParroquia(criterio,cadena)
{
	dataStoreParroquia.filter(criterio,cadena,true,false);
}

function pasarDatosParroquia(parroquia)
{
	Ext.getCmp('codpar').setValue(parroquia.get('codpar'));
	Ext.getCmp('denpar').setValue(parroquia.get('denpar'));
	Ext.getCmp('codpai').disable();
	Ext.getCmp('codest').disable();
	Ext.getCmp('codmun').disable();
	Ext.getCmp('codpar').disable();
	Actualizar=true;
}


function crearFormBusqueda()
{
		formBusquedaParroquia = new Ext.FormPanel({
        labelWidth: 80,
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'codcampo',
				id:'codcampo',
				width: 100,
				changeCheck: function(){
							var v = this.getValue();
							actDataStoreParroquia('codpar',v);
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
			                fieldLabel: 'Parroquia',
			                name: 'dencampo',
			                id:'dencampo',
							changeCheck: function()
							{
										var v = this.getValue();
										actDataStoreParroquia('denpar',v);
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


function crearGridCatalogoParroquia()
{
	crearFormBusqueda();
	crearDataStoreParroquia();
		 
	 gridParroquia = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaParroquia,
	 autoScroll:true,
     border:true,
     ds: dataStoreParroquia,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codpar'},
          {header: "Parroquias", width: 30, sortable: true,   dataIndex: 'denpar'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
} 

function irBuscar()
{
			   if((codpai==null)||(codpai=='')||(codest==null)||(codest=='')||(codmun==null)||(codest==''))
			   {
			    Ext.Msg.show({
			    title:'Mensaje',
			    msg: 'Debe seleccionar un pais, un estado y un municipio, verifique por favor!!',
			    buttons: Ext.Msg.OK,
			    animEl: 'elId',
			    icon: Ext.MessageBox.INFO
				});
			   }
			   else
			   {
				   crearGridCatalogoParroquia();
				   ObjetoFuente='definicion';
                   ventanaParroquia = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo de parroquias',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridParroquia],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridParroquia.getSelectionModel().getSelected();
                    	pasarDatosParroquia(registro); 
						Actualizar=1;
                    	gridParroquia.destroy();
		      			ventanaParroquia.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridParroquia.destroy();
		      			ventanaParroquia.destroy();
                     }
                    }]
                    
                   });
                  ventanaParroquia.show();
			 }
 }