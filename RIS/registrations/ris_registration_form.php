<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIS Resident Registration</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tesseract.js for OCR -->
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#00963B',
                        accent: '#4CAF50',
                        light: '#f8fafc',
                        dark: '#1e293b',
                    }
                }
            }
        }
    </script>

    <style>
        .file-upload-label {
            @apply flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition cursor-pointer;
        }
        .file-upload-label i {
            color: #4CAF50;
        }
        .form-section-title::before {
            content: "●";
            color: #00963B;
            margin-right: 8px;
            font-size: 1.2em;
        }
    </style>
</head>
<body class="bg-light min-h-screen py-10 px-4">

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-4 border-primary">

            <div class="bg-gradient-to-r from-primary to-accent py-6 px-8 text-white">
                <h1 class="text-3xl font-bold flex items-center gap-3">
                    <i class="fas fa-user-plus"></i>
                    Resident Registration Form
                </h1>
                <p class="text-white/90 mt-1">Barangay Bagbag Resident Information System</p>
            </div>

            <div class="p-8">

                <?php if (isset($_GET['success'])): ?>
                    <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-lg mb-8 flex items-start gap-3 animate-fadeIn">
                        <i class="fas fa-check-circle mt-1 text-green-600"></i>
                        <div><?= htmlspecialchars($_GET['success']) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg mb-8 flex items-start gap-3 animate-fadeIn">
                        <i class="fas fa-exclamation-triangle mt-1 text-red-600"></i>
                        <div><?= htmlspecialchars($_GET['error']) ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="ris_registration_process.php" enctype="multipart/form-data" class="space-y-10">

                    <section>
                        <h2 class="text-xl font-semibold text-dark mb-6 form-section-title">Personal Information</h2>
                        
                        <div class="grid md:grid-cols-3 gap-6">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                                <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                <input type="text" name="middle_name" value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                                <input type="date" name="dob" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Place of Birth *</label>
                                <input type="text" name="pob" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Age *</label>
                                <input type="number" name="age" required min="0"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                                <select name="gender" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Civil Status *</label>
                                <select name="civil_status" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                                    <option value="">Select</option>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="widow/widower">Widow/Widower</option>
                                    <option value="separated">Separated</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nationality *</label>
                                <input type="text" name="nationality" value="Filipino" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Religion</label>
                                <select name="religion" id="religion_select"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                                    <option value="">Select</option>
                                    <option value="Roman Catholicism">Roman Catholicism</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Evangelical Christianity">Evangelical Christianity</option>
                                    <option value="Iglesia ni Cristo (INC)">Iglesia ni Cristo (INC)</option>
                                    <option value="Aglipayan Church (Philippine Independent Church)">Aglipayan Church (Philippine Independent Church)</option>
                                    <option value="Other">Other</option>
                                </select>
                                <input type="text" name="other_religion" id="other_religion_input" placeholder="Specify other religion"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition mt-2 hidden">
                            </div>
                        </div>
                    </section>
                    
                    <section>
                         <h2 class="text-xl font-semibold text-dark mb-6 form-section-title">Contact & Address</h2>
                         <div class="grid md:grid-cols-2 gap-6">
                             <div class="md:col-span-2">
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                                 <textarea name="address" rows="3" required
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none"></textarea>
                             </div>
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                                 <input type="text" name="phone" required
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                             </div>
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Resident Type *</label>
                                 <select name="resident_type" required
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                                     <option value="">Select</option>
                                     <option value="permanent">Permanent</option>
                                     <option value="temporary">Temporary</option>
                                     <option value="voter">Voter</option>
                                     <option value="non-voter">Non-Voter</option>
                                 </select>
                             </div>
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Stay Length (months)</label>
                                 <input type="number" name="stay_length" min="0"
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                             </div>
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Employment Status *</label>
                                 <select name="employment_status" id="employment_status_select" required
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                                     <option value="">Select</option>
                                     <option value="student">Student</option>
                                     <option value="employed">Employed</option>
                                     <option value="unemployed">Unemployed</option>
                                     <option value="self-employed">Self-Employed</option>
                                     <option value="retired">Retired</option>
                                     <option value="homemaker">Homemaker</option>
                                     <option value="others">Others</option>
                                 </select>
                                 <input type="text" name="other_employment" id="other_employment_input" placeholder="Specify other employment status"
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition mt-2 hidden">
                             </div>
                         </div>
                    </section>

                    <section>
                         <h2 class="text-xl font-semibold text-dark mb-6 form-section-title">Identification</h2>
                         <div class="grid md:grid-cols-2 gap-6">
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Valid ID Type *</label>
                                 <select name="valid_id_type" id="valid_id_type" required
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                                     <option value="">Select ID Type</option>
                                     <option value="philid">PhilID</option>
                                     <option value="drivers_license">Driver's License</option>
                                     <option value="passport">Passport</option>
                                     <option value="sss">SSS ID</option>
                                     <option value="tin">TIN</option>
                                     <option value="voters_id">Voter's ID</option>
                                     <option value="prc">PRC ID</option>
                                     <option value="other">Other</option>
                                 </select>
                                 <input type="text" name="other_valid_id" id="other_valid_id_input" placeholder="Specify other ID type"
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition mt-2 hidden">
                             </div>
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Valid ID Number *</label>
                                 <input type="text" name="valid_id_number" id="valid_id_number" placeholder="Enter ID number"
                                     class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                                 <span id="id_error" class="text-red-500 text-sm mt-1 hidden">Invalid ID format</span>
                             </div>
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Upload Valid ID (Image or PDF) *</label>
                                 <label class="file-upload-label">
                                     <i class="fas fa-file-upload"></i>
                                     <span id="valid_id_text">Choose File</span>
                                     <input type="file" name="valid_id_image" id="valid_id_image" accept="image/*,application/pdf" class="hidden">
                                 </label>
                                 <span id="file_error" class="text-red-500 text-sm mt-1 hidden">Please upload your valid ID</span>
                                 <div id="ocr_status" class="text-blue-600 text-sm mt-1 hidden">
                                     <i class="fas fa-spinner fa-spin"></i> Processing OCR...
                                 </div>
                                 <div id="ocr_result" class="text-green-600 text-sm mt-1 hidden">
                                     <i class="fas fa-check-circle"></i> ID details extracted successfully
                                 </div>
                                 <div id="ocr_error" class="text-orange-600 text-sm mt-1 hidden">
                                     <i class="fas fa-exclamation-triangle"></i> OCR failed, please enter details manually
                                 </div>
                             </div>
                             <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Selfie with ID *</label>
                                 <label class="file-upload-label">
                                     <i class="fas fa-camera"></i>
                                     <span id="selfie_text">Choose File</span>
                                     <input type="file" name="selfie_with_id" id="selfie_with_id" accept="image/*" class="hidden">
                                 </label>
                                 <span id="selfie_error" class="text-red-500 text-sm mt-1 hidden">Please upload a selfie with your ID</span>
                             </div>
                         </div>
                    </section>

                    <section>
                         <h2 class="text-xl font-semibold text-dark mb-6 form-section-title">Demographic Indicators</h2>
                         <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                             <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                 <input type="checkbox" name="is_senior_citizen" value="1" class="w-5 h-5 text-primary rounded">
                                 <span class="text-gray-700">Senior Citizen</span>
                             </label>
                             <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                 <input type="checkbox" name="is_pwd" value="1" class="w-5 h-5 text-primary rounded">
                                 <span class="text-gray-700">Person with Disability</span>
                             </label>
                             <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                 <input type="checkbox" name="is_solo_parent" value="1" class="w-5 h-5 text-primary rounded">
                                 <span class="text-gray-700">Solo Parent</span>
                             </label>
                             <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                 <input type="checkbox" name="is_voter" value="1" class="w-5 h-5 text-primary rounded">
                                 <span class="text-gray-700">Registered Voter</span>
                             </label>
                             <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                 <input type="checkbox" name="is_student" value="1" class="w-5 h-5 text-primary rounded">
                                 <span class="text-gray-700">Student</span>
                             </label>
                             <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                 <input type="checkbox" name="is_indigenous" value="1" class="w-5 h-5 text-primary rounded">
                                 <span class="text-gray-700">Indigenous People</span>
                             </label>
                         </div>
                    </section>

                    <div class="text-center pt-6">
                        <button type="submit"
                            class="bg-primary hover:bg-opacity-90 text-white font-semibold py-4 px-10 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 mx-auto">
                            <i class="fas fa-save"></i>
                            Register Resident
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <p class="text-center text-gray-500 text-sm mt-8">Barangay Bagbag Resident Information System © <?= date('Y') ?></p>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>

    <script>
        document.getElementById('religion_select').addEventListener('change', function() {
            const otherInput = document.getElementById('other_religion_input');
            if (this.value === 'Other') {
                otherInput.classList.remove('hidden');
                otherInput.required = true;
            } else {
                otherInput.classList.add('hidden');
                otherInput.required = false;
                otherInput.value = '';
            }
        });

        document.getElementById('employment_status_select').addEventListener('change', function() {
            const otherInput = document.getElementById('other_employment_input');
            if (this.value === 'others') {
                otherInput.classList.remove('hidden');
                otherInput.required = true;
            } else {
                otherInput.classList.add('hidden');
                otherInput.required = false;
                otherInput.value = '';
            }
        });

        document.getElementById('valid_id_type').addEventListener('change', function() {
            const otherInput = document.getElementById('other_valid_id_input');
            if (this.value === 'other') {
                otherInput.classList.remove('hidden');
                otherInput.required = true;
            } else {
                otherInput.classList.add('hidden');
                otherInput.required = false;
                otherInput.value = '';
            }
        });

        // ID Validator Functions
        function validateIDNumber(idType, idNumber) {
            const trimmedNumber = idNumber.trim();
            switch(idType) {
                case 'philid':
                    return /^\d{12}$/.test(trimmedNumber);
                case 'drivers_license':
                    return /^[A-Z]\d{2}-\d{2}-\d{6}$/.test(trimmedNumber);
                case 'passport':
                    return /^[A-Z]{2}\d{7}$/.test(trimmedNumber);
                case 'sss':
                    return /^\d{10}$/.test(trimmedNumber);
                case 'tin':
                    return /^\d{9,12}$/.test(trimmedNumber);
                case 'voters_id':
                    return /^\d{10,12}$/.test(trimmedNumber);
                case 'prc':
                    return /^\d{7}$/.test(trimmedNumber);
                case 'other':
                    return trimmedNumber.length >= 5; // Minimum 5 characters for other IDs
                default:
                    return false;
            }
        }

        function getIDFormatHint(idType) {
            switch(idType) {
                case 'philid':
                    return 'Format: 12 digits (e.g., 123456789012)';
                case 'drivers_license':
                    return 'Format: A01-12-345678';
                case 'passport':
                    return 'Format: AA1234567';
                case 'sss':
                    return 'Format: 10 digits';
                case 'tin':
                    return 'Format: 9-12 digits';
                case 'voters_id':
                    return 'Format: 10-12 digits';
                case 'prc':
                    return 'Format: 7 digits';
                case 'other':
                    return 'Enter valid ID number (minimum 5 characters)';
                default:
                    return '';
            }
        }

        // Event listeners for ID validation
        document.getElementById('valid_id_type').addEventListener('change', function() {
            const idNumberInput = document.getElementById('valid_id_number');
            const idError = document.getElementById('id_error');
            const selectedType = this.value;

            if (selectedType) {
                idNumberInput.required = true;
                idNumberInput.placeholder = getIDFormatHint(selectedType);
                // Validate current value if present
                if (idNumberInput.value.trim()) {
                    const isValid = validateIDNumber(selectedType, idNumberInput.value);
                    if (!isValid) {
                        idError.textContent = `Invalid ${this.options[this.selectedIndex].text} format`;
                        idError.classList.remove('hidden');
                    } else {
                        idError.classList.add('hidden');
                    }
                } else {
                    idError.classList.add('hidden');
                }
            } else {
                idNumberInput.required = false;
                idNumberInput.placeholder = 'Enter ID number';
                idError.classList.add('hidden');
            }
        });

        document.getElementById('valid_id_number').addEventListener('input', function() {
            const idTypeSelect = document.getElementById('valid_id_type');
            const idError = document.getElementById('id_error');
            const selectedType = idTypeSelect.value;

            if (selectedType && this.value.trim()) {
                const isValid = validateIDNumber(selectedType, this.value);
                if (!isValid) {
                    idError.textContent = `Invalid ${idTypeSelect.options[idTypeSelect.selectedIndex].text} format`;
                    idError.classList.remove('hidden');
                } else {
                    idError.classList.add('hidden');
                }
            } else {
                idError.classList.add('hidden');
            }
        });

        // OCR Processing Function
        async function processOCR(file) {
            const ocrStatus = document.getElementById('ocr_status');
            const ocrResult = document.getElementById('ocr_result');
            const ocrError = document.getElementById('ocr_error');

            // Hide all status messages
            ocrStatus.classList.add('hidden');
            ocrResult.classList.add('hidden');
            ocrError.classList.add('hidden');

            // Show processing status
            ocrStatus.classList.remove('hidden');

            try {
                const { data: { text } } = await Tesseract.recognize(file, 'eng', {
                    logger: m => console.log(m)
                });

                console.log('OCR Text:', text);

                // Extract ID details from OCR text
                const extractedData = extractIDDetails(text);

                if (extractedData.idType && extractedData.idNumber) {
                    // Auto-fill the form fields
                    const idTypeSelect = document.getElementById('valid_id_type');
                    const idNumberInput = document.getElementById('valid_id_number');

                    // Set ID type
                    idTypeSelect.value = extractedData.idType;

                    // Trigger change event to update validation
                    idTypeSelect.dispatchEvent(new Event('change'));

                    // Set ID number
                    idNumberInput.value = extractedData.idNumber;

                    // Trigger input event to validate
                    idNumberInput.dispatchEvent(new Event('input'));

                    // Show success message
                    ocrResult.classList.remove('hidden');
                } else {
                    // Show error message
                    ocrError.classList.remove('hidden');

                    // Automatically set ID type to "other" when OCR fails
                    const idTypeSelect = document.getElementById('valid_id_type');
                    idTypeSelect.value = 'other';
                    idTypeSelect.dispatchEvent(new Event('change'));
                }

            } catch (error) {
                console.error('OCR Error:', error);
                ocrError.classList.remove('hidden');
            } finally {
                // Hide processing status
                ocrStatus.classList.add('hidden');
            }
        }

        // Function to extract ID details from OCR text
        function extractIDDetails(text) {
            const upperText = text.toUpperCase();

            let idType = '';
            let idNumber = '';

            // Check for PhilID (12 digits)
            const philidMatch = text.match(/\b(\d{12})\b/);
            if (philidMatch && philidMatch[1]) {
                idType = 'philid';
                idNumber = philidMatch[1];
            }

            // Check for Driver's License (A01-12-345678 format)
            const dlMatch = text.match(/\b([A-Z]\d{2}-\d{2}-\d{6})\b/);
            if (dlMatch && dlMatch[1]) {
                idType = 'drivers_license';
                idNumber = dlMatch[1];
            }

            // Check for Passport (AA1234567 format)
            const passportMatch = text.match(/\b([A-Z]{2}\d{7})\b/);
            if (passportMatch && passportMatch[1]) {
                idType = 'passport';
                idNumber = passportMatch[1];
            }

            // Check for SSS ID (10 digits)
            const sssMatch = text.match(/\b(\d{10})\b/);
            if (sssMatch && sssMatch[1] && !idNumber) {
                idType = 'sss';
                idNumber = sssMatch[1];
            }

            // Check for TIN (9-12 digits)
            const tinMatch = text.match(/\b(\d{9,12})\b/);
            if (tinMatch && tinMatch[1] && !idNumber) {
                idType = 'tin';
                idNumber = tinMatch[1];
            }

            // Check for Voter's ID (10-12 digits)
            const voterMatch = text.match(/\b(\d{10,12})\b/);
            if (voterMatch && voterMatch[1] && !idNumber) {
                idType = 'voters_id';
                idNumber = voterMatch[1];
            }

            // Check for PRC ID (7 digits)
            const prcMatch = text.match(/\b(\d{7})\b/);
            if (prcMatch && prcMatch[1] && !idNumber) {
                idType = 'prc';
                idNumber = prcMatch[1];
            }

            return { idType, idNumber };
        }

        // File upload event listeners
        document.getElementById('valid_id_image').addEventListener('change', function() {
            const fileText = document.getElementById('valid_id_text');
            const fileError = document.getElementById('file_error');
            if (this.files.length > 0) {
                fileText.textContent = this.files[0].name;
                fileError.classList.add('hidden');

                // Process OCR if it's an image file
                const file = this.files[0];
                if (file.type.startsWith('image/')) {
                    processOCR(file);
                }
            } else {
                fileText.textContent = 'Choose File';
                fileError.classList.remove('hidden');
            }
        });

        document.getElementById('selfie_with_id').addEventListener('change', function() {
            const selfieText = document.getElementById('selfie_text');
            const selfieError = document.getElementById('selfie_error');
            if (this.files.length > 0) {
                selfieText.textContent = this.files[0].name;
                selfieError.classList.add('hidden');
            } else {
                selfieText.textContent = 'Choose File';
                selfieError.classList.remove('hidden');
            }
        });

        // Form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const idTypeSelect = document.getElementById('valid_id_type');
            const idNumberInput = document.getElementById('valid_id_number');
            const idError = document.getElementById('id_error');
            const validIdFile = document.getElementById('valid_id_image');
            const selfieFile = document.getElementById('selfie_with_id');
            const fileError = document.getElementById('file_error');
            const selfieError = document.getElementById('selfie_error');

            let hasError = false;

            // Validate ID number
            if (idTypeSelect.value && idNumberInput.value.trim()) {
                const isValid = validateIDNumber(idTypeSelect.value, idNumberInput.value);
                if (!isValid) {
                    e.preventDefault();
                    idError.textContent = `Invalid ${idTypeSelect.options[idTypeSelect.selectedIndex].text} format. Please check and try again.`;
                    idError.classList.remove('hidden');
                    idNumberInput.focus();
                    hasError = true;
                }
            }

            // Validate file uploads
            if (!validIdFile.files.length) {
                e.preventDefault();
                fileError.textContent = 'Please upload your valid ID';
                fileError.classList.remove('hidden');
                hasError = true;
            }

            if (!selfieFile.files.length) {
                e.preventDefault();
                selfieError.textContent = 'Please upload a selfie with your ID';
                selfieError.classList.remove('hidden');
                hasError = true;
            }

            if (hasError) {
                return false;
            }
        });
    </script>

</body>
</html>
