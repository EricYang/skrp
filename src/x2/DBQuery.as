package x2
{
	/**
	 * create by Ken Cheng: 2013-01-29
	 */	  
	// public function
	//=======================
	//need for Flex 4.5, 4.6 does not need !!
	//import com.json.*;
	//=======================
	
	import com.Rijndael;
	import com.json.*;
	
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
	
	public class DBQuery
	{
		public static var sDebugPara:String = "XDEBUG_SESSION_START=netbeans-xdebug";	//debug parameter		
//***********************************************************************************************************************************
		/** 
		 * 同步執行後端程式 (使用 xmlHttpRequest).
		 * @param ps_app 目前的程式代號 
		 * //param ps_serverApp 後端的程式代號, 不含副檔名, 如果空白則使用 ps_app. 
		 * @param ps_serverApp 後端的程式代號, 不含副檔名, 如果空白則使用 Fun.csService 
		 * @param p_data 要傳送到後端的 JsonObject 參數. 
		 * @param pb_object 是否將執行結果解碼. 
		 * @param pb_encrypt 是否加密, 呼叫 _Service2 時，不加密　
		 * @return 字串(不解碼), 或是 Object(解碼)
		 * Ex:
		 *    var ps_app:String = "BookManaE";
		 *	  var ps_serverApp:String ="DBQuery"; 
		 *	  var p_data:Object = new Object();
		 *		  p_data.bookNo=Fun.getItem(bookNo);
		 *		  p_data.SQLCommand="BookManaE_Q1";
		 *	  var t_data:Object=DBQuery.syncQueryData(ps_app,ps_serverApp,p_data);
		 */ 
		public static function syncQueryData(ps_app:String,ps_serverApp:String="", p_data:Object=null, pb_object:Boolean=true):Object 
		{			
			if (p_data == null){
				p_data = {};
			}
			if (ps_app != ""){
				p_data.app = ps_app;
			}
			
			var ts_path:String = ExternalInterface.call("window.location.href.toString");
			var tn_pos:int = ts_path.lastIndexOf("/");
			ts_path = ts_path.substr(0,tn_pos);
			if (ts_path.indexOf("bin-") >= 0){		//both bin-debug & bin-release
				tn_pos = ts_path.lastIndexOf("/");
				ts_path = ts_path.substr(0,tn_pos);
			}else{
				sDebugPara = "";
			}
			
			var sDirRoot:String = ts_path + "/";
			var sDirApp:String = sDirRoot + "_app/";				
			ps_serverApp = serverApp(ps_app, ps_serverApp);
					
			var ts_data:String = (p_data == {}) ? "" : ("data=" +com.json.JSON.encode(p_data));						
			
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
		            "t_http.open('POST','"+ sDirApp + ps_serverApp + "',false);"+
			        "t_http.setRequestHeader('Content-Length',"+ts_data.length.toString()+");"+
        			"t_http.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=utf-8');"+
		            "t_http.send('"+ ts_data + "');"+
		            "return t_http.responseText;" +            
		        "}catch(e3){" +
		            "alert(e3)" +
		        "}";
		    
		    ts_ajax = runJS(ts_ajax);		    
		    if (!pb_object)
		    {
		    	return (ts_ajax as Object);
		    }
		    else if (ts_ajax == "" || ts_ajax == "[]" || ts_ajax == "_" || ts_ajax == "\r\n_")
		    {
		    	return null;
		    }
		    
		    var t_data:Object = null;
		    try 
		    {
			    t_data = toJson(ts_ajax, true);
		    }
		    catch (e:Error)
		    {
		    	t_data = {error: ts_ajax};
		    }
		    
		    return t_data;
		}	
//***********************************************************************************************************************************
		//get server app name
		private static function serverApp(ps_app:String, ps_serverApp:String):String 
		{
  	        var sDebugPara:String = "XDEBUG_SESSION_START=netbeans-xdebug";	//debug parameter		
			var ts_app:String;
			
			if (ps_serverApp == "")
			{
				ts_app = "_Service.php";
			}
			else if(ps_serverApp.indexOf(".") < 0)
			{
				ts_app = ps_serverApp + ".php";			
			}
			else
			{
				ts_app = ps_serverApp;
			}
			
			if (sDebugPara != "")
				 ts_app += "?" + sDebugPara;
			
			return ts_app;
		}
//***********************************************************************************************************************************
		/** 
		 * run javaScript.(AIR 不支援!)
		 * @param ps_script javaScript to be run. 
		 * @return javaScript 執行結果字串
		 */ 
		private static function runJS(ps_script:String):String 
		{
		    if (!ExternalInterface.available)
		    {
		        msg("E","mx.ExternalInterface class is not usable !");
		      	return "";		        
		    }
		    
		    var ts_result:String = "";
		    try
		    {
			    ts_result = ExternalInterface.call("function(){"+ps_script+"}");		    
		    }
		    catch (e:Error)
		    {
		        msg("E","DBQuery.runJS() Error !\n"+ps_script);
		    }
		    
		    if (ts_result == null)
		    {
		    	ts_result = "";
		    }

			return ts_result			
		}
//***********************************************************************************************************************************
		/**
		 * @param ps_data
		 * @param pb_escape 是否處理特殊字元
		 */ 
		private static function toJson(ps_data:String, pb_escape:Boolean=false):Object
		{
			var pi_data:String=pb_escape ? escape(ps_data,-1) : ps_data;
			return com.json.JSON.decode(pi_data);
		}
//***********************************************************************************************************************************
		/** 
		 * show message
		 * @param ps_type message type. 
		 * @param ps_msg messag string.
		 */ 
		private static function msg(ps_type:String, ps_msg:String):void 
		{
			var t_alert:Alert;
						
			switch (ps_type)
			{
				case "I":
					t_alert = Alert.show(ps_msg, "訊息");
					break;
				case "E":	//client error msg
					t_alert = Alert.show(ps_msg, "Client Error");
					break;
				case "S":	//server error msg
					t_alert = Alert.show(ps_msg, "Server Error");
					break;
				default:
					break;
			}						
		}
//***********************************************************************************************************************************
		/**
		 * replace string for escape string
		 * @param ps_str source string
		 * @param pn_encodeType 1(encode)/0(decode:casReplace->casFind)/-1(decode:casReplace->casReplace2)
		 * @return result string
		 */ 
		public static function escape(ps_str:String, pn_encodeType:int):String{
			if (ps_str == null || ps_str == "")
				return "";
			var casFind:Array = ["'",'"',"&","+"];
			var casReplace:Array = ["#a#","#b#","#c#","#d#"];		//for save db
			var casReplace2:Array = ["'",'\\"',"&","+"];		//db to flex json
			
			var tas_find:Array;
			var tas_replace:Array;
			switch (pn_encodeType){
				case 1:
					tas_find = casFind;
					tas_replace = casReplace;
					break;
				case 0:
					tas_find = casReplace;
					tas_replace = casFind;				
					break;
				case -1:
					tas_find = casReplace;
					tas_replace = casReplace2;				
					break;
			}
			
			for (var i:int=0; i<tas_find.length; i++){
				ps_str = ps_str.split(tas_find[i] as String).join(tas_replace[i] as String);	//可以取代全部
			}
			return ps_str;
		}
		//***********************************************************************************************************************************
	}
}