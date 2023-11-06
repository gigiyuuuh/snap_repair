document.addEventListener('DOMContentLoaded', function(){
    $('.nav_link').removeClass('active');
    $('#logs').addClass('active');
});

$(document).ready(function(){
    $("#datefrom").on('change', function(){
        var from = $(this).val();
        var to = $("#dateto").val();

        $.ajax({
            method: 'post',
            url: 'templates/logs/ajax.php',
            data: {
                datefrom: from,
                dateto: to
            },
            datatype: 'text',
            success: function(data){
                $("tbody").html(data);
            }
        })
    });

    $("#dateto").on('change', function(){
        var to = $(this).val();
        var from = $("#datefrom").val();

        $.ajax({
            method: 'post',
            url: 'templates/logs/ajax.php',
            data: {
                datefrom: from,
                dateto: to
            },
            datatype: 'text',
            success: function(data){
                $("tbody").html(data);
            }
        })
    });
});