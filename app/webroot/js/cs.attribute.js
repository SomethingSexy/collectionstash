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
		var $li = $element.closest('tr');
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
							var $attributeList = $('<ul></ul>').addClass('attribute-list');

							$.each(collectible.AttributesCollectible, function(index, attribute) {
								var pathName = attribute.Attribute.AttributeCategory.path_name;
								var name = attribute.Attribute.name;
								var description = attribute.Attribute.description;

								var $attributeItem = $('<li></li>').addClass('attribute').attr('data-id', attribute.Attribute.id).attr('data-attribute', JSON.stringify(attribute.Attribute));
								$attributeItem.html('<span class="category">' + pathName + ' - </span> ' + name + ' ' + description);
								$attributeList.append($attributeItem);
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

var AddAttributes = function(options) {
	this.options = $.extend({
		$element : ''
	}, options);
}

AddAttributes.prototype = Object.create(AttributesBase.prototype);

/**
 *Setup the main dialog for removing also add any events
 */
AddAttributes.prototype.init = function() {
	var self = this;
	this.$dialog = $('#attribute-add-dialog').dialog({
		height : 'auto',
		width : 'auto',
		modal : true,
		autoOpen : false,
		resizable : false,
	});

	$('#add-new-item-link').on('click', function() {
		var $element = $(this);
		self.open();
	});

	this.$attributeCategory = this.$dialog.children('.component-dialog').children('.inside').children('.component-view').find('.attribute-category');

	$('.change-attribute-category-link', this.$dialog).on('click', function() {
		self.$attributeCategory.show();
		self.$dialog.dialog('option', 'position', 'center');
	});

	$('#tree', self.$dialog).treeview({
		'hover' : ''
	});

	// this handles setting up the attribute category for the dialog
	// setting this up onece
	this.$attributeCategory.children('.treeview').on('click', function(event) {
		// Grab the target that was clicked;
		var $target = $(event.target);

		if ($target.is('span') && $target.hasClass('item')) {
			$('.change-attribute-category-link', self.$dialog).text($target.attr('data-path-name'));
			$('#AttributeAttributeCategoryId', self.$dialog).val($target.attr('data-id'));
			self.$attributeCategory.hide();
			self.$dialog.dialog('option', 'position', 'center');
		}

	});

	this._enableAttributeSelector();
};

AddAttributes.prototype.reset = function() {
	var self = this;
	var $attributeCategory = this.$dialog.find('div.attribute-category');
	$attributeCategory.hide();
	$('.change-attribute-category-link', this.$dialog).text('Select');

	self._clearFormFields();
};
/**
 * This gets called everytime we open the remove dialog, we need to do some reset stuff
 */
AddAttributes.prototype.open = function(attribute) {
	var self = this;
	self.reset();
	//setter
	this.$dialog.dialog("option", "buttons", [{
		text : "Add",
		'class' : 'btn btn-primary',
		"click" : function() {
			self.submit();
		}
	}, {
		text : "Cancel",
		'class' : 'btn',
		'click' : function() {
			$(this).dialog('close');
		}
	}]);

	// open the remove dialog
	self.$dialog.dialog('open');
};

AddAttributes.prototype.submit = function() {
	var self = this;
	$('#AttributeAddForm').ajaxSubmit({
		// dataType identifies the expected content type of the server response
		dataType : 'json',
		url : '/attributes/add.json',
		beforeSubmit : function(formData, jqForm, options) {
			formData.push({
				name : '_method',
				type : 'text',
				value : 'POST'
			});
			self._clearFormErrors();
			$.each(formData, function(index, value) {
				if (value.name === "data[AttributesCollectible][count]") {
					delete formData[index];
				}
			});
		},
		// success identifies the function to invoke when the server response
		// has been received
		success : function(responseText, statusText, xhr, $form) {
			if (responseText.response.isSuccess) {
				self.$dialog.dialog('close');
				$.blockUI({
					message : 'Item has been submitted!',
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
					},
					timeout : 1000
				});
			} else {
				if (responseText.response.errors) {
					$.each(responseText.response.errors, function(index, value) {
						if (value.inline) {
							$(':input[name="data[' + value.model + '][' + value.name + ']"]', '#AttributeAddForm').after('<div class="error-message">' + value.message + '</div>');
						} else {
							self.$dialog.find('.component-message.error').children('span').text(value.message);
						}

					});
				}
			}
		}
	});

};

var AddCollectibleAttributes = function(options) {
	this.options = $.extend({
		$element : ''
	}, options);

	this.collectibleId = this.options.$element.attr('data-collectible-id');
}

AddCollectibleAttributes.prototype = Object.create(AttributesBase.prototype);

/**
 *Setup the main dialog for removing also add any events
 */
AddCollectibleAttributes.prototype.init = function() {
	var self = this;
	this.$dialog = $('#attribute-collectible-add-dialog').dialog({
		height : 'auto',
		width : 'auto',
		modal : true,
		autoOpen : false,
		resizable : false,
	});

	$('#add-new-item-link').on('click', function() {
		var $element = $(this);
		self.open();
	});

	this.$attributeCategory = this.$dialog.children('.component-dialog').children('.inside').children('.component-view').find('.attribute-category');

	$('.change-attribute-category-link', this.$dialog).on('click', function() {
		self.$attributeCategory.show();
		self.$dialog.dialog('option', 'position', 'center');
	});

	$('#tree', self.$dialog).treeview({
		'hover' : ''
	});

	// this handles setting up the attribute category for the dialog
	// setting this up onece
	this.$attributeCategory.children('.treeview').on('click', function(event) {
		// Grab the target that was clicked;
		var $target = $(event.target);

		if ($target.is('span') && $target.hasClass('item')) {
			$('.change-attribute-category-link', self.$dialog).text($target.attr('data-path-name'));
			$('#AttributeAttributeCategoryId', self.$dialog).val($target.attr('data-id'));
			self.$attributeCategory.hide();
			self.$dialog.dialog('option', 'position', 'center');
		}

	});

	this._enableAttributeSelector();
};

AddCollectibleAttributes.prototype.reset = function() {
	var self = this;
	var $attributeCategory = this.$dialog.find('div.attribute-category');
	$attributeCategory.hide();
	$('.change-attribute-category-link', this.$dialog).text('Select');

	self._clearFormFields();
};
/**
 * This gets called everytime we open the remove dialog, we need to do some reset stuff
 */
AddCollectibleAttributes.prototype.open = function(attribute) {
	var self = this;
	self.reset();
	//setter
	this.$dialog.dialog("option", "buttons", [{
		text : "Add",
		'class' : 'btn btn-primary',
		"click" : function() {
			self.submit();
		}
	}, {
		text : "Cancel",
		'class' : 'btn',
		'click' : function() {
			$(this).dialog('close');
		}
	}]);

	// open the remove dialog
	self.$dialog.dialog('open');
};

AddCollectibleAttributes.prototype.submit = function() {
	var self = this;

	var url = '/attributes_collectibles/add.json';
	// If we are passing in an override admin or the options are set to admin mode
	if (self.options.adminPage) {
		url = '/admin/attributes_collectibles/add.json'
	}

	this.$dialog.find('form').ajaxSubmit({
		// dataType identifies the expected content type of the server response
		dataType : 'json',
		url : url,
		beforeSubmit : function(formData, jqForm, options) {
			formData.push({
				name : '_method',
				type : 'text',
				value : 'POST'
			});
			formData.push({
				name : 'data[AttributesCollectible][collectible_id]',
				type : 'text',
				value : self.collectibleId
			});

			self._clearFormErrors();
		},
		// success identifies the function to invoke when the server response
		// has been received
		success : function(responseText, statusText, xhr, $form) {
			if (responseText.response.isSuccess) {
				self.$dialog.dialog('close');
				$.blockUI({
					message : 'Item has been submitted!',
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
					},
					timeout : 1000
				});
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
};

/**
 *
 */
var AddExistingCollectibleAttributes = function(options) {
	this.options = $.extend({
		$element : ''
	}, options);

	this.collectibleId = this.options.$element.attr('data-collectible-id');
}

AddExistingCollectibleAttributes.prototype = Object.create(AttributesBase.prototype);

/**
 *Setup the main dialog for removing also add any events
 */
AddExistingCollectibleAttributes.prototype.init = function() {
	var self = this;
	this.$dialog = $('#attribute-collectible-add-existing-dialog').dialog({
		height : 'auto',
		width : 'auto',
		modal : true,
		autoOpen : false,
		resizable : false,
	});

	$('#add-existing-item-link').on('click', function() {
		var $element = $(this);
		self.open();
	});

	this._enableAttributeSelector();

	this.$dialog.find('.item-search').children('.paging').on('click', 'span > a', function(event) {
		//scroll top
		var link = $(this).attr('href');
		self._loadCollectiblesList(link);
		event.preventDefault();
	});

	// this is for the add, if they are adding from an existing attribute, used for the collectible page
	this.$dialog.find('.item-search').children('.items').children('ul').on('click', 'li .attribute-list li', function(event) {
		$('#select-attribute-link').text($(this).text());
		$('#AttributesCollectibleAttributeId').val($(this).closest('li.attribute').attr('data-id'));
		self.selectedAttribute = JSON.parse($(this).closest('li.attribute').attr('data-attribute'));
		self.$dialog.find('.item-search').hide();
		self.$dialog.dialog('option', 'position', 'center');
	});

	var $itemSearch = this.$dialog.find('div.item-search');
	var $attributeCategory = this.$dialog.find('div.attribute-category');

	var $seachInput = $itemSearch.children('.search').children('.component-search-input').children('form').children('.searchfield');
	$itemSearch.children('.search').children('.component-search-input').children('form').children('.searchbutton').on('click', function(event) {
		var query = $seachInput.val();

		query = '/collectibles/search?q=' + query;

		self._loadCollectiblesList(query);
		event.preventDefault();
	});

	// Setup a bunch of events we don't want to tear down and rebuild each time
};

AddExistingCollectibleAttributes.prototype.reset = function() {
	var self = this;

	var $attributeCollectibleInputs = this.$dialog.find('fieldset.attribute-collectible-inputs');

	var $itemSearch = this.$dialog.find('div.item-search');
	var $attributeCategory = this.$dialog.find('div.attribute-category');
	$('#select-attribute-link', this.$dialog).text('Select');
	// init stuff
	$itemSearch.hide();
	$attributeCollectibleInputs.show();
	self.selectedAttribute = {};

	self._clearFormFields();
};
/**
 * This gets called everytime we open the remove dialog, we need to do some reset stuff
 */
AddExistingCollectibleAttributes.prototype.open = function(attribute) {
	var self = this;

	self.reset();

	this.$dialog.dialog("option", "buttons", [{
		text : "Add",
		'class' : 'btn btn-primary',
		"click" : function() {
			self.submit();
		}
	}, {
		text : "Cancel",
		'class' : 'btn',
		'click' : function() {
			$(this).dialog('close');
		}
	}]);

	// open the remove dialog
	self.$dialog.dialog('open');
};

AddExistingCollectibleAttributes.prototype.submit = function() {
	var self = this;

	var url = '/attributes_collectibles/add.json';
	// If we are passing in an override admin or the options are set to admin mode
	if (self.options.adminPage) {
		url = '/admin/attributes_collectibles/add.json'
	}

	this.$dialog.find('form').ajaxSubmit({
		// dataType identifies the expected content type of the server response
		dataType : 'json',
		url : url,
		beforeSubmit : function(formData, jqForm, options) {
			formData.push({
				name : '_method',
				type : 'text',
				value : 'POST'
			});
			formData.push({
				name : 'data[AttributesCollectible][collectible_id]',
				type : 'text',
				value : self.collectibleId
			});

			self._clearFormErrors();
		},
		// success identifies the function to invoke when the server response
		// has been received
		success : function(responseText, statusText, xhr, $form) {
			if (responseText.response.isSuccess) {
				self.$dialog.dialog('close');
				$.blockUI({
					message : 'Item has been submitted!',
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
					},
					timeout : 1000
				});
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
};

var RemoveAttributes = function(options) {
	this.options = $.extend({
		$element : ''
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

	this.options.$element.children('table').find('tr').children('.actions').find('a.remove-attribute').on('click', function() {
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

	$('#AttributeRemoveForm').ajaxSubmit({
		// dataType identifies the expected content type of the server response
		dataType : 'json',
		url : '/attributes/remove.json',
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
				self.$dialog.dialog('close');
				$.blockUI({
					message : 'Removal has been submitted!',
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
					},
					timeout : 1000

				});
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
		$element : ''
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

	this.options.$element.children('table').find('tr').children('.actions').find('a.remove-link').on('click', function() {
		var $element = $(this);
		self.open(self.getAttribute($element));
	});
};

RemoveAttributeLinks.prototype.reset = function() {
	var self = this;
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
					self.$dialog.dialog('close');
					$.blockUI({
						message : 'Removal has been submitted!',
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
						},
						timeout : 1000

					});
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

var UpdateAttributes = function(options) {
	this.options = $.extend({
		$element : '',
		$openElement : null,
		$dataElement : null
	}, options);
}

UpdateAttributes.prototype = Object.create(AttributesBase.prototype);

/**
 *Setup the main dialog for removing also add any events
 */
UpdateAttributes.prototype.init = function() {
	var self = this;
	this.$dialog = $('#update-attribute-dialog').dialog({
		height : 'auto',
		width : 'auto',
		modal : true,
		autoOpen : false,
		resizable : false,
	});

	var $openElement = null;

	if (this.options.$openElement) {
		$openElement = this.options.$openElement;
	} else {
		$openElement = this.options.$element.children('table').find('tr').children('.actions').find('a.edit-attribute-link');
	}
	$openElement.on('click', function() {
		if (self.options.$dataElement) {
			self.open(new Attribute({
				$element : self.options.$dataElement
			}));
		} else {
			var $element = $(this);
			self.open(self.getAttribute($element));
		}
	});

	this.$attributeCategory = this.$dialog.children('.component-dialog').children('.inside').children('.component-view').find('.attribute-category');

	$('.change-attribute-category-link', this.$dialog).on('click', function() {
		self.$attributeCategory.show();
		self.$dialog.dialog('option', 'position', 'center');
	});

	$('#tree', self.$dialog).treeview({
		'hover' : ''
	});

	// this handles setting up the attribute category for the dialog
	// setting this up onece
	this.$attributeCategory.children('.treeview').on('click', function(event) {
		// Grab the target that was clicked;
		var $target = $(event.target);

		if ($target.is('span') && $target.hasClass('item')) {
			$('.change-attribute-category-link', self.$dialog).text($target.attr('data-path-name'));
			$('#AttributeAttributeCategoryId', self.$dialog).val($target.attr('data-id'));
			self.$attributeCategory.hide();
			self.$dialog.dialog('option', 'position', 'center');
		}

	});
};

UpdateAttributes.prototype.reset = function() {
	var self = this;

	var $attributeCategory = this.$dialog.find('div.attribute-category');
	$attributeCategory.hide();

	self._clearFormFields();
};
/**
 * This gets called everytime we open the remove dialog, we need to do some reset stuff
 */
UpdateAttributes.prototype.open = function(attribute) {
	var self = this;
	// reset the remove
	self.reset();

	var attributeData = attribute.getAttributeData();

	$(':input[name="data[Attribute][attribute_category_id]"]', this.$dialog).val(attributeData.categoryId);
	$(':input[name="data[Attribute][name]"]', this.$dialog).val(attributeData.name);
	$(':input[name="data[Attribute][description]"]', this.$dialog).val(attributeData.description);
	$(':input[name="data[Attribute][manufacture_id]"]', this.$dialog).val(attributeData.manufacturerId);
	$(':input[name="data[Attribute][scale_id]"]', this.$dialog).val(attributeData.scaleId);
	$('.change-attribute-category-link', this.$dialog).text(attributeData.categoryName);

	this.$dialog.dialog("option", "buttons", [{
		text : 'Submit',
		'class' : 'btn btn-primary',
		"click" : function() {
			self.submit(attribute);
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

UpdateAttributes.prototype.submit = function(attribute) {
	var self = this;

	var id = attribute.getAttributeData().id;
	var url = '/attributes/update.json';
	// If we are passing in an override admin or the options are set to admin mode
	if (self.options.adminPage) {
		url = '/admin/attributes/update.json'
	}

	this.$dialog.find('form').ajaxSubmit({
		// dataType identifies the expected content type of the server response
		dataType : 'json',
		url : url,
		beforeSubmit : function(formData, jqForm, options) {
			self._clearFormErrors();
			formData.push({
				'name' : 'data[Attribute][id]',
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
				self.$dialog.dialog('close');
				var message = 'Update has been submitted!';
				if (self.options.adminPage) {
					message = 'The part was successfully updated!';
				}
				$.blockUI({
					message : 'Update has been submitted!',
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
					},
					timeout : 1000

				});
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

var UpdateCollectibleAttributes = function(options) {
	this.options = $.extend({
		$element : ''
	}, options);
}

UpdateCollectibleAttributes.prototype = Object.create(AttributesBase.prototype);

/**
 *Setup the main dialog for removing also add any events
 */
UpdateCollectibleAttributes.prototype.init = function() {
	var self = this;
	this.$dialog = $('#update-attribute-collectible-dialog').dialog({
		height : 'auto',
		width : 'auto',
		modal : true,
		autoOpen : false,
		resizable : false,
	});

	this.options.$element.children('table').find('tr').children('.actions').find('a.edit-attribute-collectible-link').on('click', function() {
		var $element = $(this);
		self.open(self.getAttribute($element));
	});
};

UpdateCollectibleAttributes.prototype.reset = function() {
	var self = this;
};
/**
 * This gets called everytime we open the remove dialog, we need to do some reset stuff
 */
UpdateCollectibleAttributes.prototype.open = function(attribute) {
	var self = this;
	// reset the remove
	self.reset();

	var attributeData = attribute.getAttributeCollectible();

	$(':input[name="data[AttributesCollectible][count]"]', this.$dialog).val(attributeData.count);

	this.$dialog.dialog("option", "buttons", [{
		text : 'Submit',
		'class' : 'btn btn-primary',
		"click" : function() {
			self.submit(attribute);
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

UpdateCollectibleAttributes.prototype.submit = function(attribute) {
	var self = this;

	var id = attribute.getAttributeCollectible().id;

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
				self.$dialog.dialog('close');
				var message = 'Update has been submitted!';
				if (self.options.adminPage) {
					message = 'The part was successfully updated!';
				}
				$.blockUI({
					message : 'Update has been submitted!',
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
					},
					timeout : 1000

				});
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
/**
 *This is going to be the object that handles adding collectible attributes during
 * the contribute process.  This needs to extend the base one so that it can do stuff slightly differently
 */
var ContributeAddCollectibleAttributes = function(options) {
	this.options = $.extend({
		$element : ''
	}, options);

}

ContributeAddCollectibleAttributes.prototype = Object.create(AddCollectibleAttributes.prototype);

/**
 * Overriding, instead of submitting to the server, we will add it to the page
 */
ContributeAddCollectibleAttributes.prototype.submit = function() {
	this._validateAttribute(function() {

		var $form = this.$dialog.find('form');
		var row = $('<tr></tr>');

		var categoryName = $('.change-attribute-category-link', $form).text();
		var categoryId = $(':input[name="data[Attribute][attribute_category_id]"]', $form).val();
		var name = $(':input[name="data[Attribute][name]"]', $form).val();
		var description = $(':input[name="data[Attribute][description]"]', $form).val();
		var manId = $(':input[name="data[Attribute][manufacture_id]"] option:selected', $form).val();
		var manName = $(':input[name="data[Attribute][manufacture_id]"] option:selected', $form).text();
		var scaleId = $(':input[name="data[Attribute][scale_id]"] option:selected', $form).val();
		var scaleName = $(':input[name="data[Attribute][scale_id]"] option:selected', $form).text();
		var count = $(':input[name="data[AttributesCollectible][count]"]', $form).val();

		var categoryCol = $('<td></td>').text(categoryName);
		var nameCol = $('<td></td>').text(name);
		var descCol = $('<td></td>').text(description);
		var manCol = $('<td></td>').text(categoryName);
		var scaleCol = $('<td></td>').text(scaleName);
		var countCol = $('<td></td>').text(count);
		var action = $('<td></td>').html('<div class="btn-group"><a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Action<span class="caret"></span></a> <ul class="dropdown-menu"> <li><a class="link remove-attribute" title="Remove Part">Remove Part</a></li></ul></div>');

		row.append(categoryCol).append(nameCol).append(descCol).append(manCol).append(scaleCol).append(countCol).append(action);

		//var $hiddenId = $('<input/>').attr('type', 'hidden').attr('name', 'data[Attribute][' + this.attributeNumber + '][id]').val(attributeId);
		var $hiddenDescription = $('<input/>').attr('type', 'hidden').attr('name', 'data[AttributesCollectible][' + attributeNumber + '][Attribute][description]').val(description);
		var $hiddenName = $('<input/>').attr('type', 'hidden').attr('name', 'data[AttributesCollectible][' + attributeNumber + '][Attribute][name]').val(name);
		var $hiddenCategory = $('<input/>').attr('type', 'hidden').attr('name', 'data[AttributesCollectible][' + attributeNumber + '][Attribute][attribute_category_id]').val(categoryId);
		var $hiddenManId = $('<input/>').attr('type', 'hidden').attr('name', 'data[AttributesCollectible][' + attributeNumber + '][Attribute][manufacture_id]').val(manId);
		var $hiddenScaleId = $('<input/>').attr('type', 'hidden').attr('name', 'data[AttributesCollectible][' + attributeNumber + '][Attribute][scale_id]').val(scaleId);
		var $hiddenCount = $('<input/>').attr('type', 'hidden').attr('name', 'data[AttributesCollectible][' + attributeNumber + '][count]').val(count);

		row.append($hiddenDescription).append($hiddenName).append($hiddenCategory).append($hiddenManId).append($hiddenScaleId).append($hiddenCount);

		$('#collectible-attributes-list').children('table').append(row);

		attributeNumber++;
		this.$dialog.dialog('close');	});
};

var ContributeAddExistingCollectibleAttributes = function(options) {
	this.options = $.extend({
		$element : ''
	}, options);

}

ContributeAddExistingCollectibleAttributes.prototype = Object.create(AddExistingCollectibleAttributes.prototype);

ContributeAddExistingCollectibleAttributes.prototype.submit = function() {
	this._validateAttributesCollectible(function() {

		var $form = this.$dialog.find('form');
		var row = $('<tr></tr>');

		var count = $(':input[name="data[AttributesCollectible][count]"]', $form).val();
		var attributeId = $(':input[name="data[AttributesCollectible][attribute_id]"]', $form).val();

		var categoryCol = $('<td></td>').text(this.selectedAttribute.AttributeCategory.path_name);
		var nameCol = $('<td></td>').text(this.selectedAttribute.name);
		var descCol = $('<td></td>').text(this.selectedAttribute.description);
		var manCol = $('<td></td>').text(this.selectedAttribute.Manufacture.title);
		var scaleCol = $('<td></td>').text(this.selectedAttribute.Scale.scale);
		var countCol = $('<td></td>').text(count);
		var action = $('<td></td>').html('<div class="btn-group"><a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Action<span class="caret"></span></a> <ul class="dropdown-menu"> <li><a class="link remove-attribute" title="Remove Part">Remove Part</a></li></ul></div>');

		row.append(categoryCol).append(nameCol).append(descCol).append(manCol).append(scaleCol).append(countCol).append(action);

		var $hiddenCount = $('<input/>').attr('type', 'hidden').attr('name', 'data[AttributesCollectible][' + attributeNumber + '][count]').val(count);
		var $hiddenAttributeId = $('<input/>').attr('type', 'hidden').attr('name', 'data[AttributesCollectible][' + attributeNumber + '][attribute_id]').val(attributeId);

		row.append($hiddenAttributeId).append($hiddenCount);

		$('#collectible-attributes-list').children('table').append(row);

		attributeNumber++;
		this.$dialog.dialog('close');
	});
};

var ContributeRemoveAttribute = function(options) {
	this.options = $.extend({
		$element : ''
	}, options);

}
/**
 *Setup the main dialog for removing also add any events
 */
ContributeRemoveAttribute.prototype.init = function() {
	var self = this;

	this.options.$element.children('table').on('click', 'tr > td > div > ul > li > a.remove-attribute', function() {
		var $element = $(this);
		$element.closest('tr').remove();

		// if (attributeNumber > 0) {
		// attributeNumber--;
		// }
	});

};

