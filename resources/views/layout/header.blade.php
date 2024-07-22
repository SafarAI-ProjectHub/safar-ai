 <!-- ======= Header ======= -->
 <header id="header" class="header fixed-top">
     <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

         <a href="index.html" class="logo d-flex align-items-center">
             <img src="assets/img/logo1.png" alt="">
             <span><span style="color:#C45ACD">Safar</span> AI</span>
         </a>

         <nav id="navbar" class="navbar">
             <ul>
                 <li><a class="nav-link scrollto active" href="#hero">Home</a></li>
                 <li><a class="nav-link scrollto" href="#about">About</a></li>
                 <li><a class="nav-link scrollto" href="#services">Services</a></li>
                 <!-- <li><a class="nav-link scrollto" href="#portfolio">Portfolio</a></li> -->
                 <li><a class="nav-link scrollto" href="#team">Team</a></li>
                 <li><a class="nav-link scrollto" href="#contact">Contact</a></li>

                 <div class="text-center text-lg-start">
                     @if (Auth::check())
                         @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin'))
                             <a href="{{ route('dashboard') }}"
                                 class="btn-login scrollto d-inline-flex align-items-center justify-content-center align-self-center">
                                 <span>Dashboard</span>
                                 <i class="bi bi-arrow-right"></i>
                             </a>
                         @elseif(Auth::user()->hasRole('Student'))
                             <a href="{{ route('student.dashboard') }}"
                                 class="btn-login scrollto d-inline-flex align-items-center justify-content-center align-self-center">
                                 <span>Dashboard</span>
                                 <i class="bi bi-arrow-right"></i>
                             </a>
                         @elseif(Auth::user()->hasRole('Teacher'))
                             <a href="{{ route('teacher.dashboard') }}"
                                 class="btn-login scrollto d-inline-flex align-items-center justify-content-center align-self-center">
                                 <span>Dashboard</span>
                                 <i class="bi bi-arrow-right"></i>
                             </a>
                         @endif
                     @else
                         <a href="{{ route('login') }}"
                             class="btn-login scrollto d-inline-flex align-items-center justify-content-center align-self-center">
                             <span>Login</span>
                             <i class="bi bi-arrow-right"></i>
                         </a>
                     @endif
                 </div>

                 <!-- <li><a href="blog.html">Blog</a></li>  -->
                 <!--           <li class="dropdown"><a href="#"><span>Drop Down</span> <i class="bi bi-chevron-down"></i></a> -->
                 <!--             <ul> -->
                 <!--               <li><a href="#">Drop Down 1</a></li> -->
                 <!--               <li class="dropdown"><a href="#"><span>Deep Drop Down</span> <i class="bi bi-chevron-right"></i></a> -->
                 <!--                 <ul> -->
                 <!--                   <li><a href="#">Deep Drop Down 1</a></li> -->
                 <!--                   <li><a href="#">Deep Drop Down 2</a></li> -->
                 <!--                   <li><a href="#">Deep Drop Down 3</a></li> -->
                 <!--                   <li><a href="#">Deep Drop Down 4</a></li> -->
                 <!--                   <li><a href="#">Deep Drop Down 5</a></li> -->
                 <!--                 </ul> -->
                 <!--               </li> -->
                 <!--               <li><a href="#">Drop Down 2</a></li> -->
                 <!--               <li><a href="#">Drop Down 3</a></li> -->
                 <!--               <li><a href="#">Drop Down 4</a></li> -->
                 <!--             </ul> -->
                 <!--           </li> -->

                 <!--           <li class="dropdown megamenu"><a href="#"><span>Mega Menu</span> <i class="bi bi-chevron-down"></i></a> -->
                 <!--             <ul> -->
                 <!--               <li> -->
                 <!--                 <a href="#">Column 1 link 1</a> -->
                 <!--                 <a href="#">Column 1 link 2</a> -->
                 <!--                 <a href="#">Column 1 link 3</a> -->
                 <!--               </li> -->
                 <!--               <li> -->
                 <!--                 <a href="#">Column 2 link 1</a> -->
                 <!--                 <a href="#">Column 2 link 2</a> -->
                 <!--                 <a href="#">Column 3 link 3</a> -->
                 <!--               </li> -->
                 <!--               <li> -->
                 <!--                 <a href="#">Column 3 link 1</a> -->
                 <!--                 <a href="#">Column 3 link 2</a> -->
                 <!--                 <a href="#">Column 3 link 3</a> -->
                 <!--               </li> -->
                 <!--               <li> -->
                 <!--                 <a href="#">Column 4 link 1</a> -->
                 <!--                 <a href="#">Column 4 link 2</a> -->
                 <!--                 <a href="#">Column 4 link 3</a> -->
                 <!--               </li> -->
                 <!--             </ul> -->
                 <!--           </li> -->


                 <!--           <li><a class="getstarted scrollto" href="#about">Get Started</a></li> -->
                 <!--         </ul> -->
                 <i class="bi bi-list mobile-nav-toggle"></i>
         </nav><!-- .navbar -->

     </div>
 </header><!-- End Header -->

 <script>
     let currentActivityStart = new Date();

     function encryptData(data) {
         return btoa(JSON.stringify(data)); // Base64 encode for simplicity
     }

     function decryptData(data) {
         return JSON.parse(atob(data)); // Base64 decode for simplicity
     }

     function logActivity(status, additionalData = {}) {
         console.log(`Activity: ${status}, Data: `, additionalData);
     }

     function sendActivityStatus(status, additionalData = {}) {
         const data = encryptData({
             status: status,
             additionalData: additionalData
         });
         $.ajax({
             type: 'POST',
             url: '/update-activity-status',
             data: JSON.stringify({
                 data: data
             }),
             contentType: 'application/json',
             headers: {
                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             success: function(response) {
                 console.log('Activity status updated successfully.');
             },
             error: function(error) {
                 console.error('Error updating activity status:', error);
             }
         });
     }

     function handleActivityStatusChange(status) {
         const now = new Date();
         const activeTime = Math.floor((now - currentActivityStart) / 1000); // Convert to seconds
         sendActivityStatus(status, {
             activeTime: activeTime
         });
         logActivity(status, {
             activeTime: activeTime
         });
         sessionStorage.setItem('activityData', encryptData({
             currentActivityStart: now
         }));
         currentActivityStart = now; // Reset activity start time
     }

     // Handle focus event
     $(window).on('focus', function() {
         currentActivityStart = new Date();
         sendActivityStatus('active');
         logActivity('active');
     });

     // Handle blur event
     $(window).on('blur', function() {
         handleActivityStatusChange('inactive');
     });

     // Handle beforeunload event
     $(window).on('beforeunload', function() {
         handleActivityStatusChange('inactive');
     });

     // Handle visibilitychange event
     $(document).on('visibilitychange', function() {
         if (document.visibilityState === 'visible') {
             currentActivityStart = new Date();
             sendActivityStatus('active');
             logActivity('active');
         } else {
             handleActivityStatusChange('inactive');
         }
     });

     // Handle page reload
     $(document).ready(function() {
         const activityData = sessionStorage.getItem('activityData');
         if (activityData) {
             const decryptedData = decryptData(activityData);
             currentActivityStart = new Date(decryptedData.currentActivityStart);
         } else {
             currentActivityStart = new Date();
         }
         sendActivityStatus('active');
         logActivity('active');
     });

     // Handle page unload
     $(window).on('unload', function() {
         handleActivityStatusChange('inactive');
     });
 </script>
