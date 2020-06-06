<?php
global $op_warehouses;
?>
<table class="warehouse-inventory-list">
    <?php foreach($op_warehouses as $warehouse): ?>
    <tr>
        <th><?php echo $warehouse['name']; ?></th>
        <td><?php echo $warehouse['total_qty']; ?></td>
    </tr>
<?php endforeach; ?>
</table>
