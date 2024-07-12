@extends('layouts_dashboard.main')

@section('styles')
    <style>
        @media (max-width: 641px) {
            html {
                font-size: small;
            }
        }

        @media (max-width: 321px) {
            html {
                font-size: x-small;
            }
        }

        html,
        body,
        .content {
            margin: 0;
            height: 100%;
            width: 100%;
        }

        body {
            color: #606c76;
            font-family: 'Roboto', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            font-weight: 300;
            letter-spacing: .01em;
            line-height: 1.6;
        }

        .content {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .certificate-wrapper {
            margin: 1rem;
            padding: 1rem;
        }

        .certificate {
            padding: 1rem;
            text-align: center;
            background-color: #ffffff;
        }

        .certificate__body {
            padding: 1rem 0;
        }

        .certificate__title {
            font-family: Palatino;
            font-size: 4rem;
        }

        .certificate__recipient-name {
            font-family: cursive;
            font-size: 4rem;
        }

        .certificate__content {
            font-size: 2rem;
            white-space: nowrap;
        }

        .certificate__description {
            font-size: 1rem;
            margin: 0 auto;
            max-width: 70%;
        }

        .certificate__date {
            font-size: 1.5rem;
        }

        .certificate__signature {
            font-size: 1.5rem;
        }

        .certificate__footer {
            display: flex;
            justify-content: space-around;
        }

        .entry-column {
            display: flex;
            flex-direction: column;
        }

        .entry-column__input {
            font-size: 1.5rem;
            font-family: cursive;
        }

        .entry-column__label {
            border-top: 1px solid;
            font-size: 1rem;
        }

        .certificate__signature .entry-column__input {
            color: #000000;
        }

        .title-decoration {
            display: flex;
            flex-direction: column;
        }

        .title-decoration__main {
            line-height: 1em;
        }

        .title-decoration__sub {
            font-size: 0.25em;
        }

        .ribbon {
            display: inline-block;
            position: relative;
            height: 1.5em;
            line-height: 1.5em;
            text-align: center;
            padding: 0 2em;
            background: #C45ACD;
            color: #FFF;
            box-sizing: border-box;
            margin: 10px 0 10px 0;
        }

        .ribbon:before,
        .ribbon:after {
            position: absolute;
            content: '';
            width: 0px;
            height: 0px;
            z-index: 1;
        }

        .ribbon:before {
            top: 0;
            left: 0;
            border-width: 0.75em 0 0.75em 0.5em;
            border-color: transparent transparent transparent #fff;
            border-style: solid;
        }

        .ribbon:after {
            top: 0;
            right: 0;
            border-width: 0.75em 0.5em 0.75em 0;
            border-color: transparent #fff transparent transparent;
            border-style: solid;
        }

        .frame {
            position: relative;
            padding: 1.25rem;
            background:
                radial-gradient(circle at top left, transparent 1.25rem, #C45ACD 1.25rem, #C45ACD 1.5rem, transparent 1.5rem) left top / 1.5rem 1.5rem no-repeat,
                radial-gradient(circle at top right, transparent 1.25rem, #C45ACD 1.25rem, #C45ACD 1.5rem, transparent 1.5rem) right top / 1.5rem 1.5rem no-repeat,
                radial-gradient(circle at bottom left, transparent 1.25rem, #C45ACD 1.25rem, #C45ACD 1.5rem, transparent 1.5rem) left bottom / 1.5rem 1.5rem no-repeat,
                radial-gradient(circle at bottom right, transparent 1.25rem, #C45ACD 1.25rem, #C45ACD 1.5rem, transparent 1.5rem) right bottom / 1.5rem 1.5rem no-repeat,
                linear-gradient(90deg, transparent 1.25rem, #C45ACD 1.5rem) left top / 51% 0.25rem no-repeat,
                linear-gradient(-90deg, transparent 1.25rem, #C45ACD 1.5rem) right top / 51% 0.25rem no-repeat,
                linear-gradient(90deg, transparent 1.25rem, #C45ACD 1.5rem) left bottom / 51% 0.25rem no-repeat,
                linear-gradient(-90deg, transparent 1.25rem, #C45ACD 1.5rem) right bottom / 51% 0.25rem no-repeat,
                linear-gradient(180deg, transparent 1.25rem, #C45ACD 1.5rem) left top / 0.25rem 51% no-repeat,
                linear-gradient(0deg, transparent 1.25rem, #C45ACD 1.5rem) left bottom / 0.25rem 51% no-repeat,
                linear-gradient(180deg, transparent 1.25rem, #C45ACD 1.5rem) right top / 0.25rem 51% no-repeat,
                linear-gradient(0deg, transparent 1.25rem, #C45ACD 1.5rem) right bottom / 0.25rem 51% no-repeat;
        }

        .frame:before {
            position: absolute;
            content: "";
            border: 0.25rem double #C45ACD;
            margin: 0.5rem;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        @media print {
            .ribbon:before {
                left: -0.25px;
            }

            .ribbon:after {
                right: -0.25px;
            }

            .certificate__description {
                max-width: 90%;
            }
        }

        .certificate__logo {
            width: 300px;
            margin-bottom: 20px;
        }
    </style>
@endsection


@section('content')
    <div class="container" style="font-family: Arial, sans-serif;">
        <div class="certificate-wrapper frame">
            <div class="certificate">
                <div class="certificate__header">
                    <img src="{{ asset('assets/images/logo-img.png') }}" alt="Safar AI Academy Logo" class="certificate__logo">
                    <div class="certificate__title title-decoration">
                        <span class="title-decoration__main">Certificate</span>
                        <span class="title-decoration__sub">of Completion</span>
                    </div>
                </div>
                <div class="certificate__body">
                    <div class="certificate__description">This certifies that</div>
                    <div class="certificate__recipient-name">{{ Auth::user()->full_name }}</div>
                    <div class="certificate__description">has successfully completed the</div>
                    <div class="ribbon certificate__content">{{ $course->title }}</div>
                    <div class="certificate__description">course. This certificate is awarded by Safar AI Academy in
                        recognition of the dedication and commitment demonstrated in achieving this milestone.</div>
                </div>
                <div class="certificate__footer">
                    <div class="certificate__date entry-column">
                        <span class="entry-column__input">{{ date('F j, Y') }}</span>
                        <span class="entry-column__label">Date Completed</span>
                    </div>
                    <form id="ssn-form">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                    </form>
                    <div class="certificate__signature entry-column">
                        <span class="entry-column__input">{{ Auth::user()->full_name }}</span>
                        <span class="entry-column__label">Signature</span>
                    </div>
                </div>
            </div>
        </div>
        <button id="download-certificate" class="btn btn-primary d-block mx-auto"
            style="background-color: #844DCD; border-color: #844DCD; color: #fff; margin-top: 20px;">Download
            Certificate as PDF</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#download-certificate').click(function(e) {
                e.preventDefault();
                var form = $('#ssn-form');
                $.ajax({
                    url: '{{ route('certificate.generatePDF') }}',
                    type: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response, status, xhr) {
                        var filename = "";
                        var disposition = xhr.getResponseHeader('Content-Disposition');
                        if (disposition && disposition.indexOf('attachment') !== -1) {
                            var matches = /filename="([^;]+)"/.exec(disposition);
                            if (matches != null && matches[1]) filename = matches[1];
                        }
                        var link = document.createElement('a');
                        var url = window.URL.createObjectURL(response);
                        link.href = url;
                        link.download = filename;
                        document.body.append(link);
                        link.click();
                        link.remove();
                        window.URL.revokeObjectURL(url);
                    },
                    error: function() {
                        Swal.fire('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
@endsection
