document.getElementById("generateQR").addEventListener("click", function () {
    var qrMessage = document.getElementById("qrMessage");

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "set_qr_session.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "success") {
                qrMessage.textContent = " Successfully generated QR Code! Employees can see your generated QR code.";
                qrMessage.className = "success";
            } else {
                qrMessage.textContent = " You already generated a QR code!";
                qrMessage.className = "warning";
            }
            qrMessage.style.display = "block";
        }
    };
    xhr.send();
});