$( function() {
    DED.init();
    //$(".ui-icon-info").tooltip({ position: 'center right', opacity: 0.7});
    // $('.add-variant-attribute').hover( function() {
	        // $(this).addClass("ui-state-hover");
	    // }, function() {
	        // $(this).removeClass("ui-state-hover");
	    // }
    // );

    $( "#add-attribute-dialog" ).dialog({
        'autoOpen' : false,
        'width' : 700,
        'height': 'auto',
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
    
    $('.remove-attribute').live('click', function(){
    	var $li = $(this).parent('span').parent('li');
    	if($(this).text() === 'Remove') {
	     	var actionVal = $li.children('input.attribute.action').val();
	    	if(actionVal === 'A') {
	    		$li.remove();
	    	} else {
	    		$li.children('input.attribute.action').val('D');
	    		$li.css('border','solid 1px red');    	
	    		$(this).parent('.attribute-action').children('.edit-attribute').hide();	
	    		$(this).text('Cancel');  
	    	}      			
    	} else {
    		$li.children('input.attribute.action').val('');
    		$li.css('border','none');  
    		$li.css('border-bottom','solid 1px #E1E1E1'); 
    		
    		$(this).parent('.attribute-action').children('.edit-attribute').show();	
    		$(this).text('Remove');  
    	}
    });
    
    $('.edit-attribute').live('click', function(){
    	if($(this).text() === 'Edit') {
	    	var $li = $(this).parent('span').parent('li');
	    	$li.children('.attribute-description').children('span').hide();
	    	$li.children('.attribute-description').children('input').show();
	    	$(this).parent('.attribute-action').children('.remove-attribute').hide();	
	    	$(this).text('Save');    		
    	} else {
	    	var $li = $(this).parent('span').parent('li');
	    	var $descriptionVal = $li.children('.attribute-description').children('input').val();
	    	$li.children('.attribute-description').children('span').text($descriptionVal);
	    	$li.children('input[type=hidden].attribute.description').val($descriptionVal);
	    	$li.children('.attribute-description').children('span').show();
	    	$li.children('.attribute-description').children('input').hide();
	    	$(this).parent('.attribute-action').children('.remove-attribute').show();
	    	var $currentAction = $li.children('input.attribute.action').val();
	    	if($currentAction !== 'D' && $currentAction !== 'A') {
	    		$li.children('input.attribute.action').val('E');
	    	}
	    	
	    	
	    	$(this).text('Edit');     		
    	}

    });
});
var DED = function() {
    var attributeLevel = 0;
    var attributeNumber = 0;
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
                    $('#attributeLevel' + i).parent('div').parent('li').remove();
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
            var $inputWrapper = $('<div>').addClass('input').addClass('select');
            var $labelWrapper = $('<div></div>').addClass('label-wrapper');
            var $label = $('<label></label>').attr('for', 'attributeLevel' + attributeLevel).text('Attribute Category');
			
            $labelWrapper.prepend($label);
            $inputWrapper.prepend($select);
            $inputWrapper.prepend($labelWrapper);
            $li.prepend($inputWrapper);
            
            //If it is the first level, prepend to the dialog fields
            if(isFirstLevel()) {
                $('#add-attribute-dialog-fields').prepend($li);
            } else {
                //if it is not the first level then we will add it to the second last li.
                $('#description-field').before($li);
            }

        }
        $('#add-attribute-dialog').find('.error-message').remove();
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
            $('#add-attribute-dialog-fields').find('.attributeLevels').parent().parent('li').remove();
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
            $('#add-attribute-dialog').find('.error-message').remove();
            var attributeId = $('#attributeLevel' + attributeLevel,'#add-attribute-dialog').val();
            var attributeName = $('#attributeLevel' + attributeLevel +' option[value="' + attributeId +'"]','#add-attribute-dialog').text();
            var description = $('#attributeDescription','#add-attribute-dialog').val();
			if (attributeId === '-1') {
				//<div class="error-message"></div>	
				$('#attributeLevel' + attributeLevel,'#add-attribute-dialog').after('<div class="error-message">Please select a category.</div>');
				successful = false;
			} else if(description === ''){
				$('#attributeDescription','#add-attribute-dialog').after('<div class="error-message">Please enter a description.</div>');
				successful = false;							
			} else {
				var $li = $('<li></li>');
	            var $attributeName = $('<span></span>').text(attributeName).addClass('attribute-name');
	            var $attributeDescription = $('<span></span>').text(description).addClass('attribute-description');
	            var $attributeAction = $('<span></span>').addClass('attribute-action');
	            var $attributeRemove = $('<a></a>').text('Remove').addClass('remove-attribute');
	            $attributeAction.append($attributeRemove);
	            var $hiddenId = $('<input/>').attr('type','hidden').attr('name','data[AttributesCollectible][' + attributeNumber +'][attribute_id]').val(attributeId);
	            var $hiddenDescription = $('<input/>').attr('type','hidden').attr('name','data[AttributesCollectible][' + attributeNumber +'][description]').val(description);
				var $hiddenName = $('<input/>').attr('type','hidden').attr('name','data[AttributesCollectible][' + attributeNumber +'][name]').val(attributeName);
				var $hiddenAction = $('<input/>').attr('type','hidden').attr('name','data[AttributesCollectible][' + attributeNumber +'][action]').val('A').addClass('attribute').addClass('action');
	
	            $li.append($attributeName).append($attributeDescription).append($attributeAction).append($hiddenId).append($hiddenDescription).append($hiddenName).append($hiddenAction);
	
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