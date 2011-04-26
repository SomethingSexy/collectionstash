$(function(){

  if( $('#collectibleType').val()=== '1')
  {
    $('#widthWrapper').hide();
    $('#depthWrapper').hide();    
  }
  else
  {
    $('#widthWrapper').show();
    $('#depthWrapper').show();    
  }

  $('#collectibleType').change(function(){
      var value = $(this).val();
      
      if(value == '1')
      {
         $('#widthWrapper').hide();
         $('#depthWrapper').hide();
      }
      else if(value == '2')
      {
         $('#widthWrapper').show();
         $('#depthWrapper').show();
      }
  });

});