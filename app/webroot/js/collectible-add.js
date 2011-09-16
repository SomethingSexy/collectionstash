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

	function handleCollectibleTypeChange(element) {
		var collectibleTypeid = $(element).val();
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

				}
			},
			complete : function(jqXHR, textStatus) {
				//$.unblockUI();
			}
		});
	}

	function handleManufactureChange(element) {
		var manid = $(element).val();

		$.ajax({
			type : "POST",
			dataType : 'json',
			url : '/manufactures/getManufactureData.json',
			data : 'data[id]=' + manid,
			beforeSend : function(jqXHR, settings) {
				//$.blockUI();
			},
			success : function(data, textStatus, XMLHttpRequest) {
				var success = data.success.isSuccess;

				if(success) {
					if(data.data.licenses.length !== 0) {
						var output = [];
						$.each(data.data.licenses, function(key, value) {
							output.push('<option value="' + key + '">' + value + '</option>');
						});

						$('#CollectibleLicenseId').find('option').remove().end().append(output.join(''));

						$('#CollectibleLicenseId').parent('li').show();
						//.append('<option value="whatever">text</option>')
						//.val('whatever');

					} else {
						$('#CollectibleLicenseId').find('option').remove();
						$('#CollectibleLicenseId').parent('li').hide();
					}

					if(data.data.types.length !== 0) {
						var output = [];
						$.each(data.data.types, function(key, value) {
							output.push('<option value="' + key + '">' + value + '</option>');
						});

						$('#CollectibleCollectibletypeId').find('option').remove().end().append(output.join(''));

						$('#CollectibleCollectibletypeId').parent('li').show();
						//.append('<option value="whatever">text</option>')
						//.val('whatever');

					} else {
						$('#CollectibleCollectibletypeId').find('option').remove();
						$('#CollectibleCollectibletypeId').parent('li').hide();
					}

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

		//CollectibleLicenseId
		//CollectibleCollectibletypeId
	}

	return {
		init : function() {
			$('#CollectibleManufactureId').change(function() {
				handleManufactureChange(this)
			});
			$('#CollectibleLicenseId').change(function() {
				handleLicenseChange(this)
			});
			$('#CollectibleCollectibletypeId').change(function() {
				handleCollectibleTypeChange(this)
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
		},
		update : function() {

		}
	};
}();
$(function() {
	collectibleAdd.init();
});
