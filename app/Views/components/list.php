<?php 
extract($result);
// echo json_encode($columns);
?>
<div class = "row">
    <div class = "col-xs-12">
        <table class = "table table-striped datatable" style = "width:100%;">
            <thead>
                <tr>
                    <?php foreach($columns as $column){?>
                        <th><?=pascalize($column);?></th>
                    <?php }?>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) :?>
                    <tr>
                        <?php foreach($row as $cell){?>
                            <td><?=$cell;?></td>
                        <?php }?>
                        <td>
                            <a href="<?php echo site_url($feature.'/edit/'.$row['id']);?>">Edit</a> |
                            <a href="<?php echo site_url($feature.'/delete/'.$row['id']);?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.datatable').DataTable();
    });
</script>