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
    //encontrar el elemento con clase 'boxNote' mas cercano y buscar los id
    let notaValue = $(this).closest(".boxNote").find(".nota").text();
    let idnota = $(this).closest(".boxNote").find("#idNota").text();
    console.log(idnota);
    $(".textNote").val(notaValue);
    $(".sendIdNote").val(idnota);
    $(".updateNoteBox").show();
  });

  $(".btnDelete").click(function (event) {
    if (!confirm("¿Estás seguro de eliminar la nota?")) {
      // Si el usuario cancela, detener la acción predeterminada del enlace
      event.preventDefault();
    } else {
      let idDelete = $(this).closest(".boxNote").find("#idNota").text();
      console.log(idDelete);
      $("#idDelete").val(idDelete);
      $(".deleteNoteBox").submit();
    }
  });
});
