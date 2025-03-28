// JavaScript to handle color selection
document.getElementById('color-select').addEventListener('change', function() {
    const selectedIndex = this.selectedIndex;
    const selectedColor = this.options[selectedIndex].value;
    document.querySelector('.carousel-item.active img').src = selectedColor;
});
