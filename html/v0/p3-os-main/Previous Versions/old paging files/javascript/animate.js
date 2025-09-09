
// JavaScript for animation buttons

//
// Adds Event Listeners to all of the buttons
//

document.getElementById("start").addEventListener("click", function(e) {
    startAnimation();
})

document.getElementById("next").addEventListener("click", function(e) {
    nextAnimation();
})

document.getElementById("back").addEventListener("click", function(e) {
    backAnimation();
})

document.getElementById("end").addEventListener("click", function(e) {
    endAnimation();
})


// Variables for functions
var tag;
var text;
var element;

// Called when Start button is clicked
function startAnimation() {
    tag = document.createElement("p");
    text = document.createTextNode("Start Button Clicked :)");
    tag.appendChild(text);
    element = document.getElementById("animation-container");
    element.appendChild(tag);
}


// Called when Next button is clicked
function nextAnimation() {
    tag = document.createElement("p");
    text = document.createTextNode("Next Button Clicked :)");
    tag.appendChild(text);
    element = document.getElementById("animation-container");
    element.appendChild(tag);
}


// Called when Back button is clicked
function backAnimation() {
    tag = document.createElement("p");
    text = document.createTextNode("Back Button Clicked :)");
    tag.appendChild(text);
    element = document.getElementById("animation-container");
    element.appendChild(tag);
}


// Called when End button is clicked
function endAnimation() {
    tag = document.createElement("p");
    text = document.createTextNode("End Button Clicked :)");
    tag.appendChild(text);
    element = document.getElementById("animation-container");
    element.appendChild(tag);
}