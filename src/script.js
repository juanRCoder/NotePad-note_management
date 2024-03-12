$(document).ready(() => {
  $(".BoxAddNote").hide();

  $(".ShowBoxAdd").click(() => {
    $(".BoxAddNote").show();
  });

  $(".HiddenBoxAdd").click((e) => {
    e.preventDefault();
    
    $(".BoxAddNote").hide();
    $("#note").val("");
  });
});
