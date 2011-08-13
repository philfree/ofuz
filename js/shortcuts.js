//<![CDATA[
$(document).keydown(function (e) {
    if (e.ctrlKey && e.altKey) {
        switch (e.keyCode) {
        case 67: // C
        case 79: // O
            document.location.href = "/contacts.php";
            break;
        case 73: // I
            document.location.href = "/invoices.php";
            break
        case 78: // N
            document.location.href = "/contact_add.php";
            break
        case 80: // P
            document.location.href = "/projects.php";
            break;
        case 84: // T
            document.location.href = "/tasks.php";
            break;
        case 87: // W
            document.location.href = "/index.php";
            break;
        }
    }
});
//]]>