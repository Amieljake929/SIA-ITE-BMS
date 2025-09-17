<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIS Resident Registration</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom Tailwind Config for Green Theme -->
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
        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-4 border-primary">

            <!-- Header -->
            <div class="bg-gradient-to-r from-primary to-accent py-6 px-8 text-white">
                <h1 class="text-3xl font-bold flex items-center gap-3">
                    <i class="fas fa-user-plus"></i>
                    Resident Registration Form
                </h1>
                <p class="text-white/90 mt-1">Barangay Bagbag Resident Information System</p>
            </div>

            <!-- Body -->
            <div class="p-8">

                <!-- Alerts -->
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

                <!-- Form -->
                <form method="POST" action="ris_registration_process.php" enctype="multipart/form-data" class="space-y-10">

                    <!-- Personal Information -->
                    <section>
                        <h2 class="text-xl font-semibold text-dark mb-6 form-section-title">Personal Information</h2>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required
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
                                <input type="text" name="religion"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                        </div>
                    </section>

                    <!-- Contact & Address -->
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
                                <select name="employment_status" required
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
                            </div>
                        </div>
                    </section>

                    <!-- Identification -->
                    <section>
                        <h2 class="text-xl font-semibold text-dark mb-6 form-section-title">Identification</h2>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Valid ID Type</label>
                                <input type="text" name="valid_id_type" placeholder="e.g., PhilID, Driver's License"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Valid ID Number</label>
                                <input type="text" name="valid_id_number"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Valid ID (Image or PDF)</label>
                                <label class="file-upload-label">
                                    <i class="fas fa-file-upload"></i>
                                    <span>Choose File</span>
                                    <input type="file" name="valid_id_image" accept="image/*,application/pdf" class="hidden">
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Selfie with ID</label>
                                <label class="file-upload-label">
                                    <i class="fas fa-camera"></i>
                                    <span>Choose File</span>
                                    <input type="file" name="selfie_with_id" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>
                    </section>

                    <!-- Demographics -->
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

                    <!-- Submit Button -->
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

        <!-- Footer Note -->
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

</body>
</html>