<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIS Resident Registration</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        #captcha_image { border: 1px solid #d1d5db; border-radius: 8px; }
        #refreshCaptchaBtn { color: #00963B; cursor: pointer; }
        #refreshCaptchaBtn:hover { color: #4CAF50; }
    </style>

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
        /* Tinanggal ko yung .file-upload-label, pinalitan ng built-in tailwind classes */
        .form-section-title::before {
            content: "●";
            color: #00963B;
            margin-right: 8px;
            font-size: 1.2em;
        }
        /* Tinanggal ko rin yung .image-preview dito, 
           nilagay ko na directly sa <img> tag gamit ang Tailwind */
    </style>
</head>
<body class="bg-light min-h-screen py-10 px-4">

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

            <div class="relative h-56 sm:h-64 bg-gray-300"> 
                <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1740&q=80" 
                     class="absolute inset-0 w-full h-full object-cover" alt="Team working">
                
                <div class="absolute inset-0 bg-primary/70 bg-gradient-to-r from-primary/80 to-accent/70"></div>

                <div class="absolute top-4 left-4 z-10">
                    <a href="/SIA-ITE-BMS/BMS/login/website2.php" class="text-white hover:text-green-100 text-sm font-medium flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Website
                    </a>
                </div>

                <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-white text-center">
                    <h1 class="text-3xl sm:text-4xl font-bold">
                        Resident Registration
                    </h1>
                    <p class="text-white/90 text-lg mt-2">Please fill in the form below to register.</p>
                    <p class="text-white/70 mt-1 text-sm">Barangay Bagbag Resident Information System</p>
                </div>
            </div>
            <div class="p-6 sm:p-8">

                <form id="registrationForm" enctype="multipart/form-data" class="space-y-10">

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
                                 <label class="block text-sm font-medium text-gray-700 mb-2">Stay Length</label>
                                 <select name="stay_length"
                                         class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                                     <option value="">Select Stay Length</option>
                                     <option value="Less than 6 months">Less than 6 months</option>
                                     <option value="6 months - 1 year">6 months - 1 year</option>
                                     <option value="1 - 3 years">1 - 3 years</option>
                                     <option value="3 - 5 years">3 - 5 years</option>
                                     <option value="More than 5 years">More than 5 years</option>
                                 </select>
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Valid ID (JPG, PNG) *</label>

                                <div class="mt-1 h-48 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg relative bg-gray-50">

                                    <img id="valid_id_preview" src="#" alt="Valid ID Preview"
                                         class="absolute inset-0 w-full h-full object-contain rounded-lg p-1 bg-white hidden cursor-pointer"/>

                                    <button id="valid_id_change_btn" type="button" class="absolute top-2 right-2 bg-primary text-white px-3 py-1 rounded-md text-sm hidden hover:bg-accent transition-colors z-10">
                                        Change
                                    </button>

                                    <div id="valid_id_prompt" class="space-y-1 text-center flex flex-col justify-center items-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8a4 4 0 01-4 4H28m0-28v8a4 4 0 004 4h8m-12 12l-4-4m0 0l-4 4m4-4v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="valid_id_image" class="relative cursor-pointer bg-transparent rounded-md font-medium text-primary hover:text-accent focus-within:outline-none">
                                                <span>Upload a file</span>
                                                <input type="file" name="valid_id_image" id="valid_id_image" accept="image/*" class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500" id="valid_id_text">JPG, PNG</p>
                                    </div>

                                </div>
                                <span id="file_error" class="text-red-500 text-sm mt-1 hidden">Please upload your valid ID</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Selfie with ID (JPG, PNG) *</label>

                                <div class="mt-1 h-48 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg relative bg-gray-50">

                                    <img id="selfie_preview" src="#" alt="Selfie Preview"
                                         class="absolute inset-0 w-full h-full object-contain rounded-lg p-1 bg-white hidden cursor-pointer"/>

                                    <button id="selfie_change_btn" type="button" class="absolute top-2 right-2 bg-primary text-white px-3 py-1 rounded-md text-sm hidden hover:bg-accent transition-colors z-10">
                                        Change
                                    </button>

                                    <div id="selfie_prompt" class="space-y-1 text-center flex flex-col justify-center items-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8a4 4 0 01-4 4H28m0-28v8a4 4 0 004 4h8m-12 12l-4-4m0 0l-4 4m4-4v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="selfie_with_id" class="relative cursor-pointer bg-transparent rounded-md font-medium text-primary hover:text-accent focus-within:outline-none">
                                                <span>Upload a file</span>
                                                <input type="file" name="selfie_with_id" id="selfie_with_id" accept="image/*" class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500" id="selfie_text">JPG, PNG</p>
                                    </div>

                                </div>
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

                    <section>
                        <h2 class="text-xl font-semibold text-dark mb-6 form-section-title">Verification</h2>
                        <div class="grid md:grid-cols-2 gap-6 items-center">
                            <div>
                                <p class="text-sm text-gray-600 mb-2">Type the characters you see:</p>
                                <div class="flex items-center gap-4">
                                    <img src="captcha_image.php" alt="CAPTCHA Image" id="captcha_image" class="h-20">
                                    <button type="button" id="refreshCaptchaBtn" class="text-xl p-2" title="Refresh CAPTCHA">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="captcha_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-keyboard text-gray-500 mr-1"></i> Enter CAPTCHA Code *
                                </label>
                                <input type="text" id="captcha_code" name="captcha_code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition tracking-widest text-lg font-mono" placeholder="Enter code" required autocomplete="off">
                            </div>
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

    <div id="successModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm text-center mx-4 modal-animate">
            <svg class="mx-auto mb-4 w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
            <h2 class="text-2xl font-semibold mb-2">Registration Successful!</h2>
            <p class="text-gray-700 mb-4">Your application is pending approval. You will be redirected shortly.</p>
            <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-12 w-12 mx-auto"></div>
        </div>
    </div>

    <div id="errorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
        <div class="bg-white rounded-lg shadow-lg p-6 sm:p-8 max-w-sm text-center mx-4 modal-animate">
            <div class="mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
            </div>
            <h2 class="text-xl sm:text-2xl font-semibold mb-2 text-gray-800">Registration Failed</h2>
            <p id="errorModalMessage" class="text-gray-600 mb-6"></p>
            <button id="closeErrorModalBtn" class="bg-red-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-700 transition-colors w-full">Try Again</button>
        </div>
    </div>

    <style>
        /* Modal and Loader styles (No changes) */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }
        .modal-animate {
            animation: fade-in 0.3s ease-out forwards;
        }
        @keyframes fade-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .loader {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3a9d6a;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <script>
        // Other fields JS (No changes)
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

        // ID Validator Functions (No changes)
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
                    return trimmedNumber.length >= 5;
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

        // Event listeners for ID validation (No changes)
        document.getElementById('valid_id_type').addEventListener('change', function() {
            const idNumberInput = document.getElementById('valid_id_number');
            const idError = document.getElementById('id_error');
            const selectedType = this.value;

            if (selectedType) {
                idNumberInput.required = true;
                idNumberInput.placeholder = getIDFormatHint(selectedType);
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


        // ========== START CHANGE 4: UPDATED JS FOR VALID ID PREVIEW ==========
        // In-update ko 'to para i-hide/i-show 'yung upload prompt
        function handleValidIdFileChange() {
            const fileText = document.getElementById('valid_id_text');
            const fileError = document.getElementById('file_error');
            const preview = document.getElementById('valid_id_preview');
            const prompt = document.getElementById('valid_id_prompt');
            const changeBtn = document.getElementById('valid_id_change_btn');

            if (this.files.length > 0) {
                fileText.textContent = this.files[0].name; // Papalitan 'yung 'JPG, PNG' text ng filename
                fileError.classList.add('hidden');

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    changeBtn.classList.remove('hidden');
                    prompt.classList.add('hidden'); // <-- Itatago 'yung upload prompt
                }
                reader.readAsDataURL(this.files[0]);

            } else {
                fileText.textContent = 'JPG, PNG'; // Ibabalik sa default text
                preview.classList.add('hidden'); // Itatago 'yung preview
                changeBtn.classList.add('hidden');
                prompt.classList.remove('hidden'); // Ipapakita ulit 'yung prompt
                preview.src = '#';
            }
        }

        document.getElementById('valid_id_image').addEventListener('change', handleValidIdFileChange);

        // Handle change button and preview click
        document.getElementById('valid_id_change_btn').addEventListener('click', function() {
            document.getElementById('valid_id_image').click();
        });

        document.getElementById('valid_id_preview').addEventListener('click', function() {
            document.getElementById('valid_id_image').click();
        });
        // ========== END CHANGE 4 ==========


        // ========== START CHANGE 5: UPDATED JS FOR SELFIE PREVIEW ==========
        // In-update ko rin 'to, same logic ng sa taas
        function handleSelfieFileChange() {
            const selfieText = document.getElementById('selfie_text');
            const selfieError = document.getElementById('selfie_error');
            const preview = document.getElementById('selfie_preview');
            const prompt = document.getElementById('selfie_prompt');
            const changeBtn = document.getElementById('selfie_change_btn');

            if (this.files.length > 0) {
                selfieText.textContent = this.files[0].name;
                selfieError.classList.add('hidden');

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    changeBtn.classList.remove('hidden');
                    prompt.classList.add('hidden'); // <-- Itatago 'yung upload prompt
                }
                reader.readAsDataURL(this.files[0]);

            } else {
                selfieText.textContent = 'JPG, PNG'; // Ibabalik sa default text
                preview.classList.add('hidden'); // Itatago 'yung preview
                changeBtn.classList.add('hidden');
                prompt.classList.remove('hidden'); // Ipapakita ulit 'yung prompt
                preview.src = '#';
            }
        }

        document.getElementById('selfie_with_id').addEventListener('change', handleSelfieFileChange);

        // Handle change button and preview click
        document.getElementById('selfie_change_btn').addEventListener('click', function() {
            document.getElementById('selfie_with_id').click();
        });

        document.getElementById('selfie_preview').addEventListener('click', function() {
            document.getElementById('selfie_with_id').click();
        });
        // ========== END CHANGE 5 ==========

        // ========== CAPTCHA REFRESH (No changes) ==========
        document.getElementById('refreshCaptchaBtn').addEventListener('click', function() {
            const captchaImage = document.getElementById('captcha_image');
            captchaImage.src = 'captcha_image.php?' + new Date().getTime();
        });

        // Form submission validation and AJAX submission (No changes)
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault(); 

            const idTypeSelect = document.getElementById('valid_id_type');
            const idNumberInput = document.getElementById('valid_id_number');
            const idError = document.getElementById('id_error');
            const validIdFile = document.getElementById('valid_id_image');
            const selfieFile = document.getElementById('selfie_with_id');
            const fileError = document.getElementById('file_error');
            const selfieError = document.getElementById('selfie_error');
            const submitBtn = document.querySelector('button[type="submit"]');
            const successModal = document.getElementById('successModal');
            const errorModal = document.getElementById('errorModal');
            const errorModalMessage = document.getElementById('errorModalMessage');
            const closeErrorModalBtn = document.getElementById('closeErrorModalBtn');

            let hasError = false;

            if (idTypeSelect.value && idNumberInput.value.trim()) {
                const isValid = validateIDNumber(idTypeSelect.value, idNumberInput.value);
                if (!isValid) {
                    idError.textContent = `Invalid ${idTypeSelect.options[idTypeSelect.selectedIndex].text} format. Please check and try again.`;
                    idError.classList.remove('hidden');
                    idNumberInput.focus();
                    hasError = true;
                }
            }

            if (!validIdFile.files.length) {
                fileError.textContent = 'Please upload your valid ID';
                fileError.classList.remove('hidden');
                hasError = true;
            }

            if (!selfieFile.files.length) {
                selfieError.textContent = 'Please upload a selfie with your ID';
                selfieError.classList.remove('hidden');
                hasError = true;
            }

            if (hasError) {
                return false;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loader" style="border: 2px solid #f3f3f3; border-top: 2px solid #ffffff; width: 20px; height: 20px; margin-right: 8px; display: inline-block;"></span> Processing...';

            const formData = new FormData(this);

            fetch('ris_registration_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successModal.classList.remove('hidden');
                    setTimeout(() => {
                        window.location.href = '/SIA-ITE-BMS/BMS/login/website2.php';
                    }, 2500);
                } else {
                    errorModalMessage.textContent = data.message;
                    errorModal.classList.remove('hidden');
                    if (data.message.toLowerCase().includes('captcha')) {
                        document.getElementById('refreshCaptchaBtn').click();
                        document.getElementById('captcha_code').value = '';
                    }
                }
            })
            .catch(() => {
                errorModalMessage.textContent = "An unexpected error occurred. Please try again.";
                errorModal.classList.remove('hidden');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Register Resident';
            });

            closeErrorModalBtn.addEventListener('click', () => {
                errorModal.classList.add('hidden');
            });
        });
    </script>

</body>
</html>