/** 自訂函數 :
 * whenOpen():Boolean
 * afterOpen():void 
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
	
	private var in_maxApp:int = 8;
	private var in_loginWin:int = 0;	//是否開啟登入畫面, 1(是),-1(否),0(由傳入參數決定)
	//
	private var i_para:Object;			//傳入參數, 包含變數: L(login:Y/N), prog(自動開啟的程式代碼), 
	private var i_appContainer:Object;	//程式的 container: tabNavigator(in_maxApp > 1), Canvas(in_maxApp == 1) 
	private var is_app:String;			//current opened application id
	private var is_lang:String;			//initial language

	[Bindable]
	private var iR:Object;		
    	
	/*
	private function _preInit():void{
		//set default value if null
		//if (in_maxApp <= 0){
		//	in_maxApp = 8;
		//}
		
		
		//=== 必要的 ===			
		Fun2.wMain = this;
		Fun.aKeyByte0 = Fun.getKeyByte("");		//set first !!
		Fun.init();
		//======
		
		//setLang();			
	}
	*/

	/*
	 * 初始化主畫面.
	 * @param p_appContainer 程式的 Container 物件.
	 * @param pb_starAccount 登入畫面的使用者帳號是否顯示星號. 
	 */	
	//private function _initMain(p_appContainer:Object, pb_starAccount:Boolean, pb_lang:Boolean=true):void {
	private function _initMain(p_main:Object, p_appContainer:Object):void {
		//Fun.msg("I", "initMain");
				
		//=== 必要的初始化設定 ===
		Fun2.wMain = this;
		Fun.aKeyByte0 = Fun.getKeyByte();		//set first !!
		Fun.init();
		//======
		
		
		/* move to preInit();		
		//set default value if null
		if (in_maxApp <= 0){
			in_maxApp = 8;
		}

		//set Fun global first !!
		Fun2.wMain = this;
		Fun.init();
		*/
		
		//get input parameter
		//var t_para:Object = this.getPara();
		i_appContainer = p_appContainer;
		i_para = this.getPara();
		//set language first !!
		if (i_para != null && i_para.lang != null){
			Fun2.sLang = i_para.lang;		
		}
		is_lang = Fun2.sLang;
		
		
		//run whenOpen() 		
		try {
			if (Object(this).whenOpen() == false){
				Fun.exitSys();			
				return;
			}
		}catch (e:Error){
			//do nothing !
		}
		
		
		if (in_loginWin == -1){
			callAfterOpen();	//call afterOpen function
		}else if (in_loginWin == 1 || (in_loginWin == 0 && (i_para == null || i_para.login == null || i_para.login != "N"))){			
			//open login window.	
			in_loginWin = 1;
			var tw_login:Login = new Login();
			tw_login.bStarAccount = pb_starAccount;
			tw_login.fAfterLogin = initMain2;
			tw_login.bLang = pb_lang;
			//tw_login.gbTestMode = Fun.bTestMode;
			Fun.openPopup(tw_login, this);
		}else if (in_loginWin != -1){
			in_loginWin = -1;	
			initMain2();
		} 

	}
		
	//call server script for read menu records.
	private function initMain2(p_data:Object=null):void {
		//temp test
		//var ta_tyte:Array = Fun.aKeyByte;
		//var t_data:Object = (i_para == null || i_para.lang == null || i_para.lang == "") ? null : i_para.lang;		
		Fun.async("", Fun.sService, {fun:"Main"}, initMain3, null);
	}
	

	private function initMain3(p_data:Object):void {
		if (Fun.catchResult(p_data, false, true)){
	  		menu.enabled = false;
			return;
		}
		

		//set global variables
		//var tt:String = Fun.cKey;
		//Fun.aKeyByte = Fun.getKeyByte(p_data[Fun.csKey]);
		//Fun.aKeyByte0 = Fun.getKeyByte("");		//set first !!
		/*
		var ts_key:String = Fun.decode(p_data[Fun.csKey], false, false) as String;
		Fun.aKeyByte = Fun.getKeyByte(ts_key);
		Fun.sUserID = p_data[Fun.csUserID];
		Fun.sUserName = p_data[Fun.csUserName];
		Fun.sLoginTime = p_data[Fun.csLoginTime];
		Fun.sLoginID = p_data[Fun.csLoginID];
		Fun.sDeptID = (p_data.hasOwnProperty(Fun.csDeptID)) ? p_data[Fun.csDeptID] : "" ;
		Fun.sDeptIDs = (p_data.hasOwnProperty(Fun.csDeptIDs)) ? p_data[Fun.csDeptIDs] : "" ;
		Fun.dToday = Fun.strToDate(Fun.sLoginTime);
		Fun.sToday = Fun.dateToStr(Fun.dToday);
		*/
		setGlobal(p_data);
		
		//temp add
		//var tt:String = Fun.csDeptId;
		
		var ts_msg:String = "";
		var ts_comm:String = "";
		if (p_data[Fun.csAutoLogin] == "Y"){
			ts_msg = "Auto Login";
			ts_comm = ", ";
		}
		//if (p_data[Fun.csOpenAllMenu] == "Y"){
		//	ts_msg += ts_comm + "Open All Menu";
		//}
			
		if (ts_msg != ""){
			ts_msg += " !!";
			//labSysName.text = labSysName.text + " ("+ts_msg+")"
			Fun.msg("I", ts_msg);
		}
		
		/*
		try {
			Object(this).afterOpen();
		}catch (e:Error){
			//do nothing !
		}
		*/
		
		//recursive filter menu array
		//if (p_data[Fun.csOpenAllMenu] == "Y"){
		if (p_data[Fun.csAutoLogin] == "Y"){			
			menu.enabled = true;
		}else if (p_data[Fun.csMenu] == ""){
			menu.enabled = false;
		}else{
			var ta_menu:Array = JSON.decode(p_data[Fun.csMenu]);
		  	for (var i:int=ia_menu.length - 1;i>=0;i--){
			  	if (!filterMenu(ia_menu, i, ta_menu)){
			  	}
			}
			
			//re-set dataProvider property to active new property ! others way did not work !!
			//menu.validateNow();
			//menu.invalidateProperties();
			menu.dataProvider = ia_menu;
			menu.enabled = true;
		}

		//run afterOpen() 	
		callAfterOpen();
		
		//open program if need
		//temp test
		//Fun.msg("I", "open prog:"+i_para.prog);
		if (i_para != null && i_para.hasOwnProperty("prog")){
			openAppByName(i_para.prog);
		/*	
		}else if (in_maxApp == 1){
			//load FirstPage.swf
			Fun.oGlobal.item = {file:"FirstPage"}
			loadApp();
		*/	
		}
	}	
	

	//set global variables
	private function setGlobal(p_data:Object):void {
		//var ts_key:String = Fun.decode(p_data[Fun.csKey], false) as String;
		//Fun.aKeyByte = Fun.getKeyByte(ts_key);
		Fun.sUserId = p_data[Fun.csUserId];
		Fun.sUserName = p_data[Fun.csUserName];
		Fun.sLoginTime = p_data[Fun.csLoginTime];
		Fun.sLoginId = p_data[Fun.csLoginId];
		Fun.sAgentId = (p_data.hasOwnProperty(Fun.csAgentId)) ? String(p_data[Fun.csAgentId]) : "" ;
		Fun.sDeptId = (p_data.hasOwnProperty(Fun.csDeptId)) ? String(p_data[Fun.csDeptId]) : "" ;
		Fun.sDeptIds = (p_data.hasOwnProperty(Fun.csDeptIds)) ? String(p_data[Fun.csDeptIds]) : "" ;
		Fun.dToday = Fun.strToDate(Fun.sLoginTime);
		Fun.sToday = Fun.dateToStr(Fun.dToday);	
	}


	private var ib_afterOpen:Boolean = false;
	private function ??callAfterOpen():void {
		if (ib_afterOpen){
			return;
		}
		
		ib_afterOpen = true;
		try {
			Object(this).afterOpen();
		}catch (e:Error){
			//do nothing !
		}		
	}

	
	//get input parameters for main app
	private function getPara():Object{		
		var ts_url:String = ExternalInterface.call("window.location.href.toString");
		if (Fun.rightStr(ts_url, 1) == "#"){
			ts_url = ts_url.substr(0, ts_url.length - 1);
		}
		
		//temp test
		//Fun.msg("I", ts_url);
				
		var tn_pos:int = ts_url.indexOf("?");
		if (tn_pos < 0){
			return null;
		}
		
		ts_url = ts_url.substr(tn_pos+1) 
		var ta_para:Array = ts_url.split("&");
		var t_data:Object = {};
		for (var i:int=0; i<ta_para.length; i++) {			
			var ts_para:String = ta_para[i];
			tn_pos = ts_para.indexOf("=");
			if (tn_pos > 0){
				//Fun.msg("I", ts_para.substr(0,tn_pos)+":"+ts_para.substr(tn_pos+1));		
				t_data[ts_para.substr(0,tn_pos)] = ts_para.substr(tn_pos+1);
			}
		}
		
		return t_data;
	}
	
	
	/**
	 * filter out menu item, called by main app
	 * @param pa_item menu will be filter out.
	 * @pn_item menu item
	 * @p_ds 可執行的程式清單.
	 * @return true(filtered)/false(not filter)
	 */ 
	private function filterMenu(pa_item:Array, pn_item:int, p_ds:Array):Boolean{
		var t_item:Object = pa_item[pn_item];
		var i:int;
		var tb_find:Boolean = false;
		if (t_item.children == null){
			//如果是建構中, 則顯示 menu item
			if (t_item.doing){
				tb_find = true;				
			}else{
				//find menu item
				for (i=0;i<p_ds.length;i++){
					if (t_item.data == p_ds[i].data){
						tb_find = true;
						t_item.fun = p_ds[i].fun;
						break;
					}
				}
			}
		}else{
			//recursive
			var ta_item:Array = t_item.children;
			for (i=ta_item.length - 1;i>=0;i--){
				if (filterMenu(ta_item, i, p_ds)){
					tb_find = true;
				}					
			}			
		}			
			
		//remove item if not found 
		if (!tb_find){
			pa_item.splice(pn_item, 1);
		}
		
		return tb_find;			
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
	//public function openApp(p_item:Object, pa_cond:Array=null, pb_dynamicKey:Boolean=true):void {
	public function openApp(p_item:Object, pa_cond:Array=null):void {
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
	    //var ts_data:String = p_item.data;
	    //var ts_app:String = (p_item.app != null) ? p_item.app : ts_data;
		//Fun.app = ts_app; 
		is_app = p_item.data;
	    //var ts_app:String = is_app;
	    var ts_file:String = (p_item.file != null) ? p_item.file : is_app; 
		Fun.sApp = is_app; 
		Fun.oVar.item = {
			//app: ts_app,
			data: is_app,
			fun: p_item.fun,
			file: ts_file,
			inf: (p_item.inf != null) ? p_item.inf : ts_file,
			type: (p_item.type != null) ? p_item.type : "", 
			helpFile: (p_item.helpFile != null) ? p_item.helpFile : ts_file,	//new
			cond: pa_cond,
			title: p_item.label
		};
		/*		
		Fun.gObject.data = ts_data;			//for loadApp() only !!
		Fun.gObject.label = ts_label;		//for loadApp() only !!
		Fun.gObject.cond = pa_cond;
		Fun.gObject.file = (p_item.file != null) ? p_item.file : ts_data;			//for loadApp() only !!
		Fun.gObject.type = (p_item.type != null) ? p_item.type : "";	//傳參數到 application !! 
	    */
	    
			    
	    //先檢查 session 是否有效 if need
		/*
		if (pb_dynamicKey){
	    	Fun.sessionFun(loadApp);
		}else{
			loadApp();
		}
		*/
		loadApp();
		
	    /*
	    if (Fun.ajax("", Fun.CRUD, {fun:"Check"}, false) != "Y"){
	    	Fun.openRelogin(loadApp);
	    }else{
	    	loadApp();	    	
	    }
	    */
	}	  
		    
	
	//callback function of openApp(), 
	//如果 p_data=null, 則會使用 Fun.oGlobal.item    
	//private function openApp2(p_data:Object = null):void {
	private function loadApp(p_data:Object = null):void {
	    
	    //swf -> variables -> canvas -> page -> tab
		//load swf file to variables
		var t_item:Object = (p_data != null) ? p_data : Fun.oVar.item;
		//var ts_data:String = t_item.data;
		//var ts_file:String = t_item.file;
		//var ts_label:String = t_item.title;
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
	 * close tabpage 
	 */
	import flash.system.System;
	//import mx.containers.Canvas;
	//import flash.net.LocalConnection;

	public function closeApp():void{
		//var t_tab:TabNavigator = Fun2.mainApp.gTab;	  	
		//var t_color:Object = firstPage.getStyle("backgroundColor");
		var t_tab:TabNavigator = i_appContainer as TabNavigator;
		var tn_child:int = t_tab.selectedIndex;
		if (tn_child == 0){
			Fun.msg("I", iR.notFirst);
			return;
		}
		  
		//var t_swf:SWFLoader = new SWFLoader();
		//t_swf.unloadAndStop(true);
		
		//var t_app:Canvas = Canvas(t_tab.selectedChild).getChildAt(0);
		//var t_app:Canvas = Canvas(Object(t_tab.selectedChild).getChildAt(0));
		//Fun2.oGlobal.app.list_1.destroy();
		//Object(t_tab.selectedChild).removeAllChildren();
		//Object(t_tab.selectedChild).removeElementAt(0);
		//t_tab.removee
		t_tab.removeChildAt(tn_child);
		System.gc();	//可以減緩瀏覽器增加記憶體的速度
		
		/*
		try {
			new LocalConnection().connect('foo');
			new LocalConnection().connect('foo');
		} catch (e:*) {}		
		*/
		
		//var t_page:Object = t_tab.removeChildAt(tn_child);
		//t_swf = null;
		//t_page = null;
		//System.gc();
		
	}
