<?php
session_start();
require_once 'connection.php';

// TEMP user ID para gumana kahit walang login
$user_id = $_SESSION['user_id'] ?? 1;

// Save waiver acceptance kapag na-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_waiver'])) {
    $stmt = $conn->prepare("
        INSERT INTO waiver_acceptance (user_id, waiver_accepted, waiver_accepted_at)
        VALUES (?, 1, NOW())
        ON DUPLICATE KEY UPDATE
            waiver_accepted = 1,
            waiver_accepted_at = NOW()
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

// CHECK if ang user ay na-accept na ang waiver
$check = $conn->prepare("SELECT waiver_accepted FROM waiver_acceptance WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$res = $check->get_result();
$row = $res->fetch_assoc();
$alreadyAccepted = $row && $row['waiver_accepted'] == 1;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Application Form</title>

<style>
body { font-family: "Inter", sans-serif; margin:0; padding:0; }

/* MODAL OVERLAY */
.modal-overlay {
    display: none;
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(3px);
    justify-content: center; align-items: center;
    z-index: 99999;
}

/* MODAL BOX */
.waiver-modal-box {
    width: 520px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    overflow: hidden;
}

/* HEADER */
.waiver-header {
    background: #f6dd8c;
    padding: 14px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.waiver-title { font-size: 17px; font-weight: 600; color: #3a2e00; }
.waiver-close { background: none; border: none; font-size: 18px; cursor: pointer; color: #3a2e00; }

/* BODY */
.waiver-body {
    padding: 18px 22px;
    max-height: 430px;
    overflow-y: auto;
}
.waiver-body p { font-size: 14px; color: #222; line-height: 1.55; margin-bottom: 12px; }
.waiver-section-title { font-size: 15px; font-weight: 700; margin-bottom: 10px; }

/* SIGNATURE BLOCK */
.signature-block { margin-top:30px; }
.signature-block p { font-size:14px; display:block; white-space:pre; margin-bottom:5px; }

/* FOOTER BUTTONS */
.modal-buttons {
    padding: 15px;
    display: flex;
    justify-content: space-between;
    background: #fafafa;
    border-top: 1px solid #e2e2e2;
}
.yellow-btn {
    flex: 1;
    margin: 0 5px;
    padding: 10px;
    background: #f7c948;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.2s;
}
.yellow-btn:hover { background: #efb930; }
</style>

</head>
<body>

<h2>Application Form</h2>

<form method="POST">
    <label style="font-size: 16px; display:block; width:600px;">
        <input type="checkbox" name="accept_waiver" required <?php if($alreadyAccepted) echo "checked"; ?>>
        I acknowledge the risks involved in participating in the club's events and agree to the waiver
        liability for any injuries or damages that may occur.
        <a href="javascript:void(0);" id="openWaiverBtn" style="color:blue; text-decoration:underline;">
            READ FULL WAIVER
        </a>
    </label>

    <br><br>

    <label>Full Name:</label><br>
    <input type="text" name="fullname" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <button type="submit">Submit Application</button>
</form>

<!-- WAIVER MODAL -->
<div id="waiverModal" class="modal-overlay">
    <div class="waiver-modal-box">

        <!-- HEADER -->
        <div class="waiver-header">
            <span class="waiver-title">Waiver and Liability Release</span>
            <button class="waiver-close" onclick="closeWaiverModal()">✕</button>
        </div>

        <!-- BODY -->
        <div class="waiver-body" id="waiverContent">
            <h3 class="waiver-section-title">WAIVER AND LIABILITY TERMS</h3>

            <p>This waiver is provided for informational purposes. All 
            members and participants are encouraged to read and understand the 
            following terms before joining any club activity.</p>

            <p>Participation in any activity organized by the Star Touring 
            Motorcycle Club Philippines (the Club) is voluntary and may involve 
            certain risks, including but not limited to accidents, personal 
            injury, or property damage.</p>

            <p>The participant acknowledges full responsibility for their own 
            safety, actions, and equipment during rides and events.</p>

            <p>The Club and its organizers shall not be held liable for any 
            injuries, damages, or losses incurred during participation in 
            official or unofficial club activities.</p>

            <p>All riders are expected to follow traffic rules and safety 
            protocols, including wearing proper safety gear and ensuring their 
            motorcycles are in roadworthy condition.</p>

            <p>This waiver remains in effect throughout the participant's 
            involvement with the Club and applies to all current and future 
            events, unless otherwise revoked in writing.</p>

            <!-- SIGNATURE SECTION -->
            <div class="signature-block">
                <p>Signature: ____________________________</p>
                <p id="waiverDate">Date: ____________________________</p>
            </div>
        </div>

        <!-- BUTTONS -->
        <div class="modal-buttons">
            <button type="button" onclick="downloadPDF()" class="yellow-btn">Download PDF</button>
            <button type="button" onclick="downloadDOCX()" class="yellow-btn">Download DOCX</button>
            <button type="button" onclick="printWaiver()" class="yellow-btn">Print</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    
// ---- GLOBAL FUNCTIONS ----
window.closeWaiverModal = function() {
    document.getElementById("waiverModal").style.display = "none";
}

// AUTO SET DATE
function setDate() {
    const dateEl = document.getElementById('waiverDate');
    const today = new Date();
    const formattedDate = today.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    dateEl.textContent = 'Date: ' + formattedDate;
}

// DOWNLOAD PDF 
window.downloadPDF = function() { 
    setDate();
    const waiverContent = document.getElementById('waiverContent');
    html2pdf().from(waiverContent).save();
}

// DOWNLOAD DOCX
window.downloadDOCX = function() {
    setDate();
    const htmlContent = document.getElementById("waiverContent").innerHTML;
    const blob = new Blob([htmlContent], { type: "application/vnd.openxmlformats-officedocument.wordprocessingml.document" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "Waiver.docx";
    a.click();
}

// PRINT
window.printWaiver = function() {
    setDate();
    const w = window.open('', '', 'width=800,height=600');
    const waiverContent = document.getElementById("waiverContent");
    w.document.write("<html><head><title>Waiver</title></head><body>");
    w.document.write(waiverContent.innerHTML);
    w.document.write("</body></html>");
    w.document.close();
    w.print();
}

// MODAL OPEN
document.getElementById("openWaiverBtn").addEventListener("click", function() {
    document.getElementById("waiverModal").style.display = "flex";
});
</script>

</body>
</html>
