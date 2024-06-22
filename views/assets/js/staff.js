const updateRoleMenu = (ev, roleElem) => {
    const userId = parseInt(ev.currentTarget.value) ?? 0;
    if (userId < -1) {
        console.error('Invalid userId');
        return false;
    }
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get("action");
    const get = action === "grant" ? "non-user-roles" : "user-roles";
    fetch(`/staff_api.php?get=${get}&id=${userId}`)
        .then(response => response.json())
        .then(response => {
            if (response.type !== "success") {
                console.error(response.message);
                return false;
            }
            let roleMarkup = `<option value="0" disabled selected>-- SELECT --</option>`;
            for (const role of response.data) {
                roleMarkup += `<option value="${role.id}">${role.name}</option>`;
            }
            roleElem.innerHTML = roleMarkup;
            console.log(response);
        })
        .catch(err => console.error(err));
};
document.addEventListener("DOMContentLoaded", () => {
    const userElem = document.getElementById("user");
    const roleElem = document.getElementById("role");
    userElem.addEventListener("change", ev => {
        updateRoleMenu(ev, roleElem);
    });
});
