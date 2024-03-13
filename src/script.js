$(document).ready(() => {
  $(".addNoteBox").hide();
  $(".updateNoteBox").hide();

  $(".addShowBox").click(() => {
    $(".addNoteBox").show();
  });

  $(".hiddenBox").click((e) => {
    e.preventDefault();
    $(".addNoteBox").hide();
    $(".updateNoteBox").hide();
    $("#note").val("");
  });

  $(".btnUpdate").click(function () {
    let notaValue = $(this).closest(".boxNote").find(".nota").text();
    let idnota = $(this).closest(".boxNote").find("#idNota").text();
    console.log(idnota);
    $(".textNote").val(notaValue);
    $(".sendIdNote").val(idnota);
    $(".updateNoteBox").show();
  });

  // $(".updateNote").click(function () {
  //   alert("HOLA MUNDO");
  //   location.reload();
  // });
});
