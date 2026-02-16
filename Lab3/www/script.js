document.getElementById("myForm").addEventListener("submit", function(e) {

    const username = this.username.value;
    const role_team = this.role_team.value;

    alert("Привет, " + username + "! Твоя роль: " + role_team);
});