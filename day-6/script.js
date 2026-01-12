function checkAge(){
    let age = document.getElementById("age").value;

    if(age >=18){
        window.location.href= "register.html";
    }
    else{
        document.getElementById("message").innerText = "You are not eligible candidate. ❌"
    }
}