<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Responsive Email</title>
    <style type="text/css">
        .b1 {
            border: 1px solid red;
        }
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: Arial, Helvetica, sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
            width: 100%;
        }
        img {
            border: 0;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            display: block;
        }
        .image-container img {
            width: 100%; /* Responsive by default */
            max-width: 200px; /* Maximum size for larger screens */
            height: auto;
        }
        .wide-container img {
            width: 100%; /* Responsive by default */
            max-width: 600px; /* Maximum size for larger screens */
            height: auto;
        }
        .image-container img {
            width: 100%; /* Responsive by default */
            max-width: 200px; /* Maximum size for larger screens */
            height: auto;
        }
        .hero-container img {
            width: 100%; /* Responsive by default */
            max-width: 600px; /* Maximum size for larger screens */
            height: auto;
        }
        .hero-container img {
            width: 100%; /* Responsive by default */
            max-width: 600px; /* Maximum size for larger screens */
            height: auto;
        }
        .image-row td {
            padding: 2px;
        }
        .button-container {
            text-align: center;
            margin-top: 2px;
        }
        .download-button {
            display: inline-block;
            padding: 10px 30px;
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 5px;
        }
        .download-button:hover {
            background-color: #0056b3;
        }
        .small-text {
            font-size: 14px;
        }
        .shadow {
            box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.3);
        }
        @media screen {
            .text-sm {
                font-size: 12px;
                color: #0ea5e9;
                font-italic: italic;
            }
            .text-lg {
                font-size: 26px;
                color: #0ea5e9;
                font-italic: italic;
                font-weight: 400;
            }
        }
        @media screen and (max-width: 600px) {
            .image-container img {
                max-width: 100%; /* Full width for smaller screens */
            }
            .text-sm {
                font-size: 12px;
                color: #570c29;
                font-italic: italic;
            }
            .text-lg {
                font-size: 20px;
                color: #8d116a;
                font-italic: italic;
                font-weight: bold;
            }
            .responsive-table {
                width: 100%;
            }
            .responsive-table tr {
                display: flex;
                flex-direction: column; /* Stacks the table content */
                align-items: center; /* Centers the items (logo and text) horizontally */
                text-align: center; /* Centers the text inside table cells */
            }
            .responsive-table td {
                width: 100%; /* Allows the table cells to take full width */
            }
            .logo-cell {
                display: flex;
                justify-content: center; /* Centers the logo horizontally */
                align-items: center; /* Centers the logo vertically */
            }
            .text-cell {
                margin-top: 10px; /* Adds spacing between the logo and the text */
            }

        }
        @media screen and (max-width: 400px) {
            .text_variable {
                font-size: 8px;
            }
            .text-sm {
                font-size: 10px;
                color: #0ea5e9;
                font-italic: italic;
            }
            .text-lg {
                font-size: 18px;
                color: #0ea5e9;
                font-italic: italic;
            }
            .responsive-table tr {
                display: flex;
                flex-direction: column;
                align-items: center; /* Center-align items */
                text-align: center; /* Center align text */
            }
            .responsive-table td {
                width: 100%; /* Ensure each part takes the full width */
                margin-bottom: 10px; /* Add spacing between the logo and text */
            }

        }
    </style>
