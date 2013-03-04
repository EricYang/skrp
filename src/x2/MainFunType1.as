/** 注意事項 :
 *    用於後台管理者 : 標準下拉式功能表主畫面,  
 */
	
	import mx.containers.Canvas;
	import mx.containers.TabNavigator;
	import mx.controls.*;
	import mx.core.Container;
	import mx.events.*;
	import com.json.*;	
	import x2.*;	
	    	   	
	    	   		
	//get from server side.
	//private var ib_checkLogin:Boolean = false;	//check login or not
	//private var ib_openAllMenu:Boolean = false;	//false(open all menu)
	
/*
	private var in_maxApp:int = 8;
	//private var in_loginWin:int = 0;	//是否開啟登入畫面, 1(是),-1(否),0(由傳入參數決定)
	//
	private var i_para:Object;			//傳入參數, 包含變數: L(login:Y/N), prog(自動開啟的程式代碼), 
	private var i_appContainer:Object;	//程式的 container: tabNavigator(in_maxApp > 1), Canvas(in_maxApp == 1) 
	private var is_app:String;			//current opened application id
	private var is_lang:String;			//initial language

	[Bindable]
	private var iR:Object;		
*/


	/**
	 * open application, menuItem 和 menuGroup 都會觸發這個函數
	 * 如果是 menuGroup 則不做任何處理 (表示開啟 popup menu)!!
	 * @param {Object} p_item, has elements of: 
	 *   data: 
	 *   label: label or title.
	 *   fun: CRUDPEV, for CRUD only
	 *   app: 程式代號,  = data, if empty.
	 *   file: mxml file, = data, if empty.
	 *   inf: inf file, = file, if empty.
	 * @param {Array} pa_cond input parameters for retrieve records.
	 */
	//public static function openApp(p_event:Event, p_tab:Object, pn_max:int):void {
	public function openApp(p_item:Object, pa_cond:Array=null, pb_dynamicKey:Boolean=true):void {
	  	//var t_item:Object = (p_event as Object).item;		//for menu
	    //var t_item:Object = p_event.itemRenderer.data;	//for tree
		
	   	//check
		if (p_item.hasOwnProperty("children")){
			return;
		}
		
		if (p_item.doing){
			Fun.msg("I", "本作業建構中，將在下一版提供。");
			return;
		}else if (in_maxApp == 1){
			//如果已經是 current app, 則不處理
			if (p_item.data == is_app){
				return;
			}
		}else{
			//if already open this app, focus this
			var t_tab:TabNavigator = i_appContainer as TabNavigator;
		    var ts_label:String = p_item.label;
			var t_child:Object;
			for (var i:int=1;i<t_tab.numChildren;i++){
				t_child = t_tab.getChildAt(i);
				if (t_child.label == ts_label){
					t_tab.selectedIndex = i;
					return;
				}
			}
			
		    //check tabpage amount first
		    if (t_tab.numChildren >= in_maxApp){
		    	Fun.msg("I", Fun.strBuild(iR.maxForms, [in_maxApp]));
		    	return;	
		    }
		}

		//set access right to global variables.
		is_app = p_item.data;
	    var ts_file:String = (p_item.file != null) ? p_item.file : is_app; 
		Fun.sApp = is_app; 
		Fun.oGlobal.item = {
			data: is_app,
			fun: p_item.fun,
			file: ts_file,
			inf: (p_item.inf != null) ? p_item.inf : ts_file,
			type: (p_item.type != null) ? p_item.type : "", 
			helpFile: (p_item.helpFile != null) ? p_item.helpFile : ts_file,	//new
			cond: pa_cond,
			title: p_item.label
		};
	    
			    
	    //先檢查 session 是否有效 if need
		if (pb_dynamicKey){
	    	Fun.sessionFun(loadApp);
		}else{
			loadApp();
		}
	}	  
		    
	
	//callback function of openApp(), 
	//如果 p_data=null, 則會使用 Fun.oGlobal.item    
	//private function openApp2(p_data:Object = null):void {
	private function loadApp(p_data:Object = null):void {
	    
	    //swf -> variables -> canvas -> page -> tab
		//load swf file to variables
		var t_item:Object = (p_data != null) ? p_data : Fun.oGlobal.item;
	    var t_swf:SWFLoader = new SWFLoader();
	    t_swf.x = 0;
	    t_swf.y = 0;
		t_swf.percentHeight = 100; 
		t_swf.percentWidth = 100;	    
      	var ts_time:String = new Date().getTime().toString();	//for no cache !!	    
	    t_swf.load(t_item.file+".swf?tt="+ts_time);
	
	
		//add page to canvas
		if (in_maxApp == 1){
			var t_continer:Canvas = i_appContainer as Canvas;
			t_continer.addChild(t_swf);
			if (t_continer.numChildren > 1){
				t_continer.removeChildAt(0);
			}			
		}else{
			var t_page:Canvas = new Canvas();
			t_page.label = t_item.title;
			t_page.id = t_item.data;
		    t_page.x = 0;
		    t_page.y = 0;		    
			t_page.addChild(t_swf);
		
			var t_tab:TabNavigator = i_appContainer as TabNavigator;
			var t_child:Object = t_tab.addChild(t_page);
			t_tab.selectedChild = Container(t_child);
			
			//t_child.setFocus();
			//setClosePos();
		}		
	}
	
	
	/**
	 * open app by id, called by app
	 * @param {string} ps_app app id.
	 * @param {object} p_data input parameters.
	 */
	public function openAppByName(ps_app:String, pa_cond:Array=null):void {
		var t_item:Object = findApp(ps_app);
		if (t_item == null){
			Fun.msg("E", "openAppByName() error, Can not Find Function of ["+ps_app+"] !");
		}else{
			openApp(t_item, pa_cond);
		}
	}
	
		
	public function findApp(ps_app:String, pa_item:Array=null):Object{
		var ta_item:Array;
		if (pa_item != null){
			ta_item = pa_item;
		}else{
			ta_item = menu.dataProvider.source as Array;
		}
		
		for (var i:int=0;i<ta_item.length;i++){
			var t_item:Object = ta_item[i];
			if (t_item.children == null){
				if (t_item.data == ps_app){
					return t_item;	
				}
			}else{
				var t_item2:Object = findApp(ps_app, t_item.children);
				if (t_item2 != null){
					return t_item2;
				} 
			}				
		}
		
		//case of not find.
		return null;
	}
	
	
	/**
	 * close current selected tabpage 
	 */
	public function closeApp():void{
		//var t_tab:TabNavigator = Fun2.mainApp.gTab;	  	
		//var t_color:Object = firstPage.getStyle("backgroundColor");
		var t_tab:TabNavigator = i_appContainer as TabNavigator;
		var tn_child:int = t_tab.selectedIndex;
		if (tn_child == 0){
			Fun.msg("I", iR.notFirst);
			return;
		}
		  
		t_tab.removeChildAt(tn_child);
	}


	//set close button position	of tab navigator in main app  		  	
	private function setClosePos(p_tab:Object, p_close:Object):void{
		var tn_width:int = 20;
		for (var i:int=0;i<p_tab.numChildren;i++){
			var t_bar:Object = p_tab.getTabAt(i);
			tn_width += t_bar.width;
		}
		
		p_close.x = tn_width;
	}	

