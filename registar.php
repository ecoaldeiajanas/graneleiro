<?php require_once("includes/core/header1.php"); ?>
<body class="LnR">

    <?
        if($_SESSION){
            $_SESSION = array(); // destroy
        }
    ?>

    <script>
	

    $(function(){
        $(".submits").live('keypress',function(e){
            var code = (e.keyCode ? e.keyCode : e.which);
             if(code == 13) {
                console.log($("form").serialize());
                $.post("ajax/aj_register.php",$("form").serialize(),
                    function(data) {
                        $(".error").hide();
                        console.log(data);
                        if(data.substr(data,1)=="{"){
                            data = jQuery.parseJSON(data);
                            if(data.e){
                                $(".generico").show("slow");
                            }else{
                                console.log(data.SUCCESS);
                                if(data.SUCCESS=="NO"){
                                    $("."+data.REASON).show("slow");
                                }else{

                                    document.location.href = "email.php?email="+data.email+"&name="+data.name;
                                }
                            }
                        }
                    }
                );
             }
        });
    
        $('.name').blur(function(){
            var o = $(this); o.val($.trim(o.val()));
            var pNome = o.val().split(" ");
            showCuteMessage($(this),'Olá '+pNome[0]+" :)");
        });

        $('.email').blur(function(){
            var o = $(this); o.val($.trim(o.val()));
	    //var pEmail = o.val();
            showCuteMessage($(this),'não mandamos spam');
        });
		
		$('.pass1').blur(function(){
            var o = $(this); o.val($.trim(o.val()));
            showCuteMessage($(this),'confirme a password');
        });

        $('.local').keypress(function(){
            var o = $(this); 
            showCuteMessage($(this),'pressione <span class="enterBut"></span>');
        });

        $(".cuteMessage").fadeTo(0,0);

    });

    function showCuteMessage(o,message){
        if(o.val() && o.val()!=""){
            o.parent().find(".cuteMessage").html(message)
                .fadeTo(1000,0.6);
        }
    }

    </script>


    <div class="header"></div>

    <form>
    <h1>Registar</h1>
    <div class="bottomShade"></div>
    <div style="display:block; width:100%; text-align:center;">
        <div style="display:inline-block;">
            <div class="formLine">
                <span>Primeiro e último Nome</span><input type="text" name="name" class="name submits frontEnd text ui-widget-content ui-corner-all"/>      
                <span class='cuteMessage'></span>
            </div>
            <div class="formLine">
                <span>E-Mail</span><input type="text" name="email" class="email submits frontEnd text ui-widget-content ui-corner-all"/>
                <span class='cuteMessage'></span>
            </div>
            <div class="formLine">
                <span>Password</span><input type="password" name="pass1" class="pass1 submits frontEnd text ui-widget-content ui-corner-all"/>      
                <span class='cuteMessage'></span>
            </div>
            <div class="formLine">
                <span>Confirmar Password</span><input type="password" name="pass" class="pass submits frontEnd text ui-widget-content ui-corner-all"/>      
                <span class='cuteMessage'></span>
            </div>
            <div class="formLine">
                <span>Contacto</span><input type="text" name="contacto" class="contacto submits frontEnd text ui-widget-content ui-corner-all"/>
                <span class='cuteMessage'></span>
            </div>
            <div class="formLine">
                <span>Localidade e Freguesia</span><input type="text" name="localidade" class="local submits frontEnd text ui-widget-content ui-corner-all"/>
                <span class='cuteMessage'></span>
            </div>
        </div>
    </div>
    <div style="text-align: center; margin-top:1em">
        <p class="error NO_EMAIL hidden">Não introduziu o E-Mail :-(</p>
        <p class="error NOT_EMAIL hidden">O E-Mail que introduziu está errado :-(</p>
        <p class="error NO_NAME hidden">Não introduziu o seu Nome :-(</p>
        <p class="error NO_PASS hidden">Não introduziu a Password :-(</p>
        <p class="error DIF_PASS hidden">Passwords diferentes :-(</p>
        <p class="error SHORT_PASS hidden">A password tem de ser maior</p>
        <p class="error EMAIL_EXISTS hidden">O seu E-Mail já existe no sistema<br><a>Deseja recuperar a password?</a></p>
        <p class="error generico hidden">Não foi possível fazer o registo.<br>Tente mais tarde</p>
        <p class="error NO_LOCAL hidden">Não introduziu a Localidade :-(</p>
        <p class="error NO_CONTACTO hidden">Não introduziu o contacto:-(</p>
    </div>
    </form>
</body></html>
