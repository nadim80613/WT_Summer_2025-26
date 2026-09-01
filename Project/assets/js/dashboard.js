document.addEventListener("DOMContentLoaded", function () {

    /* =========================================================
       THEME TOGGLE
    ========================================================= */

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


    /* =========================================================
       ADD SCHEDULE FORM
    ========================================================= */

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

            if (
                addScheduleCard.style.display === "none" ||
                addScheduleCard.style.display === ""
            ) {

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


    /* =========================================================
       FLIGHT FILTER
    ========================================================= */

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


    /* =========================================================
       DAY PILLS
    ========================================================= */

    const dayPills =
        document.querySelectorAll(".day-pill");


    dayPills.forEach(function (pill) {

        pill.addEventListener("click", function () {

            this.classList.toggle("active");

        });

    });


    /* =========================================================
       FLIGHT STATUS
    ========================================================= */

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


    /* =========================================================
       EDIT FLIGHT
    ========================================================= */

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


            const flightNumberElement =
                row.querySelector(".flight-no-link");

            const airlineElement =
                row.querySelector(".airline-text");

            const route =
                row.querySelectorAll(".route-iata");

            const times =
                row.querySelectorAll(".schedule-time-box span");

            const aircraftElement =
                row.querySelector(".aircraft-text");

            const gateElement =
                row.querySelector(".gate-bold-cyan");


            if (
                !flightNumberElement ||
                !airlineElement ||
                route.length < 2 ||
                times.length < 2 ||
                !aircraftElement ||
                !gateElement
            ) {
                return;
            }


            const flightNumber =
                flightNumberElement.textContent.trim();

            const airline =
                airlineElement.textContent.trim();

            const departure =
                route[0].textContent.trim();

            const destination =
                route[1].textContent.trim();

            const departureTime =
                times[0].textContent.trim();

            const arrivalTime =
                times[1].textContent.trim();

            const aircraft =
                aircraftElement.textContent.trim();

            const gate =
                gateElement.textContent.trim();


            const editFlightId =
                document.getElementById("edit_flight_id");

            const editFlightNumber =
                document.getElementById("edit_flight_number");

            const editAirline =
                document.getElementById("edit_airline");

            const editDeparture =
                document.getElementById("edit_departure");

            const editDestination =
                document.getElementById("edit_destination");

            const editDepartureTime =
                document.getElementById("edit_departure_time");

            const editArrivalTime =
                document.getElementById("edit_arrival_time");

            const editAircraft =
                document.getElementById("edit_aircraft");

            const editGateNumber =
                document.getElementById("edit_gate_number");


            if (editFlightId) {
                editFlightId.value =
                    this.getAttribute("data-id");
            }

            if (editFlightNumber) {
                editFlightNumber.value =
                    flightNumber;
            }

            if (editAirline) {
                editAirline.value =
                    airline;
            }

            if (editDeparture) {
                editDeparture.value =
                    departure;
            }

            if (editDestination) {
                editDestination.value =
                    destination;
            }

            if (editDepartureTime) {
                editDepartureTime.value =
                    departureTime;
            }

            if (editArrivalTime) {
                editArrivalTime.value =
                    arrivalTime;
            }

            if (editAircraft) {
                editAircraft.value =
                    aircraft;
            }

            if (editGateNumber) {
                editGateNumber.value =
                    gate === "TBD" ? "" : gate;
            }


            if (editScheduleCard) {

                editScheduleCard.style.display =
                    "block";

                editScheduleCard.scrollIntoView({
                    behavior: "smooth"
                });

            }

        });

    });


    /* =========================================================
       CLOSE EDIT FLIGHT FORM
    ========================================================= */

    if (closeEditFormBtn) {

        closeEditFormBtn.addEventListener(
            "click",
            function () {

                if (editScheduleCard) {

                    editScheduleCard.style.display =
                        "none";

                }

            }
        );

    }


    if (cancelEditFormBtn) {

        cancelEditFormBtn.addEventListener(
            "click",
            function () {

                if (editScheduleCard) {

                    editScheduleCard.style.display =
                        "none";

                }

            }
        );

    }


    /* =========================================================
       GATE & TERMINAL MODAL
    ========================================================= */

    /*
     * IMPORTANT:
     *
     * Your PHP HTML uses:
     *
     * onclick="openGateModal(...)"
     *
     * Therefore these functions MUST be available
     * globally.
     *
     * We attach them to window.
     */


    window.openGateModal =
        function (
            gateId,
            gateNumber,
            status,
            terminal
        ) {

            const gateModal =
                document.getElementById("gateModal");

            const modalGateId =
                document.getElementById("modalGateId");

            const modalGateNumber =
                document.getElementById("modalGateNumber");

            const modalStatus =
                document.getElementById("modalStatus");

            const modalTerminal =
                document.getElementById("modalTerminal");


            if (!gateModal) {
                console.error(
                    "gateModal not found"
                );
                return;
            }


            if (modalGateId) {

                modalGateId.value =
                    gateId;

            }


            if (modalGateNumber) {

                modalGateNumber.textContent =
                    gateNumber;

            }


            if (modalStatus) {

                modalStatus.value =
                    status;

            }


            if (modalTerminal) {

                modalTerminal.value =
                    terminal;

            }


            gateModal.classList.add("show");

        };


    window.closeGateModal =
        function () {

            const gateModal =
                document.getElementById("gateModal");


            if (gateModal) {

                gateModal.classList.remove(
                    "show"
                );

            }

        };


    /* =========================================================
       ASSIGN FLIGHT MODAL
    ========================================================= */


    window.openAssignFlightModal =
        function (
            flightId,
            flightNumber
        ) {

            const assignFlightModal =
                document.getElementById(
                    "assignFlightModal"
                );

            const assignFlightId =
                document.getElementById(
                    "assignFlightId"
                );

            const assignFlightNumber =
                document.getElementById(
                    "assignFlightNumber"
                );


            if (!assignFlightModal) {

                console.error(
                    "assignFlightModal not found"
                );

                return;

            }


            if (assignFlightId) {

                assignFlightId.value =
                    flightId;

            }


            if (assignFlightNumber) {

                assignFlightNumber.textContent =
                    flightNumber;

            }


            assignFlightModal.classList.add(
                "show"
            );

        };


    window.closeAssignFlightModal =
        function () {

            const assignFlightModal =
                document.getElementById(
                    "assignFlightModal"
                );


            if (assignFlightModal) {

                assignFlightModal.classList.remove(
                    "show"
                );

            }

        };


    /* =========================================================
       CLOSE MODALS BY CLICKING OUTSIDE
    ========================================================= */

    document.addEventListener(
        "click",
        function (event) {

            const gateModal =
                document.getElementById(
                    "gateModal"
                );

            const assignModal =
                document.getElementById(
                    "assignFlightModal"
                );


            if (
                gateModal &&
                event.target === gateModal
            ) {

                window.closeGateModal();

            }


            if (
                assignModal &&
                event.target === assignModal
            ) {

                window.closeAssignFlightModal();

            }

        }
    );

    /* =====================================================
   BAGGAGE SEARCH
===================================================== */

