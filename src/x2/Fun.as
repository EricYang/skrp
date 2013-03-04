package x2{
	/**
	 * updated: 2008-12-5
	 */
	  
	// public function

	//=======================
	//need for Flex 4.5, 4.6 does not need !!
	//import com.json.*;
	//=======================
	
	import com.Rijndael;
	
	import flash.display.DisplayObject;
	import flash.display.Loader;
	import flash.events.*;
	import flash.external.ExternalInterface;
	import flash.net.*;
	
	import mx.collections.ArrayCollection;
	import mx.collections.ArrayList;
	import mx.containers.*;
	import mx.controls.Alert;
	import mx.core.Container;
	import mx.core.IFlexDisplayObject;
	import mx.events.*;
	import mx.formatters.DateFormatter;
	import mx.managers.*;
	import mx.resources.ResourceManager;
	import mx.rpc.Fault;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.http.mxml.HTTPService;
	
	import spark.components.*;
	import spark.components.gridClasses.GridColumn;
	
	/**
	 * 公用函數集合, 變數和函數的字首規則如下:<br/>
	 * a: array.<br/>
	 * b: boolean.<br/>
	 * c: constant.<br/>
	 * d: date.<br/>
	 * f: function.<br/>
	 * g: dataGrid.<br/>
	 * n: numeric.<br/>
	 * o: object.<br/>
	 * s: string.<br/>
	 * x: control.<br/>
	 * w: titleWindow.<br/>
	 */ 
   	public class Fun{		
	
		//=================
		//***** 常數 *****
		//=================
		
		/**
		 * 黑色
		 */ 
		public static const cnBlack:int = 0X000000;

		/**
		 * 黃色
		 */ 
		public static const cnYellow:int = 0Xe7eb7c;

		/**
		 * 紅色
		 */ 
		public static const cnRed:int = 0Xf67f85;

		/**
		 * 綠色
		 */ 
		public static const cnGreen:int = 0Xacfab5;
		
		/**
		 * 後端欄位名稱: 主畫面, 從主畫面傳送到後端的呼叫所使用的 is_app !!
		 */		
		public static const csMain:String = "_Main";
		
		/**
		 * 後端欄位名稱: 功能表
		 */ 
		public static const csMenu:String = "menu";

		/**
		 * 後端欄位名稱: 加密金鑰
		 */ 
		public static const csKey:String = "encryptKey";
		
		/**
		 * 後端欄位名稱: 登入時間
		 */ 
		public static const csLoginTime:String = "loginTime";
		
		/**
		 * 後端欄位名稱: 登入帳號
		 */ 
		public static const csLoginId:String = "loginId";
		
		/**
		 * 後端欄位名稱: 用戶代號
		 */ 
		public static const csUserId:String = "userId";
		
		/**
		 * 後端欄位名稱: 用戶名稱
		 */ 
		public static const csUserName:String = "userName";
		
		/**
		 * 後端欄位名稱: 被代理人帳號(登入後將 userId 和 agentId 互換!!)
		 */ 
		public static const csAgentId:String = "agentId";
		
		/**
		 * 後端欄位名稱: 部門代號
		 */ 
		public static const csDeptId:String = "deptId";
		
		/**
		 * 後端欄位名稱: 部門代號清單.(其值以逗號分隔)
		 */ 
		public static const csDeptIds:String = "deptIds";
		
		/**
		 * 後端欄位名稱: 密碼
		 */ 
		public static const csPwd:String = "pwd";
		
		/**
		 * 後端欄位名稱: 是否自動登入
		 */ 
		public static const csAutoLogin:String = "autoLogin";
		
		public static const csUploadPath:String = "upload";
		
		/**
		 * 後端欄位名稱: 是否開啟所有功能表
		 */ 
		//public static const csOpenAllMenu:String = "openAllMenu";
		
		/**
		 * 中文輸入法
		 */ 
		public static const csTW:String = "CHINESE";

		/**
		 * 英文輸入法
		 */ 
		public static const csUS:String = "ALPHANUMERIC_HALF";

		/**
		 * 可以上傳的檔案類別, 必須為小寫 !!
		 */ 
		//public static const csUploadTypes:String = "ppt,doc,xls,pdf,txt,jpg,png,gif";
		
		public static const csPicTypes:String = "jpg,jpeg,png,gif";			//代號 "P"(picture)
		public static const csSoundTypes:String = "mp3";					//代號 "S"(sound)
		public static const csVideoTypes:String = "swf,flv,mp4,avi,mpg,mp2";	//代號 "V"(video)
		//public static const csFileTypes:String = "ppt,doc,xls,pdf,txt,jpg,jpeg,png,gif,mp3";	//代號 "A"
		
		//for DW2 filter and others !!
		public static const csRowSeq:String = "_rowSeq";	//不是目前的筆數, 而是自動累加的筆數, 顯示資料時, 系統會自動設定這個位 	
		public static const csUpSeq:String = "_upSeq";		//預設只有在新增資料時, 系統才會自動寫入這個欄位, 自行設定時(沒有datagrid時), 必須同時設定  upFun
		public static const csUpFun:String = "_upFun";		//上層記錄的功能狀態, 內容同 DW dirty fun 
		
		//for upload file
		public static const csFile:String = "_file";			//(欄位名)儲存 fileReference
		public static const csFilePath:String = "_filePath";	//(欄位名)檔案上傳的相對路徑
		
		public static const csSessId:String = "sessId";			//(欄位名)session id
		public static const csPG:String = "_pg";				//(欄位名)programmer

		public static const csTextLimit:String = "^\"\'\<\>";	//預設的  TextInput 無法輸入這些字元
		
		//=================
		//***** 公用變數 *****
		//=================
			
		/**
		 * main application object, Fun.openPopup() 會用到
		 */ 
		public static var wMain:Object;
		
		/**
		 * 網站根目錄, 含反斜線 '\'
		 */ 
		public static var sDirRoot:String;
			
		/**
		 * 後端程式路徑, 目前為 _app 目錄
		 */ 
		public static var sDirApp:String;				
			
		/**
		 * 說明檔路徑, 目前為 help 目錄
		 */ 
		public static var sDirHelp:String;				
			
		/**
		 * 暫存檔路徑, 目前為 temp 目錄
		 */ 
		public static var sDirTemp:String;
			
		/**
		 * 範本檔案路徑, 目前為 _template 目錄
		 */ 
		public static var sDirTemplate:String;
		
		/**
		 * 上傳的檔案路徑, 目前為 upload 目錄
		 */ 
		public static var sDirUpload:String;
		
		
		/**
		 * 後端 CRUD 程式的檔案名稱, 不含副檔名.
		 */ 
		//public static var sCRUD:String = "_CRUD";
		
		/**
		 * 必須驗証的後端服務程式的檔案名稱, 不含副檔名.
		 */ 
		public static var sService:String = "_Service";
		
		/**
		 * 不須驗証的後端服務程式的檔案名稱, 不含副檔名.
		 */ 
		public static var sService2:String = "_Service2";
				
		/**
		 * 不須要驗証的後端服務程式的檔案名稱, 不含副檔名.
		 */ 
		//public static var sService2:String = "_Service2";
		
		/**
		 * 後端 Send mail 程式的檔案名稱, 不含副檔名.
		 */ 
		//public static var sMail:String = "_Mail";
		
		/**
		 * 系統使用的暫存的公用變數
		 */ 
		public static var oVar:Object={};
		
		/**
		 * 語系字串
		 */ 
		[Bindable] 
		public static var R:Object={};
		
		/**
		 * 重新登入的 mxml 畫面
		 */ 
		public static var wRelogin:winRelogin;
		
		/**
		 * 作業處理中的 mxml 畫面
		 */ 
		public static var wWorking:winWorking;
		
		/**
		 * 登入時間, 用來當做密碼的一部分
		 */ 
		//public static var sLoginTime:String;
		public static var nLoginTime:int;
				
		/**
		 * 登入帳號
		 */ 
		[Bindable]
		public static var sLoginId:String;
		
		/**
		 * 用戶代號
		 */ 
		[Bindable]
		public static var sUserId:String;
		
		/**
		 * 用戶名稱
		 */ 
		[Bindable]
		public static var sUserName:String;
		
		/**
		 * 被代理人帳號
		 */ 
		public static var sAgentId:String;
		
		/**
		 * 用戶所屬的部門代號
		 */ 
		public static var sDeptId:String;
		
		/**
		 * 用戶管轄的部門代號清單, 以逗號分隔
		 */ 
		public static var sDeptIds:String;
		
		/**
		 * 今天日期
		 */ 
		public static var dToday:Date; 
		
		/**
		 * 今天日期字串(yyyy/MM/dd), get from web server
		 */ 
		public static var sToday:String;  
		
		/**
		 * 目前開啟的程式代號
		 */ 
		public static var sApp:String;
		
		/**
		 * 是否為測試模式 ?? decide by getRoot()
		 */ 
		//public static var bTestMode:Boolean; 
		
		/**
		 * 從後端傳來的加密金鑰
		 */ 
		public static var sKey:String;

		/**
		 * AES key byteArray, 由於加密的動作很頻繁, 所以將加密後的KeyByte, 存成 instance.
		 * 此為固定key
		 */ 
		//public static var aKeyByte0:Array;
		public static var aKeyByte:Array;
				
		/**
		 * AES key byteArray, 由於加密的動作很頻繁, 所以將加密後的KeyByte, 存成 instance.
		 * 此為動態key
		 */ 
		//public static var aKeyByte:Array;
		
		/**
		 * 系統使用的日期格式字串
		 */ 
	    [Bindable]
	    public static var sDateFormat:String = "YYYY/MM/DD";
	    
		/**
		 * date formatter, 格式為 yyyy/MM/dd, for 系統使用
		 */ 
	    public static var df:DateFormatter = new DateFormatter();	    
	    df.formatString = "YYYY/MM/DD";

		/**
		 * date formatter(含 時:分), 格式為 yyyy/MM/dd hh:mm
		 */ 
	    public static var df2:DateFormatter = new DateFormatter();	    
	    df2.formatString = "YYYY/MM/DD JJ:NN";

		public static var sSessId:String;	    
		
		//play sound
		import flash.media.Sound; 
		import flash.media.SoundChannel; 	
		import flash.media.SoundLoaderContext;
		import flash.media.SoundTransform;
		//
		public static var oSound:Object;
		//private static var i_soundBtn:Button;	//play sound button		
		
		/**
		 * @param ps_file sound file url with full path
		 * @param pb_emptyMsg 檔案不存在時, 是否顯示訊息
		 * @param p_image image control for change icon if need.
		 * @param pf_playOk Function 播放完畢執行函數
		 * @param pf_playStop 播放中斷執行函數
		 */ 
		public static function playSound(ps_file:String, pb_emptyMsg:Boolean=false, p_image:imageSound=null, pf_playOk:Function=null, pf_playStop:Function=null):void{
			//initial if need
			//temp add
			//ps_file = "http://10.20.30.242/smart20b/Files/SysExam/9084_i.mp3";
			
			if (oSound == null){
				oSound = {
					//playing: false,
					vol: new SoundTransform(), //音量
					buffer: new SoundLoaderContext(2000, true),		//要使用音樂串流的緩衝
					playing: false,
					fPlayOk: pf_playOk,
					fPlayStop: pf_playStop
				};
			}
			
			if (p_image != null){
				oSound.image = p_image;
			}
			
			oSound.msg = pb_emptyMsg;			
			if (!oSound.playing){	//play sound
				//if file not exist, show msg
				var tn_pos:int = ps_file.indexOf(sDirRoot);
				var ts_file:String;
				if (tn_pos >= 0){
					ts_file = ps_file.substr(sDirRoot.length);					
				}else{
					ts_file = ps_file;
				}
				if (!FL.isExist(ts_file)){
					msg("I", R.notExist + " (" + ts_file + ")");
					return 
				}
				
				var t_request:URLRequest = new URLRequest(ps_file); 				
				var t_sound:Sound = new Sound(); 
				//t_sound.addEventListener(Event.COMPLETE, playSoundOk);
				t_sound.addEventListener(IOErrorEvent.IO_ERROR, playSoundFail);
				t_sound.load(t_request, oSound.buffer);
				//t_sound.play(0, 0, oSound.vol);
				//
				var t_channel:SoundChannel = new SoundChannel();
				t_channel = t_sound.play(0, 0, oSound.vol);
				t_channel.addEventListener(Event.SOUND_COMPLETE, playSoundOk);
				
				oSound.sound = t_sound; 
				oSound.channel = t_channel;
				oSound.file = ps_file;
				oSound.playing = true;
				
				//change ImageSound icon
				if (oSound.image != null)
					imageSound(oSound.image).bPlaying = true;
				
			}else if (oSound.sound.length > 0){		//相同音檔播2次表示要停止播放	
				//stop old sound
				oSound.channel.stop();
				oSound.playing = false;
				
				//change ImageSound icon
				if (oSound.image != null)
					imageSound(oSound.image).bPlaying = false;
				
				//play sound if need
				if (ps_file != oSound.file){
					playSound(ps_file, pb_emptyMsg, p_image);
				}else if (oSound.fPlayStop != null){
					oSound.fPlayStop();
				}
			}
		}
		
		private static function playSoundOk(event:Event=null):void{
			//change ImageSound icon
			if (oSound.image != null)
				imageSound(oSound.image).bPlaying = false;
			
			oSound.channel.removeEventListener(Event.SOUND_COMPLETE, playSoundOk);
			oSound.playing = false;
			
			if (oSound.fPlayOk != null){
				oSound.fPlayOk();
			}			
		}
		private static function playSoundFail(event:Event=null):void{
			//temp add
			//Fun.msg("I", "playSoundFail");
			
			oSound.sound.removeEventListener(IOErrorEvent.IO_ERROR, playSoundFail);
			oSound.playing = false;
			
			//change ImageSound icon
			if (oSound.image != null)
				imageSound(oSound.image).bPlaying = false;
			
			if (oSound.msg)
				Fun.msg("I", R.notExist);
		}
        
        public static function stopSound():void {
            if (oSound != null && oSound.hasOwnProperty("channel") && oSound.channel != null) {
                oSound.channel.stop();
                oSound = null;
            }            
        }         
		
		/**
		 * for download file, must be instance variables, or will fail !!
		 */ 
        //private static var i_file:FileReference;



		//===================
		//***** 公用函數 *****
		//===================			

		/**
		 * 初始化, 主程式必須先呼叫此函數.(使用類別建構子會比較複雜!)
		 * @param {Object} pw_main: 主畫面 for Alert()
		 */ 
		public static function init(pw_main:Object):void{
			//var ts_path:String = Application.application.url; 			
			var ts_path:String = ExternalInterface.call("window.location.href.toString");
			var tn_pos:int = ts_path.lastIndexOf("/");
			ts_path = ts_path.substr(0,tn_pos);
			if (ts_path.indexOf("bin-") >= 0){		//both bin-debug & bin-release
				tn_pos = ts_path.lastIndexOf("/");
				ts_path = ts_path.substr(0,tn_pos);
			}else{
				Fun2.sDebugPara = "";
			}

			
			//return ts_path + "/";
			wMain = pw_main;
			sDirRoot = ts_path + "/";
			sDirApp = sDirRoot + "_app/";				
			sDirTemplate = sDirRoot + "_template/";
			sDirHelp = sDirRoot + "help/";				
			sDirTemp = sDirRoot + "temp/";
			sDirUpload = sDirRoot + csUploadPath + "/";
			
			aKeyByte = Rijndael.strToBytes(Fun2.csKey + ST.preZero(Fun2.cnKeySize/8 - Fun2.csKey.length, ""));
			
			//語系
			//R = getLang("_Fun");
			//temp
			//Fun.msg("I", sDirRoot);
			
			//call Fun2.init()
			Fun2.init();
		}


		/*
		public static function loadCSS():void {
  			var ts_css:String = "css/"+Fun2.css+".swf";
	  		StyleManager.loadStyleDeclarations(ts_css);			
		}
		*/
		
		
		/** 
		 * show message
		 * @param ps_type message type. 
		 * @param ps_msg messag string.
		 */ 
		public static function msg(ps_type:String, ps_msg:String):void {
			var t_alert:Alert;
						
			switch (ps_type){
				case "I":
					t_alert = Alert.show(ps_msg, Fun.R.information);
					break;
				case "E":	//client error msg
					t_alert = Alert.show(ps_msg, Fun.R.error);
					break;
				case "S":	//server error msg
					t_alert = Alert.show(ps_msg, "Server Error");
					break;
				default:
					break;
			}
						
			//t_alert.buttonMode = true;
			//t_alert.useHandCursor = true;
			//t_alert.focusEnabled = false;
		}
		
		
		/** 
		 * show message, 使用者按下確定之後才能執行另一個函數 (flex 使用非同步 !!)
		 * @param ps_msg message string. 
		 * @param {function} pf_yes handled function for press [ok].
		 */ 
		public static function msg2(ps_type:String, ps_msg:String, pf_yes:Function):void {
			var t_fn:Function = Fun.msg3;
			Fun.oVar.fnYes = pf_yes;
			
			var ts_title:String = (ps_type == "I") ? Fun.R.information : Fun.R.error;  
			Alert.show(ps_msg, ts_title, Alert.YES, null, t_fn);			
		}

		/** 
		 * ans() callback function.
		 * @param p_event 此參數必須存在! 
		 */ 		
		private static function msg3(p_event:Event):void {
			Fun.oVar.fnYes();
		}				
		
		/** 
		 * confirm message box, callback function 無任何參數!
		 * @param ps_msg message string. 
		 * @param pn_default default selected button.
		 * @param {function} pf_yes handled function for press [ok].
		 * @param {function} pf_no handled function for press [no].
		 */ 
		public static function ans(ps_msg:String, pn_default:int, pf_yes:Function, pf_no:Function=null):void {
			var t_fn:Function = Fun.ans2;
			Fun.oVar.fnYes = pf_yes;
			if (pf_no == null){
				Alert.show(ps_msg, Fun.R.question, Alert.YES|Alert.NO, null, t_fn, null, (pn_default==1) ? Alert.YES : Alert.NO);
			}else{
				Fun.oVar.fnNo = pf_no;
				Alert.show(ps_msg, Fun.R.question, Alert.YES|Alert.NO|Alert.CANCEL, null, t_fn, null, (pn_default==1) ? Alert.YES : ((pn_default==2) ? Alert.NO : Alert.CANCEL));				
			}
		}

		/** 
		 * ans() callback function.
		 * @param p_event 此參數必須存在! 
		 */ 		
		private static function ans2(p_event:Event):void {
			switch ((p_event as Object).detail){
				case Alert.YES:
					Fun.oVar.fnYes();
					break;
				case Alert.NO: 
					if (Fun.oVar.fnNo != null){ 
						Fun.oVar.fnNo();
					}
					break;
			}
		}				


		/**
		 * open an existed file in new browser.
		 * @param ps_url full path of file.
		 */ 
		public static function openFile(ps_url:String, p_data:Object=null):void{
			if (ps_url.indexOf("/") < 0){
				ps_url = Fun.sDirTemp + ps_url;
			}
			var t_request:URLRequest = new URLRequest(ps_url);
			var t_data:URLVariables = new URLVariables();
			//t_request.method = URLRequestMethod.POST;	//navigateToURL 不可使用 post
			if (p_data == null){
				//t_data.tt = new Date().getTime();
				t_data.tt = new Date().seconds;
			}else{
				t_data.data = encode(p_data);
			}
			t_request.data = t_data;
			
			//檔案不存在時, 會出現 error.
			//try {
				navigateToURL(t_request, "_blank");	
			//}catch (e:Error){
				//do nothing
			//}
			
		}
		
		
		/**
		 * open an help file in new browser.
		 * @param ps_app 程式代號
		 */ 
		public static function openHelp(ps_app:String):void{
			//new
			//Fun.openFile(Fun.sDirHelp+ps_app+".htm");
			if (Fun.isEmpty(ps_app)){
				return;
			}
			
			if (ST.right(ps_app, 5).indexOf(".") < 0){
				ps_app += ".htm";
			}
			Fun.openFile(Fun.sDirHelp+ps_app);						
		}
		

		/**
		 * ?? generate excel and save.
		 * p_data must has data, table, file variables.
		 */ 
		/* 
		public static function genExcel(ps_app:String, p_data:Object, pf_callback:Function):void {
			var ts_file:String = p_data.file;
			if (ts_file.indexOf(".") < 0){
				ts_file += ".xls";
			}
			Fun.oGlobal.app = ps_app; 
			Fun.oGlobal.file = ts_file; 
		    p_data.fun = "Excel";
		    Fun.async(ps_app, Fun.sCRUD, p_data, pf_callback);
		}
		*/
		

		/**
		 * ??
		 */ 
		private static function saveExcel2(p_data:Object):void {
			var ts_app:String = Fun.oVar.app;
			var ts_file:String = Fun.oVar.file;  
			/*
			var t_data:Object = {
				fun: "OpenFile", 
				file: ts_file
			};
			*/
		    //Fun.async(ts_app, Fun.CRUD, null, t_data);
		    //Fun.openReport(Fun.CRUD, t_data);
		    //Fun.async(ts_app, Fun.sDirTemp+ts_file, openExcel2a);
		    //Fun.openFile(Fun.sDirTemp+ts_file);
		    FL.download(ts_app, ts_file, true);
			/*
		    //p_data.app = ps_app;
			var t_data:Object = sync(ps_app, Fun.CRUD, p_data, true);			
			if (Fun.catchResult(t_data, false, true)){
				return null;
			}else if (t_data == null){
				return null;				
			}else{
				return t_data as Array;
			}
			*/
		}


		/**
		 * ??
		 */ 
		/*
		private static function openExcel2a(p_data:Object):void {
			var ts_app:String = Fun.oGlobal.app;
			var t_data:Object = {
				fun: "DeleteFile", 
				file: Fun.oGlobal.file
			};
			
		    Fun.async(ts_app, Fun.sCRUD, t_data, null);
		}
		*/
		

		/** 
		 * 開啟報表檔案, open new browser for generating word report for customize report only.
		 * navigateToURL only works with 'GET' method in iFrame !!
		 * @param ps_app 程式代號.
		 * @param ps_serverApp 後端程式名稱, 不含副檔名, 如果空白則使用 ps_app, 後端的程式產生檔案後必須傳回Json變數含 file 欄位 !!
		 * @param p_data 傳入後端的參數.
		 * @param ps_fileType 產生的檔案格式: doc, xls, pdf, htm, html
		 */ 
		//public static function outputReport(ps_rptApp:String, p_data:Object):void{
		public static function openReport(ps_app:String, ps_serverApp:String, p_data:Object, ps_fileType:String):void{

			p_data.app = ps_app;
			ps_serverApp = Fun.serverApp(ps_app, ps_serverApp);
			
			//var ts_url:String = Fun.sDirApp + ps_serverApp;
			if (ps_fileType.toLowerCase() == "doc"){
	            var t_request:URLRequest = new URLRequest(Fun.sDirApp + ps_serverApp);
	            var t_data:URLVariables = new URLVariables();
	            //t_request.method = URLRequestMethod.POST;		//navigateToURL 不可使用 post
	            t_data.data = encode(p_data);
	            t_request.data = t_data;
	            navigateToURL(t_request, "_blank");					
			}else{
		    	Fun.async(ps_app, ps_serverApp, p_data, openReport2);			
			}
		}


		private static function openReport2(p_data:Object):void{	    
			if (!Fun.catchResult(p_data, false, true)){
		  		Fun.openFile(Fun.sDirTemp + p_data.file);
			}
		}
		
		import mx.core.IFlexModuleFactory;
		
		/**
		 * 開啟 TitleWidnow
		 * @parem pw_this TitleWindow object.
		 * @param p_parent Parent Object, //如果null, 則使用 Fun2.mainApp.
		 */ 
		//public static function openPopup(pw_this:TitleWindow, pw_up:Object):void{
		//public static function openPopup(pw_this:TitleWindow, pw_up:Object=null):void{
		//public static function openPopup(pw_this:Object, pw_up:Object=null):void{
		public static function openPopup(pw_this:Object, pw_up:Object=null):void{
			//var t_win:TitleWindow = pw_this as TitleWindow;
			//var t_win:TitleWindow = pw_this;
			if (pw_this.hasOwnProperty("creationPolicy") && pw_this.creationPolicy != "all"){	//flex bug when open FlowE !!
				pw_this.creationPolicy = "all";				
			}			
			//if (pw_this.hasOwnProperty("showCloseButton")){				
			//	pw_this.showCloseButton = true;
			//}
			
			//強制設定 style, 內容同 TitleWindow !!
			pw_this.styleName = "titleWin";
			
		    //PopUpManager.addPopUp(t_win, pw_up as DisplayObject , true);
		    //var t_parent:Object = (pw_up) ? pw_up : Fun2.wMain; 
		    //PopUpManager.addPopUp(pw_this as IFlexDisplayObject, t_parent as DisplayObject, true);
			var tw_up:DisplayObject = (pw_up != null) ? pw_up as DisplayObject : wMain as DisplayObject;
			PopUpManager.addPopUp(pw_this as IFlexDisplayObject, tw_up, true);
			//PopUpManager.addPopUp(pw_this as IFlexDisplayObject, Fun2.wMain as DisplayObject, true);
			//PopUpManager.addPopUp(pw_this, Fun2.wMain as DisplayObject, true);
			
			//PopUpManager.centerPopUp(pw_this as TitleWindow);
			PopUpManager.centerPopUp(pw_this as IFlexDisplayObject);
			
			//調整位置到中間 !!
			//var tn_h2:int = int((Fun2.wMain.height - pw_this.height)/2);
			var tn_w:int = int((tw_up.width - pw_this.width)/2);
			var tn_h:int = int((tw_up.height - pw_this.height)/3);
			//if (tn_h < 0){
			//	tn_h = 0;
			//}
			pw_this.x = tw_up.x + tn_w;
			pw_this.y = tw_up.y + tn_h;
			if (pw_this.x < 0)
				pw_this.x;
		}
			
		
		/**
		 * 關閉 TitleWindow, (pw_this 為 Object 變數)
		 * @parem pw_this TitleWindow object.
		 */ 
		public static function closePopup(pw_this:Object):void{
			PopUpManager.removePopUp(pw_this as IFlexDisplayObject);
		}
				
		
		/**
		 * 先檢查 session 狀態再執行某函數, 如果 session 已經過期, 則會先開啟 WinRelogin 畫面.
		 * @param pf_callback 要執行的函數.
		 */ 
		public static function sessionFun(pf_callback:Function):void{
		    //if (Fun.sync("", Fun.sService2, {fun:"CheckSession"}, false, false) != "Y"){
			if (Fun.sync("", Fun.sService2, {fun:"CheckSession"}, false) != "Y"){
		    	Fun.openRelogin(pf_callback);
		    }else{
		    	var t_data:Object = null;
		    	pf_callback(t_data);	    	
		    }
	 	}


		/**
		 * 檢查 session 是否有效.
		 * @param pb_reLogin 是否開啟重新登入畫面
		 * @return true(有效)/false(無效)
		 */ 
		public static function checkSession(pb_reLogin:Boolean):Boolean{
		    //var tb_session:Boolean = (Fun.sync("", Fun.sService2, {fun:"CheckSession"}, false, false) == "Y");
			var tb_session:Boolean = (Fun.sync("", Fun.sService2, {fun:"CheckSession"}, false) == "Y");
		    if (!tb_session && pb_reLogin){
		    	Fun.openRelogin();
		    }
		    
		    return tb_session;		    
	 	}

		
		/**
		 * 以不檢查權限的方式呼叫後端程式
		 * @return 後端傳回的字串結果
		 */ 
		public static function service2(p_data:Object):String{
			//return Fun.sync("", Fun.sService2, p_data, false, false) as String;
			return Fun.sync("", Fun.sService2, p_data, false) as String;
		}
		

		/** 
		 * run javaScript.(AIR 不支援!)
		 * @param ps_script javaScript to be run. 
		 * @return javaScript 執行結果字串
		 */ 
		public static function runJS(ps_script:String):String {
		    if (!ExternalInterface.available){
		        Fun.msg("E","mx.ExternalInterface class is not usable !");
		    	return "";		        
		    }
		    
		    var ts_result:String = "";
		    try{
			    ts_result = ExternalInterface.call("function(){"+ps_script+"}");		    
		    }catch (e:Error){
		        Fun.msg("E","Fun.runJS() Error !\n"+ps_script);
		    	//return "";
		    	//do nothing
		    }
//temp
//Fun.msg("I", ts_result);
		    
		    if (ts_result == null){
		    	ts_result = "";
		    }

			return ts_result			
		}
		

		//get server app name
		private static function serverApp(ps_app:String, ps_serverApp:String):String {
			var ts_app:String;
			if (ps_serverApp == ""){
				//ts_app = ps_app + Fun2.csAppExt;
				ts_app = Fun.sService + "." + Fun2.csAppExt;
			}else if(ps_serverApp.indexOf(".") < 0){
				ts_app = ps_serverApp + Fun2.csAppExt;			
			}else{
				ts_app = ps_serverApp;
			}
			if (Fun2.sDebugPara != "")
				ts_app += "?" + Fun2.sDebugPara;
			
			return ts_app;
		}
		
		
		/** 
		 * 同步執行後端程式 (使用 xmlHttpRequest).
		 * @param ps_app 目前的程式代號 
		 * //param ps_serverApp 後端的程式代號, 不含副檔名, 如果空白則使用 ps_app. 
		 * @param ps_serverApp 後端的程式代號, 不含副檔名, 如果空白則使用 Fun.csService 
		 * @param p_data 要傳送到後端的 JsonObject 參數. 
		 * @param pb_object 是否將執行結果解碼. 
		 * @param pb_encrypt 是否加密, 呼叫 _Service2 時，不加密　
		 * @return 字串(不解碼), 或是 Object(解碼)
		 */ 
		//public static function sync(ps_app:String, ps_serverApp:String="", p_data:Object=null, pb_object:Boolean=true, pb_dynamicKey:Boolean=true):Object {
		//public static function sync(ps_app:String, ps_serverApp:String="", p_data:Object=null, pb_object:Boolean=true, pn_encryptType:int=-1):Object {
		//public static function sync(ps_app:String, ps_serverApp:String="", p_data:Object=null, pb_object:Boolean=true, pb_encrypt:Boolean = true):Object {
		public static function sync(ps_app:String, ps_serverApp:String="", p_data:Object=null, pb_object:Boolean=true):Object {
			
			//add password for connect ap1
			//if (pn_encryptType == -1){
			//	pn_encryptType = Fun2.cnEncryptType;	
			//}
			if (p_data == null){
				p_data = {};
			}
			if (ps_app != ""){
				p_data.app = ps_app;
			}
			ps_serverApp = Fun.serverApp(ps_app, ps_serverApp);
					
			//不再加入 fun 參數在 p_data外面(2011/10/7)
			//var ts_data:String = (p_data == {}) ? "" : ("fun="+p_data.fun+"&data=" + Fun.encode(p_data, pb_encrypt));						
			//var ts_data:String = (p_data == {}) ? "" : ("data=" + Fun.encode(p_data, pb_encrypt));						
			var ts_data:String = (p_data == {}) ? "" : ("data=" + Fun.encode(p_data));						
			
			var ts_ajax:String = 
		    	"var t_http;"+
		        "try{"+             
		            "t_http = new ActiveXObject('Msxml2.XMLHTTP');" + 
		        "}catch(e){" + 
		            "try{" + 
		                "t_http = new ActiveXObject('Microsoft.XMLHTTP');" + 
		            "}catch(e2){" + 
		                "t_http = null;" + 
		            "}"+
		        "}" + 
		        "if (!t_http && typeof XMLHttpRequest != 'undefined') {" + 
		            "t_http = new XMLHttpRequest();" + 
		        "}"+
		        "try{" + 
		            "t_http.open('POST','"+ Fun.sDirApp + ps_serverApp + "',false);"+
			        "t_http.setRequestHeader('Content-Length',"+ts_data.length.toString()+");"+
        			"t_http.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=utf-8');"+
		            //((ps_data.indexOf('"') < 0) ? 't_http.send("'+ ps_data + '");' : "t_http.send('"+ ps_data + "');")+
		            //'t_http.send("'+ ps_data + '");'+
		            "t_http.send('"+ ts_data + "');"+
		            "return t_http.responseText;" +            
		        "}catch(e3){" +
		            "alert(e3)" +
		        "}";
		    
		    ts_ajax = runJS(ts_ajax);		    
		    if (!pb_object){
		    	return (ts_ajax as Object);
		    }else if (ts_ajax == "" || ts_ajax == "[]" || ts_ajax == "_" || ts_ajax == "\r\n_"){
		    	return null;
		    }
		    
		    var t_data:Object = null;
		    try {
			    t_data = ST.toJson(ts_ajax, true);
		    }catch (e:Error){
		    	t_data = {error: ts_ajax};
		    }
		    
		    return t_data;
		}	


		/**
		 * 非同步執行後端程式 (使用 HTTPService).
		 * @param ps_app 目前的程式代號 
		 * @param ps_serverApp 後端的程式代號, 不含副檔名, 如果空白則使用 ps_app. 
		 * @param p_data 要傳送到後端的 JsonObject 參數. 
		 * @param pf_ok 執行成功後呼叫此函數. 
		 * @param pf_fail 如果執行失敗呼叫此函數, 可為 null. 
		 * @param pb_dynamicKey 是否使用動態 key. 
		 */  
		//public static function async(ps_app:String, ps_serverApp:String, p_data:Object, pf_ok:Function, pf_fail:Function=null, pn_encryptType:int=-1):void {			
		public static function async(ps_app:String, ps_serverApp:String, p_data:Object, pf_ok:Function, pf_fail:Function=null):void {			
			//set global for handle ok result !
			Fun.oVar.fnOk = (pf_ok != null) ? pf_ok : null ;		
			Fun.oVar.fnFail = (pf_fail != null) ? pf_fail : null ;		
			
			//if (pn_encryptType == -1){
			//	pn_encryptType = Fun2.cnEncryptType;	
			//}
			if (p_data == null){
				p_data = {};
			}
			p_data.app = ps_app;
			ps_serverApp = Fun.serverApp(ps_app, ps_serverApp);
			

			//var ts_url:String = (ps_serverApp.indexOf(".ashx") >= 0) ? Fun.sDirApp + ps_serverApp : ps_serverApp ;
			//var ts_data:String = encode(p_data);           	
           	var t_http:HTTPService = new HTTPService();  
            t_http.url = Fun.sDirApp + ps_serverApp; 
            //t_http.request = {fun:p_data.fun, data:ts_data};
			t_http.request = {data:encode(p_data)};
            t_http.method = "POST";
            t_http.showBusyCursor = true;
            t_http.addEventListener("result", asyncOk);
            t_http.addEventListener("fault", asyncFail);
            t_http.send();
		}

		private static function asyncOk(p_event:ResultEvent):void {
			var ts_data:String = p_event.result.toString();
		    if (ts_data == "[]" || ts_data == "_"){
		    	ts_data = "";
		    }
		    		    		    
			var t_data:Object = {};
		    if (ts_data == ""){
		    	t_data = null;
		    }else{
			    try {
				    t_data = ST.toJson(ts_data, true);				    
					//if (Fun.catchResult(t_data, false, false)){
					if (Fun.catchResult(t_data, false, true)){
						return;
					}				    
			    }catch (e:Error){
			    	t_data.error = ts_data;
			    }
			}
			
			if (Fun.oVar.fnOk != null){
				Fun.oVar.fnOk(t_data);
			}							
		}

		//show error when HTTPService failed.
		private static function asyncFail(p_event:FaultEvent):void {
			var ts_sep:String = "\n";
			var t_fault:Fault = p_event.fault;
			var ts_msg:String = "errorID:" + t_fault.errorID + ts_sep +  
				"faultCode:"+ t_fault.faultCode + ts_sep +
				"faultString:"+ t_fault.faultString + ts_sep + 
				"faultDetail:" + t_fault.faultDetail;
			Fun.msg("E", ts_msg);
			
			if (Fun.oVar.fnFail != null){
				Fun.oVar.fnFail(null);
			}							
		}


		/** 
		 * 使用同步/非同步方式讀取後端少量資料庫. 必須同時在後端 Fun2._getSql()撰寫對應的程式碼.
		 * @param ps_app 目前的程式代號 
		 * @param p_data 傳入參數 for 後端 Fun2._getSql() 
		 * @param pf_callback callback function, 如果指定, 則會使用非同步呼叫, 並且傳回null 
		 * @return 資料陣列, 如果無資料則傳回 null.
		 */ 
		//public static function readRows(ps_app:String, p_data:Object, pf_callback:Function=null, pas_s2:Array=null):Array {
		public static function readRows(ps_app:String, p_data:Object, pf_callback:Function=null):Array {
		    p_data.fun = "SQL";
			if (pf_callback == null){
				var t_data:Object = sync(ps_app, Fun.sService, p_data, true);			
				if (Fun.catchResult(t_data, false, true)){
					return null;
				}else if (t_data == null){
					return null;				
				}else{
					//考慮 S2 欄位
					var ta_data:Array = t_data as Array;
					/*
					AR.arrayEscape(ta_data, pas_s2);
					var tn_s2:int = (pas_s2 != null) ? pas_s2.length : 0;
					for (var i:int=0;i<ta_data.length;i++){
						for (var j:int=0; j<tn_s2; j++){
							ta_data[i][pas_s2[j]] = Fun.escape(ta_data[i][pas_s2[j]], false);
						}
					}
					*/
					return ta_data;
				}				
			}else{
				Fun.oVar.fnReadRows = pf_callback;
				//Fun.oGlobal.asS2 = pas_s2;
				async(ps_app, Fun.sService, p_data, readRows2);
				return null;
			}
		}
	
		//callback function of readRows
		private static function readRows2(p_data:Object):void {
			if (!Fun.catchResult(p_data, false, true)){
				//考慮 S2 欄位
				var ta_data:Array = p_data as Array;
				/*
				var tas_s2:Array = Fun.oGlobal.asS2;
				var tn_s2:int = (tas_s2 != null) ? tas_s2.length : 0;
				for (var i:int=0;i<ta_data.length;i++){
					for (var j:int=0; j<tn_s2; j++){
						ta_data[i][tas_s2[j]] = ST.escape(ta_data[i][tas_s2[j]], false);
					}
				}
				*/
				
				Fun.oVar.fnReadRows(ta_data)				
			}							
		}
		
		public static function readRow(ps_app:String, p_data:Object):Object {
			var ta_row:Array = readRows(ps_app, p_data);
			return (ta_row != null) ? ta_row[0] : null;;
		}		

		/** 
		 * 讀取後端的變數或設定的(JsonObject), 必須參照 Fun2._getSql().
		 * @param ps_app 目前的程式代號 
		 * @param p_data 傳入參數 for 後端 Fun2._getSql() 
		 * @return Object.
		 */ 
		public static function readJson(ps_app:String, p_data:Object):Object {
		    p_data.fun = "Json";
		    //p_data.data = "Json";
			var t_data:Object = sync(ps_app, Fun.sService, p_data, true);			
			if (Fun.catchResult(t_data, false, true)){
				return null;
			}else{
				return t_data;
			}
		}

	
		/** 
		 * 讀取後端的變數或設定(字串), 必須同時在後端 Fun2._getSql()撰寫對應的程式碼.
		 * @param ps_app 目前的程式代號 
		 * @param p_data 傳入參數 for 後端 Fun2._getSql() 
		 * @return 字串.
		 */ 
		public static function readVar(ps_app:String, p_data:Object):String {
		    p_data.fun = "Var";
		    //p_data.data = "Var";
			var t_data:Object = sync(ps_app, Fun.sService, p_data, true);			
			if (Fun.catchResult(t_data, false, true)){
				return null;
			}else{
				return t_data.data;
			}
		}

		
		/** 
		 * 讀取後端的變數或設定(字串), 必須同時在後端 Fun2._getSql()撰寫對應的程式碼.
		 * @param ps_app 目前的程式代號 
		 * @param p_data 傳入參數 for 後端 Fun2._getSql() 
		 * @return 字串.
		 */ 
		public static function readConfig(ps_app:String, ps_fid:String):String {
			var t_data:Object = {fun:"Config", fid:ps_fid};
			t_data = sync(ps_app, Fun.sService, t_data, false);			
			if (Fun.catchResult(t_data, false, true)){
				return "";
			}else{
				return t_data as String;
			}
		}
		
	
		/** 
		 * 讀取後端的 Flow Proc List , 必須同時在後端 Fun2._getSql()撰寫對應的程式碼來讀取 job Accepter.
		 * @param ps_app 目前的程式代號 
		 * @param p_data 傳入參數, 必須包含以下變數: flowName, procName 
		 * @return 流程的程序清單陣列.
		 */ 
		public static function readFlowJobs(ps_app:String, p_data:Object):Array {
			//p_data.fun = "FlowProcs";
			p_data.fun = "readFlowJobs";
			var t_data:Object = sync(ps_app, Fun.sService, p_data, true);			
			if (Fun.catchResult(t_data, false, true)){
				return null;
			}else{
				return t_data as Array;
			}
		}


		/**
		 * 從後端讀取資料做為 ComboBox 的資料來源．
		 * @param ps_app 目前的程式代號 
		 * @param ps_type 資料種類, 後端 Fun2._getSql() 必須存在 data=ComboBox, type=ps_type 的設定.
		 * @param pb_addEmpty 是否增加一筆空的資料在最上面.
		 * @return 資料陣列.
		 */ 
		public static function comboDS(ps_app:String, ps_type:String, pb_addEmpty:Boolean=false, pb_editWin:Boolean=true):Array {
			var t_data:Object = {
				data: "ComboBox",
				type: ps_type
			}
			
			return comboDS2(ps_app, t_data, pb_addEmpty, pb_editWin);
		}   

		
		/**
		 * 從後端讀取資料做為 ComboBox 的資料來源, 和 comboBoxDS()不同的是此函數可以傳入比較多的參數.
		 * @param ps_app 目前的程式代號 
		 * @param p_data 傳入參數 for 後端 Fun2._getSql(), 必須包含 type 欄位. 
		 * @param pb_addEmpty 是否增加一筆空的資料在最上面.
		 * @return 資料陣列.
		 */ 
		public static function comboDS2(ps_app:String, p_data:Object, pb_addEmpty:Boolean=false, pb_editWin:Boolean=true):Array {
			if (!p_data.hasOwnProperty("data")){
				p_data.data = "ComboBox";
			}
			return AR.addEmpty(Fun.readRows(ps_app, p_data), pb_addEmpty, pb_editWin);
			
			/*
			if (ta_data == null){
				ta_data = [];
			}
			
			if (pb_addEmpty){
				ta_data.splice(0,0,{label:"", data:""});
			}
			return ta_data as Array;
			*/				
		}   
				
				
		//get codeTable ds, show error msg if not found.				
		/**
		 * 從後端讀取 "雜項檔" 資料做為 ComboBox 的資料來源, 必須參照後端 Fun2._getSql()
		 * @param ps_app 目前的程式代號 
		 * @param ps_type 資料種類, 後端 Fun2._getSql() 必須存在 data=CodeTable, type=ps_type 的設定.
		 * @param pb_addEmpty 是否增加一筆空的資料在最上面.
		 * @param pb_notIn codeId 不包含的字串清單, 裡面不必輸入引號, ex: "A,B,C"
		 * @return 資料陣列.
		 */ 
		public static function codeDS(ps_app:String, ps_type:String, pb_addEmpty:Boolean=false, ps_notIn:String=""):Array {			
			var t_data:Object = {
				data: "CodeTable",
				type: ps_type
			}
			if (ps_notIn != ""){
				t_data.notIn = ps_notIn;
			}
			return Fun.comboDS2(ps_app, t_data, pb_addEmpty);
		}
		
		
		/**
		 * 使用非同步的方式將查詢欄位的查詢結果顯示在 DataGrid.(old: condToGrid)
		 * @param ps_app 目前的程式代號 .
		 * @param pa_item 查詢畫面的查詢條件陣列.(直接使用 mxml 裡的變數)
		 * @param p_grid DataGrid.
		 * @param pb_msg 是否顯示訊息.
		 */ 
		public static function qItemsToGrid(ps_app:String, pa_item:Array, p_grid:DataGrid, pb_msg:Boolean):void {
	    	var t_data:Object = {
		    	fun: "QueryList", 
		    	data: ST.jsonToStr(pa_item)
	    	};
	    	
	    	//set global
			Fun.oVar.grid = p_grid; 
			Fun.oVar.msg = pb_msg; 

			//asychronized call	    	
	    	Fun.async(ps_app, Fun.sService, t_data, qItemsToGrid2);
	 	}
	    	    
	    //callback function	
		private static function qItemsToGrid2(pa_row:Array):void {
	    	//var ta_data:Object = Fun.sync(ps_app, Fun.CRUD, t_data, true); 			    	
			if (!Fun.catchResult(pa_row, true, true)){
				var t_grid:DataGrid = Fun.oVar.grid;
				var tb_msg:Boolean = Fun.oVar.msg;
				var t_ds:Object = t_grid.dataProvider;
				t_ds.removeAll();
				
				var tn_count:int = pa_row.length;
				for (var i:int=0;i<tn_count;i++){
					t_ds.addItem(pa_row[i]);	
				}
				
				if (tb_msg){
					Fun.msg("I", ST.format(Fun.R.findRow, [tn_count]));
				}
	    	}
		}
				
				
		/** 
		 * 檢查某個值是否為空白或null
		 * @param p_data 傳入資料, 可為字串或數字. 
		 * @return true/false
		 */ 
		public static function isEmpty(p_data:Object):Boolean {
			if (p_data == null){
				return true;
			}else{
				var ts_text:String = String(p_data);
				return (ts_text == "" || ts_text == "null");
			}
		}
		
		
		public static function isZero(pn_data:Object):Boolean {
			return (pn_data == null || pn_data == 0);
		}
		
		
		
		/**
		 * 資料加密編碼.(for 傳送到後端),目前使用的加密方式為 ECB 128 ??
		 * @param p_data 原始資料.(object)
		 * @return 加密後字串. 
		 */ 
		/* 
		public static function zz_encode(p_data:Object):String {
			//add password for connect ap1
			if (p_data == null){
				p_data = {};
			}
			if (Fun.sLoginTime){
				p_data.loginTime = Fun.sLoginTime;
			}
			
			
			//base 64 encoding
			var ts_data:String = Base64.encode(JSON.encode(p_data));
			
			//encode by changing char order
			var ts_para:String = "";
			var tn_len:int = ts_data.length;
			var tn_len2:int = int(tn_len / 2);
			var ts_tail:String = (tn_len == tn_len2*2) ? "" : ts_data.substr(tn_len - 1,1);
			for (var i:int=0;i<tn_len2;i++){
				ts_para += ts_data.substr(i,1)+ts_data.substr(tn_len2+i,1);
			}
			
			//ts_data = "data=" + Base64.encode(ts_para + ts_tail);
			return (ts_para + ts_tail);
		}			
		*/
		
		
		/**
		 * 把 Object 變數加密成16進位字串.(使用 ECB 256)
		 * @param p_data 要加密的 Object 資料
		 * @param pb_dynamicKey 是否使用動態key
		 * @return 加密後的 16進位字串
		 */ 
		//public static function encode(p_data:Object, pb_dynamicKey:Boolean=true):String {
		//public static function encode(p_data:Object, pn_encryptType:int=-1):String {
		//public static function encode(p_data:Object, pb_encrypt:Boolean=true):String {
		public static function encode(p_data:Object):String {
						
			//add lang setting !!
			//if (pn_encryptType == -1){
			//	pn_encryptType = Fun2.cnEncryptType;	
			//}
			if (p_data == null){
				p_data = {};
			}
			p_data["_lang"] = Fun2.sLang;
			return Fun2.encrypt(p_data);
			
			/*			
			if (pb_encrypt){
				return Fun2.encrypt(p_data);
				//var t_aes:Rijndael = new Rijndael(Fun.aKeyByte0);
				//return t_aes.encrypt(JSON.encode(p_data));
			}else{
				return ST.jsonToStr(p_data);				
			}
			*/
		} 	

					
		/**
		 * 資料解密(ECB)
		 * @param ps_text 輸入文字, 如果內容經過加密, 則為16 進位加密字串
		 * @param pb_object true(傳回object), false(傳回字串)
		 * @param pb_dynamicKey 是否使用動態key
		 * @return 解密後 JsonObject
		 */ 
		//public static function decode(ps_text:String, pb_object:Boolean=true, pb_dynamicKey:Boolean=true):Object {
		//public static function decode(ps_text:String, pb_object:Boolean=true, pn_encryptType:int=-1):Object {
		public static function decode(ps_text:String, pb_object:Boolean=true):Object {
		//public static function decode(ps_text:String):Object {
			//if (pn_encryptType == -1){
			//	pn_encryptType = Fun2.cnEncryptType;	
			//}
			
			//if (pn_encryptType == 0 || Fun2.cnEncryptType == 0){
				return (pb_object) ? ST.toJson(ps_text) : ps_text;				
			//}else{
			//	var t_aes:Rijndael = new Rijndael((pn_encryptType == 2) ? Fun.aKeyByte : Fun.aKeyByte0);
			//	return (pb_object) ? JSON.decode(t_aes.decrypt(ps_text)) : t_aes.decrypt(ps_text);
			//}
			//return null;
		}
		
					
		/**
		 * 使用固定key解密(ECB)
		 * @param ps_hex 16 進位加密字串
		 * @return 解密後 JsonObject
		 */
		/*
		public static function decode2(ps_hex:String):String {
			var t_aes:Rijndael = new Rijndael(getKeyByte(""));
			return t_aes.decrypt(ps_hex);			
		}
		*/
		
		/**
		 * 傳回某個鍵值字串加密後的 KeyByte 陣列.
		 * @param ps_key 鍵值字串
		 * @return 加密後的 KeyByte 陣列
		 */ 
		/*
		public static function getKeyByte(ps_key:String=""):Array {
			ps_key = Fun2.csBaseKey + ps_key;
			return Rijndael.strToBytes(ps_key + ST.preZero(Fun2.cnKeySize/8 - ps_key.length, ""));
		}
		*/
		

			
		/**
		 * get name of someone code in ds.
		 * @param ps_date input string date.
		 * @return code name.
		 */ 	
		/* 
        //public static function codeName(p_ds:ArrayCollection, p_data:Object):String {
        public static function codeName(pa_data:Array, p_data:Object):String {
			for (var i:int=0;i<pa_data.length;i++){
				if (pa_data[i].data == p_data){
					return pa_data[i].label;			
				}
			}
			
			//case of not found
			return String(p_data);			
        }
        */
		
		
		/**
		 * 檢查身份証號是否正確
		 * @param ps_idno 身份証號
		 * @return true/false
		 */  
		public static function checkIdno(ps_idno:String):Boolean{
			//check length
		    if (ps_idno == null || ps_idno.length != 10){
		    	return false;
		    }
		    
		    //check char
			var i:int;		    
			var tc_num:String = "0123456789";
			var tc_list:String = "ABCDEFGHJKLMNPQRSTUVXYWZIO";
			if (tc_list.indexOf(ps_idno.substr(0,1)) < 0){
		    	return false;
			}
			for(i=2; i<10; i++) {
				if (tc_num.indexOf(ps_idno.substr(i, 1)) < 0){
		    		return false;
				}
			}
		
			//=== get sum begin ===
			//char 1
			var tn_sum:int = 0;
			tn_sum = tc_list.indexOf(ps_idno.substr(0,1)) + 10;
			tn_sum = Math.floor(tn_sum/10) + (tn_sum%10*9);
		
			//char 2-9
			for(i=1; i<9; i++) {
				tn_sum += int(ps_idno.substr(i,1)) * (9-i);
			}
		
			//char 10
			tn_sum += int(ps_idno.substr(9,1));
			//=== end ===
			
			//return
			return (tn_sum % 10 == 0);
		}		
		
		
		/**
		 * 檢查 Email 是否正確
		 * @param ps_email Email
		 * @return true/false
		 */  
		public static function checkEmail(ps_email:String):Boolean {
			var tn_len:int = ps_email.length;
		    if (ps_email == null || tn_len == 0) {
				return false;
		    }

			//check char content		    
			var ts_email:String = ps_email.toLowerCase();
			var ts_list:String = "abcdefghijklmnopqrstuvwxyz0123456789@.-_";
			for (var i:int=0; i<tn_len; i++){
				if (ts_list.indexOf(ts_email.substr(i,1)) < 0){
					return false;
				}
			}

			//check tail char
			ts_list = "@.-_";
			var ts_tail:String = ts_email.substr(tn_len - 1, 1);
			if (ts_list.indexOf(ts_tail) >= 0){
				return false;				
			}
			
			//other rules
			var tn_mouse:int = ts_email.indexOf("@");
		    if (tn_mouse < 1){
		        return false;
		    //}else if (ts_email.lastIndexOf(".") <= tn_mouse){
		    //    return false;
		    }else if (ts_email.indexOf(".") < 0){
				return false;
		    }else if (ts_email.indexOf("..") >= 0){
				return false;
		    }else{
		    	return true;
		    }
		}

		
		/**
		 * 傳回格式化的日期字串(yyyy/MM/dd), 用於 DataGrid 日期欄位的 labelFunction.
		 * @return 日期字串.(yyyy/MM/dd)
		 */ 
		public static function formatDate(p_row:Object, p_column:GridColumn):String {
			var ts_fid:String = p_column.dataField;
			return Fun.df.format(p_row[ts_fid]);
		}


		/**
		 * 傳回格式化的日期時間字串(yyyy/MM/dd hh:mm), 用於 DataGrid 日期時間欄位的 labelFunction.
		 * @return 日期字串.(yyyy/MM/dd hh:mm)
		 */ 
		public static function formatDT(p_row:Object, p_column:GridColumn):String {
			var ts_fid:String = p_column.dataField;
			return Fun.df2.format(p_row[ts_fid]);
		}

		
		/**
		 * 傳回格式化的日期時間字串(yyyy/MM/dd hh:mm), 傳入秒數, 用於 DataGrid 日期時間欄位的 labelFunction.
		 * @return 日期字串.(yyyy/MM/dd hh:mm)
		 */ 
		public static function formatDT2(p_row:Object, p_column:GridColumn):String {
			//var ts_fid:String = p_column.dataField;
			return (p_row[p_column.dataField] == 0) ? "" : Fun.df2.format(new Date(p_row[p_column.dataField]*1000));
		}

		
		/** 
		 * 傳回 web 主機目前的時間字串.
		 * @param ps_app 目前的程式代號 
		 * @param ps_type 資料種類: DT(datetime), D(date)
		 * @return 時間字串 
		 */ 
		public static function serverDT(ps_app:String, ps_type:String="DT"):String {
	    	var ta_data:Object = Fun.readRows(ps_app, {data:"ServerDT"});
	    	if (catchResult(ta_data, false, true)){
	    		return "";
	    	}else if (ta_data == null){
				return "";
	  		}else if (ps_type == "DT"){	//return datetime string
	    		return ta_data[0].serverDT;
	    	}else{	//return date string
	    		return ta_data[0].serverDT.substr(0,10);
	    	}
		}
			
		
		/**
		 * 排序 2 個變數, 小的在前, 大的在後
		 * @param p_v1 變數1, 可為任意資料型態
		 * @param p_v2 變數2
		 * @return 排序後的陣列
		 */ 
		public static function sort2(p_v1:Object, p_v2:Object):Array{
			var ta_var:Array = [];
			if (p_v1 <= p_v2){
				ta_var[0] = p_v1;
				ta_var[1] = p_v2;
			}else{
				ta_var[0] = p_v2;
				ta_var[1] = p_v1;				
			}
			return ta_var;
		}

		
		/*
		//check startDate, endDate
		//一般用在查詢畫面 , 查詢起迄日期包含某個範圍!!
		public static function checkStartEnd(p_start:Object, p_end:Object, p_start2:Object, p_end2:Object, ps_fname:String, pb_need:Boolean):Boolean{

			//check
			var ts_start:String = Fun.getItem(p_start) as String;
			var ts_end:String = Fun.getItem(p_end) as String;
			if (ts_start == "" && ts_end == ""){
				if (pb_need){
					Fun.msg("E", "["+ps_fname+"] 不可空白 !");
					return false;
				}else{
					return true;
				}				
			}else if (ts_start == ""){
				ts_start = ts_end;
			}else if (ts_end == ""){
				ts_end = ts_start;				
			}else if (ts_start < ts_end){	//注意 !!
				//change
				var ts_mid:String = ts_start;
				ts_start = ts_end;
				ts_end = ts_mid;
			}
			
			Fun.setItem(p_start2, ts_start);
			Fun.setItem(p_end2, ts_end);			
			return true;
		}
		*/
				
		/*		
		public static function formatDate(p_date:Date):String {
	    	df.formatString = Fun2.dateFormat;
			return df.format(p_date);
		}
      	*/
      	

      	
//=== fields or controls function ===
      	
		/** ??
		 * initialize field object 
		 * @param pb_rows false(single) or (true)multiple. 
		 * @param ps_type data type of field. 
		 * @param p_field field object. 
		 */ 
		/* 
		public static function zz_initField(pb_rows:Boolean, ps_type:String, p_field:Object):void {
			//switch (p_field.className){
			switch (ps_type){
				case "D":
					if (pb_rows){
		 				p_field.rendererIsEditor = true;							
					}else{
						//p_field.editable = true;
						//p_field.formatString = Fun2.dateFormat;
						//p_field.labelFunction = Fun.formatDate;
					}										
					break;
			}
		}		
		*/
		

		/** 
		 * 傳回某個欄位的值
		 * @param p_field 欄位變數 
		 * @return 欄位值, 資料型態為 Object, 必須自行轉換.
		 */ 
		public static function getItem(p_field:Object):Object {
			if (p_field == null){
				return null;
			}
			
			switch (p_field.className){
				case "NumericStepper":
					return isNaN(p_field.value) ? null : p_field.value;
				case "Label":
				case "TextInput":
				case "Text1":
				case "Text2":
				case "TextArea":
					return p_field.text;
				case "Num1":
					return (isEmpty(p_field.text)) ? null : Number(p_field.text);
					//return (isEmpty(p_field.text)) ? null : Number(p_field.text);
				case "DateField":
				case "Date1":
					return (p_field.text == null || p_field.text == "") ? "" : p_field.text.substr(0,10);
				case "CheckBox":
					return (p_field.selected) ? 1 : 0;
				case "Check1":
					return (p_field.selected) ? p_field.gYes : p_field.gNo;
				case "ComboBox":
				case "DropDownList":
					return (p_field.selectedItem != null) ? p_field.selectedItem.data : null;
				case "Combo1":
				case "DDL1":
					//2011-5-24 spark combobox 沒有 editable 屬性, 必須使用 dropdownlist !!
					return p_field.value;
					//return p_field.selectedItem.data;
					//return (p_field.editable) ? p_field.text : p_field.value;
				case "Combo1Auto":
					if (Combo1Auto(p_field).bLimit){
						return (p_field.dropdown.selectedItem) ? p_field.dropdown.selectedItem.data : "";
					}else{
						return p_field.text;
					}
				case "RadioGroup1":
					return p_field.value;	//??
				default:
					Fun.msg("E","(Fun.getItem) Form field className = "+p_field.className +" is not supported !");
					return "";
			}
		}
		

		/** 
		 * 設定某個欄位的值
		 * @param p_field 欄位變數 
		 * @param 新的欄位值, 可為字串或數字
		 */ 
		public static function setItem(p_field:Object, p_value:Object):void {
			switch (p_field.className){
				case "NumericStepper":
					p_field.value = int(p_value);
					break;
				case "Label":
				case "TextInput":
				case "Text1":
				case "Text2":
				case "TextArea":
				case "Num1":
				case "DateField":
				case "Date1":
					p_field.text = String(p_value);
					break;
				case "CheckBox":
					p_field.selected = (p_value == 1) ? true : false;
					break;
				case "Check1":
					p_field.selected = (p_value == p_field.gYes) ? true : false;
					break;
				case "ComboBox":
				case "DropDownList":
					comboSetValue(p_field as ComboBox, p_value);
					/*
					var ta_data:Array = p_field.dataProvider.source;
					for (var i:int=0;i<ta_data.length;i++){
						if (ta_data[i].data == p_value){
							p_field.selectedIndex = i;
							break;
						}
					}
					*/
					break;
				case "Combo1":
				case "Combo2":
				case "Combo1Auto":
				case "DDL1":
				case "DDL2":
					//p_field.gValue = p_value;
					p_field.value = p_value;
					p_field.validateNow();
					//p_field.setValue(p_value);
					break;
				case "RadioGroup1":	//??
					p_field.value = p_value;
					break;
				default:
					Fun.msg("E","(Fun.setItem) Form field "+p_field.id+" className = "+p_field.className +" is not supported !");
					break;
			}			
		}
		
		
		/**
		 * 將游標移到某個欄位, //必須在上一層函數中使用 callLater 的方式呼叫此函數.<br/>
		 * //例如: callLater(Fun.setFocus, [null, p_field, true]), 其中第一個參數必須為 null<br/>
		 * //第二個參數欄位變數, 第三個參數表示是否自動選取此欄位的文字
		 * //param p_event 系統必要參數
		 * @param p_field 欄位變數
		 * @param pb_select 是否自動選取此欄位的文字
		 */ 
		//public static function setFocus(p_event:Event, p_field:Object, pb_select:Boolean=false):void{
		public static function focusField(p_field:Object, pb_select:Boolean=false):void{
			var ts_win:String = "'" + ExternalInterface.objectID + "'";
			ExternalInterface.call("eval", "document.getElementById(" + ts_win + ").tabIndex=0");	//need for chrome !!
			ExternalInterface.call("eval", "document.getElementById(" + ts_win + ").focus()");		
			p_field.setFocus();
			
			//select field string
			if (pb_select && p_field.hasOwnProperty("text")){
				try {
					p_field.setSelection(0, p_field.text.length);
				}catch (e:Error){
					//do nothing
				}
			}
		}
		
		/*
		public static function focusField2(p_win:Object, p_field:Object, pb_select:Boolean=false):void{
			Fun.oVar.selectField = pb_select;
			p_win.callLater(Fun.focusField2b, [null, p_field]);
		}

		private static function focusField2b(p_win:Object, p_field:Object, pb_select:Boolean=false):void{
			ExternalInterface.call("eval", "document.getElementById('" + ExternalInterface.objectID + "').focus()");		
			p_field.setFocus();
			
			//select field string
			if (pb_select && p_field.hasOwnProperty("text")){
				try {
					p_field.setSelection(0, p_field.text.length);
				}catch (e:Error){
					//do nothing
				}
			}
		}
		*/

		/**
		 * convert query field value to json string
		 * @param {Array} pa_item, see comQuery.gItems[]
		 * @return data string.
		 */
		 
		/*
		//public static function qFieldToData(pa_item:Array):String{		
		public static function qItemsToStr(pa_item:Array):String{
			//get sql statement
			var ts_value:String;
			var t_field:Object;
			var ts_fid:String;
			var tn_len:int;
			var tn_ary:int = 0; 
			var ta_data:Array = [];
			var ta_relat:Array = [];
			for (var i:int=0;i<pa_item.length;i++){
				t_field = pa_item[i].source;
				
				//skip empty combobox
				//if (t_field.className == "ComboBox" && t_field.selectedLabel == ""){
				//	continue;
				//}
				 
				//skip related field
				tn_len = ta_relat.length;
				var tb_find:Boolean = false; 
				for (var j:int=0;j<tn_len;j++){
					if (t_field == ta_relat[j]){
						tb_find = true;
						break;
					}
				}
				
				if (tb_find){
					continue;
				}
				
				//empty limitation check

				
				//query field and its related
				ts_value = String(Fun.getItem(t_field));	//force to string !! not use xxx as string !!
				if (pa_item[i].relat){
					//add to ta_relat[]
					var t_relat:Object = pa_item[i].relat;
					ta_relat[tn_len] = t_relat;

					//get sql for these two fields					
					var ts_value2:String = String(Fun.getItem(t_relat));
					var tb_empty:Boolean = (ts_value == "null" || ts_value == ""); 
					var tb_empty2:Boolean = (ts_value2 == "null" || ts_value2 == ""); 
					if (tb_empty && tb_empty2){
						continue;
					}else if (!tb_empty && !tb_empty2){
						ts_value = ts_value+","+ts_value2
					}else if (!tb_empty2){
						ts_value = ts_value2						
					}
				}else{
					//get field value
					if (ts_value == "null" || ts_value == ""){
						continue;
					}
				}
								
				ta_data[tn_ary] = [
					t_field.id, 
					pa_item[i].dataType,
					ts_value
				]
				tn_ary++;
			}
			
			return JSON.encode(ta_data);
		}
		*/
		

		/**
		 * 檢查欄位清單陣列是否全部空白
		 * @param pa_field 欄位清單陣列
		 * @return true(全部空白)/false
		 */
		public static function isFieldsEmpty(pa_field:Array):Boolean{
			for (var i:int=0;i<pa_field.length;i++){
				if (!Fun.isFieldEmpty(pa_field[i])){
					return false;
				}
			}
			
			//case of not found.
			return true;
		}			


		/**
		 * 檢查某個欄位是否空白
		 * @param p_field 欄位變數
		 * @return true(空白)/false
		 */
		public static function isFieldEmpty(p_field:Object):Boolean{
			//return Fun.isEmpty(String(Fun.getItem(p_field)));
			return Fun.isEmpty(Fun.getItem(p_field));
		}			


		/**
		 * 傳回欄位清單陣列中第一個空白值的欄位序號
		 * @param pa_field 欄位清單陣列
		 * @return 欄位序號, 如果找不到則傳回 -1
		 */
		public static function getEmptyField(pa_field:Array):int{
			for (var i:int=0;i<pa_field.length;i++){
				if (Fun.isFieldEmpty(pa_field[i])){
					return i;
				}
			}
			
			//case of not found.
			return -1;
		}			

		
		/**
		 * 2012-6-22 考慮 halo DateField (覆寫此函數) 
		 * 初始化 DW2 的編輯欄位(在初始化 CheckBox2, ComboBox2, DateField2, TextInput2 時呼叫此函數)<br/>
		 * 注意: 編輯欄位最多只能有一層 container! 使用這種方式來初始化 DW2 欄位比較簡單, 所以將此函數放在 Fun.as
		 * @param p_field 編輯欄位
		 * @return 編輯欄位相關的資訊 Object, 其欄位包含: dw2, col, fid, box  
		 */ 
		public static function initDW2Field(p_field:Object):Object{
			//2012-6-22: spark 無 DateField元件, 只能使用 halo.DateField, container的結構不同, 必須特別處理 !!
			if (p_field.className == "Date2"){	
				return initDW2DateField(p_field);
			}
			
			
			//spark 元件的寫法 , 最多往上查10層 owner
			var t_owner:Object = p_field;
			var tb_find:Boolean = false;
			for (var i:int=0; i<10; i++){
				if (!t_owner.hasOwnProperty("owner")){
					break;
				}
				
				t_owner = t_owner.owner;
				if (t_owner.hasOwnProperty("column")){
					tb_find = true;
					break;
				}
			}
			if (!tb_find){
				Fun.msg("E", "找不到 DW2 的欄位!");
				return null;
			}
			
			var t_col:Object = t_owner.column;	//gridColumn
			
			//t_col.rendererIsEditor = true;
			t_col.rendererIsEditable = true;
			var t_grid:Object = t_owner.grid.dataGrid;
			return {
				dw2: (t_grid.hasOwnProperty("gDW2")) ? t_grid.gDW2 : null,	//DataGrid2.gDW2 
					col: t_col,				//dataGridColumn
					fid: t_col.dataField,	//欄位代號
					//box: tb_box			//has box container or not
					row: t_owner			//用來存取資料, 不能直接指到 t_owner.data, 因為這個時候 .data 還不存在, 會得到 null !!
			};			
		}
		
		
		//2012-6-22 for Date2 欄位
		private static function initDW2DateField(p_field:Object):Object{
			if (Fun.isEmpty(p_field.id)){
				Fun.msg("E", "Date2 欄位必須設定 id 為 dataField.");
				return null;
			}
			
			var ts_id:String = p_field.id;
			var t_owner:Object = p_field;
			var t_row:Object;	//最後一個包含 data 屬性的 container, 做為欄位 data 的來源
			for (var i:int=0; i<10; i++){
				if (!t_owner.hasOwnProperty("owner")){
					return null;
				}
				
				t_owner = t_owner.owner;
				if (t_owner.hasOwnProperty("data")){
					t_row = t_owner;
				}else if (t_owner.hasOwnProperty("className") && t_owner.className == "DG2"){
					//var t_grid:Object = t_owner.grid;
					var ta_col:Array = t_owner.grid.columns.source; 
					for (var j:int=0; j<ta_col.length; j++){
						if (ta_col[j].dataField == ts_id){
							var t_grid:Object = ta_col[j].grid.dataGrid;
							return {
								dw2: (t_grid.hasOwnProperty("gDW2")) ? t_grid.gDW2 : null,	//DataGrid2.gDW2 
									col: ta_col[j],
									fid: ta_col[j].dataField,	//欄位代號
									row: t_row			//用來存取資料, 不能直接指到 t_owner.data, 因為這個時候 .data 還不存在, 會得到 null !!
							};
						}
					}
					
					break;
				}
			}			
			
			//case of not found
			Fun.msg("E", "找不到 Date2 欄位 (" + ts_id + ")");
			return null;
		}
		
				
		/**
		 * 改變物件陣列的語系.
		 * @param pa_item 物件陣列, 執行時依序檢查物件的屬性: label, text 
		 * @param ps_file resource 檔案名稱, 不含副檔名
		 */ 
		/*
		public static function zz_chgLangs(pa_item:Array, ps_file:String):void{
			var ts_prop:String;
			var ts_key:String;
			var ts_value:String;
			for (var i:int=0;i<pa_item.length;i++){
				ts_key = pa_item[i].id;	
				//??			
				if (ts_key.substr(0,1) == "_"){
					ts_key = ts_key.substr(1);					
				}
				ts_prop = (pa_item[i].hasOwnProperty("label")) ? "label" : "text";
				//ts_value = ResourceManager.getInstance().getString(ps_res, ts_key);
				ts_value = Fun.resStr(ts_key, ps_file);
				if (ts_value != null){
					pa_item[i][ts_prop] = ts_value;
				} 
			}
		}
		*/
		
		
		/**
		 * 改變物件陣列的語系.
		 * @param pa_item 物件陣列, 執行時依序檢查物件的屬性: label, text 
		 * @param ps_file resource 檔案名稱, 不含副檔名
		 */ 
		public static function setLang(pa_item:Array, p_lang:Object):void{
			var ts_prop:String;
			var ts_id:String;
			//var ts_value:String;
			for (var i:int=0;i<pa_item.length;i++){
				ts_id = pa_item[i].id;
				/*
				//??			
				if (ts_key.substr(0,1) == "_"){
					ts_key = ts_key.substr(1);					
				}
				*/
				ts_prop = (pa_item[i].hasOwnProperty("label")) ? "label" : "text";
				//ts_value = ResourceManager.getInstance().getString(ps_res, ts_key);
				//ts_value = Fun.resStr(ts_key, ps_file);
				if (p_lang[ts_id] != null){
					pa_item[i][ts_prop] = p_lang[ts_id];
				} 
			}
		}
		
		
		/**
		 * 傳回某個語系的 resource 字串
		 * @param ps_key 欄位名稱
		 * @param ps_file resorce 設定檔名稱, 預設為 Setting (系統公用設定檔)
		 * @return 欄位值字串
		 */ 
		/*
		public static function resStr(ps_fid:String, ps_file:String="Setting"):String{
			return ResourceManager.getInstance().getString(ps_file, ps_fid);			
		}
		*/
		

		/**
		 * 交換 2 個物件的狀態.(visible, index), 如果物件是否顯示在畫面上是由其他因素控制時, 會用到這個函數.
		 * @param p_item1 物件1 
		 * @param p_item2 物件2 
		 * @param pb_index 是否交換 childIndex. 
		 */ 
		public static function swap2Item(p_item1:Object, p_item2:Object, pb_index:Boolean=true):void{
			var t_up:Container = p_item1.parent as Container;
			var tn_index1:int = t_up.getChildIndex(p_item1 as DisplayObject);
			var tn_index2:int = t_up.getChildIndex(p_item2 as DisplayObject);
			var tb_vis1:Boolean = p_item1.visible;
			var tb_vis2:Boolean = p_item2.visible;
			
			if (pb_index){
				t_up.setChildIndex(p_item1 as DisplayObject, tn_index2);
				t_up.setChildIndex(p_item2 as DisplayObject, tn_index1);
			}
			
			p_item1.visible = tb_vis2;
			p_item2.visible = tb_vis1;
			p_item1.validateNow();
			p_item2.validateNow();
			t_up.validateNow();
		}
				
				
					
		/**
		 * 更新一筆資料的狀態到 DataGrid, 用於1對多對多編輯畫面.
		 * @param p_grid DataGrid 物件
		 * @param ps_fun 功能代號: C(新增)/U(修改)/D(刪除)
		 * @param p_row 新資料 Object, 如果 ps_fun = "D", 則不需此參數
		 * @param 錯誤訊息 if any.
		 */ 
		public static function rowToGrid(p_grid:DataGrid, ps_fun:String, p_row:Object=null):String{
			//check
			if (p_row == null && (ps_fun == "C" || ps_fun == "U")){
				return "p_row can not be null !";
			}
			
			var t_ds:ArrayCollection = p_grid.dataProvider as ArrayCollection;
			var tn_row:int;
			switch (ps_fun){
				case "C":	//create					
					t_ds.addItem(p_row);	
					break;
				case "U":	//update
					var t_row:Object = p_grid.selectedItem; 
					for (var i:String in p_row){
						t_row[i] = p_row[i];
					}
					//i_ds.refresh()		//update row info.
					Fun.refreshGrid(p_grid);
					break;
				case "D":	//delete
					tn_row = p_grid.selectedIndex; 
					t_ds.removeItemAt(tn_row);									
					break;
				default:
					return "Parameter of p_fun is wrong !";
			}
			
			return "";	
		}			


		/**
		 * 處理後端傳回來的結果.
		 * @param p_result: 傳回結果 Object.
		 * @param pb_catchEmpty 是否處理無資料的情形
		 * @param pb_catchError: 是否處理錯誤的情形
		 * @return true(有處理)/false(無)
		 */  		
		//public static function catchResult(p_result:Object, pb_catchError:Boolean=true, pb_catchEmpty:Boolean=true, p_win:Object=null):Boolean {
		public static function catchResult(p_result:Object, pb_catchEmpty:Boolean=true, pb_catchError:Boolean=true):Boolean {
			if (p_result == null){
				if (pb_catchEmpty){
	    			//Fun.msg("I", "此條件下無任何資料  !");
	    			Fun.msg("I", Fun.R.noRow);
	    			return true;					
				}
			}else if (p_result.hasOwnProperty("error")){				
				if (p_result.error == "S01"){		//open re-login when session off.
					Fun.openRelogin();
	    			return true;
		    	}else if(pb_catchError){
					//如果裡面不包含空白, 而且多國語系有這個欄位, 則顯示對應的內容, 否則直接顯示字串
					var ts_error:String = p_result.error;
					if (ts_error.indexOf(" ") < 0 && Fun.R.hasOwnProperty(ts_error)){
						ts_error = Fun.R[ts_error];
					}
	    			Fun.msg("S", ts_error);	//server error msg
					
	    			return true;
		  		}
			}
					    
			//else case 		    
			return false;		    						
		}


		/**
		 * 開啟 Relogin 畫面, 用於 session 失效時.
		 * @param pf_callback 重新登入成功後執行的函數, 可以為 null
		 */ 
		public static function openRelogin(pf_callback:Function=null):void {
			if (Fun.wRelogin == null){
				Fun.wRelogin = new winRelogin();
				Fun.openPopup(Fun.wRelogin);
			}else{
				Fun.wRelogin.visible = true;
			}
			
			Fun.wRelogin.fCallback = pf_callback;
		}


		/**
		 * 更新 DataGrid, 如果自行設定其 dataProvider Array 時, 必須呼叫此函數
		 * @param p_grid DataGrid 物件
		 * @param pn_row updated row, 如果不為 -1, 則會更新整個 datagrid.
		 */ 
		public static function refreshGrid(p_grid:DataGrid, pn_row:int=-1):void {
			var t_ac:ArrayCollection = p_grid.dataProvider as ArrayCollection;
			
			if (pn_row == -1){
				//temp change
				//var tn_pos:int = p_grid.verticalScrollPosition;
				var tn_pos:int = 0;		
				
				t_ac.refresh();
				//p_grid.validateNow();		//did not work !!
				
				if (tn_pos >= 0){
					//temp remark
					//p_grid.verticalScrollPosition = tn_pos;
				}
			}else{
				var t_row:Object = t_ac[pn_row];
				t_ac.itemUpdated(t_row);
			}
		}
		
		//Fun.refreshGrid(oConfig.grid);

		/**
		 * 把 DataGrid 目前這筆資料向下移動, 必須使用 ArrayCollection, 否則有 filterFunction 會不正確 !!
		 * @param p_grid DataGrid 物件
		 * @param pn_move 1(向下移動一筆), -1(向上)
		 * @return true(move)/false(not move)
		 */ 
		public static function moveGridRow(p_grid:DataGrid, pn_move:int):Boolean{
			var tn_old:int = p_grid.selectedIndex;
			return swapACRow(p_grid.dataProvider as ArrayCollection, tn_old, tn_old + pn_move);

			/*
			var tn_old:int = p_grid.selectedIndex;
			if (tn_old < 0){
				Fun.msg("E", Fun.R.selectRow);
				return false;
			}
			
			var tn_new:int = tn_old + pn_move;
			var tac_row:ArrayCollection = ArrayCollection(p_grid.dataProvider);
			if (tn_new < 0 || tn_new >= tac_row.length){
				return false;
			}
			
			var t_old:Object = tac_row.getItemAt(tn_old);
			var t_new:Object = tac_row.getItemAt(tn_new);
			if (pn_move > 0){	//執行先後順序必須正確, 否則filterFunction存在時無作用!!
				tac_row.setItemAt(t_old, tn_new);
				tac_row.setItemAt(t_new, tn_old);
			}else{
				tac_row.setItemAt(t_new, tn_old);				
				tac_row.setItemAt(t_old, tn_new);
			}
			//tac_row.refresh();
			
			//update grid, flex bug, use selectedIndices instead of selectedIndex !!  
			//p_grid.selectedIndex = tn_new;
			Fun.refreshGrid(p_grid);
			return true;
			//temp remark
	    	//p_grid.selectedIndices = [tn_new];
			*/
		}

		
		public static function swapACRow(p_ac:ArrayCollection, pn_oldRow:int, pn_newRow:int):Boolean{
			//var tn_old:int = pn_nowRow;
			if (pn_oldRow < 0){
				Fun.msg("E", Fun.R.selectRow);
				return false;
			}
			
			//var tn_new:int = pn_oldRow + pn_move;
			//var tac_row:ArrayCollection = ArrayCollection(p_grid.dataProvider);
			if (pn_newRow < 0 || pn_newRow >= p_ac.length){
				return false;
			}
			
			//change
			/*
			var t_row:Object = p_ac[tn_old];
			p_ac[tn_old] = p_ac[tn_new];
			p_ac[tn_new] = t_row;
			*/
			var t_old:Object = p_ac.getItemAt(pn_oldRow);
			var t_new:Object = p_ac.getItemAt(pn_newRow);
			if (pn_oldRow < pn_newRow){	//執行先後順序必須正確, 否則filterFunction存在時無作用!!
				p_ac.setItemAt(t_old, pn_newRow);
				p_ac.setItemAt(t_new, pn_oldRow);
			}else{
				p_ac.setItemAt(t_new, pn_oldRow);				
				p_ac.setItemAt(t_old, pn_newRow);
			}
			
			//update grid, flex bug, use selectedIndices instead of selectedIndex !!  
			//p_grid.selectedIndex = tn_new;
			//Fun.refreshGrid(p_grid);
			p_ac.refresh();
			return true;
			//temp remark
			//p_grid.selectedIndices = [tn_new];	
		}

		
		/**
		 * 設定 Browser 上的 title, 使用 iFrame 時 title 會不見, 必須呼叫此函數.
		 * @param ps_title title string
		 */
		public static function setAppTitle(ps_title:String):void {
		    //get an instance of the browser manager
		    var t_bm:IBrowserManager = BrowserManager.getInstance();
		
		    //initialize the browser manager
		    t_bm.init();
		
		    //set the page title
		    t_bm.setTitle(ps_title);
		}
		

		/**
		 * 把Object的內容加到另一個Object變數
		 * @param p_data 新加入的Object
		 * @param p_row 原本的Object, //不可為null.(by ref)
		 */ 
		public static function rowAddData(p_row:Object, p_data:Object):void{
			if (p_row == null || p_data == null){
				return;
			}
			
			for (var i:String in p_data){
				p_row[i] = p_data[i];
			}			
		}


		//只顯示 Yes 和空白
		public static function yesEmpty(p_row:Object, p_column:GridColumn):String{
			return (String(p_row[p_column.dataField]) == "1") ? Fun.R.yes : "";
		}

		//convert zero to empty
		public static function zeroToEmpty(p_row:Object, p_column:GridColumn):String{
			return (String(p_row[p_column.dataField]) == "0") ? "" : p_row[p_column.dataField];
		}

		/**
		 * 傳回功能代號名稱, 考慮多國語
		 * @param ps_fun 功能代號
		 * @return 功能代號名稱
		 */ 
		public static function getFunName(ps_fun:String):String{
			var ts_key:String;
			switch (ps_fun){
				case "C":
					ts_key = "create";
					break;
				case "R":
					ts_key = "read";
					break;
				case "U":
					ts_key = "update";
					break;
				case "D":
					ts_key = "delete2";
					break;
				case "P":
					ts_key = "print";
					break;
				case "E":
					ts_key = "export";
					break;
				case "V":
					ts_key = "view";
					break;
				default:				
					return "";	
			}
			
			//temp add
			//var ts_name:String = Fun.R[ts_key];
			 
 			return Fun.R[ts_key];
		}


		/* Flex SDK 4 only !!
		import flash.display.Bitmap;
		import flash.display.BitmapData;
		import flash.net.FileReference;
		import flash.utils.ByteArray;
		import flash.geom.Matrix;
		
		public static function saveJpg(p_canvas:Canvas):void {
			var t_file:FileReference = new FileReference();
			var t_bmpData:BitmapData = new BitmapData(p_canvas.width, p_canvas.height);
			t_bmpData.draw(p_canvas,new Matrix());
			var t_bmp:Bitmap = new Bitmap(t_bmpData);
			var t_jpg:JPEGEncoder = new JPEGEncoder();
			var t_ba:ByteArray = t_jpg.encode(t_bmpData);
			//t_file.save(t_ba, myMessageTxt.text + &apos;.jpg&apos;);
			t_file.save(t_ba, "test.jpg");
		}
		*/
		
		
		/**
		 * 設定 Browser 上的 title, 使用 iFrame 時 title 會不見, 必須呼叫此函數.
		 * @param ps_title title string
		 * @param pb_dynamicKey 是否使用動態key
		 * @return object.
		 */
		public static function getLang(ps_file:String):Object{
		    //return Fun.sync("", "_GetLang", {file:ps_file, lang:Fun2.sLang}, true, false);
			return Fun.sync("", Fun.sService2, {fun:"GetLang", file:ps_file}, true);
		}

		
		/**
		 * 呼叫郵件系統
		 * @param ps_email email 信箱
		 */
		public static function openMailClient(ps_box:String):void{
			var t_url:URLRequest = new URLRequest("mailto:"+ps_box);
			navigateToURL(t_url, "_blank");
		}

		
		/**
		 * 傳送郵件, will show error msg if any.
		 * @param ps_app app id
		 * @param p_mail mail object
		 */
		public static function sendMail(ps_app:String, p_mail:Object):void{
			p_mail.fun = "Mail";
			var t_data:Object = Fun.sync(ps_app, Fun.sService, p_mail, true);
			Fun.catchResult(t_data, false, true);
		}

		
		//private static var in_loop:int; 
		/**
		 * 上傳檔案, 呼叫後端 _UpLoad.ashx
		 * @param pa_file 包含元素: file
		 */
		//public static function upLoadFiles(ps_app:String, pa_file:Array):void{
		//}
		
		
		/**
		 * 離開系統
		 */
		public static function exitSys():void{
			var ts_script:String = "window.opener='x'; window.close();";
			Fun.runJS(ts_script);
		}

		
		//Fun2.openAddWin() 的 callback function.
		public static function openAddWin2(p_data:Object):void{
			var t_fn:Function = Fun.oVar.openAddWin_fn;
			var t_keyFid:Object = Fun.oVar.openAddWin_key;
			Fun.oVar.openAddWin_fn = null;
			Fun.oVar.openAddWin_key = null;
			
			if (t_fn != null){
				//var t_win:Object = p_data.win;
				var t_value:Object = Fun.getItem(t_keyFid);
				t_fn(t_value);
			}
		}
		
		
		//
		public static function gridSelectAll(p_grid:DataGrid, pb_select:Boolean, ps_fname:String="selected"):void{
			//temp change
			//if (p_grid.dataProvider == null || p_grid.dataProvider.source == null){
			if (p_grid.dataProvider == null){
				return;
			}
						
			/* temp remark
			var ta_row:Array = p_grid.dataProvider.source as Array;
			var tn_select:int = (pb_select) ? 1 : 0;
			for (var i:int=0; i<ta_row.length; i++){
				ta_row[i][ps_fname] = tn_select; 
			}
			*/
			
			Fun.refreshGrid(p_grid);
		}
		
		
		/**
		 * 儲存資料前, 調整簽核流程的資料, 一般在 comEdit fWhenSave()中呼叫, 
		 * 無法在此程式決定郵件要送給誰, 必須在上層程式處理, 有3種情形:
		 * (1).簽核作業(wfJobStatus != 'B')
		 * (2).申請者修改, 但不重送(wfJobStatus = 'B') 
		 * (3).申請者重送
		 * @param pb_reSend {boolean} 是否重送
		 * @param p_data {Object} 傳入參數, 包含以下欄位:
		 *   reSend, dwSign, dwLeave, aFlow, fileNext, fileBack, fileAgree, askerId(申請者代號) //wfNowLevel, wfTotalLevel, wfJobStatus, wfSignStatus,   
		 * 傳回 :
		 * (1)null: 表示不傳 mail
		 * (2)p_data.error: 表示錯誤
		 * (3)object: will send info mail, 包含以下欄位: template(mail template file name), toStaff
		 */ 	
		//設定 wfNowLevel
		public static function adjustFlowRow(pb_reSend:Boolean, p_data:Object):Object{
			//var t_send:Object = null;
			var ta_flow:Array = p_data.aFlow as Array;			
			var tdw_sign:DW = DW(p_data.dwSign);
			var tdw_leave:DW = DW(p_data.dwLeave);
			var ts_wfJobStatus:String = tdw_leave.getItemByFid("wfJobStatus") as String;
			
			//check
			//var t_send:Object = {leaveSeq: is_leaveSeq};		//傳到後端
			//var ta_flow:Array = grid_flow.dataProvider.source as Array;
			//var t_flow:Object;
			//var ts_jobStatus:String = Fun.getItem(wfJobStatus) as String;
			var i:int;
			if (pb_reSend){	//重送
				//找原退回假單的簽核流程資料, and 通知簽核者
				for (i=0; i<ta_flow.length; i++){
					if (ta_flow[i].wfSignStatus == "B"){
						tdw_leave.setItemByFid("wfJobStatus", "C", true);	//改為審核中
						
						tdw_sign.setItemByFid("signSeq", ta_flow[i].signSeq, false);	//此為key field, 不需要設定 dirty flag
						tdw_sign.setItemByFid("wfSignStatus", "0", true);	//改為處理中
						
						return {
							template: p_data.fileNext,
							toStaff: ta_flow[i].signerId,
							procName: ta_flow[i].procName,
							signerName: ST.cut(ta_flow[i].signerName, "(")
						};
						//return t_send;
						//t_send.template = "LeaveToSigner";
						//t_flow = ta_flow[i]; 
						//break;
					}
				}
			}else if (ts_wfJobStatus == "B"){	//修改退回, 不傳 mail
				return null;
				/*
				return {
					template: p_data.fileBack,
					toStaff: p_data.askerId
				};
				*/
			}else{	//簽核作業
				var ts_wfSignStatus:String = tdw_sign.getItemByFid("wfSignStatus") as String
				if (ts_wfSignStatus == "N" || ts_wfSignStatus == "B"){	//不同意或退回
					/*
					if (Fun.isFieldEmpty(comment)){
						Fun.msg("E", "簽核說明不可空白。");
						callLater(Fun.setFocus, [null, comment, false]);
						return false;
					}
					*/
					
					//更新 wfJobStatus !!
					tdw_leave.setItemByFid("wfJobStatus", ts_wfSignStatus, true);
					
					return {
						template: p_data.fileBack,
						toStaff: p_data.askerId
					};
					//通知申請者
					//t_send.template = "LeaveBack";
					//t_send.toStaff = Fun.getItem(staffId);
					
				}else if (ts_wfSignStatus == "Y"){	//同意
					//更新 wfJobStatus 或 wfNowLevel !!
					var tn_nowLevel:int = tdw_leave.getItemByFid("wfNowLevel") as int;
					if (tn_nowLevel == tdw_leave.getItemByFid("wfTotalLevel") as int){	//全部簽核已同意
						tdw_leave.setItemByFid("wfJobStatus", ts_wfSignStatus, true);
						
						//通知申請者
						return {
							template: p_data.fileAgree,
							toStaff: p_data.askerId							
						};
					}else{
						//通知下一關簽核人員
						tn_nowLevel++;
						for (i=0; i<ta_flow.length; i++){
							if (ta_flow[i].levelNo == tn_nowLevel){
								tdw_leave.setItemByFid("wfNowLevel", tn_nowLevel, true);						
								if (tdw_leave.getItemByFid("wfJobStatus") as String == "A"){
									tdw_leave.setItemByFid("wfJobStatus", "C", true);		//將狀態由 "申請中" 改為 "審核中"
								}
								
								return {
									template: p_data.fileNext,
									toStaff: ta_flow[i].signerId,						
									procName: ta_flow[i].procName,
									signerName: ST.cut(ta_flow[i].signerName, "(")
								};
								//t_send.template = "LeaveToSigner";
								//t_flow = ta_flow[i]; 
								//t_send.toStaff = ta_flow[i].signerId;			
								//break;
							}
						}
						
					}				
				}
			}

			/*		
			if (t_flow != null){
				//get from sign grid
				t_send.toStaff = t_flow.signerId;
				t_send.signerName = t_flow.signerName;
				t_send.procName = t_flow.procName;
			}
			
			//get from leave
			t_send.staffName = staffId.selectedItem.label;
			t_send.leaveName = leaveType.selectedItem.label;
			t_send.startEnd = String(Fun.getItem(startDate)).substr(0, 16) + " ~ " + String(Fun.getItem(endDate)).substr(0, 16);
			t_send.hoursToDH = Fun2.hourToDH2(Fun.getItem(sumHours) as int);
			t_send.wfJobStatusName = wfJobStatus.selectedItem.label;
			t_send.sysUrl = Fun.sDirRoot + "Main.html";
			
			//add for send to server side !!
			//edit_1.addSend(t_send);
			*/
			
			//case else
			return {error: "call Fun.adjustFlowRow() failed."};
		}	
		
		
		public static function comboSetValue(p_combo:Object,  p_value:Object) : void {
			if (p_combo.dataProvider == null)
				return;
			else if (p_value == null){
				p_combo.selectedIndex = -1;
				return;
			}
			
			// case of normal
			// 不可直接使用 dataProvider (flex 4.5)
			//var ta_row:Array = ArrayList(p_combo.dataProvider).source as Array;
			var ta_row:Array = Object(p_combo.dataProvider).source as Array;
			for (var i:int=0; i<ta_row.length; i++){
				if (ta_row[i] != null && p_value == ta_row[i].data){
					p_combo.selectedIndex = i; 
					return;
				}
			}
		}
		
		
		//get parent titleWindow or application
		public static function parentWin(p_this:Object):Object {
			var t_this:Object = p_this;
			for (var i:int=0; i<10; i++){
				//t_this = t_this.owner;
				t_this = t_this.parent;
				if (t_this == null){
					return null;
				}else if(t_this.hasOwnProperty("title") || t_this.hasOwnProperty("pageTitle")){
					return t_this;
				}				
			}	
			
			//case of not found
			return null;
		}
		

		/**
		 * 執行 update DB 的 sql, sql 的內容必須建在後端 Fun2
		 * @param ps_app {String} 程式代號
		 * //param ps_type {String} 對應到後端 Fun2.getSql() data="UpdateDB", 的 type 段落
		 * @param p_data {Object} 傳到後端的參數, 裡面必須有type 變數(不可有 fun, data)
		 * return updated rows
		 */ 
		//public static function updateDB(ps_app:String, ps_type:String, p_data:Object=null):int{
		public static function updateDB(ps_app:String, p_data:Object=null):int{
			if (p_data == null)
				p_data = {};

			if (!p_data.hasOwnProperty("type")){
				Fun.msg("E", "p_data['type'] can not be null.");
				return 0;
			}
			
			p_data.fun = "UpdateDB";
			//p_data.type = ps_type;			
			return int(Fun.sync(ps_app, Fun.sService, p_data));
		}

		
		/**
		 * generate excel by .xls3 file
		 * p_data has var: file and input para
		 */ 
		public static function genExcel3(ps_app:String, p_data:Object):void {
			//Fun.sync(ps_app, Fun.sService, p_data);
			if (p_data == null)
				return;
			
			p_data.app = ps_app;
			p_data.fun = "GenExcel";
			var t_request:URLRequest = new URLRequest(Fun.sDirApp + Fun.sService + Fun2.csAppExt);
			var t_data:URLVariables = new URLVariables();
			t_data.data = encode(p_data);
			t_request.data = t_data;
			navigateToURL(t_request, "_blank");								
		}
		

		/**
		 * load image file into image control
		 * @param p_image image control.
		 * @param ps_url image url.
		 * @param pb_emptyMsg 圖片不存在時是否顯示訊息
		 * @param pf_ok 載入成功之後要執行的函數
		 * @param pf_fail 如果載入失敗要執行的函數
		 * //param pb_autoSize auto size to original(default).
		 */ 
		//public static function loadImage(p_image:Image, ps_url:String, pb_autoSize:Boolean=true):void {
		//public static function loadImage(p_image:Image, ps_url:String, pb_emptyMsg:Boolean=false, pf_ok:Function=null, pf_fail:Function=null):void {
		public static function loadImage(p_image:Object, ps_url:String, pb_emptyMsg:Boolean=false, pf_ok:Function=null, pf_fail:Function=null):void {
			/*
			if (oImage == null){
				oImage = {
					loader: new Loader()
				};
			}
			oImage.msg = pb_emptyMsg;
			oImage.image = p_image;			
			oImage.loader.addEventListener(Event.COMPLETE, loadImageOk);
			oImage.loader.addEventListener(IOErrorEvent.IO_ERROR, loadImageFail);
			oImage.loader.load(new URLRequest(encodeURI(ps_url))); 
			*/
			
			var t_loader:Loader = new Loader();
			t_loader.contentLoaderInfo.addEventListener(Event.COMPLETE, function(e:Event):void{
				var t_image:Object = e.currentTarget.content; 
				p_image.source = t_image;
				if (pf_ok != null)
					pf_ok(t_image);
			});
			t_loader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, function(e:Event):void{
				if (pb_emptyMsg)
					Fun.msg("I", R.notExist);
				if (pf_fail != null)
					pf_fail();
			});
			
			t_loader.load(new URLRequest(encodeURI(ps_url))); 
		}

		/*
		public static var oImage:Object;
		private static function loadImageOk(p_event:Event):void{
			
			Image(oImage.image).source = Object(p_event.currentTarget).content;
			oImage.loader.removeEventListener(Event.COMPLETE, loadImageOk);
			oImage.loader.removeEventListener(IOErrorEvent.IO_ERROR, loadImageFail);
						
			//設定長寬為原始圖檔的長寬, 不要設定 p_image 的 width, height 即可
			//if (pb_autoSize){
			//	p_image.width = t_image.width;
			//	p_image.height = t_image.height;
			//}
		}
				
		private static function loadImageFail(event:Event=null):void{
			oImage.loader.removeEventListener(Event.COMPLETE, loadImageOk);
			oImage.loader.removeEventListener(IOErrorEvent.IO_ERROR, loadImageFail);
			
			if (oImage.msg)
				Fun.msg("I", R.notExist);
		}
		*/
		
		
		//set session
		public static function setSession(ps_app:String, ps_id:String, ps_value:Object):void{
			var t_data:Object = {
				fun: "SetSession", 
				type: ps_id, 
				value: ps_value
			};
			t_data = sync(ps_app, Fun.sService, t_data, true);   
			Fun.catchResult(t_data, false, true);
		}
		
		
		//md5 encrypt
		public static function md5(ps_text:String):String{
			var t_md5:MD5 = new MD5();
			var ts_code:String = t_md5.encrypt(ps_text);
			t_md5 = null;
			return ts_code;
		}
		
		/*
		//=== for flex 4.6 ===
		public static function jsonToStr(p_data:Object):String{
			return JSON.stringify(p_data);
		}		
		public static function strToJson(ps_data:String):Object{
			return JSON.parse(ps_data);
		}
		*/
		
		public static function showControl(p_obj:Object, pb_show:Boolean=true):void{
			if (p_obj.visible != pb_show){
				p_obj.visible = pb_show;
				p_obj.includeInLayout = pb_show;
			}
		}
		
   	}//class
}//package

