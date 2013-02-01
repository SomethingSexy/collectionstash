( function($) {
		/**
		 *TODO: This needs to be broken out to handle a single attribute that might not be part of a list already
		 *
		 * This will take in a URL to upload too because we will be handling different types
		 *
		 * Also needs to take in a URL to know where to get the uploads from
		 */
		$.widget("cs.balls", {
			options : {
				collectibleId : null
			},

			// Set up the widget
			_create : function() {
				var self = this;

				$('#fileupload').fileupload({
					dropZone : $('#dropzone')
				});
				$('#fileupload').fileupload('option', 'redirect', window.location.href.replace(/\/[^\/]*$/, '/cors/result.html?%s'));

				$('#fileupload').fileupload('option', {
					url : '/collectibles_uploads/upload',
					maxFileSize : 2097152,
					acceptFileTypes : /(\.|\/)(gif|jpe?g|png)$/i,
					process : [{
						action : 'load',
						fileTypes : /^image\/(gif|jpeg|png)$/,
						maxFileSize : 2097152 // 2MB
					}, {
						action : 'resize',
						maxWidth : 1440,
						maxHeight : 900
					}, {
						action : 'save'
					}]
				});

				$('#fileupload').bind('fileuploaddestroy', function(e, data) {
					var filename = data.url.substring(data.url.indexOf("=") + 1);
					console.log(data);
				});

				this.$uploadDialog = $('#upload-dialog').dialog({
					height : 'auto',
					width : 'auto',
					modal : true,
					autoOpen : false,
					resizable : false,
					close : function() {
						// $('#fileupload').fileupload('destroy');

						$('#fileupload table tbody tr.template-download').remove();
					}
				});

				this.options.$element.on('click', function() {
					$.blockUI({
						message : 'Loading...',
						css : {
							border : 'none',
							padding : '15px',
							backgroundColor : ' #F1F1F1',
							'-webkit-border-radius' : '10px',
							'-moz-border-radius' : '10px',
							color : '#222',
							background : 'none repeat scroll 0 0 #F1F1F',
							'border-radius' : '5px 5px 5px 5px',
							'box-shadow' : '0 0 10px rgba(0, 0, 0, 0.5)'
						}
					});

					$.ajax({
						dataType : 'json',
						url : '/collectibles_uploads/view/' + self.options.collectibleId,
						beforeSubmit : function(formData, jqForm, options) {

						},
						success : function(data, textStatus, jqXHR) {

							if (data && data.response.data.length) {
								var that = $('#fileupload');
								that.fileupload('option', 'done').call(that, null, {
									result : data.response.data
								});
							}

							$.unblockUI();
							self.$uploadDialog.dialog('open');

						}
					});
				});

			},
			_setOption : function(key, value) {

				switch( key ) {

					case "clear":
						// handle changes to clear option

						break;

				}
				// In jQuery UI 1.8, you have to manually invoke the _setOption method from the base widget
				$.Widget.prototype._setOption.apply(this, arguments);
				// In jQuery UI 1.9 and above, you use the _super method instead
				this._super("_setOption", key, value);

			},
			// Use the destroy method to clean up any modifications your widget has made to the DOM

			destroy : function() {
				// In jQuery UI 1.8, you must invoke the destroy method from the base widget
				$.Widget.prototype.destroy.call(this);
				// In jQuery UI 1.9 and above, you would define _destroy instead of destroy and not call the base method
			}
		});

	}(jQuery) );
