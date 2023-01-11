/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* al catalogo de estructuras presupuestarias  
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

/************************************************************/
/************CATALOGO DE ESTRUCTURA NIVEL 1******************/
/************************************************************/
//variables usadas en la creacion del catalogo de estructuras nivel 1
var dsestructuranivel1="";
var objetoestnivel1="";
var formbusquedaestructuranivel1="";
var gridestructuranivel1="";//esta variable sera usada en la funcion que crea los grid

//funciones que crean y manejan los objetos que manejaran la data
function crearDatastoreEstructuraNivel1(){
	registroestnivel1 = Ext.data.Record.create([
							{name: 'codestpro1'},    
							{name: 'denestpro1'},
							{name: 'estcla'}
						]);
	
	objetoestnivel1={"raiz":[{"codestpro1":'',"denestpro1":'',"estcla":''}]};
		
	dsestructuranivel1 =  new Ext.data.Store({
								proxy: new Ext.data.MemoryProxy(objetoestnivel1),
								reader: new Ext.data.JsonReader({
												root: 'raiz',             
												id: "id"   
											},registroestnivel1),
								data: objetoestnivel1
	  						})	
}

function actualizaDatastoreEstructuraNivel1(criterio,cadena)
{
	dsestructuranivel1.filter(criterio,cadena);
}
//fin funciones que crean y manejan los objetos que manejaran la data

//funcion para crear el formulario de busqueda 
function crearFormBusquedaEstructuraNivel1(){
		formbusquedaestructuranivel1 = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
        //url:'save-form.php',
        frame:true,
        title: 'Busqueda',
        bodyStyle:'padding:5px 5px 0',
        width: 630,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'Codigo',
                name: 'C&#243;digo',
				id:'codestniv1',
				changeCheck: function(){
							var v = this.getValue();
							actualizaDatastoreEstructuraNivel1('codestpro1',v);
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
			                id:'denestniv1',
			                width:500,
							changeCheck: function()
							{
										var v = this.getValue();
										actualizaDatastoreEstructuraNivel1('denestpro1',v);
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
//FIN CATALOGO DE ESTRUCTURA NIVEL 1

/************************************************************/
/************CATALOGO DE ESTRUCTURA NIVEL 2******************/
/************************************************************/
//variables usadas en la creacion del catalogo de estructuras nivel 2
var dsestructuranivel2="";
var objetoestnivel2="";
var formbusquedaestructuranivel2="";
var gridestructuranivel2="";//esta variable sera usada en la funcion que crea los grid

//funciones que crean y manejan los objetos que manejaran la data
function crearDatastoreEstructuraNivel2(){
	registroestnivel2 = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'codestpro2'},    
							{name: 'denestpro2'}
						]);
	
	objetoestnivel2 = {"raiz":[{"codestpro1":'',"codestpro2":'',"denestpro2":''}]};
		
	dsestructuranivel2 =  new Ext.data.Store({
								proxy: new Ext.data.MemoryProxy(objetoestnivel2),
								reader: new Ext.data.JsonReader({
												root: 'raiz',             
												id: "id"   
											},registroestnivel2),
								data: objetoestnivel2
	  						})	
}

function actualizaDatastoreEstructuraNivel2(criterio,cadena)
{
	dsestructuranivel2.filter(criterio,cadena);
}
//fin funciones que crean y manejan los objetos que manejaran la data

