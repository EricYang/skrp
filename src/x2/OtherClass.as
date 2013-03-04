package x2
{
	import flash.events.Event;

	public class Event2a extends Event
	{
		public var data:Object = {};
		
		
		public function Event2a(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		
	}
}