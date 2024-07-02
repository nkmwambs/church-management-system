<script type="text/javascript">

    $("#field-parent_id").empty();

    $('#field-hierarchy_id').chosen().change(function() {
        let hierarchy_id = $(this).val();
        
        $.ajax({
            url: '<?php echo site_url('ajax/entity/getReportingEntities')?>',
            type: 'POST',
            data: { hierarchy_id: hierarchy_id },
            success: function(response){
                
                $("#field-parent_id").empty();
                $("#field-parent_id").append('<option></option>');

                $.each(response, function(index, option) {
                    $("#field-parent_id").append($('<option>', {
                        value: option.id,
                        text: option.name
                    }));
                });
                
                $("#field-parent_id").trigger("chosen:updated");
            }
        });
    });
   
</script>