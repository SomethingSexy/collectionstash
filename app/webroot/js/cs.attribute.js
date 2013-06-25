// ES5 15.2.3.5
// http://es5.github.com/#x15.2.3.5
if (!Object.create) {

	// Contributed by Brandon Benvie, October, 2012
	var createEmpty;
	var supportsProto = Object.prototype.__proto__ === null;
	if (supportsProto) {
		createEmpty = function() {
			return {
				"__proto__" : null
			};
		};
	} else {
		// In old IE __proto__ can't be used to manually set `null`, nor does
		// any other method exist to make an object that inherits from nothing,
		// aside from Object.prototype itself. Instead, create a new global
		// object and *steal* its Object.prototype and strip it bare. This is
		// used as the prototype to create nullary objects.
		createEmpty = (function() {
			var iframe = document.createElement('iframe');
			var parent = document.body || document.documentElement;
			iframe.style.display = 'none';
			parent.appendChild(iframe);
			iframe.src = 'javascript:';
			var empty = iframe.contentWindow.Object.prototype;
			parent.removeChild(iframe);
			iframe = null;
			delete empty.constructor;
			delete empty.hasOwnProperty;
			delete empty.propertyIsEnumerable;
			delete empty.isProtoypeOf;
			delete empty.toLocaleString;
			delete empty.toString;
			delete empty.valueOf;
			empty.__proto__ = null;

			function Empty() {
			}


			Empty.prototype = empty;

			return function() {
				return new Empty();
			};
		})();
	}

	Object.create = function create(prototype, properties) {

		var object;
		function Type() {
		}// An empty constructor.

		if (prototype === null) {
			object = createEmpty();
		} else {
			if ( typeof prototype !== "object" && typeof prototype !== "function") {
				// In the native implementation `parent` can be `null`
				// OR *any* `instanceof Object` (Object|Function|Array|RegExp|etc)
				// Use `typeof` tho, b/c in old IE, DOM elements are not `instanceof Object`
				// like they are in modern browsers. Using `Object.create` on DOM elements
				// is...err...probably inappropriate, but the native version allows for it.
				throw new TypeError("Object prototype may only be an Object or null");
				// same msg as Chrome
			}
			Type.prototype = prototype;
			object = new Type();
			// IE has no built-in implementation of `Object.getPrototypeOf`
			// neither `__proto__`, but this manually setting `__proto__` will
			// guarantee that `Object.getPrototypeOf` will work as expected with
			// objects created using `Object.create`
			object.__proto__ = prototype;
		}

		if (properties !==
		void 0) {
			Object.defineProperties(object, properties);
		}

		return object;
	};
}

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
		adminPage : false,
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

var RemoveAttributes = function(options) {
	this.options = $.extend({
		$element : '',
		$openElement : null,
		$dataElement : null,
		$context : '',
		success : function() {
		}
	}, options);
}

RemoveAttributes.prototype = Object.create(AttributesBase.prototype);

/**
 *Setup the main dialog for removing also add any events
 */
RemoveAttributes.prototype.init = function() {
	var self = this;
	this.$dialog = $('#attribute-remove-dialog').dialog({
		height : 'auto',
		width : 'auto',
		modal : true,
		autoOpen : false,
		resizable : false,
	});

	// Setup a bunch of events we don't want to tear down and rebuild each time
	this.$dialog.find('.item-search').children('.paging').on('click', 'span > a', function(event) {
		//scroll top
		var link = $(this).attr('href');
		self._loadCollectiblesList(link);
		event.preventDefault();
	});

	this.$dialog.find('.item-search').children('.items').children('ul').on('click', 'li .attribute-list li', function(event) {
		var $replacementItem = self.$dialog.find('.form-fields').find('.replacement-item');
		$replacementItem.find('input').val($(this).closest('li.attribute').attr('data-id'));
		$replacementItem.find('.static-field').text($(this).text());
		$replacementItem.show();
	});

	this.options.$element.children('.attributes-list').on('click', 'div.attribute .actions a.remove-attribute', function() {
		var $element = $(this);
		self.open(self.getAttribute($element));
	});

	var $itemSearch = self.$dialog.find('div.item-search');

	// Setup the search submit stuff as well, we will want to hijack the submit button so it is all ajax
	// it will go out and so another search and then we will display the results in the dialgo
	var $formFields = $('#attribute-remove-dialog').children('.component').children('.inside').children('.component-view').find('.form-fields');
	$formFields.children('.how-link-item').children('.collectible-search').on('click', function() {
		// Load the initial list
		self._loadCollectiblesList();
	});

	var $seachInput = $itemSearch.children('.search').children('.component-search-input').children('form').children('.searchfield');
	$itemSearch.children('.search').children('.component-search-input').children('form').children('.searchbutton').on('click', function(event) {
		var query = $seachInput.val();

		query = '/collectibles/search?q=' + query;

		self._loadCollectiblesList(query);
		event.preventDefault();
	});
};

