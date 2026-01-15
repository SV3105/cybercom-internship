function checkAge(){

    try{
    let age = document.getElementById("age").value;

      if (age === "") {
      throw "Age cannot be empty";
    }

    
    if(age >=18){
        window.location.href= "register.html";
    }
    else{
        document.getElementById("message").innerText = "You are not eligible candidate. ❌"
    }

}catch (error) {
    alert("Error: " + error);
  }

}



