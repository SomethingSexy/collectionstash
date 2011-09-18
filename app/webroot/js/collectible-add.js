//TODO changing collectible type should be its own object
var collectibleAdd = function() {

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

	// function handleManufactureChange(element) {
	// var manid = $(element).val();
	//
	// $.ajax({
	// type : "POST",
	// dataType : 'json',
	// url : '/manufactures/getManufactureData.json',
	// data : 'data[id]=' + manid,
	// beforeSend : function(jqXHR, settings) {
	// //$.blockUI();
	// },
	// success : function(data, textStatus, XMLHttpRequest) {
	// var success = data.success.isSuccess;
	//
	// if(success) {
	// if(data.data.licenses.length !== 0) {
	// var output = [];
	// $.each(data.data.licenses, function(key, value) {
	// output.push('<option value="' + key + '">' + value + '</option>');
	// });
	//
	// $('#CollectibleLicenseId').find('option').remove().end().append(output.join(''));
	//
	// $('#CollectibleLicenseId').parent('li').show();
	// //.append('<option value="whatever">text</option>')
	// //.val('whatever');
	//
	// } else {
	// $('#CollectibleLicenseId').find('option').remove();
	// $('#CollectibleLicenseId').parent('li').hide();
	// }
	//
	// if(data.data.types.length !== 0) {
	// var output = [];
	// $.each(data.data.types, function(key, value) {
	// output.push('<option value="' + key + '">' + value + '</option>');
	// });
	//
	// $('#CollectibleCollectibletypeId').find('option').remove().end().append(output.join(''));
	//
	// $('#CollectibleCollectibletypeId').parent('li').show();
	// //.append('<option value="whatever">text</option>')
	// //.val('whatever');
	//
	// } else {
	// $('#CollectibleCollectibletypeId').find('option').remove();
	// $('#CollectibleCollectibletypeId').parent('li').hide();
	// }
	//
	// if(data.data.specializedTypes.length !== 0) {
	// var output = [];
	// output.push("<option value=''></option>");
	// $.each(data.data.specializedTypes, function(key, value) {
	// output.push('<option value="' + key + '">' + value + '</option>');
	// });
	//
	// $('#CollectibleSpecializedTypeId').find('option').remove().end().append(output.join(''));
	//
	// $('#CollectibleSpecializedTypeId').parent('li').show();
	// //.append('<option value="whatever">text</option>')
	// //.val('whatever');
	//
	// } else {
	// $('#CollectibleSpecializedTypeId').find('option').remove();
	// $('#CollectibleSpecializedTypeId').parent('li').hide();
	// }
	//
	// if(data.data.series.length !== 0) {
	// var output = [];
	// $.each(data.data.series, function(key, value) {
	// output.push('<option value="' + key + '">' + value + '</option>');
	// });
	//
	// $('#CollectibleSeriesId').find('option').remove().end().append(output.join(''));
	//
	// $('#CollectibleSeriesId').parent('li').show();
	// //.append('<option value="whatever">text</option>')
	// //.val('whatever');
	//
	// } else {
	// $('#CollectibleSeriesId').find('option').remove();
	// $('#CollectibleSeriesId').parent('li').hide();
	// }
	//
	// } else {
	//
	// }
	// },
	// complete : function(jqXHR, textStatus) {
	// //$.unblockUI();
	// }
	// });
	//
	// //CollectibleLicenseId
	// //CollectibleCollectibletypeId
	// }

	return {
		init : function() {
			$('#CollectibleLicenseId').change(function() {
				handleLicenseChange(this)
			});

			$('#change-collectibletype-link').click(function() {
				initCollectibleChange();
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
