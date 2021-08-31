setTimeout(
    function () {
        // get the flash message
        var flashes = document.querySelector(".flash-message");

        // if the message is displayed
        // then start animation to blur message
        if (flashes) {
            flashes.style.transition = "opacity " + 3 + "s";
            flashes.style.opacity = 0;
            flashes.addEventListener("transitionend", function () {
                flashes.style.display = "none";
            });
        }
    }, 3000
);