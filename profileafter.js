$(document).ready(function() {
    $("#personal-info-tab").click(function() {
        $(".content").hide();
        $("#personal-info").show();
        $(".nav-link").removeClass("active");
        $(this).addClass("active");
    });

    $("#orders-tab").click(function() {
        $(".content").hide();
        $("#orders").show();
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

function showOrderDetails(orderId, status, date) {
    $("#order-id").text(orderId);
    $("#order-status").text(status);
    $("#order-date").text(date);
    $("#order-details").show();
}
