package x2
{
	
	import mx.effects.Dissolve;
	import mx.effects.Glow;
	import mx.effects.Sequence;


	public class EffectField extends Sequence
	{
		
		/**
		 * 無法將這些變數當做屬性來使用 !! (設定無效)
		 * 要改變這些值時, 必須宣告 instance 變數, ex:
		 *   private var i_effect:EffectShowField = new EffectShowField({width:10, sec:800, color:0xFF0000});
		 */
		 
		private var in_width:int = 5;
		private var in_sec:int = 500;			//mini second
		private var in_color:int = 0x0000FF;	//blue
		
		
		//constructor
		public function EffectField(p_data:Object=null):void{
			super();
			
			if (p_data != null){
				if (p_data.hasOwnProperty("width")){
					in_width = p_data.width;
				}
				if (p_data.hasOwnProperty("sec")){
					in_sec = p_data.sec;
				}
				if (p_data.hasOwnProperty("color")){
					in_color = p_data.color;
				}
			}
			
			var t_solve:Dissolve = new Dissolve();
			t_solve.duration = in_sec + 300;
			t_solve.alphaFrom = 0.0;
			t_solve.alphaTo = 1.0;

			var t_glow:Glow = new Glow();
			t_glow.duration = in_sec;
	        t_glow.alphaFrom = 0.0;
	        t_glow.alphaTo = 1.0; 
	        t_glow.blurXFrom = 0.0;
	        t_glow.blurXTo = in_width;
	        t_glow.blurYFrom = 0.0;
	        t_glow.blurYTo = in_width; 
	        t_glow.color = in_color;
	        
			var t_glow2:Glow = new Glow();
			t_glow2.duration = in_sec;
	        t_glow2.alphaFrom = 1.0;
	        t_glow2.alphaTo = 0.0; 
	        t_glow2.blurXFrom = in_width;
	        t_glow2.blurXTo = 0.0;
	        t_glow2.blurYFrom = in_width;
	        t_glow2.blurYTo = 0.0; 
	        t_glow2.color = in_color;
			
			this.addChild(t_solve);
			this.addChild(t_glow);
			this.addChild(t_glow2);
  		}
  		            

		/**
		 * @pn_effect: 1(Yes),0(No),2(must effect)
		 */ 	
		public function show(pb_enable:Boolean, pa_item:Array, pn_effect:int=1):void{
			
			//set fields status
			var t_item:Object;
			for (var i:int=0; i<pa_item.length; i++){
				t_item = pa_item[i];
				if (pn_effect == 2 || t_item.enabled != pb_enable){
					t_item.enabled = pb_enable;
					
					if (pn_effect > 0){
						this.play([t_item], !pb_enable);
					}
				}
			}
		}			
	}
}