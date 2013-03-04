/**
 * Date utility
 * 定義: 
 *   Time: unit timestamp
 *   Date: flex Date
 *   DT: flex datetime
 */  
package x2{
	public class DT{
		//public function DT(){}
		
		
		/**
		 * 傳回某個日期當月份第一天的日期字串.
		 * @p_data 日期
		 * @return 日期字串 
		 */ 
		public static function date1Str(p_date:Date):String {
			return p_date.fullYear+"/"+(p_date.month+1)+"/1";
		}
		
		
		/**
		 * 傳回某個日期當月份的天數
		 * @p_data 日期
		 * @return 月份的天數
		 */ 
		public static function monthDays(p_date:Date):int {
			var tn_year:int = p_date.fullYear;
			var tn_month:int;
			if (p_date.month == 11){
				tn_year += 1;
				tn_month = 0;
			}else{
				tn_month = p_date.month + 1;
			}
			var t_date2:Date = new Date(tn_year, tn_month, 1)
			t_date2 = addDate(t_date2, -1);
			return t_date2.date;
		}
		
		
		/** 
		 * 把日期加上某個天數後傳回
		 * @param pd_old 原始日期 
		 * @param 要增加的天數, 可為負值
		 * @return 新日期
		 */ 
		public static function addDate(pd_old:Date, pn_days:int):Date {
			if (pn_days == 0){
				return pd_old;
			}else{
				return new Date(pd_old.fullYear, pd_old.month, pd_old.date + pn_days, 0, 0, 0);
			}
		}
				
		
		/**
		 * 計算 2 個日期之間的天數, 可為負數, 同一天傳回 0, 日期較小的為第一個參數
		 * @param pd_start 起日
		 * @param pd_end 迄日
		 * @return 天數
		 */ 
		public static function dateDiff(pd_start:Date, pd_end:Date):int {
			//if (isNaN(pd_start.date) || isNaN(pd_end.date)){
			if (pd_start == null || pd_end == null){
				return 0;
			} 
			
			var tn_end:Number = pd_end.getTime();
			var tn_start:Number = pd_start.getTime();
			return int((pd_end.getTime() - pd_start.getTime())/(1000*60*60*24));			
		}
		
		/**
		 * data diff by string(date)
		 */  
		public static function dateDiffS(ps_start:String, ps_end:String):int {
			return dateDiff(ST.toDate(ps_start), ST.toDate(ps_end));
		}
		
		/**
		 * data diff by field
		 */  
		public static function dateDiffF(pf_start:Object, pf_end:Object):int {
			return dateDiffS(Fun.getItem(pf_start) as String, Fun.getItem(pf_end) as String);
		}

		
		public static function timeToDT(pn_sec:int):Date {
			return new Date(pn_sec * 1000);
		}
		
		
		/**
		 * convert time to dhm string(yyyy-mm-dd hh:mm:ss)
		 */  
		public static function timeToDtStr(pn_sec:int):String {
			//var t_date:Date = new Date(pn_sec * 1000);
			return Fun.df2.format(timeToDT(pn_sec));
		}

		
		public static function dateToTime(p_date:Date):int {
			return Math.round(p_date.getTime()/1000);
		}
		
		
		public static function dateStrToTime(ps_date:String):int {
			return dateToTime(ST.toDate(ps_date));
		}		
	}//class
}//package