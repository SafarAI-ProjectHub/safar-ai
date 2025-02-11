<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Certificate</title>
    <style>
        /* Import the 'Great Vibes' font */
        @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');

        @page {
            size: 990mm 290mm;
            margin: 0;
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
            height: 100%;
        }

        .certificate-wrapper {
            width: 990mm;
            /* 10mm smaller */
            height: 290mm;
            /* 10mm smaller */
            padding: 0;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .certificate-wrapper:before,
        .certificate-wrapper:after,
        .certificate:before,
        .certificate:after {
            content: '';
            position: absolute;
            background: url('<?php echo e(url('img/corner.png')); ?>') no-repeat;
            background-size: contain;
            filter: grayscale(100%) brightness(50%);
        }

        .certificate-wrapper:before {
            top: 55px;
            /* Adjusted for better positioning */
            left: 65px;
            /* Adjusted for better positioning */
            width: 150px;
            height: 150px;
            z-index: 1;
        }

        .certificate-wrapper:after {
            bottom: 60px;
            /* Adjusted for better positioning */
            right: 65px;
            /* Adjusted for better positioning */
            width: 150px;
            height: 150px;
            transform: rotate(180deg);
        }

        .certificate:before {
            top: 0px;
            /* Adjusted for better positioning */
            right: -5px;
            /* Adjusted for better positioning */
            width: 150px;
            height: 150px;
            transform: rotate(90deg);
        }

        .certificate:after {
            bottom: 8px;
            /* Adjusted for better positioning */
            left: 0px;
            /* Adjusted for better positioning */
            width: 150px;
            height: 150px;
            transform: rotate(270deg);
        }

        .certificate {
            text-align: center;
            background-color: #ffffff;
            padding: 2rem;
            box-sizing: border-box;
            width: 90%;
            height: 90%;
            position: relative;
            background-color: #f8f1e4;
            background-image: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent), linear-gradient(225deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent), radial-gradient(circle, rgba(238, 232, 202, 0.5) 1px, transparent 1px), radial-gradient(circle, rgba(238, 232, 202, 0.5) 1px, transparent 1px);
            background-size: 15px 44px, 27px 27px, 12px 23px, 21px 24px;
            background-position: 0 0, 30px 30px, 0 0, 15px 15px;
            color: #5a4e3c;
            text-shadow: 1px 1px #f5f0df;
        }

        .certificate__header {
            margin-bottom: 1.5rem;
        }

        .certificate__title {
            font-family: Palatino;
            font-size: 5rem;
        }

        .certificate__recipient-name {
            font-size: 4rem;
            margin: 1rem 0;
        }

        .certificate__body {
            padding: 1rem 0;
        }

        .certificate__content {
            font-size: 2.5rem;
            white-space: nowrap;
        }

        .certificate__description {
            font-size: 1.5rem;
            margin: 0 auto 1rem auto;
            max-width: 70%;
        }

        .certificate__date,
        .certificate__signature {
            font-size: 1.8rem;
        }

        .certificate__footer {
            display: flex;
            justify-content: space-around;
            margin-top: 2rem;
        }

        .entry-column {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .entry-column__input {
            font-size: 1.8rem;
            font-family: 'Great Vibes', cursive;
        }

        .entry-column__label {
            border-top: 1px solid;
            font-size: 1.2rem;
        }

        .ribbon {
            display: inline-block;
            position: relative;
            height: 1.5em;
            line-height: 1.5em;
            text-align: center;
            padding: 0 2em;
            background: #d4af37;
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

        @page {
            size: 990mm 290mm;
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
        <div class="certificate-wrapper">
            <div class="certificate">
                <div class="certificate__header">
                    <img src="<?php echo e(url('assets/images/logo-img.png')); ?>" alt="Safar AI Academy Logo"
                        class="certificate__logo">
                    <div class="certificate__title title-decoration">
                        <span class="title-decoration__main">Certificate</span>
                        <span class="title-decoration__sub">of Completion</span>
                    </div>
                </div>
                <div class="certificate__body">
                    <div class="certificate__description">This certifies that</div>
                    <div class="certificate__recipient-name"><?php echo e(Auth::user()->full_name); ?></div>
                    <div class="certificate__description">has successfully completed the</div>
                    <div class="ribbon certificate__content"><?php echo e($course->title); ?></div>
                    <div class="certificate__description">course. This certificate is awarded by Safar AI Academy in
                        recognition of the dedication and commitment demonstrated in achieving this milestone.</div>
                </div>
                <div class="certificate__footer">
                    <div class="certificate__date entry-column">
                        <span class="entry-column__input"><?php echo e(\Carbon\Carbon::parse($date)->format('F j, Y')); ?></span>
                        <span class="entry-column__label">Date Completed</span>
                    </div>
                    <div class="certificate__signature entry-column">
                        <span class="entry-column__input"><?php echo e(Auth::user()->full_name); ?></span>
                        <span class="entry-column__label">Signature</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php /**PATH /var/www/html/safar-ai-staging/resources/views/pdf/certificate.blade.php ENDPATH**/ ?>