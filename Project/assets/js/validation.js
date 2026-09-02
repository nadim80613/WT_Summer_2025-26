function validateRegister()
{

    let valid = true;


    let name = document.getElementById("name").value.trim();

    let email = document.getElementById("email").value.trim();

    let password = document.getElementById("password").value.trim();

    let confirm = document.getElementById("confirm_password").value.trim();



    document.getElementById("nameError").innerHTML="";
    document.getElementById("emailError").innerHTML="";
    document.getElementById("passwordError").innerHTML="";
    document.getElementById("confirmError").innerHTML="";



    if(name=="")
    {
        document.getElementById("nameError").innerHTML=
        "Please enter your full name";

        valid=false;
    }



    if(email=="")
    {
        document.getElementById("emailError").innerHTML=
        "Please enter your email address";

        valid=false;
    }



    if(password=="")
    {
        document.getElementById("passwordError").innerHTML=
        "Please enter password";

        valid=false;
    }



    if(confirm=="")
    {
        document.getElementById("confirmError").innerHTML=
        "Please confirm your password";

        valid=false;
    }



    if(password!="" && confirm!="" && password!=confirm)
    {
        document.getElementById("confirmError").innerHTML=
        "Password does not match";

        valid=false;
    }



    return valid;

}





function validateLogin()
{

    let valid=true;


    let email=document.getElementById("email").value.trim();

    let password=document.getElementById("password").value.trim();



    document.getElementById("emailError").innerHTML="";
    document.getElementById("passwordError").innerHTML="";



    if(email=="")
    {
        document.getElementById("emailError").innerHTML=
        "Please enter your email";

        valid=false;
    }



    if(password=="")
    {
        document.getElementById("passwordError").innerHTML=
        "Please enter your password";

        valid=false;
    }



    return valid;

}





// Live Validation

document.addEventListener("DOMContentLoaded",function(){



    let name=document.getElementById("name");

    let email=document.getElementById("email");

    let password=document.getElementById("password");

    let confirm=document.getElementById("confirm_password");




    if(name)
    {

        name.addEventListener("focus",function(){

            if(name.value.trim()=="")
            {
                document.getElementById("nameError").innerHTML=
                "Please enter your full name";
            }

        });



        name.addEventListener("input",function(){

            document.getElementById("nameError").innerHTML="";

        });

    }





    if(email)
    {

        email.addEventListener("focus",function(){

            if(email.value.trim()=="")
            {
                document.getElementById("emailError").innerHTML=
                "Please enter your email address";
            }

        });



        email.addEventListener("input",function(){

            let pattern=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;


            if(email.value.trim()== "")
            {
                document.getElementById("emailError").innerHTML=
                "Please enter your email address";
            }

            else if(!pattern.test(email.value))
            {
                document.getElementById("emailError").innerHTML=
                "Please enter a valid email address";
            }

            else
            {
                document.getElementById("emailError").innerHTML="";
            }


        });

    }






    if(password)
    {

        password.addEventListener("focus",function(){

            if(password.value.trim()=="")
            {
                document.getElementById("passwordError").innerHTML=
                "Please enter password";
            }

        });



        password.addEventListener("input",function(){

            if(password.value.trim()=="")
            {
                document.getElementById("passwordError").innerHTML=
                "Please enter password";
            }

            else
            {
                document.getElementById("passwordError").innerHTML="";
            }


        });

    }





    if(confirm)
    {

        confirm.addEventListener("focus",function(){

            if(confirm.value.trim()=="")
            {
                document.getElementById("confirmError").innerHTML=
                "Please confirm your password";
            }

        });



        confirm.addEventListener("input",function(){


            if(confirm.value.trim()=="")
            {
                document.getElementById("confirmError").innerHTML=
                "Please confirm your password";
            }


            else if(password.value != confirm.value)
            {
                document.getElementById("confirmError").innerHTML=
                "Password does not match";
            }


            else
            {
                document.getElementById("confirmError").innerHTML="";
            }


        });

    }



});