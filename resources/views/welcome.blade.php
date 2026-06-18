<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Prabhu Insurance') }} — Securing Your Future</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css?family=open-sans:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-prabhu-dark antialiased bg-white">
        <!-- Top Bar -->
        <div class="bg-prabhu-red-600 text-white text-xs py-1.5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <span>Prabhu Insurance Co. Ltd.</span>
                <span class="hidden sm:block">📞 +977-1-4532100 | ✉️ info@prabhuinsurance.com</span>
            </div>
        </div>

        <!-- Navigation -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo.png') }}" alt="Prabhu Insurance Logo" class="h-10 w-auto">
                        </a>
                    </div>

                    <!-- Nav Links -->
                    <nav class="hidden md:flex items-center space-x-8">
                        <a href="{{ url('/') }}" class="text-prabhu-red-600 font-semibold text-sm border-b-2 border-prabhu-red-600 pb-1">Home</a>
                        <a href="#about" class="text-prabhu-dark hover:text-prabhu-red-600 text-sm font-medium transition">About Us</a>
                        <a href="#services" class="text-prabhu-dark hover:text-prabhu-red-600 text-sm font-medium transition">Services</a>
                        <a href="#contact" class="text-prabhu-dark hover:text-prabhu-red-600 text-sm font-medium transition">Contact</a>
                    </nav>

                    <!-- Auth Buttons -->
                    <div class="flex items-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-prabhu-red-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-prabhu-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-prabhu-red-500 transition">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-prabhu-red-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-prabhu-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-prabhu-red-500 transition">
                                    Log in
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-prabhu-red-600 to-prabhu-red-800 text-white overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1440 320%22><path fill=%22white%22 d=%22M0,160L48,176C96,192,192,224,288,208C384,192,480,128,576,133.3C672,139,768,213,864,229.3C960,245,1056,203,1152,165.3C1248,128,1344,96,1392,80L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z%22></path></svg>'); background-position: bottom; background-repeat: no-repeat; background-size: cover;"></div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32 relative">
                <div class="lg:grid lg:grid-cols-2 lg:gap-12 items-center">
                    <div>
                        <h1 class="text-4xl lg:text-5xl xl:text-6xl font-extrabold leading-tight mb-6">
                            Protecting What<br class="hidden sm:block"> Matters Most
                        </h1>
                        <p class="text-lg lg:text-xl text-red-100 mb-8 max-w-lg leading-relaxed">
                            Prabhu Insurance Co. Ltd. is one of Nepal's leading insurance companies, providing comprehensive general insurance solutions for individuals, families, and businesses across the nation.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-white text-prabhu-red-600 font-semibold rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-prabhu-red-600 transition text-sm">
                                Sign In to Your Account
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="#about" class="inline-flex items-center px-6 py-3 border-2 border-white text-white font-semibold rounded-md hover:bg-white hover:text-prabhu-red-600 focus:outline-none focus:ring-2 focus:ring-white transition text-sm">
                                Learn More
                            </a>
                        </div>
                    </div>
                    <div class="hidden lg:flex justify-center">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="text-center">
                                    <div class="text-4xl font-extrabold">15+</div>
                                    <div class="text-red-200 text-sm mt-1">Years of Trust</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-4xl font-extrabold">500K+</div>
                                    <div class="text-red-200 text-sm mt-1">Happy Clients</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-4xl font-extrabold">50+</div>
                                    <div class="text-red-200 text-sm mt-1">Branches</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-4xl font-extrabold">24/7</div>
                                    <div class="text-red-200 text-sm mt-1">Claim Support</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-16 lg:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-prabhu-darker">About Prabhu Insurance</h2>
                    <div class="w-16 h-1 bg-prabhu-red-600 mx-auto mt-4"></div>
                    <p class="mt-4 text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        Established with a vision to provide reliable and accessible insurance solutions, Prabhu Insurance has grown to become one of the most trusted names in Nepal's insurance industry.
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center p-8 rounded-xl border border-gray-100 hover:shadow-lg hover:border-prabhu-red-200 transition group">
                        <div class="w-16 h-16 bg-prabhu-red-50 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-prabhu-red-100 transition">
                            <svg class="w-8 h-8 text-prabhu-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-prabhu-darker mb-3">Trusted & Reliable</h3>
                        <p class="text-gray-500 leading-relaxed text-sm">Over 15 years of experience in providing comprehensive insurance solutions with a commitment to excellence and customer satisfaction.</p>
                    </div>
                    <div class="text-center p-8 rounded-xl border border-gray-100 hover:shadow-lg hover:border-prabhu-red-200 transition group">
                        <div class="w-16 h-16 bg-prabhu-red-50 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-prabhu-red-100 transition">
                            <svg class="w-8 h-8 text-prabhu-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-prabhu-darker mb-3">Fast Claims</h3>
                        <p class="text-gray-500 leading-relaxed text-sm">Streamlined claim processing with dedicated support teams ensuring quick and hassle-free settlements when you need them most.</p>
                    </div>
                    <div class="text-center p-8 rounded-xl border border-gray-100 hover:shadow-lg hover:border-prabhu-red-200 transition group">
                        <div class="w-16 h-16 bg-prabhu-red-50 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-prabhu-red-100 transition">
                            <svg class="w-8 h-8 text-prabhu-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-prabhu-darker mb-3">Nationwide Network</h3>
                        <p class="text-gray-500 leading-relaxed text-sm">50+ branches across Nepal ensuring we're always within reach, with personalized service tailored to your local needs.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-16 lg:py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-prabhu-darker">Our Insurance Products</h2>
                    <div class="w-16 h-1 bg-prabhu-red-600 mx-auto mt-4"></div>
                    <p class="mt-4 text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        Comprehensive insurance solutions designed to protect every aspect of your life and business.
                    </p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-xl p-6 border-t-4 border-prabhu-red-600 shadow-sm hover:shadow-md transition">
                        <h3 class="font-semibold text-prabhu-darker mb-2">Motor Insurance</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Comprehensive coverage for your vehicles — private cars, commercial vehicles, and two-wheelers.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-t-4 border-prabhu-red-600 shadow-sm hover:shadow-md transition">
                        <h3 class="font-semibold text-prabhu-darker mb-2">Health Insurance</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Protect your family's health with our affordable medical and hospitalization plans.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-t-4 border-prabhu-red-600 shadow-sm hover:shadow-md transition">
                        <h3 class="font-semibold text-prabhu-darker mb-2">Property Insurance</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Safeguard your home, business, and valuable assets against fire, theft, and natural disasters.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-t-4 border-prabhu-red-600 shadow-sm hover:shadow-md transition">
                        <h3 class="font-semibold text-prabhu-darker mb-2">Travel Insurance</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Travel worry-free with coverage for medical emergencies, trip cancellations, and lost baggage.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-t-4 border-prabhu-red-600 shadow-sm hover:shadow-md transition">
                        <h3 class="font-semibold text-prabhu-darker mb-2">Marine Insurance</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Cargo and hull insurance for your goods in transit, both domestic and international.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-t-4 border-prabhu-red-600 shadow-sm hover:shadow-md transition">
                        <h3 class="font-semibold text-prabhu-darker mb-2">Engineering Insurance</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Coverage for construction projects, machinery breakdown, and electronic equipment.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-t-4 border-prabhu-red-600 shadow-sm hover:shadow-md transition">
                        <h3 class="font-semibold text-prabhu-darker mb-2">Agriculture Insurance</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Protect your crops and livestock from unforeseen risks with our agricultural plans.</p>
                    </div>
                    <div class="bg-white rounded-xl p-6 border-t-4 border-prabhu-red-600 shadow-sm hover:shadow-md transition">
                        <h3 class="font-semibold text-prabhu-darker mb-2">Micro Insurance</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Affordable insurance products designed for low-income individuals and rural communities.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-16 lg:py-20 bg-prabhu-red-600">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl lg:text-4xl font-extrabold text-white mb-4">Ready to Get Protected?</h2>
                <p class="text-red-100 text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
                    Join thousands of satisfied customers who trust Prabhu Insurance for their insurance needs. Get started today and secure your tomorrow.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3 bg-white text-prabhu-red-600 font-semibold rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-prabhu-red-600 transition text-sm">
                            Sign In to Your Account
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-16 lg:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-prabhu-darker">Get In Touch</h2>
                    <div class="w-16 h-1 bg-prabhu-red-600 mx-auto mt-4"></div>
                    <p class="mt-4 text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        Have questions about our insurance products? We're here to help. Reach out to us through any of the channels below.
                    </p>
                </div>
                <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="text-center p-6">
                        <div class="w-12 h-12 bg-prabhu-red-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-prabhu-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h3 class="font-semibold text-prabhu-darker">Head Office</h3>
                        <p class="text-gray-500 text-sm mt-1">Prabhu Complex, Kathmandu, Nepal</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="w-12 h-12 bg-prabhu-red-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-prabhu-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <h3 class="font-semibold text-prabhu-darker">Phone</h3>
                        <p class="text-gray-500 text-sm mt-1">+977-1-4532100</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="w-12 h-12 bg-prabhu-red-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-prabhu-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="font-semibold text-prabhu-darker">Email</h3>
                        <p class="text-gray-500 text-sm mt-1">info@prabhuinsurance.com</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-prabhu-darker text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid md:grid-cols-3 gap-8">
                    <div>
                        <img src="{{ asset('images/logo.png') }}" alt="Prabhu Insurance Logo" class="h-8 w-auto mb-4 brightness-0 invert">
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Prabhu Insurance Co. Ltd. is one of Nepal's premier general insurance companies, committed to providing innovative and reliable insurance solutions.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-white transition">Home</a></li>
                            <li><a href="#about" class="text-gray-400 hover:text-white transition">About Us</a></li>
                            <li><a href="#services" class="text-gray-400 hover:text-white transition">Our Services</a></li>
                            <li><a href="#contact" class="text-gray-400 hover:text-white transition">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white mb-4">Account</h4>
                        <ul class="space-y-2 text-sm">
                            @if (Route::has('login'))
                                @auth
                                    <li><a href="{{ url('/dashboard') }}" class="text-gray-400 hover:text-white transition">Dashboard</a></li>
                                @else
                                    <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition">Log In</a></li>
                                @endauth
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                    <p class="text-gray-500 text-sm">
                        &copy; {{ date('Y') }} Prabhu Insurance Co. Ltd. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </body>
</html>
