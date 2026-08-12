<?php
include '../../function.php';

$cat_id = $_GET['cat_id'];

$data = data("subcategories");

echo "<option value=''>Select Subcategory</option>";

foreach($data as $row){
    if($row['category_id'] == $cat_id){
        echo "<option value='{$row['id']}'>{$row['name']}</option>";
    }
}