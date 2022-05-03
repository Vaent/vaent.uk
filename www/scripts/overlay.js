"use strict";

const expandedImageOverlay = document.getElementById("expanded-image-overlay");
const expandedImage = document.getElementById("expanded-image");

function expandImage(src) {
    expandedImage.src = src;
    expandedImageOverlay.style.display = "block";
}

function collapseImage() {
    expandedImageOverlay.style.display = "none";
}

document.addEventListener("keydown", event => {
    if (event.code == "Escape") collapseImage();
});
