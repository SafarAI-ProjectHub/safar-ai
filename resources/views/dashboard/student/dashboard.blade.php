@extends('layouts_dashboard.main')

@section('content')

    @hasrole('Student')
        @if (Auth::user()->status == 'pinding')
            {{-- we need to alert the student that his acount is pindding until he take the level test  --}}
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Your account is pinding</h4>
                <p>you need to take the level test to start your learning journey</p>
                <hr>
                <p class="mb-0">please take the level test</p>
            </div>
        @else
            {{-- we need to show the student the courses he can take based on his level test score --}}
            <div class="row">
                <div class="col-md-12">
                    <h1>Available Courses</h1>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        @foreach ($courses as $course)
                            <div class="col-md-4">
                                <div class="card" style="width: 18rem;">
                                    {{-- center the image --}}
                                    <div class="text-center">
                                        <img src="https://cdn-icons-png.flaticon.com/512/4762/4762311.png" class="card-img-top"
                                            alt="..." style="width: 80%;">
                                    </div>

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $course->title }}</h5>
                                        {{-- <p class="card-text">{{ $course->description }}</p> --}}
                                        <a href="{{ route('admin.showcourse', $course->id) }}" class="btn btn-primary">Start</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endhasrole

@endsection
