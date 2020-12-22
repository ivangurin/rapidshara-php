var gv_check_email    = false;
var gv_check_password = false;

var gv_wrong_email    = "";
var gv_wrong_password = "";

function set_focus(){
  document.getElementById("email").select();
  document.getElementById("email").focus();
}

function check(){
  check_email();
  check_password();
}

function wrong_email(email){
  gv_wrong_email = email;
}

function check_email(){

  var emailMask = "^[_\\.0-9a-z-]+@([0-9a-z][0-9a-z_-]+\\.)+[a-z]{2,4}$";
  var regex = new RegExp(emailMask);

  form = document.forms["entry"];
  div  = document.getElementById("email_info");

  if(form.email.value != ""){
    if(!regex.test(form.email.value) || !(form.email.value.length > 0)){
      gv_check_email = false;
      div.style.color = "red";
      div.innerHTML = "плохо";
    }else{
      if(gv_wrong_email != "" && gv_wrong_email == form.email.value){
        gv_check_email = false;
        div.style.color = "red";
        div.innerHTML = "Пользователь с таким адресом не зарегистрирован!";
      }else{
        gv_check_email = true;
        div.style.color = "green";
        div.innerHTML = "отлично";
      }
    }
  }else{
    if(gv_wrong_email == "X"){
      gv_check_email = false;
      div.style.color = "red";
      div.innerHTML = "Вы забыли свою почту?";
    }else{
      gv_check_email = false;
      div.innerHTML = "";
    }
  }
  check_entry()
}

function wrong_password(password)
{
  gv_wrong_password = password;
}

function check_password()
{
  form = document.forms["entry"];
  div  = document.getElementById("password_info");

  if(form.password.value != ""){
    if(gv_wrong_password != "" && gv_wrong_password == form.password.value){
      gv_check_password = false;
      div.style.color = "red";
      div.innerHTML = "Веденный пароль неверен!";
    }else{
      gv_check_password = true;
      div.style.color = "green";
      div.innerHTML = "отлично";
    }
  }else{
    if(gv_wrong_password == "X"){
      gv_check_password = false;
      div.style.color = "red";
      div.innerHTML = "А пароль?";
    }else{
      gv_check_password = false;
      div.innerHTML = "";
    }
  }

  check_entry()

}

function check_entry(){
  form = document.forms["entry"];
  form.entry.disabled = true;
  if(gv_check_email && gv_check_password){
    form.entry.disabled = false;
  }
}