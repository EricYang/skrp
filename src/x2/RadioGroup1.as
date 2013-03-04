package x2
{
	
	//import mx.controls.RadioButtonGroup;
	import spark.components.RadioButtonGroup;
	//import mx.core.IFlexDisplayObject;

	public class RadioGroup1 extends RadioButtonGroup
	{
				
		/**
		 * Flex 4.6 無法讀取RadioButtionGroup id (bug), 所以重新宣告 sId
		 * 建立這個物件時, 同時設定 id 和 sId 即可
		 */ 
		private var is_id:String;
		public function set sId(ps_id:String):void{
			is_id = ps_id;
		}
		
		/*
		//getter
		public function get sId():String{
			return is_id;
		}
		*/
		
		public function get id():String{
			return is_id;
		}
		//public var gsId:String;
		
		//setter
		public function set value(p_value:Object):void{
			this.selectedValue = p_value;
		}
				
		//getter
		public function get value():Object{
			return this.selectedValue;
		}
				
		/*
		public function get id():String{
			return gsId;
		}
		*/
		
		public function get className():String{
			return "RadioGroup1";
		}
		
		public function get visible():Boolean{
			return false;
		}
	}
}