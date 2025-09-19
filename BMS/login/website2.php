<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Community Portal</title>
    <script src="https://cdn.tailwindcss.com  "></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #008233 0%, #00c64e 100%);
        }
        
        .facility-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        input:focus, textarea:focus {
            outline: 2px solid #00c64e;
        }

        nav {
    transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
}

@keyframes marquee {
    0% {
        transform: translateX(100%);
    }
    100% {
        transform: translateX(-100%);
    }
}
.animate-marquee {
    animation: marquee 20s linear infinite;
}
        

        
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="gradient-bg text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <img src="../images/Bagbag.png" alt="Barangay Seal - Circular emblem with green and white colors featuring local landmarks" class="h-12 w-12 rounded-full">
                    <div>
                        <h1 class="text-xl font-bold">Barangay Bagbag</h1>
                        <p class="text-xs opacity-80">Community Services & Facilities</p>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="hover:text-green-200 transition">Home</a>
                    <a href="#facilities" class="hover:text-green-200 transition">Facilities</a>
                    <a href="#about" class="hover:text-green-200 transition">About Us</a>
                    <a href="#contact" class="hover:text-green-200 transition">Contact</a>
                    <a href="../../RIS/registrations/ris_registration_form.php" class="bg-white text-green-700 px-4 py-2 rounded-full font-medium hover:bg-green-100 transition flex items-center">
                        <span>Resident<br>Registration</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <button class="md:hidden focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>
    <div class="bg-yellow-100 text-green-800 text-sm font-medium py-2 overflow-hidden whitespace-nowrap">
    <div class="animate-marquee">
        ✨ Welcome to Barangay Bagbag! | Community Updates | Barangay Services Available | Events & Announcements
    </div>
