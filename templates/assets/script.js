document.addEventListener('DOMContentLoaded', function(){
    $('.nav_link').removeClass('active');
    $('#assets').addClass('active');
});

$(document).ready(function(){
  table = new DataTable('#asset_table', {
    paging: true,
    scrollCollapse: true,
    scrollX: true,
    scrollY: '55vh'
  });
  var showArchives = false;

  $('#showArchivedToggle').on('change',function(){
    var val = this.checked;
    showArchives = val;

    $.ajax({
      method: 'post',
      url: 'templates/assets/ajax.php',
      data: {
          action: 'filter',
          archive: val,
          category: $("#asset_category").val()
      },
      datatype: "text",
      success: function(data){
          table.destroy();
          $("tbody").html(data);
          table = new DataTable('#asset_table', {
            paging: true,
            scrollX: true,
            scrollCollapse: true,
            scrollY: '55vh'
          });
      }
    });
  });

  $("#asset_category").change(function(){
    var val = $(this).val();
    
    $.ajax({
      method: 'post',
      url: 'templates/assets/ajax.php',
      data: {
          action: 'filter',
          archive: showArchives,
          category: val
      },
      datatype: "text",
      success: function(data){
          table.destroy();
          $("tbody").html(data);
          table = new DataTable('#asset_table', {
            paging: true,
            scrollX: true,
            scrollCollapse: true,
            scrollY: '55vh'
          });
      }
    });
  });

  $("#search_asset").keyup(function(){
    var search = $(this).val();
    console.log(search)
    table.search(search).draw();
  });
})