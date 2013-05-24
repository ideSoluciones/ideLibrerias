package  {
    import flash.display.Sprite;
    import flash.text.TextField;
    import flash.text.TextFieldAutoSize;
    import flash.text.TextFormat;
	
	public class asError extends Sprite {

		function asError():void{
			var label:TextField = new TextField();
            label.autoSize = TextFieldAutoSize.LEFT;
            label.background = true;
            label.border = true;
            
            label.text="ERROR.";

            var format:TextFormat = new TextFormat();
            format.font = "Verdana";
            format.color = 0xFF0000;
            format.size = 10;
            format.underline = true;
            
			label.defaultTextFormat = format;

			addChild(label);	
		}
	}
}
