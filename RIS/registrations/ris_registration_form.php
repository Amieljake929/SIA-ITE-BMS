<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIS Registration Form</title>
    <!-- 🔧 Fixed: Removed extra space in URL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f8f9fa; 
            padding: 40px 0; 
        }
        .form-container { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }
        .form-section { 
            margin-bottom: 25px; 
        }
        .form-section h4 { 
            margin-bottom: 15px; 
            color: #0d6efd; 
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="col-lg-10 mx-auto">
        <div class="form-container">

            <h2 class="text-center mb-4">RIS Resident Registration</h2>

            <!-- ✅ FIXED: Added action to process file -->
            <form method="POST" action="ris_registration_process.php" enctype="multipart/form-data">

                <!-- ✅ Added feedback area (if you pass message via GET or include) -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">✅ <?= htmlspecialchars($_GET['success']) ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">❌ <?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>

                <!-- Personal Information -->
                <div class="form-section">
                    <h4>Personal Information</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth *</label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Place of Birth *</label>
                            <input type="text" name="pob" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Age *</label>
                            <input type="number" name="age" class="form-control" required min="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Gender *</label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Civil Status *</label>
                            <select name="civil_status" class="form-control" required>
                                <option value="">Select</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widow/widower">Widow/Widower</option>
                                <option value="separated">Separated</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Nationality *</label>
                            <input type="text" name="nationality" class="form-control" value="Filipino" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Contact & Address -->
                <div class="form-section">
                    <h4>Contact & Address</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address *</label>
                            <textarea name="address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Resident Type *</label>
                            <select name="resident_type" class="form-control" required>
                                <option value="">Select</option>
                                <option value="permanent">Permanent</option>
                                <option value="temporary">Temporary</option>
                                <option value="voter">Voter</option>
                                <option value="non-voter">Non-Voter</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stay Length (months, optional)</label>
                            <input type="number" name="stay_length" class="form-control" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Employment Status *</label>
                            <select name="employment_status" class="form-control" required>
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
                </div>

                <!-- Identification -->
                <div class="form-section">
                    <h4>Identification</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Valid ID Type</label>
                            <input type="text" name="valid_id_type" class="form-control" placeholder="e.g., PhilID, Driver's License">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Valid ID Number</label>
                            <input type="text" name="valid_id_number" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload Valid ID (Image or PDF)</label>
                            <input type="file" name="valid_id_image" class="form-control" accept="image/*,application/pdf">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Selfie with ID</label>
                            <input type="file" name="selfie_with_id" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <!-- Demographics -->
                <div class="form-section">
                    <h4>Demographic Indicators</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_senior_citizen" value="1">
                                <label class="form-check-label">Senior Citizen</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_pwd" value="1">
                                <label class="form-check-label">Person with Disability (PWD)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_solo_parent" value="1">
                                <label class="form-check-label">Solo Parent</label>
                            </div>
                        </div>
                        <div class="col-md-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_voter" value="1">
                                <label class="form-check-label">Registered Voter</label>
                            </div>
                        </div>
                        <div class="col-md-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_student" value="1">
                                <label class="form-check-label">Student</label>
                            </div>
                        </div>
                        <div class="col-md-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_indigenous" value="1">
                                <label class="form-check-label">Indigenous People (IP)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Register Resident</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✅ Fixed: Removed extra space in JS URL -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>