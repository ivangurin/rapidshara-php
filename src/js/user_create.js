var gv_check_name     = false;
var gv_check_email    = false;
var gv_check_password = false;
var gv_check_invite   = false;

var gv_wrong_name     = "";
var gv_wrong_email    = "";
var gv_wrong_password = "";
var gv_wrong_invite   = "";

function set_focus(){
  document.getElementById("name").select();
  document.getElementById("name").focus();
}

function check(){
  check_name();
  check_email();
  check_password();
  check_invite();
}

function wrong_name(flag){
  gv_wrong_name = flag;
}

function wrong_email(email){
  gv_wrong_email = email;
}

function wrong_password(flag){
  gv_wrong_password = flag;
}

function wrong_invite(flag){
  gv_wrong_invite = flag;
}

function check_name(){

  form = document.forms["registration"];
  div  = document.getElementById("name_info");

  if(form.name.value != ""){
    gv_check_name = true;
    div.style.color = "green";
    div.innerHTML = "отлично";
  }else{
    if(gv_wrong_name == "X"){
      gv_check_name = false;
      div.style.color = "red";
      div.innerHTML = "У вас нет имени?";
    }else{
      gv_check_name = false;
      div.innerHTML = "";
    }
  }

  check_submit()

}

function check_email(){

  var email_mask = "^[_\\.0-9a-z-]+@([0-9a-z][0-9a-z_-]+\\.)+[a-z]{2,4}$";
  var regex = new RegExp(email_mask);

  form = document.forms["registration"];
  div  = document.getElementById("email_info");

  if (form.email.value != ""){
    if (!regex.test(form.email.value) || !(form.email.value.length > 0)){
      gv_check_email = false;
      div.style.color = "red";
      div.innerHTML = "плохо";
    }else{
      if(gv_wrong_email != "" && gv_wrong_email == form.email.value){
        gv_check_email = false;
        div.style.color = "red";
        div.innerHTML = "Уже кто-то занял этот адрес";
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
      div.innerHTML = "У вас нет почты?";
    }else{
      gv_check_email = false;
      div.innerHTML = "";
    }
  }

  check_submit()

}

function check_password(){

  form = document.forms["registration"];
  div  = document.getElementById("password_info");

  if(form.password.value != ""){
    gv_check_password = true;
      div.style.color = "green";
      div.innerHTML = "отлично";
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

  check_submit()

}

function check_invite(){

  form = document.forms["registration"];
  div  = document.getElementById("invite_info");

  if (form.invite.value != ""){
    if (!(form.invite.value.length > 0)){
      gv_check_invite = false;
      div.style.color = "red";
      div.innerHTML = "плохо";
    }else{
      if(gv_wrong_invite != "" && gv_wrong_invite == form.invite.value){
        gv_check_invite = false;
        div.style.color = "red";
        div.innerHTML = "Нет тот инвайт";
      }else{
        gv_check_invite = true;
        div.style.color = "green";
        div.innerHTML = "отлично";
      }
    }
  }else{
    if(gv_wrong_invite == "X"){
      gv_check_invite = false;
      div.style.color = "red";
      div.innerHTML = "У вас нет инвйта?";
    }else{
      gv_check_invite = false;
      div.innerHTML = "";
    }
  }

  check_submit()

}

function check_submit(){
  form = document.forms['registration'];
  form.create.disabled = true;
  if (gv_check_name && gv_check_email && gv_check_password && gv_check_invite){
    form.create.disabled = false;
  }
}