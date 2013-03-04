
package{
	public class LogFun{
		
		//===== programmer set begin !! ========
		//先設定系統代號 !!
		private static is_sysId:String = "1";
		
		//設定記錄的起迄日期
		private static is_start:String = "2010/10/1";
		private static is_end:String = "2010/10/31";
		//===== programmer set end !! ========
		
		
		private static in_writeLog:int = 0;		//0(未設定), 1(Yes), -1(No)
		private static i_proxy:ServiceProxy;
		
		
		//記錄使用者的操作動作 
		public static function logAct(ps_form:String, ps_act:String):void{
			
			//check date limit first
			if (in_writeLog == 0){
				var t_start:Date = new Date(Date.parse(is_start));
				var t_end:Date = new Date(Date.parse(is_end));
				var t_today:Date = new Date();
				if (t_today < t_start || t_today > t_end){
					in_writeLog = -1;
				}else{
					in_writeLog = 1;
				}
			}
			
			if (in_writeLog == -1){
				return;
			}
			
			//remote call for log user action
			if (i_proxy == null){
				var ps_url:String  = "http://192.168.1.68/LogAct/Gateway.aspx";
				var ps_class:String = "LogAct";
				//private var _msg			:TextField = new TextField();		
				i_proxy = new ServiceProxy(ps_url, ps_class);	
				//i_proxy.addEventListener(ResultEvent.RESULT, onResult);
				//i_proxy.addEventListener(FaultEvent.FAULT, onFault);
			}
			
			i_proxy.log(is_sysId, ps_form, ps_act);
		}
		
	}
}