$( function() {
    DED.init();
    $(".ui-icon-info").tooltip({ position: 'center right', opacity: 0.7});
    // $('.add-variant-attribute').hover( function() {
	        // $(this).addClass("ui-state-hover");
	    // }, function() {
	        // $(this).removeClass("ui-state-hover");
	    // }
    // );

    $( "#add-attribute-dialog" ).dialog({
        'autoOpen' : false,
        'width' : 500,
        'height': 425,
        'modal': true,
        'resizable': false,
        'buttons': {
            "Submit": function() {
                var successful = DED.submit();
                if (successful) {
                	$( this ).dialog( "close" );
               	 	DED.reset();                	
                }
            },
            Cancel: function() {
                $( this ).dialog( "close" );
                DED.reset();
            }
        },
        close: function(event, ui) {
            DED.reset();

        }
    });

    $('.add-attribute').click( function() {
        DED.initAttributeList();
    })
});
var DED = function() {
    var attributeLevel = 0;
    var attributeNumber = 1;
    function isFirstLevel() {
        return attributeLevel == 1;

    }

    function handleChange(element) {
        var currentLevel = $(element).data('id');

        if(currentLevel) {
            //If the current level being selected, is less than the total levels
            //Then remove all levels above it
            if(currentLevel < attributeLevel) {
                var i = currentLevel + 1;
                for (i; i <= attributeLevel; i++) {
                    $('#attributeLevel' + i).parent('li').remove();
                }
                attributeLevel = currentLevel;
            } else {

            }
        } else {
            //null so not added yet
        }

        $('#attributeLevel' + attributeLevel).data('id', attributeLevel);

        DED.addAttributeList('attributeLevel' + attributeLevel);
    }

    function handleSuccess(data) {
        //Removal
        //TODO Show the description field when they are at the end of a category

        if(data.length == 0) {

        } else {
            attributeLevel++;
            var output = [];
            output.push('<option value="-1">Select</option>');
            //TODO need to wrap this in the LI and setup the label tags appropriately for each level
            var $select = $('<select></select>').addClass('attributeLevels').attr('id','attributeLevel' + attributeLevel).change( function() {
                handleChange(this)
            });
            $.each(data, function(key, value) {
                output.push('<option value="'+ key +'">'+ value +'</option>');
            });
            $select.html(output.join(''))

            var $li = $('<li></li>');
            var $labelWrapper = $('<div></div>').addClass('label-wrapper');
            var $label = $('<label></label>').attr('for', 'attributeLevel' + attributeLevel).text('Attribute Category');

            $labelWrapper.prepend($label);
            $li.prepend($select);
            $li.prepend($labelWrapper);
            //If it is the first level, prepend to the dialog fields
            if(isFirstLevel()) {
                $('#add-attribute-dialog-fields').prepend($li);
            } else {
                //if it is not the first level then we will add it to the second last li.
                $('#description-field').before($li);
            }

        }
        // $('#add-attribute-dialog-fields').after($select);
        $( "#add-attribute-dialog" ).dialog('open');

    }

    return {
    	init : function() {
    		//This var is located on the add_variant.ctp
    		if(typeof lastAttributeKey !== "undefined") {
    			//If there is at least one added already then we will want to take that one and +1 for the next.
    			attributeNumber = ++lastAttributeKey;
    		}
    	},
        reset : function() {
            //TODO remove all except for the description field.
            $('#add-attribute-dialog-fields').find('.attributeLevels').parent('li').remove();
            $('#attributeDescription','#add-attribute-dialog').val('');
            attributeLevel = 0;

        },
        submit : function () {
        	var successful = true;
            //data[Attribute][0][description]
            //data[Attribute][0][attribute_id]
            /*
             * <ul>
             * 		<li>
             * 			<span>Attribute Name</span><span>Description</span>
             * 			<input type="hidden" name="data[Attribute][0][attribute_id]" value=""/>
             * 			<input type="hidden" name="data[Attribute][0][description]" value=""/>
             * 			<input type="hidden" name="data[Attribute][0][variant]" value=""/>
             * 		</li>
             * </ul>
             */
            var attributeId = $('#attributeLevel' + attributeLevel,'#add-attribute-dialog').val();
            var attributeName = $('#attributeLevel' + attributeLevel +' option[value="' + attributeId +'"]','#add-attribute-dialog').text();
            var description = $('#attributeDescription','#add-attribute-dialog').val();
			if (attributeId === '-1') {
				//<div class="error-message"></div>	
				$('#attributeLevel' + attributeLevel,'#add-attribute-dialog').after('<div class="error-message">Please select a category.</div>');
				successful = false;				
			} else {
				var $li = $('<li></li>');
				var $attributeLabel = $('<span>Feature: </span>').addClass('attribute-label');
	            var $attributeName = $('<span></span>').text(attributeName).addClass('attribute-name');
	            var $attributeDescription = $('<span></span>').text(description).addClass('attribute-description');
	            var $hiddenId = $('<input/>').attr('type','hidden').attr('name','data[AttributesCollectible][' + attributeNumber +'][attribute_id]').val(attributeId);
	            var $hiddenDescription = $('<input/>').attr('type','hidden').attr('name','data[AttributesCollectible][' + attributeNumber +'][description]').val(description);
				var $hiddenName = $('<input/>').attr('type','hidden').attr('name','data[AttributesCollectible][' + attributeNumber +'][name]').val(attributeName);
				var $hiddenVariant = $('<input/>').attr('type','hidden').attr('name','data[AttributesCollectible][' + attributeNumber +'][variant]').val('0');
	
	
	            $li.append($attributeLabel).append($attributeName).append($attributeDescription).append($hiddenId).append($hiddenDescription).append($hiddenName).append($hiddenVariant);
	
	            $('#collectible-attributes-list').children('ul').append($li);
	            
	            attributeNumber++;	
			}
			
			return successful;

        },
        addAttributeList : function(selectId) {
            var selectValue = $('#' + selectId).val();

            $.ajax({
                type: "POST",
                dataType:  'json',
                url: '/attributes/getAttributeList/' + selectValue + '.json',
                success: function(data, textStatus, XMLHttpRequest) {
                    handleSuccess(data);
                }
            });

        },
        initAttributeList : function() {
            attributeLevel = 0;
            $.ajax({
                type: "POST",
                dataType:  'json',
                url: '/attributes/getAttributeList.json',
                //data: "name=John&location=Boston",
                success: function(data, textStatus, XMLHttpRequest) {
                    handleSuccess(data);
                }
            });
        }
    };
}();