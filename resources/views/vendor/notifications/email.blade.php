<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safar AI</title>
    <style>
        /* Basic styles for email compatibility */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #212529;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding: 10px 0;
        }

        .header img {
            max-width: 100px;
        }

        .content {
            padding: 20px;
            text-align: left;
        }

        .content h1 {
            color: #333333;
        }

        .content p {
            line-height: 1.6;
            margin: 10px 0;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            color: #ffffff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #777777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="Logo">
            <div style="font-size: 24px;font-wight:bold; margin-top: 10px;">
                <span style="color: #844DCD;">Safar<span style="color: #C45ACD;">AI</span></span>
            </div>
        </div>
        <div class="content">
            {{-- Greeting --}}
            @if (!empty($greeting))
                <h1>{{ $greeting }}</h1>
            @else
                @if ($level === 'error')
                    <h1>@lang('Whoops!')</h1>
                @else
                    <h1>@lang('Hello!')</h1>
                @endif
            @endif

            {{-- Intro Lines --}}
            @foreach ($introLines as $line)
                <p>{{ $line }}</p>
            @endforeach

            {{-- Action Button --}}
            @isset($actionText)
                <?php
                $color = match ($level) {
                    'success', 'error' => $level,
                    default => 'primary',
                };
                ?>
                <a href="{{ $actionUrl }}" class="button"
                    style="background-color: {{ $color === 'error' ? '#dc3545' : ($color === 'success' ? '#28a745' : '#C45ACD') }};">
                    {{ $actionText }}
                </a>
            @endisset

            {{-- Outro Lines --}}
            @foreach ($outroLines as $line)
                <p>{{ $line }}</p>
            @endforeach

            {{-- Salutation --}}
            @if (!empty($salutation))
                <p>{{ $salutation }}</p>
            @else
                <p>@lang('Regards'),<br>{{ config('app.name') }}</p>
            @endif

            {{-- Subcopy --}}
            @isset($actionText)
                <p style="font-size: 12px; color: #844DCD;">
                    @lang("If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n" . 'into your web browser:', [
                        'actionText' => $actionText,
                    ]) <span
                        style="word-break: break-all;">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
                </p>
            @endisset
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Safar-AI. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
