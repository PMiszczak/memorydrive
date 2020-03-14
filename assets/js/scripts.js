$(function() {
  setTimeout(function() {
    $("#loader").fadeOut("slow", function() {
      $(this).remove();
    });
  }, 1000);
});

$('#file_to_upload').on('change', function() {
  var file_name = document.getElementById("file_to_upload").files[0].name;
  $(this).next('.custom-file-label').html(file_name);
})

$(document).ready(function() {
  var classes = ["btn-primary", "btn-success", "btn-danger", "btn-info", "btn-dark"];
  $("#index_submit_button").each(function() {
    $(this).addClass(classes[~~(Math.random() * classes.length)]);
  });
});
