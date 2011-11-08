//TODO changing collectible type should be its own object
var collectibleAdd = function() {

	var _currentSeriesLevel = 0;

	function handleLicenseChange(element) {
		var licenseid = $(element).val();
		var manid = $('#CollectibleManufactureId').val();
		$.ajax({
			type : "POST",
			dataType : 'json',
			url : '/licenses/getLicenseData.json',
			data : 'data[license_id]=' + licenseid + '&data[manufacture_id]=' + manid,
			beforeSend : function(jqXHR, settings) {
				//$.blockUI();
			},
			success : function(data, textStatus, XMLHttpRequest) {
				var success = data.success.isSuccess;

				if(success) {
					if(data.data.series.length !== 0) {
						var output = [];
						$.each(data.data.series, function(key, value) {
							output.push('<option value="' + key + '">' + value + '</option>');
						});
						//TODO: If the license changes, then basically reset the series...this should return
						//something to tell us if we have series avaliable to choose from
						$('#CollectibleSeriesId').find('option').remove().end().append(output.join(''));

						$('#CollectibleSeriesId').parent('li').show();
						//.append('<option value="whatever">text</option>')
						//.val('whatever');

					} else {
						$('#CollectibleSeriesId').find('option').remove();
						$('#CollectibleSeriesId').parent('li').hide();
					}
				} else {

				}
			},
			complete : function(jqXHR, textStatus) {
				//$.unblockUI();
			}
		});
	}

	function handleCollectibleTypeChange() {
		var collectibleTypeid = $('#CollectibleCollectibletypeId').val();
		var manid = $('#CollectibleManufactureId').val();
		$.ajax({
			type : "POST",
			dataType : 'json',
			url : '/specialized_types/getSpecializedTypesData.json',
			data : 'data[collectibletype_id]=' + collectibleTypeid + '&data[manufacture_id]=' + manid,
			beforeSend : function(jqXHR, settings) {
				//$.blockUI();
			},
			success : function(data, textStatus, XMLHttpRequest) {
				var success = data.success.isSuccess;

				if(success) {
					if(data.data.specializedTypes.length !== 0) {
						var output = [];
						output.push("<option value=''></option>");
						$.each(data.data.specializedTypes, function(key, value) {
							output.push('<option value="' + key + '">' + value + '</option>');
						});

						$('#CollectibleSpecializedTypeId').find('option').remove().end().append(output.join(''));

						$('#CollectibleSpecializedTypeId').parent('li').show();
						//.append('<option value="whatever">text</option>')
						//.val('whatever');

					} else {
						$('#CollectibleSpecializedTypeId').find('option').remove();
						$('#CollectibleSpecializedTypeId').parent('li').hide();
					}
				} else {
					$('#CollectibleSpecializedTypeId').find('option').remove();
					$('#CollectibleSpecializedTypeId').parent('li').hide();
				}
			},
			complete : function(jqXHR, textStatus) {
				$('#edit-collectibletype-dialog').dialog("close");
			}
		});
	}

	function handleChangeCollectibleType(element) {
		var manid = $('#CollectibleManufactureId').val();
		var collectibleTypeid = $(element).val();
		$('#edit-collectibletype-dialog').find('.error-message').remove();
		$('#typeLevel1').parent('li').remove();
		if(collectibleTypeid !== '-1') {
			$.ajax({
				type : "POST",
				dataType : 'json',
				url : '/collectibletypes/getCollectibletypesData.json',
				data : 'data[collectibletype_id]=' + collectibleTypeid + '&data[manufacture_id]=' + manid + '&data[init]=false',
				beforeSend : function(jqXHR, settings) {
					//$.blockUI();
				},
				success : function(data, textStatus, XMLHttpRequest) {
					var success = data.success.isSuccess;

					if(success) {
						if(data.data.collectibleTypes && data.data.collectibleTypes.length > 0) {
							var output = [];
							output.push('<option value="-1">Select</option>');
							//TODO need to wrap this in the LI and setup the label tags appropriately for each level
							var $select = $('<select></select>').attr('id', 'typeLevel1').change(function() {
								//handleChangeCollectibleType(this);
							});
							$.each(data.data.collectibleTypes, function(key, value) {
								output.push('<option value="' + value.Collectibletype.id + '">' + value.Collectibletype.name + '</option>');
							});
							$select.html(output.join(''))

							var $li = $('<li></li>');
							var $labelWrapper = $('<div></div>').addClass('label-wrapper');
							var $label = $('<label></label>').attr('for', 'attributeLevel').text('Collectible Type');

							$labelWrapper.prepend($label);
							$li.prepend($select);
							$li.prepend($labelWrapper);
							$('#edit-collectibletype-dialog-fields').append($li);
						}
					} else {
						//eh do nothing since we are changing
					}
				},
				complete : function(jqXHR, textStatus) {
					//$.unblockUI();
				}
			});
		}

	}

	function initCollectibleChange() {
		var manid = $('#CollectibleManufactureId').val();
		var collectibleTypeid = $('#CollectibleCollectibletypeId').val();
		$('#edit-collectibletype-dialog').find('.error-message').remove();
		$('#edit-collectibletype-dialog-fields').children().remove();
		$.ajax({
			type : "POST",
			dataType : 'json',
			url : '/collectibletypes/getCollectibletypesData.json',
			data : 'data[collectibletype_id]=' + collectibleTypeid + '&data[manufacture_id]=' + manid + '&data[init]=true',
			beforeSend : function(jqXHR, settings) {
				//$.blockUI();
			},
			success : function(data, textStatus, XMLHttpRequest) {
				var success = data.success.isSuccess;

				if(success) {
					if(data.data.collectibleTypes) {
						//Hard coding for now, fix this so we know the number of levels returned and loop through them
						//TODO: make this code reusable
						if(data.data.collectibleTypes.collectibletypes_L0) {
							var output = [];
							output.push('<option value="-1">Select</option>');
							//TODO need to wrap this in the LI and setup the label tags appropriately for each level
							var $select = $('<select></select>').attr('id', 'typeLevel0').change(function() {
								//handleChange(this)
								handleChangeCollectibleType(this);
							});
							var selected = data.data.collectibleTypes.selectedTypes.L0;
							$.each(data.data.collectibleTypes.collectibletypes_L0, function(key, value) {
								if(selected == key) {
									output.push('<option value="' + key + '" selected="selected">' + value + '</option>');
								} else {
									output.push('<option value="' + key + '">' + value + '</option>');
								}

							});
							$select.html(output.join(''))

							var $li = $('<li></li>');
							var $labelWrapper = $('<div></div>').addClass('label-wrapper');
							var $label = $('<label></label>').attr('for', 'attributeLevel').text('Collectible Type');

							$labelWrapper.prepend($label);
							$li.prepend($select);
							$li.prepend($labelWrapper);
							$('#edit-collectibletype-dialog-fields').prepend($li);

							if(data.data.collectibleTypes.collectibletypes_L1 && data.data.collectibleTypes.collectibletypes_L1.length > 0) {
								var output = [];
								output.push('<option value="-1">Select</option>');
								//TODO need to wrap this in the LI and setup the label tags appropriately for each level
								var $select = $('<select></select>').attr('id', 'typeLevel1').change(function() {
									//handleChange(this)
								});
								var selected = data.data.collectibleTypes.selectedTypes.L1;
								$.each(data.data.collectibleTypes.collectibletypes_L1, function(key, value) {
									if(selected == value.Collectibletype.id) {
										output.push('<option value="' + value.Collectibletype.id + '" selected="selected">' + value.Collectibletype.name + '</option>');
									} else {
										output.push('<option value="' + value.Collectibletype.id + '">' + value.Collectibletype.name + '</option>');
									}

								});
								$select.html(output.join(''))

								var $li = $('<li></li>');
								var $labelWrapper = $('<div></div>').addClass('label-wrapper');
								var $label = $('<label></label>').attr('for', 'attributeLevel').text('Collectible Type');

								$labelWrapper.prepend($label);
								$li.prepend($select);
								$li.prepend($labelWrapper);
								$('#edit-collectibletype-dialog-fields').append($li);

							} else {
								$('#typeLevel1').parent('li').remove();
							}
						} else {
							//error
						}
					} else {
						//error
					}
				} else {

				}
				$("#edit-collectibletype-dialog").dialog("open");
			},
			complete : function(jqXHR, textStatus) {
				//$.unblockUI();
			}
		});

	}

	function changeCollectibleType() {
		//This means that we don't have a level 1
		var collectibleTypeId = '';
		var collectibleText = '';
		var success = true;
		$('#edit-collectibletype-dialog').find('.error-message').remove();
		if($('#typeLevel1').length != 0 && $('#typeLevel1').val() !== '-1') {
			collectibleTypeId = $('#typeLevel1 option:selected').val();
			collectibleText = $('#typeLevel1 option:selected').text();
		} else {
			if($('#typeLevel0').val() !== '-1') {
				collectibleTypeId = $('#typeLevel0 option:selected').val();
				collectibleText = $('#typeLevel0 option:selected').text();
			} else {
				$('#typeLevel0').after('<div class="error-message">Please select a type.</div>');
				success = false;
			}
		}

		if(success) {
			$('#CollectibleCollectibletypeId').val(collectibleTypeId);
			$('#change-collectibletype-link').text(collectibleText);
		}

		return success;
	}

	/**
	 * This gets called when we are about to submit the change,
	 * it does the validation and if it is valid it will switch
	 * the value.
	 *
	 * TODO combine these with the collectible type
	 *
	 * We need to namespace out the level ids so that we do not collide
	 * with other types, or have a context...probably can't use ids then
	 */
	function isChangeSeries() {
		//This means that we don't have a level 1
		var collectibleTypeId = '';
		var collectibleText = '';
		var success = true;
		$('#edit-series-dialog').find('.error-message').remove();
		/**
		 * TODO: We might have to make this automatic, with what the level
		 * is
		 */
		if($('#seriesLevel' + _currentSeriesLevel + ' option:selected').length != 0 && $('#seriesLevel' + _currentSeriesLevel + ' option:selected').val() !== '-1') {
			collectibleTypeId = $('#seriesLevel' + _currentSeriesLevel + ' option:selected').val();
			collectibleText = $('#seriesLevel' + _currentSeriesLevel + ' option:selected').text();
		} else {
			$('#seriesLevel' + _currentSeriesLevel + ' option:selected').after('<div class="error-message">Please select a series.</div>');
			success = false;			
		}

		if(success) {
			$('#CollectibleSeriesId').val(collectibleTypeId);
			$('#change-series-link').text(collectibleText);
		}		
		
		return success;
	}

	function buildSelect(data) {
		var levelCount = data.data.levelCount;
		//Set the current series level to the return level count minus 1
		_currentSeriesLevel = levelCount - 1;
		var i = 0;
		//This is a cheap cheap hack, need ot figure out a better way. Creating a dummy
		//object to store the jQuery DOM objects so we make sure events properly carry over
		var $html = $('<li></li>');
		for(i; i < levelCount; i++) {
			var currentLevelList = data.data['L' + i];
			var output = [];
			output.push('<option value="-1">Select</option>');
			//TODO need to wrap this in the LI and setup the label tags appropriately for each level
			var $select = $('<select></select>').attr('id', 'seriesLevel' + i).data('level', i).on('change',function() {
				//handleChange(this)
				handleSeriesSelect(this);
			});
			var selected = data.data.selected['L' + i];
			$.each(currentLevelList, function(key, value) {
				if(selected == key) {
					output.push('<option value="' + key + '" selected="selected">' + value + '</option>');
				} else {
					output.push('<option value="' + key + '">' + value + '</option>');
				}

			});
			$select.html(output.join(''))

			var $li = $('<li></li>');
			var $labelWrapper = $('<div></div>').addClass('label-wrapper');
			var $label = $('<label></label>').attr('for', 'attributeLevel').text('Series');

			$labelWrapper.prepend($label);
			$li.prepend($select);
			$li.prepend($labelWrapper);
			$html.append($li)
			//html += '<li>' + $li.html() + '</li>';
		}
		
		return $html;
	}

	/**
	 * This gets called when a series is selected from a drop down, it determines
	 * if there are any other series below it, in the hierarchy
	 */
	function handleSeriesSelect(element) {
		var manid = $('#CollectibleManufactureId').val();
		var licenseid = $('#CollectibleLicenseId option:selected').val();
		var seriesid = $(element).val();
		$('#edit-series-dialog').find('.error-message').remove();
		var currentLevel = $(element).data('level')
		
		if(currentLevel < _currentSeriesLevel) {
			var i = currentLevel + 1;
			for(i; i <= _currentSeriesLevel; i++) {
				$('#seriesLevel' + i).parent('li').remove();
			}
			_currentSeriesLevel = currentLevel;
		} 	
		
		if(seriesid !== '-1') {
			$.ajax({
				type : "POST",
				dataType : 'json',
				url : '/series/getSeriesData.json',
				data : 'data[series_id]=' + seriesid + '&data[manufacture_id]=' + manid + '&data[license_id]=' + licenseid,
				beforeSend : function(jqXHR, settings) {
					//$.blockUI();
				},
				success : function(data, textStatus, XMLHttpRequest) {
					var success = data.success.isSuccess;
					/*
					 * This is going to return all series so we will need to redraw everything
					 */
					if(success) {
						if(data.data.levelCount) {
							var html = buildSelect(data);
							$('#edit-series-dialog-fields').children().remove();
							$('#edit-series-dialog-fields').prepend(html);								
						} else {
							//error, no level count
						}
					} else {
						//eh do nothing since we are changing
					}
				},
				complete : function(jqXHR, textStatus) {
					//$.unblockUI();
				}
			});
		}

	}

	/**
	 * This method gets called when the dialog is opened for either an edit
	 * or a series add.  It initalizes the adding/editing of a series
	 *
	 * TODO: We need to combine this with the collectible type for complete reuse
	 */
	function initSeriesChange() {
		//We need the manufacture id and the license id to get the series for this one
		_currentSeriesLevel = 0;
		var manid = $('#CollectibleManufactureId').val();
		var licenseid = $('#CollectibleLicenseId option:selected').val();
		var seriesId = $('#CollectibleSeriesId').val();
		$('#edit-series-dialog').find('.error-message').remove();
		$('#edit-series-dialog-fields').children().remove();
		$.ajax({
			type : "POST",
			dataType : 'json',
			url : '/series/getSeriesData.json',
			data : 'data[series_id]=' + seriesId + '&data[manufacture_id]=' + manid + '&data[license_id]=' + licenseid,
			beforeSend : function(jqXHR, settings) {
				//$.blockUI();
			},
			success : function(data, textStatus, XMLHttpRequest) {
				var success = data.success.isSuccess;

				if(success) {
					/*
					 * We should only be return the levels that exist
					 */
					if(data.data.levelCount) {
						var html = buildSelect(data);
						$('#edit-series-dialog-fields').prepend(html);								
					} else {
						//error, no level count
					}
				} else {

				}
				$("#edit-series-dialog").dialog("open");
			},
			complete : function(jqXHR, textStatus) {
				//$.unblockUI();
			}
		});

	}

	return {
		init : function() {
			$('#CollectibleLicenseId').change(function() {
				handleLicenseChange(this)
			});

			$('#change-collectibletype-link').click(function() {
				initCollectibleChange();
			});

			$('#change-series-link').click(function() {
				initSeriesChange();
			});

			$('#CollectibleLimited').change(function() {
				if($(this).is(':checked')) {
					$('#CollectibleEditionSize').parent('li').show();
				} else {
					$('#CollectibleEditionSize').val('').parent('li').hide();
				}
			});
			$('#CollectibleMsrp').blur(function() {
				$('#CollectibleMsrp').formatCurrency();
			});
			//TODO this is really only for image add so should get moved out of here eventually
			$('#remove-image-submit').click(function() {
				$('#remove-image-form').submit();
				return false;
			});

			$("#edit-series-dialog").dialog({
				'autoOpen' : false,
				'width' : 500,
				'height' : 375,
				'modal' : true,
				'resizable' : false,
				'buttons' : {
					Change : function() {
						if(isChangeSeries()) {
							$('#edit-series-dialog').dialog("close");
						}
					},
					Cancel : function() {
						$(this).dialog("close");

					}
				},
				close : function(event, ui) {

				}
			});

			$("#edit-collectibletype-dialog").dialog({
				'autoOpen' : false,
				'width' : 500,
				'height' : 325,
				'modal' : true,
				'resizable' : false,
				'buttons' : {
					Change : function() {
						if(changeCollectibleType()) {
							handleCollectibleTypeChange();
						}
					},
					Cancel : function() {
						$(this).dialog("close");

					}
				},
				close : function(event, ui) {

				}
			});
		},
		update : function() {

		}
	};
}();
$(function() {
	collectibleAdd.init();
});
