<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barangay Bagbag Community Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        /* */
        input:focus,
        textarea:focus {
            border-color: #10B981; /* green-500 */
            box-shadow: 0 0 0 2px #A7F3D0; /* green-200 */
            outline: none;
        }
        
        /* */
        .facility-card {
            transition: all 0.3s ease-in-out;
            border-radius: 1rem; /* Mas rounded corners */
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .facility-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px -4px rgba(22, 163, 74, 0.15); /* Mas modern na shadow */
        }
        
        /* */
        .service-card {
             transition: all 0.3s ease-in-out;
        }
        .service-card:hover {
            transform: translateY(-6px);
            background-color: white;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.07);
        }

        /* --- Marquee Animation (No Change) --- */
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-marquee {
            animation: marquee 25s linear infinite;
            display: inline-block;
            min-width: 100%;
        }

        /* --- Scroll-reveal animation (No Change) --- */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .fade-in.appear {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-white text-gray-700"> <nav id="navbar" class="bg-green-600 text-white shadow-md sticky top-0 z-50 transition-all duration-300">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <img src="../images/Bagbag.png" alt="Barangay Bagbag Seal" class="h-12 w-12 rounded-full object-cover" />
                    <div>
                        <h1 class="text-xl font-bold text-white">Barangay Bagbag</h1>
                        <p class="text-xs text-green-100 opacity-90">Serving Novaliches with Pride</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-6">
                    <a href="#home" class="text-green-100 hover:text-white transition font-medium">Home</a>
                    <a href="#services" class="text-green-100 hover:text-white transition font-medium">Services</a>
                    <a href="#facilities" class="text-green-100 hover:text-white transition font-medium">Facilities</a>
                    <a href="#about" class="text-green-100 hover:text-white transition font-medium">About Us</a>
                    <a href="#contact" class="text-green-100 hover:text-white transition font-medium">Contact</a>
                    
                    <a href="../../RIS/registrations/ris_registration_form.php" 
                       class="bg-white text-green-700 px-5 py-2.5 rounded-full font-semibold hover:bg-gray-100 transition flex items-center shadow-md hover:shadow-lg">
                        Resident Registration
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <button id="mobile-menu-button" class="md:hidden focus:outline-none text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-green-800 shadow-lg border-t border-green-700 px-6 py-4 space-y-3">
            <a href="#home" class="block text-green-100 hover:text-white font-medium">Home</a>
            <a href="#services" class="block text-green-100 hover:text-white font-medium">Services</a>
            <a href="#facilities" class="block text-green-100 hover:text-white font-medium">Facilities</a>
            <a href="#about" class="block text-green-100 hover:text-white font-medium">About Us</a>
            <a href="#contact" class="block text-green-100 hover:text-white font-medium">Contact</a>
            <a href="../../RIS/registrations/ris_registration_form.php" class="block bg-white text-green-700 px-4 py-2.5 rounded-full text-center font-medium mt-2">Resident Registration</a>
        </div>
    </nav>

    <div class="bg-green-50 border-b border-green-200 text-green-800 text-sm font-medium py-2.5 overflow-hidden whitespace-nowrap">
        <div class="animate-marquee px-4">
            📢 Welcome to Barangay Bagbag! | Community Updates • Barangay Services Available • Events & Announcements
        </div>
    </div>

    <section id="home" class="relative">
        <video src="../images/bagbeg.mp4" autoplay muted loop playsinline class="w-full h-[500px] md:h-[650px] object-cover"></video>
        <div class="absolute inset-0 bg-black/50 flex items-center justify-center text-center px-4">
            <div class="text-white max-w-3xl">
                <h1 class="text-4xl md:text-6xl font-bold mb-4 animate__animated animate__fadeInUp">
                    Welcome to Barangay Bagbag
                </h1>
                <p class="text-lg md:text-xl mb-8 opacity-90 animate__animated animate__fadeInUp animate__delay-1s">
                    A peaceful and progressive community dedicated to serving our residents with care and compassion.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="#services" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-full font-semibold transition shadow-lg text-lg">
                        View Services
                    </a>
                    <a href="login.php" class="bg-white/90 hover:bg-white text-green-800 px-8 py-3 rounded-full font-semibold transition flex items-center justify-center shadow-lg text-lg">
                        Brgy. System Access
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-3xl md:text-4xl font-bold text-green-800 mb-3">Barangay Services</h2>
                <div class="w-24 h-1.5 bg-green-500 mx-auto rounded-full"></div>
                <p class="text-gray-600 max-w-2xl mx-auto mt-5 text-lg">Access key services online. Quick, easy, and convenient.</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="#" class="service-card text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 fade-in">
                    <div class="bg-green-100 text-green-600 h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Request Document</h3>
                    <p class="text-gray-500 mt-1">Brgy. Clearance, Indigency, etc.</p>
                </a>
                
                <a href="#" class="service-card text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 fade-in" style="animation-delay: 100ms;">
                    <div class="bg-green-100 text-green-600 h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6M7 8h6" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">File a Complaint</h3>
                    <p class="text-gray-500 mt-1">Submit community reports or blotters.</p>
                </a>

                <a href="#" class="service-card text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 fade-in" style="animation-delay: 200ms;">
                    <div class="bg-green-100 text-green-600 h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.144-6.363m5.561-1.443l1.13-3.377a1.76 1.76 0 013.417.592l-2.145 6.363m-3.41-1.442l.01.03m-.01-.03l-2.229 6.687m2.23-6.687l2.229 6.687m0 0l2.229-6.687m-2.229 6.687l-2.229-6.687" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Announcements</h3>
                    <p class="text-gray-500 mt-1">View news and updates.</p>
                </a>

                <a href="#" class="service-card text-center bg-white p-8 rounded-2xl shadow-sm border border-gray-100 fade-in" style="animation-delay: 300ms;">
                    <div class="bg-green-100 text-green-600 h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Hotlines</h3>
                    <p class="text-gray-500 mt-1">Emergency directory.</p>
                </a>
            </div>
        </div>
    </section>

    <section id="facilities" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-3xl md:text-4xl font-bold text-green-800 mb-3">Our Community Facilities</h2>
                <div class="w-24 h-1.5 bg-green-500 mx-auto rounded-full"></div>
                <p class="text-gray-600 max-w-2xl mx-auto mt-5 text-lg">Well-maintained spaces to support health, recreation, and unity.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="facility-card bg-white fade-in">
                    <img src="../images/health-center.jpg" alt="Barangay Health Center" class="w-full h-52 object-cover" />
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-green-700 mb-2">Health Center</h3>
                        <p class="text-gray-600 mb-4">Medical consultations, immunizations, maternal care, and emergency response.</p>
                        <div class="flex items-center text-sm text-green-600 font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span>Near Barangay Hall</span>
                        </div>
                    </div>
                </div>

                <div class="facility-card bg-white fade-in"><img src="../images/multi-purpose.png" alt="Multi-Purpose Court" class="w-full h-52 object-cover" /><div class="p-6"><h3 class="text-xl font-bold text-green-700 mb-2">Multi-Purpose Court</h3><p class="text-gray-600 mb-4">For sports, events, and community gatherings.</p><div class="flex items-center text-sm text-green-600 font-medium"><svg class="h-5 w-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/></svg><span>Central Area</span></div></div></div>
                <div class="facility-card bg-white fade-in"><img src="../images/ggg.jpg" alt="Barangay Hall" class="w-full h-52 object-cover" /><div class="p-6"><h3 class="text-xl font-bold text-green-700 mb-2">Barangay Hall</h3><p class="text-gray-600 mb-4">Process documents, attend meetings, and access assistance.</p><div class="flex items-center text-sm text-green-600 font-medium"><svg class="h-5 w-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/></svg><span>Main Road</span></div></div></div>
                <div class="facility-card bg-white fade-in"><img src="../images/playground.png" alt="Children's Playground" class="w-full h-52 object-cover" /><div class="p-6"><h3 class="text-xl font-bold text-green-700 mb-2">Children's Playground</h3><p class="text-gray-600 mb-4">Safe, fun, and supervised play area for kids.</p><div class="flex items-center text-sm text-green-600 font-medium"><svg class="h-5 w-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/></svg><span>Next to Court</span></div></div></div>
                <div class="facility-card bg-white fade-in"><img src="../images/evacuation.jpg" alt="Evacuation Center" class="w-full h-52 object-cover" /><div class="p-6"><h3 class="text-xl font-bold text-green-700 mb-2">Evacuation Center</h3><p class="text-gray-600 mb-4">Equipped shelter for emergencies and disasters.</p><div class="flex items-center text-sm text-green-600 font-medium"><svg class="h-5 w-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5.05 4.05a1 1 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/></svg><span>Beside Hall</span></div></div></div>
                <div class="facility-card bg-white fade-in"><img src="../images/communitypark.png" alt="Community Park" class="w-full h-52 object-cover" /><div class="p-6"><h3 class="text-xl font-bold text-green-700 mb-2">Community Park</h3><p class="text-gray-600 mb-4">Green space for walking, relaxing, and family time.</p><div class="flex items-center text-sm text-green-600 font-medium"><svg class="h-5 w-5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/></svg><span>Northside</span></div></div></div>
            </div>
        </div>
    </section>

    <section id="about" class="py-20 bg-gray-50"> <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2 fade-in">
                    <img src="../images/healthy.jpg" alt="Barangay Officials and Residents" class="rounded-2xl shadow-lg w-full" />
                </div>
                <div class="lg:w-1/2 fade-in">
                    <h2 class="text-3xl md:text-4xl font-bold text-green-800 mb-4">About Barangay Bagbag</h2>
                    <div class="w-20 h-1.5 bg-green-500 mb-6 rounded-full"></div>
                    <p class="text-gray-700 mb-4 text-lg">We are committed to fostering a safe, healthy, and progressive community where every resident can thrive.</p>
                    <p class="text-gray-700 mb-8">Through transparent governance and active citizen participation, we deliver accessible services and sustainable development.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-xl mr-4">
                                <svg class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div><h4 class="font-semibold text-lg text-green-800">Health Services</h4><p class="text-gray-600">Checkups & immunizations</p></div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-xl mr-4">
                                <svg class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            </div>
                            <div><h4 class="font-semibold text-lg text-green-800">Education</h4><p class="text-gray-600">Scholarships & programs</p></div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-xl mr-4">
                                <svg class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            </div>
                            <div><h4 class="font-semibold text-lg text-green-800">Safety</h4><p class="text-gray-600">24/7 patrol & response</p></div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-xl mr-4">
                                <svg class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div><h4 class="font-semibold text-lg text-green-800">Livelihood</h4><p class="text-gray-600">Skills training & support</p></div>
                        </div>
                    </div>
                    <a href="#" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-7 py-3 rounded-full font-medium transition shadow-md hover:shadow-lg">
                        Learn More
                        <svg class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-20 bg-white"> <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-3xl md:text-4xl font-bold text-green-800 mb-3">Contact Us</h2>
                <div class="w-20 h-1.5 bg-green-500 mx-auto rounded-full"></div>
                <p class="text-gray-600 max-w-2xl mx-auto mt-5 text-lg">We’re here to assist you. Reach out anytime!</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">
                <div class="bg-gray-50 border border-gray-100 p-8 rounded-2xl shadow-sm fade-in">
                    <h3 class="text-2xl font-bold text-green-800 mb-6">Send a Message</h3>
                    <form>
                        <div class="mb-5">
                            <label class="block text-gray-700 mb-2 font-medium">Full Name</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:bg-white" placeholder="Juan Dela Cruz" />
                        </div>
                        <div class="mb-5">
                            <label class="block text-gray-700 mb-2 font-medium">Email</label>
                            <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:bg-white" placeholder="juan@example.com" />
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 mb-2 font-medium">Message</label>
                            <textarea rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:bg-white" placeholder="How can we help?"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3.5 rounded-lg font-semibold transition shadow-md hover:shadow-lg text-lg">
                            Send Message
                        </button>
                    </form>
                </div>

                <div class="fade-in">
                    <div class="bg-gray-50 border border-gray-100 p-8 rounded-2xl shadow-sm h-full">
                        <h3 class="text-2xl font-bold text-green-800 mb-6">Barangay Information</h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="bg-green-100 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg text-green-800">Address</h4>
                                    <p class="text-gray-600">625 Pagkabuhay Road, Brgy. Bagbag, Novaliches, Quezon City</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-green-100 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg text-green-800">Contact</h4>
                                    <p class="text-gray-600"> 89527011 (Direct Line)</p>
                                    <p class="text-gray-600"> 87787783 (Trunk Line)</p>
                                    <p class="text-gray-600">0998 3333 463 (Mobile)</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-green-100 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg text-green-800">Email</h4>
                                    <p class="text-gray-600">barangaybagbagmanagementsystem@gmail.com</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-green-100 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg text-green-800">Office Hours</h4>
                                    <p class="text-gray-600">Mon–Fri: 8:00 AM – 5:00 PM</p>
                                    <p class="text-gray-600">Sat: 8:00 AM – 12:00 NN</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl overflow-hidden h-96 fade-in">
                <img src="../images/location.png" alt="Barangay Bagbag Location Map" class="w-full h-full object-cover" />
            </div>
        </div>
    </section>

    <footer class="bg-gray-800 text-gray-300 pt-16 pb-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-10">
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">Barangay Bagbag</h3>
                    <p class="text-gray-400 leading-relaxed">Serving our community with integrity, transparency, and compassion since 1990.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-400 hover:text-white transition">Home</a></li>
                        <li><a href="#services" class="text-gray-400 hover:text-white transition">Services</a></li>
                        <li><a href="#facilities" class="text-gray-400 hover:text-white transition">Facilities</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition">About Us</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Services</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Health Programs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Document Processing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Community Events</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Complaint Desk</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-4">Connect With Us</h3>
                    <div class="flex space-x-3 mb-5">
                        <a href="#" class="bg-gray-700 hover:bg-green-600 text-white p-2.5 rounded-full transition">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="bg-gray-700 hover:bg-green-600 text-white p-2.5 rounded-full transition">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                    <a href="../login-system/login.php" class="inline-flex items-center text-white font-medium bg-white/10 hover:bg-white/20 px-4 py-2.5 rounded-lg transition">
                        <svg class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        Barangay System Portal
                    </a>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-8 text-center text-sm text-gray-400">
                <p>© 2025 Barangay Bagbag Community Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile Menu Toggle
            const mobileBtn = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            let menuOpen = false;

            mobileBtn.addEventListener('click', () => {
                menuOpen = !menuOpen;
                mobileMenu.classList.toggle('hidden');
            });

            // Smooth Scroll + Close Mobile Menu
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        // CHANGE: Adjusted offset to 100px for the new navbar
                        window.scrollTo({
                            top: target.offsetTop - 100, 
                            behavior: 'smooth'
                        });
                        if (menuOpen) {
                            mobileMenu.classList.add('hidden');
                            menuOpen = false;
                        }
                    }
                });
            });

            // Simple Scroll Reveal (Your code is great!)
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('appear');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>