</head>
<body style="background-color: #eae5e5; margin: 0; padding: 0;">
<div style="background-color: #cdced0; max-width: 600px; margin: 0 auto; padding: 10px;">
    <!-- Header -->
    <table width="100%" class="responsive-table" style="text-align: center;">
        <tr>
            <td class="logo-cell" style="display: flex; align-items: center; justify-content: center; gap: 2px; padding: 1px;">
                <img width="120px" height="auto" src="{{ asset('img/logos/logo.png') }}" alt="Logo">
            </td>
            <td style="display: flex; align-items: center; justify-content: center; gap: 2px; padding: 1px;">
                <div class="text-lg" style="color: #333333; margin: 0;">
                    {{ $record->first  }} - Here's the link to Your <br/>
                    "Free Costa Rica Photo Guide" in case you forgot to download it.</div>
            </td>
        </tr>
    </table>
    <!-- Hero Row -->
    <table align="center" class="image-row" style="text-align: center; width: 100%; margin-top: 1px;">
        <tr>
            <td style="padding: 5px;" class="hero-container">
                <img class="shadow" src="{{ asset('img/emails/_DSC8767.jpg') }}" alt="Image 1" style="border-radius: 10px">
            </td>
        </tr>
    </table>
    <!-- Text Content -->
    <table width="100%" style="text-align: left; margin-top: 2px;" class="responsive-table">
        <tr>
            <td width="25%" style="vertical-align: top;" class="logo-cell">
                <img
                    src="{{  asset('img/emails/james1.jpg') }}"
                    alt="James"
                    style="width: 120px; height: auto; margin-top: 10px; border-radius: 10px; box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);"
                >
            </td>
            <td style="padding: 8px; vertical-align: top;" class="text-cell">
                <p style="font-size: 16px; color: #000000; margin: 0; line-height: 1.4;">
                    Hello, I’m James, the guide behind Real Cool Photography Tours.
                    I’ve spent years chasing light, mist, and magic across the globe,
                    and now I’ve created this guide about Costa Rica to help you capture its wild beauty like a pro.
                    Whether it’s your first visit or you’re a seasoned photographer,
                    this is the insider advice I’d share with my closest photo friends.</p>
            </td>
            </td>
        </tr>
    </table>
    <!-- Image Row -->
    <table align="center" class="image-row" style="text-align: center; width: 100%; height:auto; margin-top: 10px;">
        <tr>
            <td style="padding: 5px;" class="image-container">
                <img class="shadow" src="{{ asset('img/emails/small/_DSC9295.jpg') }}" alt="Image 1" style="border-radius: 10px">
            </td>
            <td style="padding: 5px;" class="image-container">
                <img class="shadow" src="{{ asset('img/emails/small/_DSC9423.jpg') }}" alt="Image 2" style="border-radius: 10px">
            </td>
            <td style="padding: 5px;" class="image-container">
                <img class="shadow" src="{{ asset('img/emails/small/_DSC9042.jpg') }}" alt="Image 3" style="border-radius: 10px">
            </td>
        </tr>
    </table>
    <table width="100%" style="text-align: center;" class="">
        <tr>
            <td style="padding: 2px; width: 100%">
                <h2 style="font-size: 24px; color: #333333;">Enjoy Your Photo Guide!</h2>
            </td>
        </tr>
    </table>
    <!-- Download Button -->
    <div class="button-container">
        <a
            href="https://www.realcoolphototours.com/?page_id=799"
            class="download-button shadow"
            target="_blank"
            style="color: #ffffff;"
            rel="noopener noreferrer"
        >
            Download Your Guide
        </a>
    </div>
    <!-- Image Row -->
    <table align="center" class="image-row" style="text-align: center; width: 100%; height:auto; margin-top: 20px; margin-bottom: 20px">
        <tr>
            <td style="padding: 1px;" class="wide-container">
                <img class="shadow wide-container" src="{{ asset('img/emails/small/wide__DSC9054.jpg') }}" alt="Jungle Image" style="border-radius: 10px">
            </td>
        </tr>
    </table>
    <!-- Text Content -->
    <table width="100%" style="text-align: left; margin-top: 10px;">
        <tr>
            <td style="padding: 20px;">
                <p style="font-size: 16px; color: #666666; margin: 0;">
                    Epic Locations. Extraordinary Images.
                    Join us for a small, custom photo adventure across Costa Rica,
                    where every day brings new opportunities to capture once-in-a-lifetime shots.
                    <br/><br/>
                    From lush jungles to incredible wildlife, you'll discover challenging and inspiring photographic moments.
                    For those new to photography, we’ve included dedicated time to discuss workflow and Lightroom editing techniques.
                    <br/><br/>
                    With accommodation, meals, internet, local transport, and even a few cold beers (or wine) included, all you need to bring is your camera and a sense of adventure.
                </p>
            </td>
        </tr>
    </table>
    <div class="button-container">
        <a
            href="https://www.realcoolphototours.com/"
            class="download-button shadow"
            style="color: #ffffff;"
            target="_blank"
            rel="noopener noreferrer"
        >
            Find out more about Costa Rica Photo Tours
        </a>
    </div>
    <!-- Unsub -->
    <table width="100%" style="text-align: center; margin-top: 10px;">
        <tr>
            <td style="padding: 20px;">
                <p style="font-size: 16px; color: #666666; margin: 0;">
                    If for any reason you are not interested in hearing more about Costa Rica Photo Tours then please click here to <a href="{{ route('unsubscribe', ['token' => $unsubscribe_token]) }}">unsubscribe.</a>

                </p>
            </td>
        </tr>
    </table>
    <!-- details -->
    <table width="100%" style="text-align: left; margin-top: 2px;" class="responsive-table">
        <tr>
            <td width="25%" style="vertical-align: top;" class="logo-cell">
                <img
                    src="{{  asset('img/logos/logo.png') }}"
                    alt="RCP Logo"
                    style="width: 120px; height: 100px; margin-top: 20px; border-radius: 10px; box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);"
                >
            </td>
            <td style="padding: 8px; vertical-align: top;" class="text-cell">
                <p style="color: #000000; margin: 0; margin-top: 10px;" class="text-sm">Real Cool Photo Tours are provided by RealCoolProductions Inc. An award winning production and training company
                    working world-wide on commercial projects. Visit <a href="https://www.rcplearning.com">Real Cool Productions.</a></p>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
