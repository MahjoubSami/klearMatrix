;(function load($) {

	this.count = this.count || 0;
	
	if ( (!$.klearmatrix) ||
			(typeof $.klearmatrix.edit != 'function')) {
		if (++this.count == 20) {
			throw "JS Dependency error! [new]";
		}
		setTimeout(function() {load($);},100);
		return;
	}
	
	$.widget("klearmatrix.new", $.klearmatrix.edit, {
		options: {
			data : null,
			moduleName: 'new'
		},
		_super: $.klearmatrix.module.prototype,
		_create : function() {
			this._super._create.apply(this);
		},
		_init: function() {
			
			$.extend(this.options.data,{randIden:Math.round(Math.random(1000,9999)*100000)});

			this.options.data.title = this.options.data.title || this.element.klearModule("option","title");

			var $appliedTemplate = this._loadTemplate("klearmatrixNew");
			
			$(this.element.klearModule("getPanel")).append($appliedTemplate);
			
			this._applyDecorators()
				._registerBaseEvents()
				._initFormElements()
				._registerEvents()
				._registerMainActionEvent();
				
		},
		_doAction : function() {
			
			var self = this;
			var $self = $(this.element);
			
			var $dialog = $self.klearModule("getModuleDialog") 
			
			$.klear.request(
					{
						file: $self.klearModule("option","file"),
						type: 'screen',
						execute: 'save',
						screen: self.options.data.screen,
						post : self.options.theForm.serialize()
					},
					function(data) {
						
						
						
						if (data.error) {
							//TO-DO: FOK OFF
						} else {
							var $parentModule = $self.klearModule("option","parentScreen");
							$parentModule.klearModule("reDispatch");
							
							
						}
						$("input,select,textarea",self.options.theForm).val('');
						self._initSavedValueHashes();
						self.options.theForm.trigger('updateChangedState');

						$dialog.moduleDialog("option","buttons",
								 [
								  	{
			    						text: $.translate("Cerrar"),
			    						click: function() {
			    							$(this).moduleDialog("close");
			    							$self.klearModule("close");
			    						}
									},
									{
										text: $.translate("Añadir otro registro"),
										click: function() {
											$(this).moduleDialog("close");
										}
									}
								]
						);
						
						$dialog.moduleDialog("updateContent",data.message);
														
						
					},
					// Error from new/index/save
					function(data) {
						
									
					}
			);			
			
		}
	});

	$.widget.bridge("klearMatrixNew", $.klearmatrix.new);
})(jQuery);