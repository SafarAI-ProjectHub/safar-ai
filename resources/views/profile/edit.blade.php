@extends('layouts_dashboard.main')


@section('content')


        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">User Profile</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">User Profile</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                {{-- <div class="btn-group">
                    <button type="button" class="btn btn-primary">Settings</button>
                    <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                        <a class="dropdown-item" href="javascript:;">Action</a>
                        <a class="dropdown-item" href="javascript:;">Another action</a>
                        <a class="dropdown-item" href="javascript:;">Something else here</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="javascript:;">Separated link</a>
                    </div>
                </div> --}}
            </div>
        </div>
        <!--end breadcrumb-->
        <div class="container">
            <div class="main-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                      <div class="d-flex flex-column align-items-center text-center">
    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="profile_image" class="form-label" style="cursor: pointer;">
                <span>Click on the image to choose a new one</span> <br>
                <img src="{{ Auth::user()->profile_image ? asset( Auth::user()->profile_image) : asset('assets/images/avatars/profile-Img.png') }}" alt="Profile Image" class="rounded-circle p-1" width="110" height="110" id="profileImagePreview">
            </label>
            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(event)" style="display:none;">
            <span id="profileImageError" class="text-danger"></span>
        </div>
        <div class="mt-3">
            <h4>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h4>
            <p class="text-secondary mb-1">Position: {{ Auth::user()->role->role_name }}</p>
            <p class="text-muted font-size-sm">Country: {{ Auth::user()->country_location }}</p>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('profileForm').addEventListener('submit', function(event) {
        var profileImageInput = document.getElementById('profile_image');
        var profileImageError = document.getElementById('profileImageError');
        
        if (profileImageInput.files.length === 0) {
            profileImageError.textContent = 'Please select an image to upload.';
            event.preventDefault(); // Prevent form submission
        } else {
            profileImageError.textContent = ''; // Clear any previous error message
        }
    });

    function previewImage(event) {
        var output = document.getElementById('profileImagePreview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src) // free memory
        }
    }
</script>



                                {{-- <hr class="my-4" />
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0"><i class="feather feather-globe me-2 icon-inline"></i>Website</h6>
                                        <span class="text-secondary">https://codervent.com</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0"><i class="feather feather-github me-2 icon-inline"></i>Github</h6>
                                        <span class="text-secondary">codervent</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0"><i class="feather feather-twitter me-2 icon-inline text-info"></i>Twitter</h6>
                                        <span class="text-secondary">@codervent</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0"><i class="feather feather-instagram me-2 icon-inline text-danger"></i>Instagram</h6>
                                        <span class="text-secondary">codervent</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0"><i class="feather feather-facebook me-2 icon-inline text-primary"></i>Facebook</h6>
                                        <span class="text-secondary">codervent</span>
                                    </li>
                                </ul> --}}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                               <form id="profile-update-form" action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('put')
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">First Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', Auth::user()->first_name) }}" />
                                        @error('first_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Last Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', Auth::user()->last_name) }}" />
                                        @error('last_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Email</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email) }}" />
                                       
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Phone</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', Auth::user()->phone_number) }}" />
                                        @error('phone_number')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Date of Birth</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') ?: Auth::user()->date_of_birth->format('Y-m-d') }}"  />
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Country</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="country_location" class="form-control @error('country_location') is-invalid @enderror" value="{{ old('country_location', Auth::user()->country_location) }} " disabled/>
                                        <input type="hidden" name="country_location" value="{{ old('country_location', Auth::user()->country_location) }}" />
                                        @error('country_location')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-secondary">
                                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                    </div>
                                </div>
                            </form>


                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                @php
                                    if(Auth::user()->role->role_name == 'Student') {
                                @endphp
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="d-flex align-items-center mb-3">Courses Status</h5>
                                        <p>Web Design</p>
                                        <div class="progress mb-3" style="height: 5px">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p>Website Markup</p>
                                        <div class="progress mb-3" style="height: 5px">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: 72%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p>One Page</p>
                                        <div class="progress mb-3" style="height: 5px">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 89%" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p>Mobile Template</p>
                                        <div class="progress mb-3" style="height: 5px">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p>Backend API</p>
                                        <div class="progress" style="height: 5px">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    }
                                @endphp
                            </div>

                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="max-w-xl">
                                    @include('profile.partials.update-password-form')
                                </div>
                            </div>
                        </div>
                        {{-- <div class="card">
                            <div class="card-body">
                                <div class="max-w-xl">
                                    @include('profile.partials.delete-user-form')
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

@endsection
