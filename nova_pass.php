<? require_once("includes/core/header1.php"); ?>
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
                $.post("ajax/aj_newPass.php",$("form").serialize(),
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
                                    document.location.href = "login.php";
                                }
                            }
                        }
                    }
                );
             }
        });
    
       
		
		$('.pass1').blur(function(){
            var o = $(this); o.val($.trim(o.val()));
            showCuteMessage($(this),'confirme a palavra-chave');
        });

        $('.pass').keypress(function(){
            var o = $(this); o.val($.trim(o.val()));
            showCuteMessage($(this),'pressione tecla enter<span class="enterBut"></span>');
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
<?php $email=$_GET["e"]; ?>
    <form>
    <h1>Criar nova password</h1>
    <div class="bottomShade"></div>
    <div style="display:block; width:100%; text-align:center;">
        <div style="display:inline-block;">
            
            <div class="formLine">
                <span>Password</span><input type="password" name="pass1" class="pass1 submits frontEnd text ui-widget-content ui-corner-all"/>      
                <span class='cuteMessage'></span>
            </div>
            <div class="formLine">
                <span>Confirmar Password</span><input type="password" name="pass" class="pass submits frontEnd text ui-widget-content ui-corner-all"/>      
                <span class='cuteMessage'></span>
		<input type="hidden" name="email" value="<?php echo $email ?>"/>
            </div>
        </div>
    </div>
    <div style="text-align: center; margin-top:1em">
        
        <p class="error NO_PASS hidden">Não introduziu a Password :-(</p>
        <p class="error DIF_PASS hidden">Passwords diferentes :-(</p>
        
    </div>
    </form>
</body></html>
