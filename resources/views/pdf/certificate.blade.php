<!DOCTYPE html>
<html>

<head>
    <title>Certificate of Completion</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

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
            height: 100vh;
            /* Ensure content covers the viewport height */
        }

        .certificate-wrapper {
            width: 100%;
            max-width: 800px;
            /* Adjust to fit content within A4 */
            padding: 2rem;
            box-sizing: border-box;
        }

        .certificate {
            padding: 2rem;
            text-align: center;
            background-color: #ffffff;
            border: 1px solid #C45ACD;
            /* Add a border to fit within A4 */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* Optional shadow for aesthetics */
        }

        .certificate__header {
            margin-bottom: 2rem;
            /* Add spacing */
        }

        .certificate__body {
            padding: 2rem 0;
        }

        .certificate__title {
            font-family: Palatino;
            font-size: 3rem;
            /* Adjust font size */
            margin-bottom: 1rem;
        }

        .certificate__recipient-name {
            font-family: cursive;
            font-size: 2.5rem;
            /* Adjust font size */
            margin-bottom: 1rem;
        }

        .certificate__content {
            font-size: 2rem;
            white-space: nowrap;
            margin-bottom: 1rem;
        }

        .certificate__description {
            font-size: 1.2rem;
            /* Adjust font size */
            margin: 0 auto 1rem auto;
            max-width: 80%;
            /* Adjust width */
        }

        .certificate__date,
        .certificate__signature {
            font-size: 1.2rem;
            /* Adjust font size */
            display: inline-block;
            width: 40%;
            /* Adjust width */
            vertical-align: top;
            margin: 0 5%;
            text-align: center;
        }

        .entry-column__label {
            border-top: 1px solid #606c76;
            margin-top: 0.5rem;
            display: block;
        }

        .ribbon {
            display: inline-block;
            position: relative;
            height: 2em;
            line-height: 2em;
            text-align: center;
            padding: 0 2em;
            background: #C45ACD;
            color: #FFF;
            box-sizing: border-box;
            margin: 1rem 0;
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
            border-width: 1em 0 1em 0.5em;
            border-color: transparent transparent transparent #fff;
            border-style: solid;
        }

        .ribbon:after {
            top: 0;
            right: 0;
            border-width: 1em 0.5em 1em 0;
            border-color: transparent #fff transparent transparent;
            border-style: solid;
        }

        .frame {
            position: relative;
            padding: 1.5rem;
            background:
                radial-gradient(circle at top left, transparent 1.5rem, #C45ACD 1.5rem, #C45ACD 1.75rem, transparent 1.75rem) left top / 1.75rem 1.75rem no-repeat,
                radial-gradient(circle at top right, transparent 1.5rem, #C45ACD 1.5rem, #C45ACD 1.75rem, transparent 1.75rem) right top / 1.75rem 1.75rem no-repeat,
                radial-gradient(circle at bottom left, transparent 1.5rem, #C45ACD 1.5rem, #C45ACD 1.75rem, transparent 1.75rem) left bottom / 1.75rem 1.75rem no-repeat,
                radial-gradient(circle at bottom right, transparent 1.5rem, #C45ACD 1.5rem, #C45ACD 1.75rem, transparent 1.75rem) right bottom / 1.75rem 1.75rem no-repeat,
                linear-gradient(90deg, transparent 1.5rem, #C45ACD 1.75rem) left top / 51% 0.25rem no-repeat,
                linear-gradient(-90deg, transparent 1.5rem, #C45ACD 1.75rem) right top / 51% 0.25rem no-repeat,
                linear-gradient(90deg, transparent 1.5rem, #C45ACD 1.75rem) left bottom / 51% 0.25rem no-repeat,
                linear-gradient(-90deg, transparent 1.5rem, #C45ACD 1.75rem) right bottom / 51% 0.25rem no-repeat,
                linear-gradient(180deg, transparent 1.5rem, #C45ACD 1.75rem) left top / 0.25rem 51% no-repeat,
                linear-gradient(0deg, transparent 1.5rem, #C45ACD 1.75rem) left bottom / 0.25rem 51% no-repeat,
                linear-gradient(180deg, transparent 1.5rem, #C45ACD 1.75rem) right top / 0.25rem 51% no-repeat,
                linear-gradient(0deg, transparent 1.5rem, #C45ACD 1.75rem) right bottom / 0.25rem 51% no-repeat;
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
            width: 200px;
            margin-bottom: 20px;
        }

        @media print {
            .certificate__description {
                max-width: 90%;
            }

            .ribbon:before {
                left: -0.25px;
            }

            .ribbon:after {
                right: -0.25px;
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
