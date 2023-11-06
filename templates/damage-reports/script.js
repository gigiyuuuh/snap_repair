document.addEventListener('DOMContentLoaded', function(){
    $('.nav_link').removeClass('active');
    $('#damageReports').addClass('active');
});

$(document).ready(function(){
  var table = new DataTable('#asset_table', {
    paging: true,
    scrollCollapse: true,
    scrollY: '55vh'
  });

  $("#dd_asset_category").change(function(){
    var category = $(this).val();
    var type = 'all';

    $.ajax({
      method: 'post',
      url: 'templates/damage-reports/ajax.php',
      data: {
        assetCategory: category
      },
      datatype: "text",
      success: function(data){
          $("#dd_asset_type").html(data);
      }
    });

    $.ajax({
      method: 'post',
      url: 'templates/damage-reports/ajax.php',
      data: {
        action: 'filter-asset-category',
        category: category,
        type: type
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

  $("#dd_asset_type").change(function(){
    var category = $("#dd_asset_category").val();
    var type = $(this).val();

    $.ajax({
      method: 'post',
      url: 'templates/damage-reports/ajax.php',
      data: {
        action: 'filter-asset-category',
        category: category,
        type: type
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
  })
  
  $("#search_asset").keyup(function(){
    var search = $(this).val();
    table.search(search).draw();
  });

  $("#asset_id").keyup(function(){
    var txtID = $(this).val();
    
    $.ajax({
      method: 'post',
      url: 'templates/damage-reports/ajax.php',
      data: {
        damageAssetID: txtID
      },
      datatype: "text",
      success: function(data){
        $("#asset_name").val(data);

        if($("#asset_name").val() != ''){
          $("#damaged_type").val("");
          $("#damaged_part").val("");

          $.ajax({
            method: 'post',
            url: 'templates/damage-reports/ajax.php',
            data: {
              loadcomponents: txtID
            },
            datatype: "text",
            success: function(data){
              $("#damage_part_list").html(data);
            }
          })
          $.ajax({
            method: 'post',
            url: 'templates/damage-reports/ajax.php',
            data: {
              loadprevdamage: txtID
            },
            datatype: "text",
            success: function(data){
              $("#damage_type_list").html(data);
            }
          })
        }

        if($("#asset_name").val() != ''){
          $("#report_damage_btn").prop("disabled", false);
          
        }
        else{
          $("#report_damage_btn").prop("disabled", true);
        }
      }
    })
  })

  $("#damaged_part").change(function(){
    var part = $(this).val();

    $.ajax({
      method: 'post',
      url: 'templates/damage-reports/ajax.php',
      data: {
        loadrepaircost: part
      },
      datatype: "text",
      success: function(data){
       $("#repair_cost_list").html(data);
      }
    })
  })

  
})