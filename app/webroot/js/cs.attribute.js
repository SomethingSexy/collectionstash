var Attribute = function(options) {
	this.options = $.extend({
		$element : ''
	}, options);
}
Attribute.prototype = {
	getAttributeData : function($dataElement) {
		var attributeData = $.parseJSON(this.options.$element.attr('data-attribute'));
		return attributeData;
	},
	isAttached : function() {
		var attached = this.options.$element.attr('data-attached');
		return attached;
	},
	getAttributeCollectible : function($dataElement) {
		var attributeCollectibleData = $.parseJSON(this.options.$element.attr('data-attribute-collectible'));
		return attributeCollectibleData;
	}
};

var AttributesBase = function(options) {
	this.options = $.extend({
		allowAdd : true,
		allowRemove : true,
		allowUpdate : true,
		singleView : false,
		$element : ''
	}, options);
}

AttributesBase.prototype = {
	// One time setup function
	init : function() {
		if (this.options.allowAdd) {
			this.initAdd();
		}

		if (this.options.allowUpdate) {
			this.initUpdate();
		}

		if (this.options.allowRemove) {
			this.initRemove();
		}
	},
	initAdd : function() {

	},
	initUpdate : function() {

	},
	getAttribute : function($element) {
		var $li = $element.closest('div.attribute');
		var attribute = new Attribute({
			$element : $li
		});

		return attribute;
	},
	_addInputError : function(model, fieldName, message) {
		$(':input[name="data[' + model + '][' + fieldName + ']"]', this.$dialog.find('form')).after('<div class="error-message">' + message + '</div>');
	},
	_clearFormErrors : function() {
		this.$dialog.find('.error-message').remove();
		this.$dialog.find('.error-message').remove();
	},
	_clearFormFields : function() {
		var self = this;
		this.$dialog.find('form').find(':input[type="text"], :input[type="password"], :input[type="file"], select, textarea, :input[type="number"], :input[type="hidden"]').val('');
		this.$dialog.find('form').find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
		self._clearFormErrors();
	},
	_enableAttributeSelector : function() {
		var self = this;
		this._disableAttributeSelector();
		this.$dialog.on('click', '#select-attribute-link', function(event) {
			self._loadCollectiblesList();
		});
	},
	_disableAttributeSelector : function() {
		this.$dialog.off('click', '#select-attribute-link');
	},
	_validateAttribute : function(successCallback) {
		var self = this;
		this.$dialog.find('form').ajaxSubmit({
			// dataType identifies the expected content type of the server response
			dataType : 'json',
			type : 'post',
			url : '/attributes/isValid.json',
			beforeSubmit : function(formData, jqForm, options) {
				formData.push({
					name : '_method',
					type : 'text',
					value : 'POST'
				});
				self._clearFormErrors();
			},
			// success identifies the function to invoke when the server response
			// has been received
			success : function(responseText, statusText, xhr, $form) {
				if (responseText.response.isSuccess) {
					successCallback.call(self);
				} else {
					if (responseText.response.errors) {
						$.each(responseText.response.errors, function(index, value) {
							if (value.inline) {
								$(':input[name="data[' + value.model + '][' + value.name + ']"]', self.$dialog.find('form')).after('<div class="error-message">' + value.message + '</div>');
							} else {
								self.$dialog.find('.component-message.error').children('span').text(value.message);
							}

						});
					}
				}
			}
		});

	},

	_validateAttributesCollectible : function(successCallback) {
		var self = this;
		this.$dialog.find('form').ajaxSubmit({
			// dataType identifies the expected content type of the server response
			dataType : 'json',
			type : 'post',
			url : '/attributes_collectibles/isValid.json',
			beforeSubmit : function(formData, jqForm, options) {
				formData.push({
					name : '_method',
					type : 'text',
					value : 'POST'
				});
				self._clearFormErrors();
			},
			// success identifies the function to invoke when the server response
			// has been received
			success : function(responseText, statusText, xhr, $form) {
				if (responseText.response.isSuccess) {
					successCallback.call(self);
				} else {
					if (responseText.response.errors) {
						$.each(responseText.response.errors, function(index, value) {
							if (value.inline) {
								$(':input[name="data[' + value.model + '][' + value.name + ']"]', self.$dialog.find('form')).after('<div class="error-message">' + value.message + '</div>');
							} else {
								self.$dialog.find('.component-message.error').children('span').text(value.message);
							}

						});
					}
				}
			}
		});

	},
	_loadCollectiblesList : function(query) {
		if ( typeof query === 'undefined') {
			query = '/collectibles/search';
		}

		var self = this;

		var $itemSearch = self.$dialog.find('div.item-search');
		var $itemList = $itemSearch.children('.items').children('ul');
		$.ajax({
			dataType : 'json',
			url : query,
			beforeSubmit : function(formData, jqForm, options) {

			},
			success : function(data, textStatus, jqXHR) {
				if (data) {
					$itemList.children().remove();
					$itemSearch.children('.paging').children().remove();
					$.each(data.results, function(index, collectible) {
						var title = '<span class="title"><span class="name">' + collectible.Collectible.name + '</span>' + ' - ' + collectible.Manufacture.title + ' ' + collectible.License.name + ' ' + collectible.Collectibletype.name;
						if (collectible.Collectible.exclusive) {
							title += '<span class="exclusive"> Exclsuive</span>';
						}

						if (collectible.Collectible.variant) {
							title += '<span class="variant"> Variant</span>';
						}

						title += '</span>';

						var $listItem = $('<li></li>').html(title);

						if ($.isArray(collectible.AttributesCollectible) && collectible.AttributesCollectible.length > 0) {
							var $attributeList = $('<ul></ul>').addClass('attribute-list').addClass('unstyled');

							$.each(collectible.AttributesCollectible, function(index, attribute) {
								// If there is no category then don't show it, I think this is a problem
								// right now because we have some attributes that are still in the features list
								if ( typeof attribute.Attribute.AttributeCategory !== 'undefined') {
									var pathName = attribute.Attribute.AttributeCategory.path_name;
									var name = attribute.Attribute.name;
									if (name === '') {
										name = attribute.Attribute.description;
									}

									var $attributeItem = $('<li></li>').addClass('attribute').attr('data-id', attribute.Attribute.id).attr('data-attribute', JSON.stringify(attribute.Attribute));
									$attributeItem.html('<span class="category">' + pathName + ' - </span> ' + name + ' - ' + attribute.count);
									$attributeList.append($attributeItem);
								}

							});
							$listItem.append($attributeList);
						} else {
							$listItem.addClass('no-attributes');
						}

						$itemList.append($listItem);
					});

					$itemSearch.children('.paging').append(data.metadata.pagingHtml);

					$itemSearch.show();
					self.$dialog.dialog('option', 'position', 'center');

				} else {
					//do something
				}
			}
		});
	}
};

