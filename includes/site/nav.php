<div id="header">
  <div id="smalllogo"></div>
    <div class="heading">
      <ul>
        <li>
          <h1><a href="../storecostumers/index.php">QUEM SOMOS</a></h1>
        </li>
        <?php if ($existEnc > 0) { ?>
          <li><h1><a href="#">STOCK DA SEMANA</a></h1></li>
        <?php } else { ?>
          <li><h1><a href="../storecostumers/stock_semana.php?idCat=1">STOCK DA SEMANA</a></h1></li>
        <?php } ?>
        <li><h1><a href="../storecostumers/servicos.php">SERVIÇOS</a></h1></li>
        <li><h1><a href="../storecostumers/info.php">Info</a></h1></li>
      </ul>
  </div>
  <div class="lastHeading">
    <div class="userOptions">
      <div id="userOptionsInner">
        <?php if ($existEnc == 1) {
          echo '<h1><a href="../storecostumers/ver_encomenda.php">VER ENCOMENDA</a></h1>';
        }else{
          echo '<h1><a href="../storecostumers/cart.php">O MEU CABAZ</a></h1>';
        }?>
        <?php if ($admin) { echo '<h1 ><a href="../storeadmin/index.php">ADMIN</a></h1>'; } ?>
        <?php if ($produtor) { echo '<h1 ><a href="../storeprodutor/index.php">PRODUTOR</a></h1>'; } ?>
        <h1 ><a href="logout.php">SAIR</a></h1>
      </div>
    </div>
  </div>
</div>