/* source: github.com/Vaent/image-gallery */
"use strict";

function toggle(elementClicked) {
    if (elementClicked.classList.contains("toggle-slider") || elementClicked.classList.contains("toggle-not-selected")) {
        const siblings = Array.from(elementClicked.parentElement.children);
        const slider = siblings.find(s => s.classList.contains("toggle-slider"));
        const oldPosition = slider.dataset.currentlySelected;
        const newPosition = 1 ^ oldPosition;
        slider.dataset.currentlySelected = newPosition;
        const oldSelectedElement = siblings.find(s => s.dataset.togglePosition == oldPosition);
        const newSelectedElement = siblings.find(s => s.dataset.togglePosition == newPosition);
        oldSelectedElement.classList.replace("toggle-currently-selected", "toggle-not-selected");
        newSelectedElement.classList.replace("toggle-not-selected", "toggle-currently-selected");
        oldSelectedElement.onclick = newSelectedElement.onclick;
        newSelectedElement.onclick = null;
        return true;
    }
}

function toggleNudes(elementClicked) {
    if (toggle(elementClicked)) {
        const isSelectedShowNude = elementClicked.parentElement.getElementsByClassName("toggle-slider")[0].dataset.currentlySelected;
        for (let frame of document.getElementsByClassName("image-box")) {
            if (isSelectedShowNude == true) {
                frame.querySelector("img").src ||= frame.querySelector("img").dataset.src;
                frame.style.display = "";
            } else if (frame.dataset.isNude) {
                frame.style.display = "none";
            }
        }
        window.scrollTo({top: 0, behavior: "smooth"});
    }
}
