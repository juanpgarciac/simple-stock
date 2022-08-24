<?=$message?>
<form method="POST" action="/product">
    <?=  isset($id) ? '<input type="hidden" name="'.$id.'" value="'.$id.'">' : '' ?>
    <label for="name">Name</label> <input name="name" value="<?=$name?>"><br>
    <label for="presentation">Presentation</label> <input name="presentation" value="<?=$presentation?>"><br>
    <label for="unit">Unit</label> <input name="unit"><br>
    <label for="category">Category</label> <input name="category"><br>
    <label for="stock">Stock</label> <input name="stock"><br>
    <button type="submit">Save product</button>
</form>