RemoveAttributes.prototype.reset = function() {
	var self = this;
	var $formFields = $('#attribute-remove-dialog').children('.component').children('.inside').children('.component-view').find('.form-fields');
	var $itemSearch = self.$dialog.find('div.item-search');

	// We need to reset the status of the dialog each time
	$itemSearch.hide();
	$itemSearch.children('.items').children('ul').children().remove();
	$formFields.children('.how-link-item').hide();
	$formFields.children('.link-item').children('input').attr('checked', false);
	$formFields.children('.replacement-item').find('input').val('');
	$formFields.children('.replacement-item').hide();

};
/**
 * This gets called everytime we open the remove dialog, we need to do some reset stuff
 */
RemoveAttributes.prototype.open = function(attribute) {
	var self = this;
	// reset the remove
	self.reset();
	// Now do stuff based on what attribute we are looking at
	var $formFields = $('#attribute-remove-dialog').children('.component').children('.inside').children('.component-view').find('.form-fields');

	if (attribute.isAttached() === 'true') {
		$('#attribute-remove-dialog').children('.component').children('.inside').find('.component-info').children('.attribute-attached').show();
		$('#attribute-remove-dialog').children('.component').children('.inside').find('.component-info').children('.attribute-not-attached').hide();
		$formFields.children('.link-item').show();
		$formFields.children('.directional-text').show();

		$formFields.children('.link-item').children('input').off().on('change', function() {
			$formFields.children('.how-link-item').toggle();
		});

	} else {
		$('#attribute-remove-dialog').children('.component').children('.inside').children('.component-info').children('.attribute-not-attached').show();
		$('#attribute-remove-dialog').children('.component').children('.inside').children('.component-info').children('.attribute-attached').hide();
		$formFields.children('.link-item').hide();
		$formFields.children('.directional-text').hide();
	}

	this.$dialog.dialog("option", "buttons", [{
		text : 'Submit',
		'class' : 'btn btn-primary',
		"click" : function() {
			if ($.trim($('#AttributeReason').val()) !== '') {
				self.submit(attribute);
			} else {
				$('#AttributeReason').after('<div class="error-message">Reason is required.</div>');
			}
		}
	}, {
		text : 'Cancel',
		'class' : 'btn',
		'click' : function() {
			$(this).dialog('close');
		}
	}]);

	// open the remove dialog
	self.$dialog.dialog('open');
};

RemoveAttributes.prototype.submit = function(attribute) {
	var self = this;

	var id = attribute.getAttributeData().id;
	var url = '/attributes/remove.json';
	// If we are passing in an override admin or the options are set to admin mode
	if (self.options.adminPage) {
		url = '/admin/attributes/remove.json'
	}
	$('#AttributeRemoveForm').ajaxSubmit({

		// dataType identifies the expected content type of the server response
		dataType : 'json',
		url : url,
		beforeSubmit : function(formData, jqForm, options) {
			formData.push({
				name : '_method',
				type : 'text',
				value : 'POST'
			});
			self._clearFormErrors();
			formData.push({
				'name' : 'data[Attribute][id]',
				'value' : id
			});
		},
		// success identifies the function to invoke when the server response
		// has been received
		success : function(responseText, statusText, xhr, $form) {
			if (responseText.response.isSuccess) {
				responseText.response.data.id = id;
				self.$dialog.dialog('close');
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
							$(':input[name="data[' + value.model + '][' + value.name + ']"]', '#AttributeRemoveForm').after('<div class="error-message">' + value.message + '</div>');
						} else {
							self.$dialog.find('.component-message.error').children('span').text(value.message);
						}

					});
				}
			}
		}
	});
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
	this.$dialog = $('#attribute-remove-link-dialog').dialog({
		height : 'auto',
		width : 'auto',
		modal : true,
		autoOpen : false,
		resizable : false,
	});
	this.options.$element.children('.attributes-list').on('click', 'div.attribute .actions a.remove-link', function() {
		var $element = $(this);
		self.open(self.getAttribute($element));
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
RemoveAttributeLinks.prototype.open = function(attribute) {
	var self = this;
	// reset the remove
	self.reset();

	this.$dialog.dialog("option", "buttons", [{
		text : 'Submit',
		'class' : 'btn btn-primary',
		"click" : function() {
			if ($.trim($('#AttributesCollectibleReason').val()) !== '') {
				self.submit(attribute);
			} else {
				$('#AttributesCollectibleReason').after('<div class="error-message">Reason is required.</div>');
			}
		}
	}, {
		text : 'Cancel',
		'class' : 'btn',
		'click' : function() {
			$(this).dialog('close');
		}
	}]);

	// open the remove dialog
	self.$dialog.dialog('open');
};

RemoveAttributeLinks.prototype.submit = function(attribute) {
	var self = this;
	var id = attribute.getAttributeCollectible().id;
	if ($('#AttributesCollectibleRemove').is(":checked")) {
		var url = '/attributes_collectibles/remove.json';
		// If we are passing in an override admin or the options are set to admin mode
		if (self.options.adminPage) {
			url = '/admin/attributes_collectibles/remove.json'
		}

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
					self.$dialog.dialog('close');
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
	} else {
		$('#AttributesCollectibleRemove').after('<div class="error-message">Must select remove to submit.</div>');
	}
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
	// If we are passing in an override admin or the options are set to admin mode
	if (self.options.adminPage) {
		url = '/admin/attributes_collectibles/update.json'
	}

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
				if (self.options.adminPage || !responseText.response.data.isEdit) {
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

