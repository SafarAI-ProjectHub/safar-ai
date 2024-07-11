<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>
            <div class="position-relative search-bar d-lg-block d-none" data-bs-toggle="modal"
                data-bs-target="#SearchModal">
                <input class="form-control px-5" disabled type="search" placeholder="Search">
                <span class="position-absolute top-50 search-show ms-3 translate-middle-y start-0 top-50 fs-5"><i
                        class='bx bx-search'></i></span>
            </div>
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal"
                        data-bs-target="#SearchModal">
                        <a class="nav-link" href="avascript:;"><i class='bx bx-search'></i>
                        </a>
                    </li>

                    {{-- <li class="nav-item dropdown dropdown-laungauge d-none d-sm-flex">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="avascript:;"
                            data-bs-toggle="dropdown"><img src="{{ asset('assets/images/county/02.png') }}"
                                width="22" alt="">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:;">
                                    <img src="{{ asset('assets/images/county/01.png') }}" width="20" alt="">
                                    <span class="ms-2">English</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:;">
                                    <img src="{{ asset('assets/images/county/02.png') }}" width="20" alt="">
                                    <span class="ms-2">Catalan</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:;">
                                    <img src="{{ asset('assets/images/county/03.png') }}" width="20" alt="">
                                    <span class="ms-2">French</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:;">
                                    <img src="{{ asset('assets/images/county/04.png') }}" width="20" alt="">
                                    <span class="ms-2">Belize</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:;">
                                    <img src="{{ asset('assets/images/county/05.png') }}" width="20" alt="">
                                    <span class="ms-2">Colombia</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:;">
                                    <img src="{{ asset('assets/images/county/06.png') }}" width="20" alt="">
                                    <span class="ms-2">Spanish</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:;">
                                    <img src="{{ asset('assets/images/county/07.png') }}" width="20" alt="">
                                    <span class="ms-2">Georgian</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:;">
                                    <img src="{{ asset('assets/images/county/08.png') }}" width="20" alt="">
                                    <span class="ms-2">Hindi</span>
                                </a>
                            </li>
                        </ul>

                    </li> --}}

                    <li class="nav-item dark-mode d-none d-sm-flex">
                        <a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
                        </a>
                    </li>

                    {{-- <li class="nav-item dropdown dropdown-app">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown"
                            href="javascript:;">
                            <i class='bx bx-grid-alt'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-0">
                            <div class="app-container p-2 my-2">
                                <div class="row gx-0 gy-2 row-cols-3 justify-content-center p-2">
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/slack.png') }}" width="30"
                                                        alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Slack
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/behance.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Behance
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/google-drive.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Dribble
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/outlook.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Outlook
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/github.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        GitHub
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/stack-overflow.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Stack
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/figma.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Stack
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/twitter.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Twitter
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/google-calendar.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Calendar
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/spotify.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Spotify
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/google-photos.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Photos
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/pinterest.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Photos
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/linkedin.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        LinkedIn
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/dribble.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Dribble
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/youtube.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        YouTube
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/google.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        News
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/envato.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Envato
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="javascript:;">
                                            <div class="app-box text-center">
                                                <div class="app-icon">
                                                    <img src="{{ asset('assets/images/app/safari.png') }}"
                                                        width="30" alt="">
                                                </div>
                                                <div class="app-name">
                                                    <p class="mb-0 mt-1">
                                                        Safari
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <!--end row-->
                            </div>
                        </div>
                    </li> --}}


                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            data-bs-toggle="dropdown">
                            <span class="alert-count">0</span>
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Notifications</p>
                                    <p class="msg-header-badge"><span id="unread-count">0</span> New</p>
                                </div>
                            </a>
                            <div class="header-notifications-list app-container" id="notification-list">
                                <!-- Notifications will be appended here -->
                            </div>
                            <a href="{{ route('notifications.index') }}">
                                <div class="text-center msg-footer">
                                    <button class="btn btn-primary w-100">View All Notifications</button>
                                </div>
                            </a>
                        </div>
                    </li>


                    {{-- <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="alert-count">8</span>
                            <i class='bx bx-shopping-bag'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">My Cart</p>
                                    <p class="msg-header-badge">10 Items</p>
                                </div>
                            </a>
                            <div class="header-message-list">
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <div class="cart-product rounded-circle bg-light">
                                                <img src="{{ asset('assets/images/products/11.png') }}"
                                                    class="" alt="product image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="cart-product-title mb-0">Men White T-Shirt</h6>
                                            <p class="cart-product-price mb-0">1 X $29.00</p>
                                        </div>
                                        <div class="">
                                            <p class="cart-price mb-0">$250</p>
                                        </div>
                                        <div class="cart-product-cancel">
                                            <i class="bx bx-x"></i>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <div class="cart-product rounded-circle bg-light">
                                                <img src="{{ asset('assets/images/products/02.png') }}"
                                                    class="" alt="product image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="cart-product-title mb-0">Men White T-Shirt</h6>
                                            <p class="cart-product-price mb-0">1 X $29.00</p>
                                        </div>
                                        <div class="">
                                            <p class="cart-price mb-0">$250</p>
                                        </div>
                                        <div class="cart-product-cancel">
                                            <i class="bx bx-x"></i>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <div class="cart-product rounded-circle bg-light">
                                                <img src="{{ asset('assets/images/products/03.png') }}"
                                                    class="" alt="product image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="cart-product-title mb-0">Men White T-Shirt</h6>
                                            <p class="cart-product-price mb-0">1 X $29.00</p>
                                        </div>
                                        <div class="">
                                            <p class="cart-price mb-0">$250</p>
                                        </div>
                                        <div class="cart-product-cancel">
                                            <i class="bx bx-x"></i>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <div class="cart-product rounded-circle bg-light">
                                                <img src="{{ asset('assets/images/products/04.png') }}"
                                                    class="" alt="product image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="cart-product-title mb-0">Men White T-Shirt</h6>
                                            <p class="cart-product-price mb-0">1 X $29.00</p>
                                        </div>
                                        <div class="">
                                            <p class="cart-price mb-0">$250</p>
                                        </div>
                                        <div class="cart-product-cancel">
                                            <i class="bx bx-x"></i>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <div class="cart-product rounded-circle bg-light">
                                                <img src="{{ asset('assets/images/products/05.png') }}"
                                                    class="" alt="product image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="cart-product-title mb-0">Men White T-Shirt</h6>
                                            <p class="cart-product-price mb-0">1 X $29.00</p>
                                        </div>
                                        <div class="">
                                            <p class="cart-price mb-0">$250</p>
                                        </div>
                                        <div class="cart-product-cancel">
                                            <i class="bx bx-x"></i>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <div class="cart-product rounded-circle bg-light">
                                                <img src="{{ asset('assets/images/products/06.png') }}"
                                                    class="" alt="product image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="cart-product-title mb-0">Men White T-Shirt</h6>
                                            <p class="cart-product-price mb-0">1 X $29.00</p>
                                        </div>
                                        <div class="">
                                            <p class="cart-price mb-0">$250</p>
                                        </div>
                                        <div class="cart-product-cancel">
                                            <i class="bx bx-x"></i>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <div class="cart-product rounded-circle bg-light">
                                                <img src="{{ asset('assets/images/products/07.png') }}"
                                                    class="" alt="product image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="cart-product-title mb-0">Men White T-Shirt</h6>
                                            <p class="cart-product-price mb-0">1 X $29.00</p>
                                        </div>
                                        <div class="">
                                            <p class="cart-price mb-0">$250</p>
                                        </div>
                                        <div class="cart-product-cancel">
                                            <i class="bx bx-x"></i>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <div class="cart-product rounded-circle bg-light">
                                                <img src="{{ asset('assets/images/products/08.png') }}"
                                                    class="" alt="product image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="cart-product-title mb-0">Men White T-Shirt</h6>
                                            <p class="cart-product-price mb-0">1 X $29.00</p>
                                        </div>
                                        <div class="">
                                            <p class="cart-price mb-0">$250</p>
                                        </div>
                                        <div class="cart-product-cancel">
                                            <i class="bx bx-x"></i>
                                        </div>
                                    </div>
                                </a>
                                <a class="dropdown-item" href="javascript:;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <div class="cart-product rounded-circle bg-light">
                                                <img src="{{ asset('assets/images/products/09.png') }}"
                                                    class="" alt="product image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="cart-product-title mb-0">Men White T-Shirt</h6>
                                            <p class="cart-product-price mb-0">1 X $29.00</p>
                                        </div>
                                        <div class="">
                                            <p class="cart-price mb-0">$250</p>
                                        </div>
                                        <div class="cart-product-cancel">
                                            <i class="bx bx-x"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <a href="javascript:;">
                                <div class="text-center msg-footer">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h5 class="mb-0">Total</h5>
                                        <h5 class="mb-0 ms-auto">$489.00</h5>
                                    </div>
                                    <button class="btn btn-primary w-100">Checkout</button>
                                </div>
                            </a>
                        </div>
                    </li> --}}


                </ul>
            </div>
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret"
                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/images/avatars/profile-Img.png') }}"
                        class="user-img" alt="user avatar">
                    <div class="user-info">
                        <p class="user-name mb-0">
                            @if (Auth::check())
                                {{ Auth::user()->getFullNameAttribute() }}
                            @endif
                        </p>
                        <p class="designattion mb-0">
                            @if (Auth::check())
                                {{ Auth::user()->role->role_name }}
                            @endif
                        </p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}"><i
                                class="bx bx-user fs-5"></i><span>Profile</span></a>
                    </li>
                    <!--                     <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i -->
                    <!--                                 class="bx bx-cog fs-5"></i><span>Settings</span></a> -->
                    <!--                     </li> -->
                    <!--                     <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i -->
                    <!--                                 class="bx bx-home-circle fs-5"></i><span>Dashboard</span></a> -->
                    <!--                     </li> -->
                    <!--                     <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i -->
                    <!--                                 class="bx bx-dollar-circle fs-5"></i><span>Earnings</span></a> -->
                    <!--                     </li> -->
                    <!--                     <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i -->
                    <!--                                 class="bx bx-download fs-5"></i><span>Downloads</span></a> -->
                    <!--                     </li> -->
                    <li>
                        <div class="dropdown-divider mb-0">
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-log-out-circle"></i><span>Logout</span>
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </ul>
            </div>
        </nav>
    </div>
