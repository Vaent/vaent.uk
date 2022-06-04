"use strict";

const expandedImageOverlay = document.getElementById("expanded-image-overlay");
const expandedImage = document.getElementById("expanded-image");

function expandImage(src) {
    expandedImage.src = `images/${src}.png`;
    expandedImageOverlay.style.display = "block";
}

function collapseImage() {
    expandedImageOverlay.style.display = "none";
}

function handleUriFragment() {
    if (location.hash) {
        let frag = location.hash.substring(1);
        let img = document.querySelector(`img[onclick="updateHash('${frag}')"]`);
        if (img?.parentNode.parentNode.style.display != "none") {
            expandImage(frag);
        } else {
            location.hash = "";
        }
    } else {
        collapseImage();
    }
}

function updateHash(fragment) {
    location.hash = fragment;
}

addEventListener('hashchange', handleUriFragment);

document.addEventListener("keydown", event => {
    if (event.code == "Escape") updateHash('');
});

// check and action any initially supplied fragment
handleUriFragment();
