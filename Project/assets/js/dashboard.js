
document.addEventListener("DOMContentLoaded", function () {

    const themeToggle = document.getElementById("themeToggle");
    const themeIcon = document.getElementById("themeIcon");
    const themeText = document.getElementById("themeText");

    if (!themeToggle) {
        console.error("Theme toggle not found!");
        return;
    }


    /* =========================
       LOAD SAVED THEME
    ========================= */

    const savedTheme = localStorage.getItem("staffTheme");

    if (savedTheme === "light") {
        document.body.classList.add("light-mode");
    }


    updateTheme();


    /* =========================
       TOGGLE THEME
    ========================= */

    themeToggle.addEventListener("click", function () {

        document.body.classList.toggle("light-mode");

        if (document.body.classList.contains("light-mode")) {

            localStorage.setItem("staffTheme", "light");

        } else {

            localStorage.setItem("staffTheme", "dark");

        }

        updateTheme();

    });


    /* =========================
       UPDATE ICON + TEXT
    ========================= */

    function updateTheme() {

        if (document.body.classList.contains("light-mode")) {

            themeIcon.textContent = "🌙";
            themeText.textContent = "Night Mode";

        } else {

            themeIcon.textContent = "☀️";
            themeText.textContent = "Day Mode";

        }

    }

});

