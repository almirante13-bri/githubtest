window.addEventListener('scroll', function () {
    const secondaryFooter = document.querySelector('.secondary-footer');
    const primaryFooter = document.querySelector('.custom-footer');
    const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
    const scrolled = window.scrollY;

    if (scrolled >= scrollableHeight - 10) {
        secondaryFooter.style.bottom = "0";
        primaryFooter.style.transform = "translateY(-100%)";
    } else {
        secondaryFooter.style.bottom = "-100px";
        primaryFooter.style.transform = "translateY(0)";
    }
});