//funcion para crear el formulario de busqueda 
function crearFormBusquedaEstructuraNivel2(){
		formbusquedaestructuranivel2 = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
        //url:'save-form.php',
        frame:true,
        title: 'Busqueda',
        bodyStyle:'padding:5px 5px 0',
        width: 630,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'Codigo',
                name: 'C&#243;digo',
				id:'codestniv2',
				changeCheck: function(){
							var v = this.getValue();
							actualizaDatastoreEstructuraNivel2('codestpro2',v);
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
			                id:'denestniv2',
			                width:500,
							changeCheck: function()
							{
										var v = this.getValue();
										actualizaDatastoreEstructuraNivel2('denestpro2',v);
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
//FIN CATALOGO DE ESTRUCTURA NIVEL 2

/************************************************************/
/************CATALOGO DE ESTRUCTURA NIVEL 3******************/
/************************************************************/
//variables usadas en la creacion del catalogo de estructuras nivel 3
var dsestructuranivel3="";
var objetoestnivel3="";
var formbusquedaestructuranivel3="";
var gridestructuranivel3="";//esta variable sera usada en la funcion que crea los grid

//funciones que crean y manejan los objetos que manejaran la data
function crearDatastoreEstructuraNivel3(){
	registroestnivel3 = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'codestpro2'},
							{name: 'codestpro3'},    
							{name: 'denestpro3'}
						]);
	
	objetoestnivel3 = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"denestpro3":''}]};
		
	dsestructuranivel3 =  new Ext.data.Store({
								proxy: new Ext.data.MemoryProxy(objetoestnivel3),
								reader: new Ext.data.JsonReader({
												root: 'raiz',             
												id: "id"   
											},registroestnivel3),
								data: objetoestnivel3
	  						})	
}

function actualizaDatastoreEstructuraNivel3(criterio,cadena)
{
	dsestructuranivel3.filter(criterio,cadena);
}
//fin funciones que crean y manejan los objetos que manejaran la data

//funcion para crear el formulario de busqueda 
function crearFormBusquedaEstructuraNivel3(){
		formbusquedaestructuranivel3 = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
        //url:'save-form.php',
        frame:true,
        title: 'Busqueda',
        bodyStyle:'padding:5px 5px 0',
        width: 630,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'Codigo',
                name: 'C&#243;digo',
				id:'codestniv3',
				changeCheck: function(){
							var v = this.getValue();
							actualizaDatastoreEstructuraNivel3('codestpro3',v);
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
			                id:'denestniv3',
			                width:500,
							changeCheck: function()
							{
										var v = this.getValue();
										actualizaDatastoreEstructuraNivel3('denestpro3',v);
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
//FIN CATALOGO DE ESTRUCTURA NIVEL 3

/************************************************************/
/************CATALOGO DE ESTRUCTURA NIVEL 4******************/
/************************************************************/
//variables usadas en la creacion del catalogo de estructuras nivel 4
var dsestructuranivel4="";
var objetoestnivel4="";
var formbusquedaestructuranivel4="";
var gridestructuranivel4="";//esta variable sera usada en la funcion que crea los grid

//funciones que crean y manejan los objetos que manejaran la data
function crearDatastoreEstructuraNivel4(){
	registroestnivel4 = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'codestpro2'},
							{name: 'codestpro3'},
							{name: 'codestpro4'},    
							{name: 'denestpro4'}
						]);
	
	objetoestnivel4 = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',"denestpro4":''}]};
		
	dsestructuranivel4 =  new Ext.data.Store({
								proxy: new Ext.data.MemoryProxy(objetoestnivel4),
								reader: new Ext.data.JsonReader({
												root: 'raiz',             
												id: "id"   
											},registroestnivel4),
								data: objetoestnivel4
	  						})	
}

function actualizaDatastoreEstructuraNivel4(criterio,cadena)
{
	dsestructuranivel4.filter(criterio,cadena);
}
//fin funciones que crean y manejan los objetos que manejaran la data

