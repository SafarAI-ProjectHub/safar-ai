 <!-- ======= Header ======= -->
 <header id="header" class="header fixed-top">
     <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

         <a href="/" class="logo d-flex align-items-center">
             <img src="assets/img/logo1.png" alt="">
             <span><span style="color:#C45ACD">Safar</span> AI</span>
         </a>

         <nav id="navbar" class="navbar">
             <ul>
                 <?php if(!Request::is('/')): ?>
                     <li class="<?php echo e(request()->url() == url('/') ? 'active' : ''); ?>"><a class="nav-link scrollto"
                             href="/">Home</a></li>
                     <li class="<?php echo e(request()->url() == route('privacy') ? 'active' : ''); ?>"><a class="nav-link scrollto"
                             href="<?php echo e(route('privacy')); ?>">Privacy Policy</a></li>
                     <li class="<?php echo e(request()->url() == route('terms') ? 'active' : ''); ?>"><a class="nav-link scrollto"
                             href="<?php echo e(route('terms')); ?>">Terms & Conditions</a></li>
                 <?php else: ?>
                     <li><a class="nav-link scrollto active" href="#hero">Home</a></li>
                     <li><a class="nav-link scrollto" href="#about">About</a></li>
                     <li><a class="nav-link scrollto" href="#services">Services</a></li>
                     <!-- <li><a class="nav-link scrollto" href="#portfolio">Portfolio</a></li> -->
                     <li><a class="nav-link scrollto" href="#team">Team</a></li>
                     <li><a class="nav-link scrollto" href="#contact">Contact</a></li>
                 <?php endif; ?>
                 <div class="text-center text-lg-start">
                     <?php if(Auth::check()): ?>
                         <?php if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin')): ?>
                             <a href="<?php echo e(route('dashboard')); ?>"
                                 class="btn-login scrollto d-inline-flex align-items-center justify-content-center align-self-center">
                                 <span>Dashboard</span>
                                 <i class="bi bi-arrow-right"></i>
                             </a>
                         <?php elseif(Auth::user()->hasRole('Student')): ?>
                             <a href="<?php echo e(route('student.dashboard')); ?>"
                                 class="btn-login scrollto d-inline-flex align-items-center justify-content-center align-self-center">
                                 <span>Dashboard</span>
                                 <i class="bi bi-arrow-right"></i>
                             </a>
                         <?php elseif(Auth::user()->hasRole('Teacher')): ?>
                             <a href="<?php echo e(route('teacher.dashboard')); ?>"
                                 class="btn-login scrollto d-inline-flex align-items-center justify-content-center align-self-center">
                                 <span>Dashboard</span>
                                 <i class="bi bi-arrow-right"></i>
                             </a>
                         <?php endif; ?>
                     <?php else: ?>
                         <a href="<?php echo e(route('login')); ?>"
                             class="btn-login scrollto d-inline-flex align-items-center justify-content-center align-self-center">
                             <span>Login</span>
                             <i class="bi bi-arrow-right"></i>
                         </a>
                     <?php endif; ?>
                 </div>

             </ul>
             <i class="bi bi-list mobile-nav-toggle"></i>
         </nav><!-- .navbar -->

     </div>
 </header><!-- End Header -->

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
<?php /**PATH /var/www/html/safar-ai-staging/resources/views/layout/header.blade.php ENDPATH**/ ?>