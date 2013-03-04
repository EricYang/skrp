package x2{
	
	import mx.resources.ResourceManager;
	
		
	public class RC{

		private var is_fileName:String;		//locale source file name		
			
			
		/**
		 * Constructor.
		 * @param ps_fileName file name<br>
		 *   line2
		 */ 
		public function RC(ps_fileName:String){
			is_fileName = ps_fileName;
		}
		

		/**
		 * get source string.
		 * 
		 * @default null 
		 */			
		public function gs(ps_key:String):String{
			return ResourceManager.getInstance().getString(is_fileName, ps_key);			
		}
	}
}//x2