//funcion para crear el formulario de busqueda 
function crearFormBusquedaEstructuraNivel4(){
		formbusquedaestructuranivel4 = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
        //url:'save-form.php',
        frame:true,
        title: 'Busqueda',
        bodyStyle:'padding:5px 5px 0',
        width: 630,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'Codigo',
                name: 'C&#243;digo',
				id:'codestniv4',
				changeCheck: function(){
							var v = this.getValue();
							actualizaDatastoreEstructuraNivel4('codestpro4',v);
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
			                id:'denestniv4',
			                width:500,
							changeCheck: function()
							{
										var v = this.getValue();
										actualizaDatastoreEstructuraNivel4('denestpro4',v);
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
//FIN CATALOGO DE ESTRUCTURA NIVEL 4

/************************************************************/
/************CATALOGO DE ESTRUCTURA NIVEL 5******************/
/************************************************************/
//variables usadas en la creacion del catalogo de estructuras nivel 5
var dsestructuranivel5="";
var objetoestnivel5="";
var formbusquedaestructuranivel5="";
var gridestructuranivel5="";//esta variable sera usada en la funcion que crea los grid

//funciones que crean y manejan los objetos que manejaran la data
function crearDatastoreEstructuraNivel5(){
	registroestnivel5 = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'codestpro2'},
							{name: 'codestpro3'},
							{name: 'codestpro4'},
							{name: 'codestpro5'},    
							{name: 'denestpro5'}
						]);
	
	objetoestnivel5 = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',"codestpro5":'',"denestpro5":''}]};
		
	dsestructuranivel5 =  new Ext.data.Store({
								proxy: new Ext.data.MemoryProxy(objetoestnivel5),
								reader: new Ext.data.JsonReader({
												root: 'raiz',             
												id: "id"   
											},registroestnivel5),
								data: objetoestnivel5
	  						})	
}

function actualizaDatastoreEstructuraNivel5(criterio,cadena)
{
	dsestructuranivel5.filter(criterio,cadena);
}
//fin funciones que crean y manejan los objetos que manejaran la data

//funcion para crear el formulario de busqueda 
function crearFormBusquedaEstructuraNivel5(){
		formbusquedaestructuranivel5 = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
        //url:'save-form.php',
        frame:true,
        title: 'Busqueda',
        bodyStyle:'padding:5px 5px 0',
        width: 630,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'Codigo',
                name: 'C&#243;digo',
				id:'codestniv5',
				changeCheck: function(){
							var v = this.getValue();
							actualizaDatastoreEstructuraNivel5('codestpro5',v);
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
			                id:'denestniv5',
			                width:500,
							changeCheck: function()
							{
										var v = this.getValue();
										actualizaDatastoreEstructuraNivel5('denestpro5',v);
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
//FIN CATALOGO DE ESTRUCTURA NIVEL 5

/************************************************************/
/************CATALOGO DE ESTRUCTURA NIVEL N******************/
/************************************************************/
//variables usadas en la creacion del catalogo de estructuras nivel 5
var dsestructuranivelN="";
var objetoestnivelN="";
var formbusquedaestructuranivelN="";
var gridestructuranivelN="";//esta variable sera usada en la funcion que crea los grid

//funciones que crean y manejan los objetos que manejaran la data
function crearDatastoreEstructuraNivelN(){

	switch(cantnivel) {
		case 1:
			registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},    
							{name: 'denestpro1'},
							{name: 'estcla'}
						]);
	
			objetoestnivelN={"raiz":[{"codestpro1":'',"denestpro1":'',"estcla":''}]};
			break;
		case 2:
			registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'denestpro1'},
							{name: 'codestpro2'},    
							{name: 'denestpro2'},
							{name: 'estcla'}
						]);
	
			objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"denestpro2":''}]};
			break;
		case 3:
			registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'denestpro1'},
							{name: 'codestpro2'},
							{name: 'denestpro2'},
							{name: 'codestpro3'},    
							{name: 'denestpro3'},
							{name: 'estcla'}
						]);
	
			objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"denestpro3":''}]};
			break;
		case 4:
			registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'denestpro1'},
							{name: 'codestpro2'},
							{name: 'denestpro2'},
							{name: 'codestpro3'},
							{name: 'denestpro3'},
							{name: 'codestpro4'},    
							{name: 'denestpro4'},
							{name: 'estcla'}
						]);
	
			objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',"denestpro4":''}]};
			break;
		case 5:
	    	registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'denestpro1'},
							{name: 'codestpro2'},
							{name: 'denestpro2'},
							{name: 'codestpro3'},
							{name: 'denestpro3'},
							{name: 'codestpro4'},
							{name: 'denestpro4'},
							{name: 'codestpro5'},    
							{name: 'denestpro5'},
							{name: 'estcla'}
						]);
	
			objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',"codestpro5":'',"denestpro5":''}]};
			break;
	}
	
	dsestructuranivelN =  new Ext.data.Store({
								proxy: new Ext.data.MemoryProxy(objetoestnivelN),
								reader: new Ext.data.JsonReader({
												root: 'raiz',             
												id: "id"   
											},registroestnivelN),
								data: objetoestnivelN
	  						})
}

