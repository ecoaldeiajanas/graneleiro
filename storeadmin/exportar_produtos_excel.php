<?php 
require_once("../storescripts/connectMysql.php");
require_once("../storescripts/php-export-data.class.php");

    @$i=0;
    $q = $conDB->sql_query("SELECT id, products.product_name, imagem, price, quantidade, people.name, category, cultura, stock, unit, peso, products.id_produtor, products.id_category, details FROM products, category, people WHERE category.id_category=products.id_category AND products.id_produtor=people.id_people ORDER BY products.product_name DESC ");
    @$return[] = array('id', 'produto', 'imagem', 'preço', 'quantidade', 'produtor', 'categoria', 'cultura', 'stock', 'uni', 'peso', 'id produtor', 'id categoria', 'detalhes');
    while($r = $conDB->sql_fetchrow($q)) {
        foreach ($r as $key => $value) {
            $i++;
            if($i==1){
                $sql=mysql_query("SELECT * FROM products WHERE id='$value'");
                    while($row=mysql_fetch_array($sql)){
                        $unit=$row['unit'];     
                        }
                            if($unit==1){
                                $unid=" Unid.";
                            }else{
                                $unid=" Kg";
                            }
            }
            if($i==4){ $value=$value." Euros";}
            if($i==5){ $value=$value.$unid;}
            if($i==9 || $i==10 || $i==11){ if($value==1){$value="Sim";}else{ $value="-";};}
            if(!@$_GET['normalJSON']) $r[$key] = $value;
        }
        @$return[] = $r;
        $i=0;
    }

$excel = new ExportDataExcel('browser');
$excel->filename = "catalogo_produtos_".date('Y-m-d').".xls";

$excel->initialize();
foreach($return as $row) {
    $excel->addRow($row);
}
$excel->finalize();