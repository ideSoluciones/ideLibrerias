/*package {  

	import flash.display.Sprite;
	public class _Arial extends Sprite {  

		[Embed(
			source='arial.ttf', 
			fontName='_Arial', 
		)]
			
		public static var _Arial:Class;  

	}  

}

*/
package {
	import flash.display.Sprite;
	public class _Arial extends Sprite {
		[Embed(source='arial.ttf', 
			fontName='_Arial', fontFamily='_arial')]
		public static var _Arial:Class;

		[Embed(source='arial_bold.ttf', 
			fontName='_Arial', fontFamily='_arial', fontWeight='bold')]
		public static var _Arial_bold:Class;

		/*[Embed(source='_arial_italic.ttf', fontFamily='_arial', fontStyle='italic')]
		public static var _arial_italic:Class;

		[Embed(source='_arial_bold_italic.ttf', fontFamily='_arial', fontWeight='bold', fontStyle='italic')]
		public static var _arial_bold_italic:Class;*/

		public static var styles:Array = new Array(
			{ label:"normal", fontName:"_arial", fontClass:"_arial" },
			{ label:"bold", fontName:"_arial", fontClass:"_arial_bold" }
			/*{ label:"italic", fontName:"_arial", fontClass:"_arial_italic" },
			{ label:"bold italic", fontName:"_arial", fontClass:"_arial_bold_italic" }
			*/ );

		public static var name:String = "_Arial";
	}
}