</header>

<script>
    // Initialize Pusher
    Pusher.logToConsole = true;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env('PUSHER_APP_KEY') }}',
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                Authorization: 'Bearer ' + '{{ csrf_token() }}',
            },
        },
    });

    function truncateMessage(message, maxLength) {
        if (message.length > maxLength) {
            return message.substring(0, maxLength) + '...';
        }
        return message;
    }

    function timeAgo(date) {
        const now = new Date();
        const seconds = Math.floor((now - new Date(date)) / 1000);
        let interval = Math.floor(seconds / 31536000);

        if (interval > 1) {
            return interval + ' years ago';
        }
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) {
            return interval + ' months ago';
        }
        interval = Math.floor(seconds / 604800);
        if (interval > 1) {
            return interval + ' weeks ago';
        }
        interval = Math.floor(seconds / 86400);
        if (interval > 1) {
            return interval + ' days ago';
        }
        interval = Math.floor(seconds / 3600);
        if (interval > 1) {
            return interval + ' hours ago';
        }
        interval = Math.floor(seconds / 60);
        if (interval > 1) {
            return interval + ' minutes ago';
        }
        return Math.floor(seconds) + ' seconds ago';
    }

    $(document).ready(function() {
        function fetchNotifications() {
            $.ajax({
                url: "{{ route('notifications.get') }}",
                method: "GET",
                success: function(response) {
                    console.log('Notifications:', response);
                    $('.alert-count').text(response.unread_count);
                    $('.msg-header-badge').text(response.unread_count + ' New');
                    $('#notification-list').empty();
                    response.notifications.forEach(function(notification) {
                        let truncatedMessage = truncateMessage(notification.message, 30);
                        let notificationUrl;

                        if (notification.type === 'meeting') {
                            notificationUrl = `/student/meetings/${notification.model_id}`;
                        } else if (notification.type === 'subscription') {
                            notificationUrl = `{{ route('subscription.details') }}`;
                        } else if (notification.type === 'admin-subscription') {
                            notificationUrl = `{{ route('showPendingPayments') }}`;
                        } else {
                            notificationUrl = '#';
                        }

                        let notificationItem = `
                            <a class="dropdown-item" href="${notificationUrl}">
                                <div class="d-flex align-items-center">
                                    <div class="notify bg-light-primary p-2 fs-4">
                                        <i class='bx ${notification.icon}'></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="msg-name">${notification.title}<span class="msg-time float-end">${timeAgo(notification.created_at)}</span></h6>
                                        <p class="msg-info">${truncatedMessage}</p>
                                    </div>
                                </div>
                            </a>
                        `;
                        $('#notification-list').append(notificationItem);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching notifications:', error);
                }
            });
        }

        fetchNotifications();

        Echo.private('notifications.' + '{{ Auth::id() }}')
            .listen('NotificationEvent', (e) => {
                console.log('Notification:', e.notification.title);
                if (e.notification.type == 'subscription' || e.notification.title ==
                    'Payment Completed' || e.notification.title ===
                    'Subscription Activated' || e.notification.title == 'Subscription Approved' || e
                    .notification.type == 'Subscription Cancelled') {


                    window.location.reload();
                }
                fetchNotifications();
            });
    });
</script>
