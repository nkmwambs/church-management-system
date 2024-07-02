<script type="text/javascript">

    $("#field-level").empty();

    $('#field-denomination_id').chosen().change(function() {
        let denomination_id = $(this).val();
        
        $.ajax({
            url: '<?php echo site_url('ajax/hierarchy/getNextHierarchyLevel')?>',
            type: 'POST',
            data: { denomination_id: denomination_id },
            success: function(response){
                $("#field-level").empty();
                $("#field-level").append('<option></option>');

                $.each(response, function(index, option) {
                    $("#field-level").append($('<option>', {
                        value: option.levelNumber,
                        text: option.levelName
                    }));
                });
                
                $("#field-level").trigger("chosen:updated");
            }
        });
    });
   
</script>