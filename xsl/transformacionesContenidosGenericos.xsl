<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="TContenido" match="Tema/Contenido" priority="2">
		<div style='border:1px solid green'>
			<xsl:for-each select="Texto/Campo">
				<xsl:if test="@nombre = 'titulo'">
					<xsl:choose>
						 <xsl:when test="@nivel = 1 ">
						 	<h1><xsl:value-of select="@valor"/></h1>
						 </xsl:when>
						 <xsl:when test="@nivel = 2 ">
						 	<h2><xsl:value-of select="@valor"/></h2>
						 </xsl:when>
						 <xsl:when test="@nivel = 3 ">
						 	<h3><xsl:value-of select="@valor"/></h3>
						 </xsl:when>
						 <xsl:when test="@nivel &gt; 3 ">
						 	<h4><xsl:value-of select="@valor"/></h4>
						 </xsl:when>
					</xsl:choose>
				</xsl:if>
				<xsl:if test="@nombre = 'contenido'">
					<div><xsl:value-of select="@valor"/></div>
				</xsl:if>
			</xsl:for-each>
			
			<form method='POST' target='_self'>
				<xsl:for-each select="Formulario/Propiedad">
					<xsl:if test="@nombre = 'Accion'">
						<xsl:attribute name="action">
							<xsl:value-of select="@valor"/>
						</xsl:attribute>
					</xsl:if>
				</xsl:for-each>
				<xsl:for-each select="Formulario/Campo">
					<xsl:choose>
						<xsl:when test="@tipo = 'etiqueta'">
							<label><xsl:value-of select="@valor"/></label>
						</xsl:when>
						<xsl:when test="@tipo = 'entero' or @tipo = 'decimal' or @tipo = 'cadena' or @tipo = 'correo' or @tipo = 'fecha' or @tipo = 'clave'">
							<div>
								<xsl:if test="@requerido != 'true'">
										<div>*</div>
								</xsl:if>
								<div><xsl:value-of select="@titulo"/></div>
								<xsl:if test="@tipo = 'clave'">
									<input type='password'>
										<xsl:attribute name="name">
											<xsl:value-of select="@nombre"/>
										</xsl:attribute>
									</input>
								</xsl:if>
								<xsl:if test="@tipo != 'clave'">
									<input type='text'>
										<xsl:attribute name="name">
											<xsl:value-of select="@nombre"/>
										</xsl:attribute>
										<xsl:attribute name="value">
											<xsl:value-of select="@valorPorDefecto"/>
										</xsl:attribute>
									</input>
								</xsl:if>
							</div>
						</xsl:when>
						<xsl:when test="@tipo = 'booleano'">
						</xsl:when>
						<xsl:when test="@tipo = 'texto' or @nombre = 'xml'">
						</xsl:when>
						<xsl:when test="@tipo = 'enviar'">
							<input type='submit'>
								<xsl:attribute name="value">
									<xsl:value-of select="@titulo"/>
								</xsl:attribute>
							</input>
						</xsl:when>
						<xsl:when test="@tipo = 'oculto'">
						</xsl:when>
						<xsl:when test="@tipo = 'boton'">
						</xsl:when>
						<xsl:when test="@tipo = 'listaSeleccion'">
						</xsl:when>
						<xsl:when test="@tipo = 'selectores'">
						</xsl:when>
						<xsl:when test="@tipo = 'radios'">
							<xsl:variable name="nombre" select="@nombre" />
							<xsl:variable name="valorPorDefecto" select="@valorPorDefecto" />
							<fieldset id='' class='' ><legend><xsl:value-of select="@titulo"/></legend>
								<xsl:for-each select="Opcion">
									<div id='' class='' ><div class=''><xsl:value-of select="@nombre"/></div>
										<input onpress='' id='' type='radio' >
											<xsl:attribute name="name">
												<xsl:value-of select="$nombre"/>
											</xsl:attribute>
											<xsl:attribute name="value">
												<xsl:value-of select="@valor"/>
											</xsl:attribute>
											<xsl:if test="$valorPorDefecto = @valor">
												<xsl:attribute name="checked" />
											</xsl:if>
										</input>
									</div>
								</xsl:for-each>
							</fieldset>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</form>
		</div>
	</xsl:template>
</xsl:stylesheet>
