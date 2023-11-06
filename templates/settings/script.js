document.addEventListener('DOMContentLoaded', function(){
    $('.nav_link').removeClass('active');
    $('#settings').addClass('active');
});

$(document).ready(function(){
    $("#datatype").on('change', function(){
        var val = $(this).val();

        if(val == 'inventory' || val == 'damagereports'){
            $("#selectContainer").addClass("show")
            $("#datefrom").prop('required', true)
            $("#dateto").prop('required', true)
        }
        else{
            $("#selectContainer").removeClass("show")
            $("#datefrom").prop('required', false)
            $("#dateto").prop('required', false)
        }
    })
})