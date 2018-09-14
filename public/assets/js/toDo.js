// Check off specific ToDo by clicking
$("ul").on("click", "li", function(){
    $(this).toggleClass("completed");
});

// click on delete icon to delete ToDo
$("ul").on("click", "span", function(event){
  $(this).parent().fadeOut(400, function(){
    $(this).remove();
  });
  event.stopPropagation();
});

// add new ToDo
$('input[type="text"]').keypress(function(event){
  // check if enter key pressed
  if(event.which === 13){
    // store input value in variable
    var toDo = $(this).val();
    // clear input
    $(this).val("");
    // create new li with new toDo and add to end of UL
    $("ul").append("<li><span><i class='fa fa-trash'></i></span> " + toDo + "</li>");
  }
});

// add event listener to Plus icon
$(".fa-plus").click(function(){
  $('input[type="text"]').fadeToggle();
  $('input[type="text"]').focus();
});
