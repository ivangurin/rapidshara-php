var gv_check_name     = false;
var gv_check_email    = false;
var gv_check_password = false;

var gv_wrong_email    = "";

function check(){
  check_name();
  check_email();
  check_password();
}

function check_name(){

  form = document.forms["Profile"];
  div  = document.getElementById("NameInfo");

  if (form.Name.value != ""){
    gv_check_name = true;
    div.style.color = "green";
    div.innerHTML = "отлично";
  }else{
    gv_check_name = false;
    div.innerHTML = " ";
  }

  check_submit()

}

function wrong_email(iv_email){
  gv_wrong_email = iv_email;
}

function check_email(){

  var EmailMask = "^[_\\.0-9a-z-]+@([0-9a-z][0-9a-z_-]+\\.)+[a-z]{2,4}$";
  var regex = new RegExp(EmailMask);

  form = document.forms["Profile"];
  div  = document.getElementById("EmailInfo");

  if (form.Email.value != ""){
    if (!regex.test(form.Email.value) || !(form.Email.value.length > 0))    {
      gv_check_email = false;
      div.style.color = "red";
      div.innerHTML = "плохо";
    }else{
      if (gv_wrong_email != "" && gv_wrong_email == form.Email.value){
        gv_check_email = false;
        div.style.color = "red";
        div.innerHTML = "Пользователь с таким адресом уже зарегистрирован!";
      }else{
        gv_check_email = true;
        div.style.color = "green";
        div.innerHTML = "отлично";
      }
    }
  }else{
    gv_check_email = false;
    div.style.color = "red";
    div.innerHTML   = "укажите почтовый адрес";
  }

  check_submit()

}

function check_password(){

  form = document.forms["Profile"];
  div  = document.getElementById("PasswordInfo");

  if(form.Password.value != "")  {
    gv_check_password = true;
    div.style.color = "green";
    div.innerHTML = "отлично";
  }else{
    gv_check_password = false;
    div.style.color = "red";
    div.innerHTML = "укажите пароль";
  }

  check_submit();

}

function check_submit(){
  form = document.forms["Profile"];
  form.Submit.disabled = true;
  if(gv_check_name && gv_check_email && gv_check_password){
    form.Submit.disabled = false;
  }
}