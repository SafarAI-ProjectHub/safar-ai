<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Certificate</title>
    <style>
        html,
        body,
        .content {
            margin: 10px 0 10px 0;
            height: 97%;
            width: 100%;
            overflow: hidden;
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
            height: 100%;
        }

        .certificate-wrapper {
            width: 500mm;
            height: 300mm;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .certificate {
            text-align: center;
            background-color: #ffffff;
            padding: 2rem;
            box-sizing: border-box;
            width: 90%;
            height: 90%;
        }

        .certificate__header {
            margin-bottom: 1.5rem;
        }

        .certificate__title {
            font-family: Palatino;
            font-size: 5rem;
        }

        .certificate__recipient-name {
            font-family: cursive;
            font-size: 5rem;
            margin: 1rem 0;
        }

        .certificate__content {
            font-size: 2.5rem;
        }

        .certificate__description {
            font-size: 1.5rem;
            margin: 0 auto 1rem auto;
            max-width: 90%;
        }

        .certificate__date,
        .certificate__signature {
            font-size: 1.8rem;
        }

        .certificate__footer {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .entry-column {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .entry-column__input {
            font-size: 1.8rem;
            font-family: cursive;
        }

        .entry-column__label {
            border-top: 1px solid;
            font-size: 1.2rem;
        }

        .ribbon {
            display: inline-block;
            position: relative;
            height: 2em;
            line-height: 2em;
            text-align: center;
            padding: 0 3em;
            background: #C45ACD;
            color: #FFF;
            margin: 10px 0;
            font-size: 2rem;
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
            border-width: 1em 0 1em 0.75em;
            border-color: transparent transparent transparent #fff;
            border-style: solid;
        }

        .ribbon:after {
            top: 0;
            right: 0;
            border-width: 1em 0.75em 1em 0;
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

        .certificate__logo {
            width: 300px;
            margin-bottom: 20px;
        }

        @page {
            size: 1000mm 300mm;
            margin: 0;
        }

        @media print {
            .ribbon:before {
                left: -0.25px;
            }

            .ribbon:after {
                right: -0.25px;
            }

            .certificate__description {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="certificate-wrapper frame">
            <div class="certificate">
                <div class="certificate__header">
                    <img src="{{ asset('assets/images/logo-img.png') }}" alt="Safar AI Academy Logo"
                        class="certificate__logo">
                    <div class="certificate__title title-decoration">
                        <span class="title-decoration__main">Certificate</span>
                        <span class="title-decoration__sub">of Completion</span>
                    </div>
                </div>
                <div class="certificate__body">
                    <div class="certificate__description">This certifies that</div>
                    <div class="certificate__recipient-name">{{ $user->full_name }}</div>
                    <div class="certificate__description">has successfully completed the</div>
                    <div class="ribbon certificate__content">{{ $course->title }}</div>
                    <div class="certificate__description">This certificate is awarded by Safar AI Academy in recognition
                        of the dedication and commitment demonstrated in achieving this milestone.</div>
                </div>
                <div class="certificate__footer">
                    <div class="certificate__date entry-column">
                        <span class="entry-column__input">{{ $date }}</span>
                        <span class="entry-column__label">Date Completed</span>
                    </div>
                    <div class="certificate__signature entry-column">
                        <span class="entry-column__input">{{ $user->full_name }}</span>
                        <span class="entry-column__label">Signature</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
