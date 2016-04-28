<?

require_once("../storescripts/connectMysql.php");
require_once("../includes/functions.php");
$listEnc = $conDB->sql_query("SELECT encomenda.id_encomenda, people.name, encomenda.total, encomenda.date, encomenda.semana  FROM encomenda, people WHERE encomenda.id_people=people.id_people AND encomenda.semana=0");
?>
<table style="width: 100%" class="table">
  <tr>
    <th>#ID</th>
    <th>Nome</th>
    <th>Total</th>
    <th>Data</th>
  </tr>
  <? while($enc = $conDB->sql_fetchrow($listEnc)) { ?>
    <tbody>
      <tr>
        <td><?=$enc['id_encomenda']?></td>
        <td><?=$enc['name']?></td>
        <td><?=$enc['total']?></td>
        <td><?=$enc['date']?></td>
      </tr>
      <tr>
        <td colspan="4">
          <table width="100%" class="table-produtos">
            <tr>
              <th style="width: 5%;">Estado</th>
              <th style="width: 5%;">ID</th>
              <th style="width: 25%;">Produto</th>
              <th style="width: 12%;">Produtor</th>
              <th style="width: 7%;">Preço/unit</th>
              <th style="width: 7%;">Quantidade</th>
              <th style="width: 10%;">Preço total</th>
              <th style="width: 29%;">Notas</th>
            </tr>
            <?php
            $totalEnc = 0;
            $targetID = $enc['id_encomenda'];
            $produtos = $conDB->sql_query("SELECT products.id, products.product_name, people.name, products.price, encomenda_has_products.quant, products.price*encomenda_has_products.quant as total, products.peso, products.unit FROM encomenda, encomenda_has_products, products, people WHERE encomenda.id_encomenda='$targetID' AND encomenda_has_products.id_produto=products.id AND encomenda_has_products.id_encomenda='$targetID' AND products.id_produtor=people.id_people ");

            while ($prod = $conDB->sql_fetchrow($produtos)) {
              $aferirPeso = $prod['aferirPeso'];
              $peso = $prod['peso'];
              $unit = $prod['unit'];
              if ($peso == 1 && $aferirPeso == 0) {
                $quantidade = 0;
              } else {
                $quantidade = 1;
                $unid = " Kg";
              }
              if ($unit==1 || ($peso==1 && $aferirPeso==0)) {
                $unid = " Unid.";
              } else {
                $unid = " Kg";
              }
              $totalEnc = $totalEnc + $prod['total'];
            ?>
            <tr>
              <td></td>
              <td><?=$prod['id']?></td>
              <td><?=$prod['product_name']?></td>
              <td><?=$prod['name']?></td>
              <td><?=$prod['price']?></td>
              <td><?=$prod['quant']?><?=$unid?></td>
              <td><?=number_format($prod['total'], 2)?></td>
              <td></td>
            </tr>
            <? } ?>
          </table>
          <h5>Total Encomenda: <?=number_format($totalEnc, 3);?>&euro;</h5>
          <h5>Total Encomenda+Taxa: <span style="color:red"><?=number_format($totalEnc + ($totalEnc*0.1), 3);?>&euro;</span></h5>
          <hr style="border-top: 3px solid #000" />
        </td>
      </tr>
    </tbody>
  <? } ?>
</table>
