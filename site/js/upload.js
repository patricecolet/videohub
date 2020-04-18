
$(function() {
  enable_cb();
  $("#ftpenable").click(enable_cb);
});

function enable_cb() {
  if (this.checked) {
    $("input.ftpcell").removeAttr("disabled");
  } else {
    $("input.ftpcell").attr("disabled", true);
  }
}
