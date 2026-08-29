
document.addEventListener("DOMContentLoaded", function () {

    const themeToggle = document.getElementById("themeToggle");
    const themeIcon = document.getElementById("themeIcon");
    const themeText = document.getElementById("themeText");

    if (themeToggle) {

        const savedTheme = localStorage.getItem("staffTheme");

        if (savedTheme === "light") {
            document.body.classList.add("light-mode");
        }

        updateThemeButton();

        themeToggle.addEventListener("click", function () {

            document.body.classList.toggle("light-mode");

            if (document.body.classList.contains("light-mode")) {
                localStorage.setItem("staffTheme", "light");
            } else {
                localStorage.setItem("staffTheme", "dark");
            }

            updateThemeButton();

        });

    }


    function updateThemeButton() {

        if (!themeIcon || !themeText) {
            return;
        }

        if (document.body.classList.contains("light-mode")) {

            themeIcon.textContent = "🌙";
            themeText.textContent = "Night Mode";

        } else {

            themeIcon.textContent = "☀️";
            themeText.textContent = "Day Mode";

        }

    }


    const toggleFormBtn =
        document.getElementById("toggleFormBtn");

    const addScheduleCard =
        document.getElementById("addScheduleCard");

    const closeFormBtn =
        document.getElementById("closeFormBtn");

    const cancelFormBtn =
        document.getElementById("cancelFormBtn");


    if (toggleFormBtn && addScheduleCard) {

        toggleFormBtn.addEventListener("click", function () {

            if (addScheduleCard.style.display === "none" ||
                addScheduleCard.style.display === "") {

                addScheduleCard.style.display = "block";

            } else {

                addScheduleCard.style.display = "none";

            }

        });

    }


    if (closeFormBtn && addScheduleCard) {

        closeFormBtn.addEventListener("click", function () {

            addScheduleCard.style.display = "none";

        });

    }


    if (cancelFormBtn && addScheduleCard) {

        cancelFormBtn.addEventListener("click", function () {

            addScheduleCard.style.display = "none";

        });

    }


    const filterButtons =
        document.querySelectorAll(".filter-btn");

    const flightRows =
        document.querySelectorAll(".flight-row");

    const visibleCount =
        document.getElementById("visibleCount");


    filterButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            filterButtons.forEach(function (btn) {

                btn.classList.remove("active");

            });

            this.classList.add("active");

            const selectedStatus =
                this.getAttribute("data-status");

            let count = 0;


            flightRows.forEach(function (row) {

                const rowStatus =
                    row.getAttribute("data-status");


                if (
                    selectedStatus === "all" ||
                    rowStatus === selectedStatus
                ) {

                    row.style.display = "";

                    count++;

                } else {

                    row.style.display = "none";

                }

            });


            if (visibleCount) {

                visibleCount.textContent = count;

            }

        });

    });


    const dayPills =
        document.querySelectorAll(".day-pill");


    dayPills.forEach(function (pill) {

        pill.addEventListener("click", function () {

            this.classList.toggle("active");

        });

    });


    const statusBadges =
        document.querySelectorAll(".clickable-badge");


    const statuses = [

    {
        name: "on time",
        text: "On Time",
        className: "status-scheduled"
    },

    {
        name: "boarding",
        text: "Boarding",
        className: "status-boarding"
    },

    {
        name: "delayed",
        text: "Delayed",
        className: "status-delayed"
    },

    {
        name: "departed",
        text: "Departed",
        className: "status-departed"
    },

    {
        name: "arrived",
        text: "Arrived",
        className: "status-arrived"
    },

    {
        name: "cancelled",
        text: "Cancelled",
        className: "status-cancelled"
    }

];




    statusBadges.forEach(function (badge) {

        badge.addEventListener("click", function () {

            const row =
                this.closest(".flight-row");


            if (!row) {
                return;
            }


            const currentStatus =
                row.getAttribute("data-status");


            let currentIndex =
                statuses.findIndex(function (status) {

                    return status.name === currentStatus;

                });


            if (currentIndex === -1) {
                currentIndex = 0;
            }


            const nextIndex =
                (currentIndex + 1) % statuses.length;


            const nextStatus =
                statuses[nextIndex];


            statuses.forEach(function (status) {

                badge.classList.remove(
                    status.className
                );

            });


            badge.classList.add(
                nextStatus.className
            );


            badge.textContent =
                "• " + nextStatus.text;


            row.setAttribute(
                "data-status",
                nextStatus.name
            );

        });

    });

    /* =========================================
   EDIT FLIGHT
   ========================================= */

const editButtons =
    document.querySelectorAll(".edit-flight-btn");

const editScheduleCard =
    document.getElementById("editScheduleCard");

const closeEditFormBtn =
    document.getElementById("closeEditFormBtn");

const cancelEditFormBtn =
    document.getElementById("cancelEditFormBtn");


editButtons.forEach(function (button) {

    button.addEventListener("click", function () {

        const row =
            this.closest(".flight-row");

        if (!row) {
            return;
        }


        /* Get values from table */

        const flightNumber =
            row.querySelector(".flight-no-link").textContent.trim();

        const airline =
            row.querySelector(".airline-text").textContent.trim();

        const route =
            row.querySelectorAll(".route-iata");

        const departure =
            route[0].textContent.trim();

        const destination =
            route[1].textContent.trim();

        const times =
            row.querySelectorAll(".schedule-time-box span");

        const departureTime =
            times[0].textContent.trim();

        const arrivalTime =
            times[1].textContent.trim();

        const aircraft =
            row.querySelector(".aircraft-text").textContent.trim();

        const gate =
            row.querySelector(".gate-bold-cyan").textContent.trim();


        /* Put values into edit form */

        document.getElementById(
            "edit_flight_id"
        ).value =
            this.getAttribute("data-id");


        document.getElementById(
            "edit_flight_number"
        ).value =
            flightNumber;


        document.getElementById(
            "edit_airline"
        ).value =
            airline;


        document.getElementById(
            "edit_departure"
        ).value =
            departure;


        document.getElementById(
            "edit_destination"
        ).value =
            destination;


        document.getElementById(
            "edit_departure_time"
        ).value =
            departureTime;


        document.getElementById(
            "edit_arrival_time"
        ).value =
            arrivalTime;


        document.getElementById(
            "edit_aircraft"
        ).value =
            aircraft;


        document.getElementById(
            "edit_gate_number"
        ).value =
            gate === "TBD" ? "" : gate;


        /* Show edit form */

        editScheduleCard.style.display =
            "block";

        editScheduleCard.scrollIntoView({
            behavior: "smooth"
        });

    });

});


/* Close Edit Form */

if (closeEditFormBtn) {

    closeEditFormBtn.addEventListener(
        "click",
        function () {

            editScheduleCard.style.display =
                "none";

        }
    );

}


/* Cancel Edit */

if (cancelEditFormBtn) {

    cancelEditFormBtn.addEventListener(
        "click",
        function () {

            editScheduleCard.style.display =
                "none";

        }
    );

}

});