/**
 *Object for remove attirubte links
 */
var RemoveAttributeLinks = function(options) {
	this.options = $.extend({
		$element : '',
		$openElement : null,
		$dataElement : null,
		$context : '',
		success : function() {
		}
	}, options);
}

RemoveAttributeLinks.prototype = Object.create(AttributesBase.prototype);

/**
 *Setup the main dialog for removing also add any events
 */
RemoveAttributeLinks.prototype.init = function() {
	var self = this;
	this.$dialog = $('#attribute-remove-link-dialog').modal({
		show : false
	});

	$('.btn.btn-primary', '#attribute-remove-link-dialog').click(function() {
		if ($.trim($('#AttributesCollectibleReason').val()) !== '') {
			self.submit();
		} else {
			$('#AttributesCollectibleReason').after('<div class="error-message">Reason is required.</div>');
		}
	});

	this.options.$element.children('.attributes-list').on('click', 'div.attribute .actions a.remove-link', function() {
		var $element = $(this);
		self.attribute = self.getAttribute($element);
		self.open();
	});
};

RemoveAttributeLinks.prototype.reset = function() {
	var self = this;
	var $formFields = $('#attribute-remove-link-dialog').children('.component').children('.inside').children('.component-view').find('.form-fields');

	$('textarea', $formFields).val('');
	$('input[type=checkbox]', $formFields).attr('checked', false);
};
/**
 * This gets called everytime we open the remove dialog, we need to do some reset stuff
 */
RemoveAttributeLinks.prototype.open = function() {
	var self = this;
	// reset the remove
	self.reset();

	// open the remove dialog
	self.$dialog.modal('show');
};

