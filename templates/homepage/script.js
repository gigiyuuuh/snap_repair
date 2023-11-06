document.addEventListener("DOMContentLoaded", function(event) {
   
    const showNavbar = (toggleId, navId, bodyId, headerId) =>{
        const toggle = document.getElementById(toggleId),
        nav = document.getElementById(navId),
        bodypd = document.getElementById(bodyId),
        headerpd = document.getElementById(headerId)
        
        // Validate that all variables exist
        if(toggle && nav && bodypd && headerpd){
            toggle.addEventListener('click', ()=>{
                // show navbar
                nav.classList.toggle('show')
                // change icon
                toggle.classList.toggle('bx-x')
                // add padding to body
                bodypd.classList.toggle('body-pd')
                // add padding to header
                headerpd.classList.toggle('body-pd')
            })
        }
    }
    
    showNavbar('header-toggle','nav-bar','body-pd','header')
    
    /*===== LINK ACTIVE =====*/
    const linkColor = document.querySelectorAll('.nav_link')
    
    function colorLink(){
    if(linkColor){
    linkColor.forEach(l=> l.classList.remove('active'))
    this.classList.add('active')
    }
    }
    linkColor.forEach(l=> l.addEventListener('click', colorLink))
    
     // Your code to run since DOM is loaded and ready
});

$(document).ready(function() {
    $('#header-toggle').click(function() {
        var hasClass = $('#nav-bar').hasClass('show')
        if(hasClass){
            $('#smart-views-label').css('color', '#fff')
        }
        else{
            $('#smart-views-label').css('color', '#252525')
        }
    });
    $('#dashboard').click(function(){
        $.ajax({
            method: 'post',
            url: 'templates/dashboard/index.php',
            data: {
                page: "dashboard"
            },
            datatype: "text",
            success: function(data){
                $("#main-content").html(data);
            }
        });
        window.history.pushState('page2', 'Title', '?page=dashboard');
    });
    $('#assets').click(function(){
        $.ajax({
            method: 'post',
            url: 'templates/assets/index.php',
            data: {
                page: "assets"
            },
            datatype: "text",
            success: function(data){
                $("#main-content").html(data);
            }
        });
        window.history.pushState('page2', 'Title', '?page=assets');
    });
    $('#inventory').click(function(){
        $.ajax({
            method: 'post',
            url: 'templates/inventory/index.php',
            data: {
                page: "inventory"
            },
            datatype: "text",
            success: function(data){
                $("#main-content").html(data);
            }
        });
        window.history.pushState('page2', 'Title', '?page=inventory');
    });
    $('#damageReports').click(function(){
        $.ajax({
            method: 'post',
            url: 'templates/damage-reports/index.php',
            data: {
                page: "damage-reports"
            },
            datatype: "text",
            success: function(data){
                $("#main-content").html(data);
            }   
        });
        window.history.pushState('page2', 'Title', '?page=damage-reports');
    });

    $('#logs').click(function(){
        $.ajax({
            method: 'post',
            url: 'templates/logs/index.php',
            data: {
                page: "logs"
            },
            datatype: "text",
            success: function(data){
                $("#main-content").html(data);
            }   
        });
        window.history.pushState('page2', 'Title', '?page=logs');
    });
    
    $('#settings').click(function(){
        $.ajax({
            method: 'post',
            url: 'templates/settings/index.php',
            data: {
                page: "settings"
            },
            datatype: "text",
            success: function(data){
                $("#main-content").html(data);
            }
        });
        window.history.pushState('page2', 'Title', '?page=settings');
    });

    $("#txt_email").keyup(function(){
        var txt = $(this).val();
        $.ajax({
          method: 'post',
          url: 'templates/homepage/ajax.php',
          data: {
              uname: $("#txt_username").val(),
              email: txt
          },
          datatype: "text",
          success: function(data){
              $("#emailBadge").html(data);
          }
        });
    })

    $("#txt_username").keyup(function(){
        var txt = $(this).val();
        $.ajax({
          method: 'post',
          url: 'templates/homepage/ajax.php',
          data: {
              eml: $("#txt_email").val(),
              username: txt
          },
          datatype: "text",
          success: function(data){
              $("#usernameBadge").html(data);
          }
        });
    })
    $("#txt_searchUser").keyup(function(){
        var txt = $(this).val();
        $.ajax({
          method: 'post',
          url: 'templates/homepage/ajax.php',
          data: {
              search_username: txt
          },
          datatype: "text",
          success: function(data){
              $("#accountlist").html(data);
          }
        });
    })
  });