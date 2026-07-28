
document.addEventListener("DOMContentLoaded", function () {

    console.log("Jakup Ceraja JS loaded");

    const slides = document.querySelectorAll(".hero-slide");
    const dots = document.querySelectorAll(".hero-dot");

    const nextButton = document.querySelector(".hero-next");
    const prevButton = document.querySelector(".hero-prev");

    console.log("Slides:", slides.length);
    console.log("Next button:", nextButton);
    console.log("Previous button:", prevButton);

    if (slides.length <= 1) {
        console.log("Nuk ka mjaft slides.");
        return;
    }

    let currentSlide = 0;
    let autoSlide;


    function showSlide(index) {

        if (index >= slides.length) {
            index = 0;
        }

        if (index < 0) {
            index = slides.length - 1;
        }

        currentSlide = index;


        slides.forEach(function (slide) {
            slide.classList.remove("active");
        });


        dots.forEach(function (dot) {
            dot.classList.remove("active");
        });


        slides[currentSlide].classList.add("active");


        if (dots[currentSlide]) {
            dots[currentSlide].classList.add("active");
        }

        console.log("Current slide:", currentSlide);

    }


    function nextSlide() {

        showSlide(currentSlide + 1);

    }


    function previousSlide() {

        showSlide(currentSlide - 1);

    }


    if (nextButton) {

        nextButton.addEventListener("click", function () {

            console.log("NEXT clicked");

            nextSlide();

            resetAutoSlide();

        });

    }


    if (prevButton) {

        prevButton.addEventListener("click", function () {

            console.log("PREVIOUS clicked");

            previousSlide();

            resetAutoSlide();

        });

    }


    dots.forEach(function (dot, index) {

        dot.addEventListener("click", function () {

            showSlide(index);

            resetAutoSlide();

        });

    });


    function startAutoSlide() {

        autoSlide = setInterval(function () {

            nextSlide();

        }, 6000);

    }


    function resetAutoSlide() {

        clearInterval(autoSlide);

        startAutoSlide();

    }


    showSlide(0);

    startAutoSlide();

});