const baggageSearch = document.getElementById("baggageSearch");

const baggageRows = document.querySelectorAll(".baggage-row");

const itemsCount = document.getElementById("itemsCount");


if (baggageSearch) {

    baggageSearch.addEventListener("input", function () {

        const searchText = this.value.toLowerCase().trim();

        let visibleItems = 0;


        baggageRows.forEach(function (row) {

            const rowText = row.innerText.toLowerCase();


            if (rowText.includes(searchText)) {

                row.style.display = "";

                visibleItems++;

            } else {

                row.style.display = "none";

            }

        });


        if (itemsCount) {

            itemsCount.textContent = visibleItems;

        }

    });

}


/* =====================================================
   UPDATE BAGGAGE STATUS
===================================================== */

function updateBaggageStep(bagId, step) {

    const formData = new FormData();


    formData.append(
        "update_step",
        "1"
    );


    formData.append(
        "bag_id",
        bagId
    );


    formData.append(
        "step",
        step
    );


    fetch(
        "baggage_status.php",
        {
            method: "POST",
            body: formData
        }
    )


    .then(function (response) {

        return response.json();

    })


    .then(function (data) {

        if (data.success) {

            location.reload();

        } else {

            alert("Could not update baggage status.");

        }

    })


    .catch(function (error) {

        console.log(error);

        alert("Something went wrong.");

    });

}


});