function actualizaDatastoreEstructuraNivelN(criterio,cadena)
{
	dsestructuranivelN.filter(criterio,cadena);
}
//fin funciones que crean y manejan los objetos que manejaran la data

//funcion para crear el formulario de busqueda 
function crearFormBusquedaEstructuraNivelN(){
		formbusquedaestructuranivelN = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
        //url:'save-form.php',
        frame:true,
        title: 'Busqueda',
        bodyStyle:'padding:5px 5px 0',
        width: 630,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'Codigo',
				id:'codestnivN',
				changeCheck: function(){
							var v = this.getValue();
							actualizaDatastoreEstructuraNivelN('codestpro'+cantnivel,v);
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
			                id:'denestnivN',
			                width:500,
							changeCheck: function()
							{
										var v = this.getValue();
										actualizaDatastoreEstructuraNivelN('denestpro'+cantnivel,v);
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
//FIN CATALOGO DE ESTRUCTURA NIVEL N

//Aqui funcion para el request al controlador y capturar los datos del mismo
function enviarOperacion(operacion){
	
	cadenaJson="{'operacion':'" + operacion + "','cantnivel':'" + cantnivel + "',";
	for (var i = 0;i<campos.length;i++){
		if(i==campos.length-1){
			cadenaJson= cadenaJson + "'codest"+i+"':'" + campos[i].getValue() + "'}";
		}else{
			cadenaJson= cadenaJson + "'codest"+i+"':'" + campos[i].getValue() + "',";
		}
	}
	
	parametros = 'ObjSon='+cadenaJson; 
	Ext.Ajax.request({
		url : '../../controlador/spg/sigesp_ctr_spg_catestpresupuestaria.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request)	{ 
			datos = resultado.responseText;
			//segun el nivel cargamos los disferente resultados en los datastore correspondientes
			switch(operacion) {
				case 'nivel1':
					objetoestnivel1 = eval('(' + datos + ')');//objeto nivel 1
					if(objetoestnivel1!=''){
						dsestructuranivel1.loadData(objetoestnivel1);//ds nivel 1
					}
				break;
				
				case 'nivel2':
					objetoestnivel2 = eval('(' + datos + ')');//objeto nivel 2
					if(objetoestnivel2!=''){
						dsestructuranivel2.loadData(objetoestnivel2);//ds nivel 2
					}
				break;
				
				case 'nivel3':
					objetoestnivel3 = eval('(' + datos + ')');//objeto nivel 3
					if(objetoestnivel3!=''){
						dsestructuranivel3.loadData(objetoestnivel3);//ds nivel 3
					}
				break;
				
				case 'nivel4':
					objetoestnivel4 = eval('(' + datos + ')');//objeto nivel 4
					if(objetoestnivel4!=''){
						dsestructuranivel4.loadData(objetoestnivel4);//ds nivel 4
					}
				break;
				
				case 'nivel5':
					objetoestnivel5 = eval('(' + datos + ')');//objeto nivel 5
					if(objetoestnivel5!=''){
						dsestructuranivel5.loadData(objetoestnivel5);//ds nivel 5
					}
				break;
				
				case 'nivelN':
					objetoestnivelN = eval('(' + datos + ')');//objeto nivel N
					if(objetoestnivelN!=''){
						dsestructuranivelN.loadData(objetoestnivelN);//ds nivel N
					}
				break;
			}
		}	
	})
}
//fin funcion enviar operacion.....

function mostrarEstatus(est){
	
	if (est=='P'){
			return 'Proyecto';
	}else if (est=='A'){
			return 'Acci&#243;n Centralizada';	
	}else if (est=='-'){
			return 'POR DEFECTO';	
	}
}

//Aqui creaciones de las grid...
function crear_grid_catalogoestrutura(operacion){
	//aqui creamos los grid....
	switch(operacion) {
		case 'nivel1':
			crearDatastoreEstructuraNivel1()
			enviarOperacion(operacion)	
	    	crearFormBusquedaEstructuraNivel1();//invocando el crear form de busqueda
			gridestructuranivel1 = new Ext.grid.GridPanel({
	 								width:770,
	 								height:400,
	 								tbar: formbusquedaestructuranivel1,
	 								autoScroll:true,
     								border:true,
     								ds: dsestructuranivel1,
     								cm: new Ext.grid.ColumnModel([
          								{header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codestpro1'},
          								{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro1'},
										{header: "Tipo", width: 50, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatus}
       								]),
       								stripeRows: true,
      								viewConfig: {forceFit:true}
								});
			break;
			
		case 'nivel2':
			crearDatastoreEstructuraNivel2()
			enviarOperacion(operacion)	
	    	crearFormBusquedaEstructuraNivel2();//invocando el crear form de busqueda
			gridestructuranivel2 = new Ext.grid.GridPanel({
	 								width:770,
	 								height:400,
	 								tbar: formbusquedaestructuranivel2,
	 								autoScroll:true,
     								border:true,
     								ds: dsestructuranivel2,
     								cm: new Ext.grid.ColumnModel([
          								{header: "C&#243;digo Nivel 1", width: 30, sortable: true,   dataIndex: 'codestpro1'},
										{header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codestpro2'},
          								{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro2'}
									]),
       								stripeRows: true,
      								viewConfig: {forceFit:true}
								});
			break;
		
		case 'nivel3':
			crearDatastoreEstructuraNivel3()
			enviarOperacion(operacion)	
	    	crearFormBusquedaEstructuraNivel3();//invocando el crear form de busqueda
			gridestructuranivel3 = new Ext.grid.GridPanel({
	 								width:770,
	 								height:400,
	 								tbar: formbusquedaestructuranivel3,
	 								autoScroll:true,
     								border:true,
     								ds: dsestructuranivel3,
     								cm: new Ext.grid.ColumnModel([
          								{header: "C&#243;digo Nivel 1", width: 30, sortable: true,   dataIndex: 'codestpro1'},
										{header: "C&#243;digo Nivel 2", width: 30, sortable: true,   dataIndex: 'codestpro2'},
										{header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codestpro3'},
          								{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro3'}
									]),
       								stripeRows: true,
      								viewConfig: {forceFit:true}
								});
			break;
		case 'nivel4':
			crearDatastoreEstructuraNivel4()
			enviarOperacion(operacion)	
	    	crearFormBusquedaEstructuraNivel4();//invocando el crear form de busqueda
			gridestructuranivel4 = new Ext.grid.GridPanel({
	 								width:770,
	 								height:400,
	 								tbar: formbusquedaestructuranivel4,
	 								autoScroll:true,
     								border:true,
     								ds: dsestructuranivel4,
     								cm: new Ext.grid.ColumnModel([
          								{header: "C&#243;digo Nivel 1", width: 30, sortable: true,   dataIndex: 'codestpro1'},
										{header: "C&#243;digo Nivel 2", width: 30, sortable: true,   dataIndex: 'codestpro2'},
										{header: "C&#243;digo Nivel 3", width: 30, sortable: true,   dataIndex: 'codestpro3'},
										{header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codestpro4'},
          								{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro4'}
									]),
       								stripeRows: true,
      								viewConfig: {forceFit:true}
								});
			break;
		case 'nivel5':
			crearDatastoreEstructuraNivel5()
			enviarOperacion(operacion)	
	    	crearFormBusquedaEstructuraNivel5();//invocando el crear form de busqueda
			gridestructuranivel5 = new Ext.grid.GridPanel({
	 								width:770,
	 								height:400,
	 								tbar: formbusquedaestructuranivel5,
	 								autoScroll:true,
     								border:true,
     								ds: dsestructuranivel5,
     								cm: new Ext.grid.ColumnModel([
          								{header: "C&#243;digo Nivel 1", width: 30, sortable: true,   dataIndex: 'codestpro1'},
										{header: "C&#243;digo Nivel 2", width: 30, sortable: true,   dataIndex: 'codestpro2'},
										{header: "C&#243;digo Nivel 3", width: 30, sortable: true,   dataIndex: 'codestpro3'},
										{header: "C&#243;digo Nivel 4", width: 30, sortable: true,   dataIndex: 'codestpro4'},
										{header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codestpro5'},
          								{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denestpro5'}
									]),
       								stripeRows: true,
      								viewConfig: {forceFit:true}
								});
			break;
		case 'nivelN':
			crearDatastoreEstructuraNivelN()
			enviarOperacion(operacion)	
	    	crearFormBusquedaEstructuraNivelN();//invocando el crear form de busqueda
			modelogridN="[";
			for(var x=1;x<=cantnivel;x++){
				if(x==cantnivel){
					modelogridN = modelogridN + "{header: 'C&#243;digo Nivel "+x+"', width: 45, sortable: true,   dataIndex: 'codestpro"+x+"'},"+
												"{header: 'Denominaci&#243;n', width: 45, sortable: true,   dataIndex: 'denestpro"+x+"'},"+
												"{header: 'Tipo', width: 30, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatus}";
				}else{
					modelogridN = modelogridN + "{header: 'C&#243;digo Nivel "+x+"', width: 30, sortable: true,   dataIndex: 'codestpro"+x+"'},";
				}	
			}
			modelogridN = modelogridN + "]";
			objetomodelo = Ext.util.JSON.decode(modelogridN);
			gridestructuranivelN = new Ext.grid.GridPanel({
	 								width:770,
	 								height:400,
	 								tbar: formbusquedaestructuranivelN,
	 								autoScroll:true,
     								border:true,
     								ds: dsestructuranivelN,
     								cm: new Ext.grid.ColumnModel(objetomodelo),
       								stripeRows: true,
      								viewConfig: {forceFit:true}
								});
			 //gridestructuranivelN.getColumnModel().setHidden(5, true);
			break; 
	}
} 
//fin crear grid..........

//funciones para llamar a los catalogos....
function catalogoEstructuraNivel1(){
	crear_grid_catalogoestrutura('nivel1');				   
    ventana = new Ext.Window({
    	title: 'Cat&#225;logo de Estructuras Presupuestarias',
		autoScroll:true,
        width:800,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridestructuranivel1],
        buttons: [{
					text:'Aceptar',  
			        handler: function()
			        	{
			        		estnivel1 = gridestructuranivel1.getSelectionModel().getSelected();
							campos[0].setValue(estnivel1.get('codestpro1'));
							etiqueta[0].setText(estnivel1.get('denestpro1'));
							formPlanctapre.getComponent('estcla').setValue(estnivel1.get('estcla'));
							gridestructuranivel1.destroy();
							ventana.destroy();
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			gridestructuranivel1.destroy();
							ventana.destroy();
			       		}
                  }]
      });
      ventana.show();
}

function catalogoEstructuraNivel2(){
	crear_grid_catalogoestrutura('nivel2');				   
    ventana = new Ext.Window({
    	title: 'Cat&#225;logo de Estructuras Presupuestarias',
		autoScroll:true,
        width:800,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridestructuranivel2],
        buttons: [{
					text:'Aceptar',  
			        handler: function()
			        	{
			        		estnivel2 = gridestructuranivel2.getSelectionModel().getSelected();
							campos[1].setValue(estnivel2.get('codestpro2'));
							etiqueta[1].setText(estnivel2.get('denestpro2'))
							gridestructuranivel2.destroy();
							ventana.destroy();
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			gridestructuranivel2.destroy();
							ventana.destroy();
			       		}
                  }]
      });
      ventana.show();
}

function catalogoEstructuraNivel3(){
	crear_grid_catalogoestrutura('nivel3');				   
    ventana = new Ext.Window({
    	title: 'Cat&#225;logo de Estructuras Presupuestarias',
		autoScroll:true,
        width:800,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridestructuranivel3],
        buttons: [{
					text:'Aceptar',  
			        handler: function()
			        	{
			        		estnivel3 = gridestructuranivel3.getSelectionModel().getSelected();
							campos[2].setValue(estnivel3.get('codestpro3'));
							etiqueta[2].setText(estnivel3.get('denestpro3'))
							gridestructuranivel3.destroy();
							ventana.destroy();
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			gridestructuranivel3.destroy();
							ventana.destroy();
			       		}
                  }]
      });
      ventana.show();
}

function catalogoEstructuraNivel4(){
	crear_grid_catalogoestrutura('nivel4');				   
    ventana = new Ext.Window({
    	title: 'Cat&#225;logo de Estructuras Presupuestarias',
		autoScroll:true,
        width:800,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridestructuranivel4],
        buttons: [{
					text:'Aceptar',  
			        handler: function()
			        	{
			        		estnivel4 = gridestructuranivel4.getSelectionModel().getSelected();
							campos[3].setValue(estnivel4.get('codestpro4'));
							etiqueta[3].setText(estnivel4.get('denestpro4'))
							gridestructuranivel4.destroy();
							ventana.destroy();
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			gridestructuranivel4.destroy();
							ventana.destroy();
			       		}
                  }]
      });
      ventana.show();
}

function catalogoEstructuraNivel5(){
	crear_grid_catalogoestrutura('nivel5');				   
    ventana = new Ext.Window({
    	title: 'Cat&#225;logo de Estructuras Presupuestarias',
		autoScroll:true,
        width:800,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridestructuranivel5],
        buttons: [{
					text:'Aceptar',  
			        handler: function()
			        	{
			        		estnivel5 = gridestructuranivel5.getSelectionModel().getSelected();
							campos[4].setValue(estnivel5.get('codestpro5'));
							etiqueta[4].setText(estnivel5.get('denestpro5'))
							gridestructuranivel5.destroy();
							ventana.destroy();
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			gridestructuranivel5.destroy();
							ventana.destroy();
			       		}
                  }]
      });
      ventana.show();
}

function catalogoEstructuraNivelN(){
	if(campos[0].getValue()!=""){
		var funcion = new Array();
		funcion[0]=catalogoEstructuraNivel1;
		funcion[1]=catalogoEstructuraNivel2;
		funcion[2]=catalogoEstructuraNivel3;
		funcion[3]=catalogoEstructuraNivel4;
		funcion[4]=catalogoEstructuraNivel5;
		funcion[campos.length - 1]();
	}else{
		crear_grid_catalogoestrutura('nivelN');				   
    	ventana = new Ext.Window({
    	title: 'Cat&#225;logo de Estructuras Presupuestarias',
		autoScroll:true,
        width:800,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridestructuranivelN],
        buttons: [{
					text:'Aceptar',  
			        handler: function()
			        	{
			        		estnivelN = gridestructuranivelN.getSelectionModel().getSelected();
							for (var i = campos.length - 1 ; i >= 0; i--){
								campos[i].setValue(estnivelN.get('codestpro'+(i+1)));
								etiqueta[i].setText(estnivelN.get('denestpro'+(i+1)))
							};
							formPlanctapre.getComponent('estcla').setValue(estnivelN.get('estcla'));
							gridestructuranivelN.destroy();
							ventana.destroy();
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			gridestructuranivelN.destroy();
							ventana.destroy();
			       		}
                  }]
      	});
      	ventana.show();
	}
}