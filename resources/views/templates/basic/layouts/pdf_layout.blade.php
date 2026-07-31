<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $pageTitle }}</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,100..900;1,100..900&display=swap");

        :root {
            --heading-color: 17 24 39;
            --black: 0 0 0;
            --white: 255 255 255;
            --text-color: 107 107 107;
            --bg-color: 250 250 252;
            --base: 43 91 238;
            --primary: 0 102 255;
            --info: 0 163 204;
            --success: 59 191 0;
            --danger: 230 57 70;
            --warning: 255 204 0;
        }

        * {
            font-family: "Albert Sans", serif;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            list-style: none;
            text-decoration: none;
        }

        @page {
            size: 49.625rem 70.188rem;
        }

        body {
            padding: 5rem 3rem 4rem;
            background-color: #ffffff;
        }

        .invoice-area {
            position: relative;
            width: 7.27in;
            background-color: rgb(var(--white));
            border-radius: 16px;
        }

        .fs-10 {
            font-size: 10px;
        }

        .fs-11 {
            font-size: 11px;
        }

        .fs-12 {
            font-size: 12px;
        }

        .fs-13 {
            font-size: 13px;
        }

        .fs-14 {
            font-size: 14px;
        }

        .fs-15 {
            font-size: 15px;
        }

        .fs-16 {
            font-size: 16px;
        }

        .fs-17 {
            font-size: 17px;
        }

        .fs-18 {
            font-size: 18px;
        }

        .fs-19 {
            font-size: 19px;
        }

        .fs-20 {
            font-size: 17px;
        }

        .fw-bold {
            font-weight: 700;
        }

        .fw-semibold {
            font-weight: 600;
        }

        .fw-medium {
            font-weight: 500;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-start {
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .d-block {
            display: block;
        }

        .mt-0 {
            margin-top: 0;
        }

        .ms-auto {
            margin-left: auto;
        }

        .me-auto {
            margin-right: auto;
        }

        .mx-auto {
            margin: 0 auto;
        }

        .m-0 {
            margin: 0;
        }

        .mt-3 {
            margin-top: 16px;
        }

        .mt-4 {
            margin-top: 24px;
        }

        .mt-5 {
            margin-top: 32px;
        }

        .mb-3 {
            margin-bottom: 16px;
        }

        .mb-4 {
            margin-bottom: 24px;
        }

        .mb-5 {
            margin-bottom: 32px;
        }

        table {
            margin: 0;
            border: 0;
            width: 100%;
            border-collapse: collapse;
        }

        .text-muted {
            color: rgb(var(--text-color));
        }

        .pdf-top {
            border-bottom: 1px solid rgb(var(--black) / .1);
            padding: 20px;
        }

        .pdf-top-left {
            width: 60px;
        }

        .pdf-top-right {
            padding-left: 16px;
        }

        .pdf-top-image {
            height: 60px;
            width: 60px;
        }

        .pdf-top-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: rgb(var(--heading-color));
            margin-bottom: 6px;
        }

        .pdf-details {
            padding: 32px;
        }

        .pdf-details-title {
            font-size: 18px;
            font-weight: 700;
            color: rgb(var(--heading-color))
        }

        .pdf-info-title {
            font-size: 20px;
            color: rgb(var(--black) / .7);
            text-align: left;
            padding-bottom: 12px;
        }


        .pdf-info-value {
            font-size: 20px;
            color: rgb(var(--heading-color));
            font-weight: 500;
            text-align: right;
            padding-bottom: 12px;
        }

        .total {
            border-top: 1px solid rgb(var(--black) /.1);
        }

        .total .pdf-info-title {
            padding-top: 12px;
            padding-bottom: 24px;
        }

        .total .pdf-info-value {
            padding-top: 12px;
            padding-bottom: 24px;
            font-weight: 700;
        }

        .current-balance {
            background-color: rgb(var(--base) / .1);
        }

        .current-balance .pdf-info-title {
            color: rgb(var(--base));
            padding: 16px;
            border-radius: 12px 0 0 12px;
        }

        .current-balance .pdf-info-value {
            color: rgb(var(--base));
            font-weight: 700;
            padding: 16px;
            border-radius: 0 12px 12px 0;
        }
    </style>
</head>

<body>

    @yield('content')

</body>

</html>
