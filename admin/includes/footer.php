<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

const toggleBtn = document.getElementById("toggleSidebar");

const sidebar = document.querySelector(".sidebar");

const mainContent = document.querySelector(".main-content");

const topbar = document.querySelector(".topbar");

const sidebarLogo = document.querySelector(".sidebar .logo");

if(localStorage.getItem("sidebarCollapsed") === "true") {

    sidebar.classList.add("collapsed");
    mainContent.classList.add("expanded");
    topbar.classList.add("expanded");
    sidebarLogo.textContent = "SV";

}

toggleBtn.addEventListener("click", () => {

    sidebar.classList.toggle("collapsed");

    mainContent.classList.toggle("expanded");

    topbar.classList.toggle("expanded");

    sidebarLogo.textContent = sidebar.classList.contains("collapsed") ? "FS" : "Fashion Shop Admin";
    
    const isCollapsed = sidebar.classList.contains("collapsed");
    localStorage.setItem("sidebarCollapsed", isCollapsed);

    setTimeout(() => {

        if(typeof revenueChart !== "undefined") revenueChart.resize();
        if(typeof statusChart !== "undefined") statusChart.resize();

    }, 300);

});

</script>