</div>

    <!-- Hero Section -->
    <section id="home" class="relative">
        <!-- REPLACED IMAGE WITH VIDEO -->
        <video src="../images/bagbeg.mp4" autoplay muted loop playsinline class="w-full h-96 md:h-screen object-cover"></video>
        <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
            <div class="text-center px-4">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">Welcome to Our Barangay</h1>
                <p class="text-xl text-white mb-8 max-w-2xl mx-auto">A peaceful and progressive community dedicated to serving our residents with care and compassion.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#facilities" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-full text-lg font-medium transition">Our Facilities</a>
                    <a href="login.php" class="bg-white hover:bg-gray-100 text-green-700 px-8 py-3 rounded-full text-lg font-medium transition flex items-center justify-center">
                        Brgy. System Access
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section id="facilities" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-green-700 mb-2">Our Community Facilities</h2>
                <div class="w-20 h-1 bg-green-500 mx-auto"></div>
                <p class="text-gray-600 max-w-2xl mx-auto mt-4">We provide well-maintained facilities to serve the needs of our community members.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Facility 1 -->
                <div class="facility-card bg-white rounded-lg shadow-md overflow-hidden transition duration-300">
                    <img src="../images/health-center.jpg" alt="Barangay Health Center with modern equipment and clean waiting area, doctors attending to patients" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-green-700 mb-2">Health Center</h3>
                        <p class="text-gray-600 mb-4">Our fully-equipped health center provides medical consultations, immunizations, and basic healthcare services for all residents.</p>
                        <div class="flex items-center text-sm text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span>Near Barangay Hall</span>
                        </div>
                    </div>
                </div>
                
                <!-- Facility 2 -->
                <div class="facility-card bg-white rounded-lg shadow-md overflow-hidden transition duration-300">
                    <img src="../images/multi-purpose.png" alt="Barangay covered court with basketball game in progress and spectators watching from the sides" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-green-700 mb-2">Multi-Purpose Court</h3>
                        <p class="text-gray-600 mb-4">A versatile space for sports, events, and community gatherings. Equipped for basketball, volleyball, and other activities.</p>
                        <div class="flex items-center text-sm text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span>Central Barangay Area</span>
                        </div>
                    </div>
                </div>
                
                <!-- Facility 3 -->
                <div class="facility-card bg-white rounded-lg shadow-md overflow-hidden transition duration-300">
                    <img src="../images/ggg.jpg" alt="Barangay hall with clean facade, Philippine flag, and residents conducting business at the front desk" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-green-700 mb-2">Barangay Hall</h3>
                        <p class="text-gray-600 mb-4">The center of our community governance where you can process documents, seek assistance, and participate in community programs.</p>
                        <div class="flex items-center text-sm text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span>Main Road, Near Elementary School</span>
                        </div>
                    </div>
                </div>
                
                <!-- Facility 4 -->
                <div class="facility-card bg-white rounded-lg shadow-md overflow-hidden transition duration-300">
                    <img src="../images/playground.png" alt="Children playing in barangay playground with colorful equipment and safety flooring under supervision" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-green-700 mb-2">Children's Playground</h3>
                        <p class="text-gray-600 mb-4">A safe and fun space for children with modern play equipment, benches for parents, and shaded areas.</p>
                        <div class="flex items-center text-sm text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span>Adjacent to the Multi-Purpose Court</span>
                        </div>
                    </div>
                </div>
                
                <!-- Facility 5 -->
                <div class="facility-card bg-white rounded-lg shadow-md overflow-hidden transition duration-300">
                    <img src="../images/evacuation.jpg" alt="Children playing in barangay playground with colorful equipment and safety flooring under supervision" class="w-full h-48 object-cover">

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-green-700 mb-2">Evacuation Center</h3>
                        <p class="text-gray-600 mb-4">A secure facility equipped with emergency supplies to serve as temporary shelter during disasters or emergencies.</p>
                        <div class="flex items-center text-sm text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span>Beside the Barangay Hall</span>
                        </div>
                    </div>
                </div>
                
                <!-- Facility 6 -->
                <div class="facility-card bg-white rounded-lg shadow-md overflow-hidden transition duration-300">
                    <img src="../images/communitypark.png" alt="Children playing in barangay playground with colorful equipment and safety flooring under supervision" class="w-full h-48 object-cover">

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-green-700 mb-2">Community Park</h3>
                        <p class="text-gray-600 mb-4">A green space with walking paths, benches, and gardens for residents to relax, exercise, and enjoy nature.</p>
                        <div class="flex items-center text-sm text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span>Northside of the Barangay</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center">
                <div class="lg:w-1/2 mb-10 lg:mb-0 lg:pr-10">
                    <img src="../images/healthy.jpg" alt="Group of barangay officials and residents working together on a community project, showing unity and cooperation" class="rounded-lg shadow-lg w-full">
                </div>
                <div class="lg:w-1/2">
                    <h2 class="text-3xl font-bold text-green-700 mb-4">About Our Barangay</h2>
                    <div class="w-20 h-1 bg-green-500 mb-6"></div>
                    <p class="text-gray-600 mb-4">Our barangay is committed to fostering a safe, healthy, and progressive community where residents can live harmoniously and thrive together.</p>
                    <p class="text-gray-600 mb-6">We prioritize transparency in governance, community participation in development programs, and accessible services for all our constituents.</p>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="flex items-start">
                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-green-700">Health Services</h4>
                                <p class="text-sm text-gray-500">Medical checkups & consultations</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-green-700">Education</h4>
                                <p class="text-sm text-gray-500">Scholarships & learning programs</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-green-700">Safety</h4>
                                <p class="text-sm text-gray-500">24/7 Barangay patrol</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-green-700">Livelihood</h4>
                                <p class="text-sm text-gray-500">Skills training programs</p>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-full text-base font-medium transition inline-flex items-center">
                        Learn More
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-green-700 mb-2">Contact Our Barangay</h2>
                <div class="w-20 h-1 bg-green-500 mx-auto"></div>
                <p class="text-gray-600 max-w-2xl mx-auto mt-4">Have questions or concerns? Reach out to us through any of these channels.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
                    <h3 class="text-xl font-bold text-green-700 mb-4">Get In Touch</h3>
                    <form>
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:bg-white" placeholder="Juan Dela Cruz">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:bg-white" placeholder="juan@example.com">
                        </div>
                        <div class="mb-4">
                            <label for="message" class="block text-gray-700 mb-2">Your Message</label>
                            <textarea id="message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:bg-white" placeholder="Your message here..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium transition">
                            Send Message
                        </button>
                    </form>
                </div>
                
                <div>
                    <div class="bg-gray-50 p-8 rounded-lg shadow-sm h-full">
                        <h3 class="text-xl font-bold text-green-700 mb-4">Our Information</h3>
                        <div class="space-y-6">
                            <div class="flex">
                                <div class="bg-green-100 p-3 rounded-full mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-green-700">Address</h4>
                                    <p class="text-gray-600">625 Pagkabuhay Road, Barangay Bagbag, Novaliches, Quezon City, Philippines</p>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="bg-green-100 p-3 rounded-full mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-green-700">Contact Numbers</h4>
                                    <p class="text-gray-600">(02) 1234-5678 (Office)</p>
                                    <p class="text-gray-600">0985-814-6011 (Mobile)</p>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="bg-green-100 p-3 rounded-full mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-green-700">Email</h4>
                                    <p class="text-gray-600">barangaybagbagmanagementsystem@gmail.com</p>
                                </div>
                            </div>
                            <div class="flex">
                                <div class="bg-green-100 p-3 rounded-full mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-green-700">Office Hours</h4>
                                    <p class="text-gray-600">Monday to Friday: 8:00 AM - 5:00 PM</p>
                                    <p class="text-gray-600">Saturday: 8:00 AM - 12:00 NN</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="rounded-lg overflow-hidden h-96">
                <img src="../images/location.png" alt="Google Map location pin marking the barangay hall and surrounding community facilities" class="w-full h-full object-cover">
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="gradient-bg text-white pt-12 pb-6">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">Barangay Portal</h3>
                    <p class="text-green-100">Serving our community with integrity, transparency, and compassion since 1990.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-green-100 hover:text-white transition">Home</a></li>
                        <li><a href="#facilities" class="text-green-100 hover:text-white transition">Facilities</a></li>
                        <li><a href="#about" class="text-green-100 hover:text-white transition">About Us</a></li>
                        <li><a href="#contact" class="text-green-100 hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Services</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-green-100 hover:text-white transition">Health Services</a></li>
                        <li><a href="#" class="text-green-100 hover:text-white transition">Document Processing</a></li>
                        <li><a href="#" class="text-green-100 hover:text-white transition">Community Programs</a></li>
                        <li><a href="#" class="text-green-100 hover:text-white transition">Complaint Center</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Connect With Us</h3>
                    <div class="flex space-x-4 mb-4">
                        <a href="#" class="bg-green-700 hover:bg-green-600 p-2 rounded-full transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#" class="bg-green-700 hover:bg-green-600 p-2 rounded-full transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <a href="#" class="bg-green-700 hover:bg-green-600 p-2 rounded-full transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                            </svg>
                        </a>
                    </div>
                    <a href="../login-system/login.php" class="inline-flex items-center text-white font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Barangay Bagbag System Portal
                    </a>
                </div>
            </div>
            <div class="border-t border-green-700 pt-6 text-center text-sm text-green-100">
                <p>© 2025 Barangay Community Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('nav button');
            const mobileMenu = document.createElement('div');
            mobileMenu.className = 'md:hidden bg-green-700 absolute w-full left-0 px-6 py-4 space-y-4';
            mobileMenu.innerHTML = `
                <a href="#home" class="block text-white hover:text-green-200">Home</a>
                <a href="#facilities" class="block text-white hover:text-green-200">Facilities</a>
                <a href="#about" class="block text-white hover:text-green-200">About Us</a>
                <a href="#contact" class="block text-white hover:text-green-200">Contact</a>
                <a href="#" class="block bg-white text-green-700 px-4 py-2 rounded-full font-medium hover:bg-green-100 text-center">Brgy. System</a>
            `;
            mobileMenu.style.display = 'none';
            document.querySelector('nav').appendChild(mobileMenu);
            
            let isMenuOpen = false;
            mobileMenuButton.addEventListener('click', function() {
                isMenuOpen = !isMenuOpen;
                mobileMenu.style.display = isMenuOpen ? 'block' : 'none';
            });
            
            // Smooth scrolling for all links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    isMenuOpen = false;
                    mobileMenu.style.display = 'none';
                    
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.querySelector('nav');
        const mobileMenuButton = document.querySelector('nav button');
        const mobileMenu = document.querySelector('.mobile-menu');
        let lastScrollTop = 0;
        let isMenuOpen = false;

        // Mobile menu toggle
        mobileMenuButton.addEventListener('click', function () {
            isMenuOpen = !isMenuOpen;
            mobileMenu.style.display = isMenuOpen ? 'block' : 'none';
        });

        // Smooth scroll for all links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                isMenuOpen = false;
                mobileMenu.style.display = 'none';
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Scroll fade navbar
        window.addEventListener('scroll', function () {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;

            if (!isMenuOpen) {
                if (scrollTop > lastScrollTop) {
                    // Scrolling down
                    navbar.style.opacity = '0';
                    navbar.style.transform = 'translateY(-100%)';
                } else {
                    // Scrolling up
                    navbar.style.opacity = '1';
                    navbar.style.transform = 'translateY(0)';
                }
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        }, { passive: true });
    });
</script>
</body>
</html>