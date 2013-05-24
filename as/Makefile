all: ideMotorXML.swf ideMotor.swf


asError.swf: asError.as
	mxmlc asError.as #external-library-path

ideMotorXML.swf: ideMotorXML.as
	mxmlc ideMotorXML.as

Librerias: ide/animations/CCarrusel.as ide/animations/CEfecto.as ide/animations/CMover.as ide/animations/CSwap.as ide/display/CideSprite.as ide/display/CMensaje.as ide/net/CComunicador.as

Fuentes: ide/fonts/_Arial.as
	mxmlc ide/fonts/_Arial.as -output ide/fonts/_Arial.swf

ideMotor.swf: ideMotor.mxml ide/display/ideComponente.as ide/display/ideBase.as
	mxmlc ideMotor.mxml
