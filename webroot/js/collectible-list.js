$(function(){
  
  if($('#dialog').length)
  {
    
  
  }
  else
  {
    $('body').append('<div id="dialog"></div>');
    $('#dialog').dialog({
			autoOpen: false,
		//	show: "blind",
			hide: "explode",
			height: 'auto',
      width: 'auto',
      modal: 'true'
		});
  }



  $('.collectible.image').click(function(){
    var img$ = $(this).children('.image-fullsize').children('img');
    //var height = img$.height();
    //var width = img$.width();
    //$('dialog').dialog('option','width',width);
    //$('dialog').dialog('option','height',height);
    $('#dialog').children().remove();
    img$.clone().appendTo('#dialog');
    //$('#dialog').appendTo(img$);
    $('#dialog').dialog('open');
  });  

});