RemoveAttributeLinks.prototype.submit = function() {
	var self = this;
	var id = this.attribute.getAttributeCollectible().id;
	var url = '/attributes_collectibles/remove.json';

	$('#AttributeCollectibleRemoveForm').ajaxSubmit({
		// dataType identifies the expected content type of the server response
		dataType : 'json',
		url : url,
		beforeSubmit : function(formData, jqForm, options) {
			self._clearFormErrors();
			formData.push({
				'name' : 'data[AttributesCollectible][id]',
				'value' : id
			});
			formData.push({
				name : '_method',
				type : 'text',
				value : 'POST'
			});
		},
		// success identifies the function to invoke when the server response
		// has been received
		success : function(responseText, statusText, xhr, $form) {
			if (responseText.response.isSuccess) {
				responseText.response.data.id = id;
				self.$dialog.modal('hide');
				var message = 'Removal has been submitted!';
				if (!responseText.response.data.isEdit) {
					message = 'Part has been removed!';
				}
				$.blockUI({
					message : '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
					showOverlay : false,
					css : {
						top : '100px',
						'background-color' : '#DDFADE',
						border : '1px solid #93C49F',
						'box-shadow' : '3px 3px 5px rgba(0, 0, 0, 0.5)',
						'border-radius' : '4px 4px 4px 4px',
						color : '#333333',
						'margin-bottom' : '20px',
						padding : '8px 35px 8px 14px',
						'text-shadow' : '0 1px 0 rgba(255, 255, 255, 0.5)',
						'z-index' : 999999
					},
					timeout : 2000
				});
				self.options.success(responseText.response.data);
			} else {
				if (responseText.response.errors) {
					$.each(responseText.response.errors, function(index, value) {
						if (value.inline) {
							$(':input[name="data[' + value.model + '][' + value.name + ']"]', '#AttributeCollectibleRemoveForm').after('<div class="error-message">' + value.message + '</div>');
						} else {
							self.$dialog.find('.component-message.error').children('span').text(value.message);
						}

					});
				}
			}
		}
	});
};

var UpdateCollectibleAttributes = function(options) {
	this.options = $.extend({
		$element : '',
		$openElement : null,
		$dataElement : null,
		$context : '',
		success : function() {
		}
	}, options);
}

UpdateCollectibleAttributes.prototype = Object.create(AttributesBase.prototype);

/**
 *Setup the main dialog for removing also add any events
 */
UpdateCollectibleAttributes.prototype.init = function() {
	var self = this;
	this.$dialog = $('#update-attribute-collectible-dialog').modal({
		show : false
	});

	$('.btn.btn-primary', '#update-attribute-collectible-dialog').click(function() {
		self.submit();
	});

	this.options.$element.children('.attributes-list').on('click', 'div.attribute .actions a.edit-attribute-collectible-link', function() {
		var $element = $(this);
		self.attribute = self.getAttribute($element);
		self.open();
	});
};

UpdateCollectibleAttributes.prototype.reset = function() {
	var self = this;
};
/**
 * This gets called everytime we open the remove dialog, we need to do some reset stuff
 */
UpdateCollectibleAttributes.prototype.open = function() {
	var self = this;
	// reset the remove
	self.reset();

	var attributeData = this.attribute.getAttributeCollectible();

	$(':input[name="data[AttributesCollectible][count]"]', this.$dialog).val(attributeData.count);
	// Also set the selected type
	$(':input[name="data[AttributesCollectible][attribute_collectible_type_id]"]', this.$dialog).val(attributeData.attributeCollectibleTypeId);

	// open the remove dialog
	self.$dialog.modal('show');
};

UpdateCollectibleAttributes.prototype.submit = function() {
	var self = this;

	var id = this.attribute.getAttributeCollectible().id;

	var url = '/attributes_collectibles/update.json';

	this.$dialog.find('form').ajaxSubmit({
		// dataType identifies the expected content type of the server response
		dataType : 'json',
		url : url,
		beforeSubmit : function(formData, jqForm, options) {
			self._clearFormErrors();
			formData.push({
				'name' : 'data[AttributesCollectible][id]',
				'value' : id
			});
			formData.push({
				name : '_method',
				type : 'text',
				value : 'POST'
			});
		},
		// success identifies the function to invoke when the server response
		// has been received
		success : function(responseText, statusText, xhr, $form) {
			if (responseText.response.isSuccess) {
				self.$dialog.modal('hide');
				var message = 'Update has been submitted!';
				if (!responseText.response.data.isEdit) {
					message = 'The part was successfully updated!';
				}
				$.blockUI({
					message : '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
					showOverlay : false,
					css : {
						top : '100px',
						'background-color' : '#DDFADE',
						border : '1px solid #93C49F',
						'box-shadow' : '3px 3px 5px rgba(0, 0, 0, 0.5)',
						'border-radius' : '4px 4px 4px 4px',
						color : '#333333',
						'margin-bottom' : '20px',
						padding : '8px 35px 8px 14px',
						'text-shadow' : '0 1px 0 rgba(255, 255, 255, 0.5)',
						'z-index' : 999999
					},
					timeout : 2000
				});
				self.options.success(responseText.response.data);
			} else {
				if (responseText.response.errors) {
					$.each(responseText.response.errors, function(index, value) {
						if (value.inline) {
							$(':input[name="data[' + value.model + '][' + value.name + ']"]', self.$dialog).after('<div class="error-message">' + value.message + '</div>');
						} else {
							self.$dialog.find('.component-message.error').children('span').text(value.message);
						}

					});
				}
			}
		}
	});
};

