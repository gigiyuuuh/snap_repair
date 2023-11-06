document.addEventListener('DOMContentLoaded', function(){
    $('.nav_link').removeClass('active');
    $('#inventory').addClass('active');
});

$(document).ready(function(){
  var table = new DataTable('#asset_table', {
    paging: true,
    scrollCollapse: true,
    scrollX: true,
    scrollY: '55vh'
  });

  $("#asset_category").change(function(){
    var category = $(this).val();

    $.ajax({
      method: 'post',
      url: 'templates/inventory/ajax.php',
      data: {
          action: 'filter',
          category: category
      },
      datatype: "text",
      success: function(data){
          table.destroy();
          $("tbody").html(data);
          table = new DataTable('#asset_table', {
            paging: true,
            scrollCollapse: true,
            scrollY: '55vh'
          });
      }
    });
  });

  $("#dd_asset_category").change(function(){
    var category = $(this).val();

    $.ajax({
      method: 'post',
      url: 'templates/inventory/ajax.php',
      data: {
          assetCategory: category
      },
      datatype: "text",
      success: function(data){
          $("#dd_asset_type").html(data);
      }
    });
  });

  
  $("#search_asset").keyup(function(){
    var search = $(this).val();
    console.log(search)
    table.search(search).draw();
  });
})