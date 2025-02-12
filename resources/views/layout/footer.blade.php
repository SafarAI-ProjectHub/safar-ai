  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">

      {{-- <div class="footer-newsletter">
          <div class="container">
              <div class="row justify-content-center">
                  <div class="col-lg-12 text-center">
                      <h4>Our Newsletter</h4>
                      <p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
                  </div>
                  <div class="col-lg-6">
                      <form action="" method="post">
                          <input type="email" name="email"><input type="submit" value="Subscribe">
                      </form>
                  </div>
              </div>
          </div>
      </div> --}}

      <div class="footer-top">
          <div class="container">
              <div class="row gy-4">
                  <div class="col-lg-5 col-md-12 footer-info">
                      <a href="index.html" class="logo d-flex align-items-center">
                          <img src="assets/img/logo1.png" alt="">
                          <span><span style="color:#C45ACD">Safar</span> AI</span>
                      </a>
                      <p>Cras fermentum odio eu feugiat lide par naso tierra. Justo eget nada terra videa magna derita
                          valies darta donna mare fermentum iaculis eu non diam phasellus.</p>
                      <div class="social-links mt-3">
                          <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                          <a href="https://www.facebook.com/people/Safar-AI/61570366034528/" class="facebook"><i class="bi bi-facebook"></i></a>
                          <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                          <a href="https://www.linkedin.com/in/safarai/" class="linkedin"><i class="bi bi-linkedin"></i></a>
                      </div>
                  </div>

                  <div class="col-lg-2 col-6 footer-links">
                      <h4>Useful Links</h4>
                      <ul>
                          <li><i class="bi bi-chevron-right"></i> <a
                                  href="{{ request()->is('/') ? '#hero' : '/#hero' }}">Home</a></li>
                          <li><i class="bi bi-chevron-right"></i> <a
                                  href="{{ request()->is('/') ? '#about' : '/#about' }}">About us</a></li>
                          <li><i class="bi bi-chevron-right"></i> <a
                                  href="{{ request()->is('/') ? '#services' : '/#services' }}">Services</a></li>
                          <li><i class="bi bi-chevron-right"></i> <a
                                  href="{{ request()->routeIs('terms') ? '#' : route('terms') }}">Terms of service</a>
                          <li><i class="bi bi-chevron-right"></i> <a
                                  href="{{ request()->routeIs('privacy') ? '#' : route('privacy') }}">Privacy
                                  policy</a>
                      </ul>
                  </div>



                  {{-- <div class="col-lg-2 col-6 footer-links">
            <h4>Our Services</h4>
            <ul>
              <li><i class="bi bi-chevron-right"></i> <a href="#">Web Design</a></li>
              <li><i class="bi bi-chevron-right"></i> <a href="#">Web Development</a></li>
              <li><i class="bi bi-chevron-right"></i> <a href="#">Product Management</a></li>
              <li><i class="bi bi-chevron-right"></i> <a href="#">Marketing</a></li>
              <li><i class="bi bi-chevron-right"></i> <a href="#">Graphic Design</a></li>
            </ul>
          </div> --}}

                  <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
                      <h4>Contact Us</h4>
                      <p>
                            Mecca Street <br>
                            Amman, Jordan 11185<br> 
                            Jordan <br><br>


                          <strong>Phone:</strong> {{ env('phone_number') }}<br>
                          <strong>Email:</strong> {{ env('Email_Adrees') }}<br>
                      </p>

                  </div>

              </div>
          </div>
      </div>

      <div class="container">
          <div class="copyright">
              &copy; Copyright <strong> <span><span style="color:#C45ACD">Safar</span> AI</span></strong>. All Rights
              Reserved
          </div>
      </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
          class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="/assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="/assets/vendor/aos/aos.js"></script>
  <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="/assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="/assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="/assets/js/main.js"></script>
