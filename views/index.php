<h1>Product Index</h1>
<a href="/product/create">Create product</a><br>
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
    <td><a href="/product/edit/<?=$id?>">Edit</a>&nbsp;<form method="POST" action="/product/delete/<?=$id?>"><button type="submit">Delete</button></form></td>
</tr>
<?php
}
?>
</tbody></table>