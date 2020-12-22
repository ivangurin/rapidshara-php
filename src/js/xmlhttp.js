function getXMLHttp(){
  var XMLHttp = null;
  if (window.XMLHttpRequest) {
    XMLHttp = new XMLHttpRequest();
  } else if (window.ActiveXObject) {
    XMLHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  return XMLHttp;
}

var XMLHttp  = getXMLHttp();

function StartUpload(){
  window.setInterval("get_data('"+id+"')", 5000);
}

function get_data(id){
  XMLHttp.open("POST", "progress");
  XMLHttp.onreadystatechange = set_data;
  XMLHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  XMLHttp.send("id="+id);
}

function set_data(){
  if (XMLHttp.readyState == 4){
    document.getElementById("Percent").innerHTML = "<p>" + XMLHttp.responseText + "</p>";
  }
}