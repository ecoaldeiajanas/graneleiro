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
                $.post("ajax/aj_login.php",$("form").serialize(),
                    function(data) {
                        $(".error").hide();
                        console.log(data);
                        if(data.substr(data,1)=="{"){
                            data = jQuery.parseJSON(data);
                            if(data.e){
                                $(".generico").show("slow");
                            }else{
                                if(data.SUCCESS=="NO"){
                                    $("."+data.REASON).show("slow");
                                }else{
									if(data.flag=='cp'){	
									document.location.href="storecostumers/index.php";
									}else if(data.flag=='acp' || data.flag=='c'|| data.flag=='ac'){	
										document.location.href="storecostumers/index.php";
									}
                                    //document.location.href = "index.php";
                                }
                            }
                        }
                    }
                );
             }
        });
$('.pass').keypress(function(){
            var o = $(this); o.val($.trim(o.val()));
            showCuteMessage($(this),'pressione <span class="enterBut"></span>');
        });
    });

    </script>


    <div class="header"></div>

    <form>
    <h1>Login</h1>
    <div class="bottomShade"></div>
    <div style="display:block; width:100%; text-align:center;">
        <div style="display:inline-block;">
            <div class="formLine">
                <span>E-Mail</span><input type="text" name="email" class="submits frontEnd text ui-widget-content ui-corner-all"/>
            </div>
            <div class="formLine">
                <span>Password</span><input type="password" name="pass" class="submits frontEnd text ui-widget-content ui-corner-all"/>      
            </div>
        </div>
        Pressione tecla Enter<span class="enterBut"></span>
    </div>
    <div style="text-align: center; margin-top:1em">
        <p class="error WRONG_PASS hidden">Password errada.<br><a href="recuperar_pass.php">Deseja recuperar a password?</a></p>
        <p class="error UNKNOWN_USER hidden">O E-Mail que inseriu não existe no sistema.<br><a>Registe-se aqui</a></p>
        <p class="error NO_PASS hidden">O E-Mail existe no sistema, mas não tem password associada.<br>Para utilizar esta conta tem de <a>recuperar a password.</a></p>
        <p class="error NO_PERMISSION hidden">O E-Mail existe no sistema, mas não tem permissão associada.<br>Por favor aguarde email do Administrador com a confirmação de permissão. </p>
        <p class="error generico hidden">Não foi possível fazer login.<br>Tente mais tarde</p>
    </div>
    </form>
    <div class="advisedBrowsers">
        <p>É aconselhado o uso do<br>Google Chrome ou do Mozilla Firefox<br>para visualizar este site correctamente</p>
        <a href="https://www.google.com/chrome">
            <img src="style/css/images/chrome.png" style='margin: 6px;'/>
        </a>
        <a href="http://www.mozilla.org/en-US/firefox/new/" >
            <img src="style/css/images/firefox.png" style='margin: 6px;'/>
        </a>
    </div>
<? //include_once('includes/core/footer.php'); ?>
</body></html>
