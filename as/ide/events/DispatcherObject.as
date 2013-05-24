//DispatcherObject.as

package ide.events{
	import flash.events.EventDispatcher;
	import flash.events.Event;

	public class DispatcherObject extends EventDispatcher{
		private var label:String;

		public function DispatcherObject(label:String){
			this.label = label;
		}

		public override function toString():String{
			return "[ Dispatcher "+label+" ]";
		}

		public function dispatch():void{
			this.dispatchEvent(new Event("testEvent"));
		}
	}
}
