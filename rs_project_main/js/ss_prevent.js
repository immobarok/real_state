function clp_clear() {
  var content = window.clipboardData.getData("Text");
  if (content == null) {
    window.clipboardData.clearData();
  }
  setTimeout("clp_clear();", 1000);
}