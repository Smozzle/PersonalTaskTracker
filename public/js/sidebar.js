document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggle-btn");

    toggleBtn.addEventListener("click", () => {
        // For desktop view
        if (window.innerWidth > 768) {
            if (sidebar.classList.contains("collapsed")) {
                sidebar.classList.remove("collapsed");
            } else {
                sidebar.classList.add("collapsed");
            }
        } 
        // For mobile view
        else {
            if (sidebar.classList.contains("active")) {
                sidebar.classList.remove("active");
            } else {
                sidebar.classList.add("active");
            }
        }
    });
});
