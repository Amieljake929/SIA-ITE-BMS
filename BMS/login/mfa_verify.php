<?php
session_start();
if (empty($_SESSION['mfa_user_id'])) {
  header("Location: login.php");
  exit();
}
$full_name = $_SESSION['mfa_full_name'] ?? 'Resident';
$email     = $_SESSION['mfa_email'] ?? '';
$expires   = $_SESSION['mfa_expires'] ?? '';
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>MFA Verification</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
    <h1 class="text-xl font-bold text-green-800 mb-2">Two-Factor Verification</h1>
    <p class="text-sm text-gray-600 mb-4">
      Hi <span class="font-medium"><?php echo htmlspecialchars($full_name); ?></span>, we sent a 6-digit code to
      <span class="font-mono"><?php echo htmlspecialchars($email); ?></span>.
      <?php if ($expires): ?>
        <br/>Code expires at <span class="font-medium"><?php echo htmlspecialchars(date('M d, Y h:i A', strtotime($expires))); ?></span>.
      <?php endif; ?>
    </p>
    <div id="alert" class="hidden mb-3 px-3 py-2 rounded text-sm"></div>
    <form id="verifyForm" class="space-y-3">
      <label class="block text-sm font-medium text-gray-700">Enter 6-digit code</label>
      <input type="text" name="code" id="code" maxlength="6" inputmode="numeric" pattern="\d{6}"
             class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
             placeholder="123456" required />
      <div class="flex justify-end gap-2 pt-1">
        <a href="login.php" class="px-4 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200">Back</a>
        <button type="submit" class="px-5 py-2 text-sm rounded-lg bg-green-700 hover:bg-green-800 text-white">Verify</button>
      </div>
    </form>
  </div>
<script>
const form = document.getElementById('verifyForm');
const alertBox = document.getElementById('alert');
function showAlert(msg, ok=false){
  alertBox.className = 'mb-3 px-3 py-2 rounded text-sm ' + (ok ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200');
  alertBox.textContent = msg; alertBox.classList.remove('hidden');
}
form.addEventListener('submit', async (e)=>{
  e.preventDefault();
  const code = document.getElementById('code').value.trim();
  if (!/^\d{6}$/.test(code)) { showAlert('Please enter the 6-digit code.'); return; }
  try {
    const res = await fetch('mfa_verify_process.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({code})});
    const data = await res.json();
    if (data.success){ showAlert('Verified! Redirecting…', true); window.location.href = data.redirect_url || '../resident/resident_dashboard.php'; }
    else { showAlert(data.message || 'Invalid code.'); }
  } catch(e){ showAlert('Network error. Please try again.'); }
});
</script>
</body></html>
