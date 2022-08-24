<h1>Product Index</h1>

<table border="1"><tbody>
    <tr><th>ID</th><th>Name</th><th>Presentation</th><th>Category</th><th>Stock</th><th>Actions</th></tr>
<?php
//$products = (object)$products;
foreach ($products as $id => $product) {
    extract($product);
?>
<tr>
    <td><?=$id?></td>
    <td><a href="/product/<?=$id?>"><?=$name?></a></td>
    <td><?=$presentation?></td>
    <td><?=$category?></td>
    <td><?=$stock?></td>
    <td><a href="/product/edit/<?=$id?>">Edit</a>&nbsp;<a href="">Delete</a></td>
</tr>
<?php
}
?>
</tbody></table>