<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: ../login.php");
    exit();
}

// Database connection
include '../../login/db_connect.php';


// Get parameters
$tab = $_GET['tab'] ?? '';
$id = $_GET['id'] ?? 0;

if (!$tab || !$id) {
    die("Invalid request.");
}

$allowed_tables = [
    'barangay_clearance', 'business_permit', 'certificate_of_residency',
    'certificate_of_indigency', 'cedula', 'solo_parents', 'first_time_job_seekers'
];

if (!in_array($tab, $allowed_tables)) {
    die("Invalid document type.");
}

$table = $conn->real_escape_string($tab);
$id = (int)$id;

// Fetch record
$stmt = $conn->prepare("SELECT * FROM `$table` WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Record not found or not approved yet.");
}

$record = $result->fetch_assoc();

// Check if approved
if ($record['status'] !== 'Approved') {
    die("This document is not yet approved. Cannot print.");
}

$conn->close();

// Helper: Format name
function fullName($first, $middle, $last) {
    $middle_initial = '';
    if (!empty($middle)) {
        $middle_initial = strtoupper(substr(trim($middle), 0, 1)) . '.';
    }
    return htmlspecialchars(strtoupper(trim($first)) . ' ' . $middle_initial . ' ' . strtoupper(trim($last)));
}

// Document title mapping
$doc_titles = [
    'barangay_clearance' => 'BARANGAY CLEARANCE',
    'business_permit' => 'BUSINESS PERMIT',
    'certificate_of_residency' => 'CERTIFICATE OF RESIDENCY',
    'certificate_of_indigency' => 'CERTIFICATE OF INDIGENCY',
    'cedula' => 'COMMUNITY TAX CERTIFICATE (CEDULA)',
    'solo_parents' => 'SOLO PARENT ID & CERTIFICATE',
    'first_time_job_seekers' => 'CERTIFICATE OF FIRST-TIME JOB SEEKER'
];

$doc_title = $doc_titles[$tab] ?? 'DOCUMENT';
$doc_subtitle = [
    'barangay_clearance' => 'This certifies that the bearer is a bonafide resident.',
    'business_permit' => 'Issued to operate a business within the jurisdiction.',
    'certificate_of_residency' => 'Certifies the residency of the individual.',
    'certificate_of_indigency' => 'Certifies that the bearer belongs to an indigent family.',
    'cedula' => 'Official community tax certificate issued by the barangay.',
    'solo_parents' => 'Recognition of the bearer as a Solo Parent under RA 8972.',
    'first_time_job_seekers' => 'Certifies that the bearer is a first-time job seeker under RA 11261.'
][$tab] ?? 'OFFICIAL BARANGAY DOCUMENT';

