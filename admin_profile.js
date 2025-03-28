$(document).ready(function() {
    $("#personal-info-tab").click(function() {
        $(".content").hide();
        $("#personal-info").show();
        $(".nav-link").removeClass("active");
        $(this).addClass("active");
    });

    $("#products-tab").click(function() {
        $(".content").hide();
        $("#products").show();
        $(".nav-link").removeClass("active");
        $(this).addClass("active");
    });

    $("#add-product-tab").click(function() {
        $(".content").hide();
        $("#add-product").show();
        $(".nav-link").removeClass("active");
        $(this).addClass("active");
    });

    // Initially display personal info
    $("#personal-info-tab").click();
});

function updatePersonalInfo() {
    // Add your AJAX call or form submission logic here
    alert("Personal info updated!");
}

function showProductDetails(productId, status, date) {
    $("#product-id").text(productId);
    $("#product-status").text(status);
    $("#product-date").text(date);
    $("#product-details").show();
}

function addNewProduct() {
    // Add your AJAX call or form submission logic here
    alert("New product added!");
}


function loadContent(page) {
    $.ajax({
        url: page,
        type: 'GET',
        success: function(data) {
            $('#dynamic-content').html(data);
        },
        error: function() {
            $('#dynamic-content').html('<p>Error loading content. Please try again.</p>');
        }
    });
}



