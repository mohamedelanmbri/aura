function toggleAddressForm() {
    var addressChoice = document.getElementById("address_choice").value;
    var homeAddress = document.getElementById("home_address");
    var newAddress = document.getElementById("new_address");
    
    if (addressChoice === "home") {
        homeAddress.style.display = "block";
        newAddress.style.display = "none";
    } else {
        homeAddress.style.display = "none";
        newAddress.style.display = "block";
    }
}