// Define verification URL base
$verification_url = 'verify.bagbageservices.gov.ph';
$qr_code_link = "https://{$verification_url}/verify?id={$id}&type={$tab}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Print Document - <?= $doc_title ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/qrcode.js/lib/qrcode.min.js"></script>
  <style>
    /* Custom Styles for Screen */
    .header-logo { height: 80px; } 
    /* CRITICAL FIX FOR SEAL: Set Max Width and Height, use object-fit: contain */
    .seal-logo { 
        height: 80px; 
        max-width: 80px; 
        object-fit: contain; /* Ensures the whole image is visible and scaled */
    }
    .signature-line { min-height: 60px; border-bottom: 2px solid #000; }
    .document-body { text-indent: 50px; text-align: justify; } 
    .print-container-screen { border: 1px solid #ddd; } 
    .print-container { position: relative; } 

    /* Print Styles Optimization */
    @media print {
      .no-print { display: none !important; }
      body { 
        margin: 0; 
        padding: 0; 
        -webkit-print-color-adjust: exact; 
        color-adjust: exact;
        color: #000; 
      }
      
      @page { 
        size: A4; 
        margin: 0.4in; /* Minimum margin */
      }
      
      .print-container { 
        width: 100%; 
        max-width: none; 
        box-shadow: none !important; 
        border: none !important;
        padding: 0; 
        overflow: hidden; 
      }
      
      .print-area-margin {
          margin-left: 0.4in !important;
          margin-right: 0.4in !important;
      }

      #qrcode.qr-print-position { 
          display: block !important; 
          position: fixed; 
          top: 0.4in; 
          right: 0.4in; 
          width: 80px; 
          height: 80px;
          z-index: 1000;
      }
      
      .no-break-inside {
          break-inside: avoid !important;
      }

      /* Font adjustment for print to ensure single page fit */
      .document-body, .text-xl, .text-lg, .leading-loose {
          font-size: 10.5pt; 
          line-height: 1.4;
      }
      
      .footer-print-text {
          font-size: 8pt;
      }
    }
  </style>
</head>
<body class="font-sans bg-gray-100 p-6">

  <div class="print-container print-container-screen bg-white p-8 max-w-5xl mx-auto shadow-xl relative">
    
    <div id="qrcode" class="qr-print-position no-print bg-white p-1"></div>

    <div class="mb-6 pb-3 border-b-4 border-double border-green-700">
      <div class="flex items-center justify-between">
        <img src="../../images/Bagbag.png" alt="Barangay Logo" class="header-logo">
        
        <div class="text-center flex-grow mx-4">
          <p class="text-sm text-gray-700">Republic of the Philippines</p>
          <p class="text-md font-semibold text-green-800">CITY OF QUEZON</p> 
          <h2 class="text-3xl font-extrabold text-green-900 leading-tight">BARANGAY BAGBAG</h2>
          <p class="text-sm text-gray-600">Office of the Punong Barangay | Contact No: (02) 123-4567</p>
        </div>

        <img src="../../images/Quezon_City.png" alt="Quezon City Seal" class="seal-logo">
      </div>
    </div>

    <div class="text-center my-8 print-area-margin no-break-inside">
      <h1 class="text-4xl font-black uppercase text-green-900 border-b-2 border-green-500 inline-block pb-1"><?= $doc_title ?></h1>
      <p class="text-sm text-gray-600 mt-3 italic font-serif leading-relaxed">
        <span class="border-b border-dashed border-gray-400 pb-0.5"><?= $doc_subtitle ?></span>
      </p>
    </div>

    <div class="my-8 text-xl text-gray-800 leading-loose print-area-margin no-break-inside">
        <p class="document-body">
            This is to certify that **<?= fullName($record['first_name'], $record['middle_name'], $record['last_name']) ?>**,
            of legal age, Filipino, and a **bonafide resident** of Barangay Bagbag, Quezon City,
            has been issued this document for official purposes.
        </p>

        <?php if ($tab === 'business_permit'): ?>
          <p class="mt-4 document-body">
            This permit is issued to **<?= htmlspecialchars(strtoupper($record['business_name'])) ?>**,
            located at *<?= htmlspecialchars($record['business_address']) ?>*, engaged in **<?= htmlspecialchars($record['business_nature']) ?>**.
          </p>
        <?php endif; ?>

        <?php if ($tab === 'solo_parents'): ?>
          <p class="mt-4 document-body">
            Recognized as a Solo Parent under **Republic Act No. 8972**. The recognized dependents are:
          </p>
          <ul class="list-none ml-16 mt-2 text-lg space-y-1">
            <?php
            // Re-open connection for child data 
            $child_conn = new mysqli("localhost", "root", "", "barangay_management_system");
            if ($child_conn->connect_error) {
                echo '<li><em class="text-sm text-red-500">Error fetching dependents.</em></li>';
            } else {
                $child_stmt = $child_conn->prepare("SELECT child_first_name, child_middle_name, child_last_name FROM solo_parent_children WHERE parent_id = ?");
                $child_stmt->bind_param("i", $id);
                $child_stmt->execute();
                $children = $child_stmt->get_result();
                
                if ($children->num_rows > 0) {
                    while ($child = $children->fetch_assoc()):
                    ?>
                        <li class="pl-4 relative before:content-['•'] before:absolute before:left-0 before:text-green-700">
                          <?= fullName($child['child_first_name'], $child['child_middle_name'], $child['child_last_name']) ?>
                        </li>
                    <?php 
                    endwhile; 
                } else {
                    echo '<li><em class="text-sm text-gray-500">No dependents listed.</em></li>';
                }
                $child_conn->close(); 
            }
            ?>
          </ul>
        <?php endif; ?>

        <p class="mt-4 document-body">
          This <?= strtolower(str_replace('_', ' ', $tab)) ?> is issued upon the request of the above-named individual 
          for **<?= htmlspecialchars($record['purpose'] ?? 'general purposes') ?>**.
        </p>

        <p class="mt-4 text-lg text-right mr-10 font-semibold">
            Issued this **<?= date('j\<\s\u\p\>S\<\/\s\u\p\>') ?>** day of **<?= date('F, Y') ?>**.
        </p>
    </div>

    <div class="mt-16 print-area-margin no-break-inside">
      <div class="flex justify-between items-end">
        
        <div class="text-left w-1/3">
          <div class="w-28 h-36 mx-0 mb-2 border-2 border-gray-400 flex items-center justify-center text-sm text-gray-500">
              2x2 ID Photo
          </div>
          <div class="signature-line w-full mx-0 mb-2"></div>
          <p class="font-semibold text-sm">Applicant's Signature/Right Thumbmark</p>
        </div>

        <div class="text-right w-1/3">
          <div class="text-center mb-4 no-print mx-auto">
              <p class="text-sm font-semibold text-green-700 mb-1">SCAN TO VERIFY</p>
              <div class="w-24 h-24 mx-auto mb-1 border border-gray-300" id="qrcode-preview"></div>
              <p class="text-xs text-gray-500 break-words"><?= $verification_url ?></p>
          </div>
          
          <div class="signature-line mb-1"></div>
          <p class="font-bold text-base uppercase text-green-900">Hon. RICHARD V. AMBITA, MPA</p>
          <p class="font-semibold text-sm text-green-800">Punong Barangay</p>
          <p class="text-xs text-gray-600">Barangay Bagbag, Quezon City</p>
        </div>
      </div>
    

      <div class="mt-8 pt-2 border-t-2 border-dashed border-gray-300 text-xs text-gray-600 text-center footer-print-text">
        <p class="font-mono">
          **CONTROL NO:** **<?= htmlspecialchars($record[$tab . '_id'] ?? $record['id']) ?>** | 
          **ISSUED:** <?= date('F j, Y', strtotime($record['application_date'])) ?> | 
          **FEE:** P<?= number_format($record['fee'] ?? 0, 2) ?>
        </p>
        <p class="mt-1 text-sm text-red-600 font-bold">
          This document is valid for three (3) months from the date of issuance.
        </p>
      </div>
    </div>
  </div>

  <div class="text-center mt-8 no-print">
    <button onclick="window.print()" class="bg-blue-600 text-white px-10 py-4 rounded-full font-semibold shadow-lg hover:bg-blue-700 transition flex items-center mx-auto text-lg">
      <i class="fas fa-print mr-3"></i> PRINT OFFICIAL DOCUMENT
    </button>
    <a href="R.view_document_details.php?tab=<?= $tab ?>&id=<?= $id ?>" class="block mt-4 text-blue-600 hover:text-blue-800 font-medium">
        <i class="fas fa-arrow-left mr-1"></i> Back to Document Details
    </a>
  </div>

  <script>
    window.onload = function () {
      const qrText = "<?= $qr_code_link ?>";
      
      // Target 1: The absolute positioned element for printing
      new QRCode(document.getElementById("qrcode"), {
        text: qrText,
        width: 80, // Size for printing
        height: 80
      });
      
      // Target 2: The preview element for on-screen viewing (no-print)
      new QRCode(document.getElementById("qrcode-preview"), {
        text: qrText,
        width: 96, // Slightly larger size for screen preview
        height: 96
      });

      // Simple Print Enhancement: Remove the screen border before printing
      window.onbeforeprint = function() {
        document.querySelector('.print-container').classList.remove('print-container-screen');
      };

      window.onafterprint = function() {
        document.querySelector('.print-container').classList.add('print-container-screen');
      };
    };
  </script>

</body>
</html>