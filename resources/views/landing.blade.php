<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Coming Soon | {{ $company->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ $company->name }}" name="description" />
    <meta content="{{ $company->name }}" name="author" />

    <!-- App favicon -->
    <link rel="icon" href="{{ $company->small_light_logo_url }}">

    <!-- style css -->
    <link href="{{asset('css/landing/landing.css')}}" rel="stylesheet" type="text/css">

    <!-- unicons Icons css -->
    <link href="{{asset('css/landing/line.css')}}" rel="stylesheet" type="text/css">
    <style>
        .bg-custom {
            background-image: url('{{ $company->login_image_url }}');

        }
    </style>
</head>

<body>
    <section class="relative flex justify-center items-center h-screen bg-center bg-no-repeat bg-cover bg-custom">
        <div class="absolute inset-0 w-full h-full bg-black/80"></div>
        <!-- end backdrop -->
        <div class="container">
            <div class="">
                <div class="text-center text-white relative py-9">
                    <a href="index.html" class="flex items-center justify-center mb-10">
                        <img src="{{$company->dark_logo_url}}" alt="" class="h-16" style="height: 100%; width:15%">
                    </a>
                    <div class="max-w-3xl mx-auto">
                        <h2 class="text-4xl font-bold capitalize mb-4">
                            Bienvenido a Nuestro Portal de Servicio...</h2>
                        <p class="text-base font-medium text-gray-300">
                            Gracias por utilizar nuestro servicio de atención al cliente.</p>
                    </div>

                    <div class="flex justify-center">
                        <div class="relative mt-4">
                            <ul class="flex flex-wrap items-center gap-6 font-maven">
                                <li>
                                    <a href="https://desarrollocr.ucontactcloud.com/webchatclient/#/?ip=desarrollocr.ucontactcloud.com&campaign=Click2CallUC&name=Cick2Call&mail=soporte@ucbusinesscr.com&initialMessage=Cick2Call"
                                        target="_blank"
                                        class="inline-flex items-center justify-center px-6 py-2 backdrop-blur-2xl bg-white/20 rounded-lg transition-all duration-500 group hover:bg-primary/20 hover:text-primary mt-5"
                                        data-hs-overlay="#hs-vertically-centered-modal">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="18" width="18"
                                            viewBox="0 0 512 512">
                                            <!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                            <path fill="#ffffff"
                                                d="M280 0C408.1 0 512 103.9 512 232c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-101.6-82.4-184-184-184c-13.3 0-24-10.7-24-24s10.7-24 24-24zm8 192a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm-32-72c0-13.3 10.7-24 24-24c75.1 0 136 60.9 136 136c0 13.3-10.7 24-24 24s-24-10.7-24-24c0-48.6-39.4-88-88-88c-13.3 0-24-10.7-24-24zM117.5 1.4c19.4-5.3 39.7 4.6 47.4 23.2l40 96c6.8 16.3 2.1 35.2-11.6 46.3L144 207.3c33.3 70.4 90.3 127.4 160.7 160.7L345 318.7c11.2-13.7 30-18.4 46.3-11.6l96 40c18.6 7.7 28.5 28 23.2 47.4l-24 88C481.8 499.9 466 512 448 512C200.6 512 0 311.4 0 64C0 46 12.1 30.2 29.5 25.4l88-24z" />
                                        </svg>
                                        &nbsp;Click2Call
                                    </a>
                                </li>
                                <li>
                                    <a href="https://desarrollocr.ucontactcloud.com/webchatclient/#/?ip=desarrollocr.ucontactcloud.com&campaign=videollamadaUC&name=VideoLlamada&mail=soporte@ucbusinesscr.com&initialMessage=VideoLlamada"
                                        target="_blank"
                                        class="inline-flex items-center justify-center px-6 py-2 backdrop-blur-2xl bg-white/20 rounded-lg transition-all duration-500 group hover:bg-primary/20 hover:text-primary mt-5"
                                        data-hs-overlay="#hs-vertically-centered-modal">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="18" width="18"
                                            viewBox="0 0 576 512">
                                            <!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                            <path fill="#ffffff"
                                                d="M64 0C28.7 0 0 28.7 0 64L0 352c0 35.3 28.7 64 64 64l176 0-10.7 32L160 448c-17.7 0-32 14.3-32 32s14.3 32 32 32l256 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-69.3 0L336 416l176 0c35.3 0 64-28.7 64-64l0-288c0-35.3-28.7-64-64-64L64 0zM512 64l0 224L64 288 64 64l448 0z" />
                                        </svg>&nbsp;video llamada</a>
                                </li>
                                <li>
                                    <a href="https://desarrollocr.ucontactcloud.com/webchatclient/#/?ip=desarrollocr.ucontactcloud.com&campaign=BotWebchatUC&name=WebChat&mail=soporte@ucbusinesscr.com&initialMessage=WebchatUC"
                                        target="_blank" class="inline-flex items-center justify-center px-6 py-2 backdrop-blur-2xl
                                        bg-white/20 rounded-lg transition-all duration-500 group hover:bg-primary/20
                                        hover:text-primary mt-5" data-hs-overlay="#hs-vertically-centered-modal">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="18" width="18"
                                            viewBox="0 0 576 512">
                                            <!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                            <path fill="#ffffff"
                                                d="M284 224.8a34.1 34.1 0 1 0 34.3 34.1A34.2 34.2 0 0 0 284 224.8zm-110.5 0a34.1 34.1 0 1 0 34.3 34.1A34.2 34.2 0 0 0 173.6 224.8zm220.9 0a34.1 34.1 0 1 0 34.3 34.1A34.2 34.2 0 0 0 394.5 224.8zm153.8-55.3c-15.5-24.2-37.3-45.6-64.7-63.6-52.9-34.8-122.4-54-195.7-54a406 406 0 0 0 -72 6.4 238.5 238.5 0 0 0 -49.5-36.6C99.7-11.7 40.9 .7 11.1 11.4A14.3 14.3 0 0 0 5.6 34.8C26.5 56.5 61.2 99.3 52.7 138.3c-33.1 33.9-51.1 74.8-51.1 117.3 0 43.4 18 84.2 51.1 118.1 8.5 39-26.2 81.8-47.1 103.5a14.3 14.3 0 0 0 5.6 23.3c29.7 10.7 88.5 23.1 155.3-10.2a238.7 238.7 0 0 0 49.5-36.6A406 406 0 0 0 288 460.1c73.3 0 142.8-19.2 195.7-54 27.4-18 49.1-39.4 64.7-63.6 17.3-26.9 26.1-55.9 26.1-86.1C574.4 225.4 565.6 196.4 548.3 169.5zM285 409.9a345.7 345.7 0 0 1 -89.4-11.5l-20.1 19.4a184.4 184.4 0 0 1 -37.1 27.6 145.8 145.8 0 0 1 -52.5 14.9c1-1.8 1.9-3.6 2.8-5.4q30.3-55.7 16.3-100.1c-33-26-52.8-59.2-52.8-95.4 0-83.1 104.3-150.5 232.8-150.5s232.9 67.4 232.9 150.5C517.9 342.5 413.6 409.9 285 409.9z" />
                                        </svg>&nbsp;
                                        Webchat</a>
                                </li>

                            </ul><!-- social end -->

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- contact modal start -->
        <div id="hs-vertically-centered-modal"
            class="hs-overlay hidden w-full h-full fixed top-0 left-0 z-[60] overflow-x-hidden overflow-y-auto">
            <div
                class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-screen-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
                <div class="flex flex-col bg-white shadow-sm rounded-lg w-full relative">
                    <div class="flex justify-between items-center ">
                        <div class="overflow-y-auto w-full p-6">
                            <div class="">
                                <h4 class="text-2xl/tight text-gray-800 font-bold mb-2">Contact us</h4>
                                <p class="text-base font-medium text-gray-500 capitalize mb-6">Our design projects are
                                    fresh and simple and will benefit your business greatly. Learn more about our work!
                                </p>
                            </div>

                            <form>
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="mb-4">
                                        <label class="block text-base/normal font-semibold text-gray-600 mb-2"
                                            for="forContactName">Name <span
                                                class="text-sm text-primary">*</span></label>
                                        <input id="forContactName"
                                            class="block w-full rounded-md py-1.5 px-3 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                            type="text" placeholder="Your Name">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-base/normal font-semibold text-gray-600 mb-2"
                                            for="forContactLastName">Last Name <span
                                                class="text-sm text-primary">*</span></label>
                                        <input id="forContactLastName"
                                            class="block w-full rounded-md py-1.5 px-3 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                            type="text" placeholder="Your Last Name">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-base/normal font-semibold text-gray-600 mb-2"
                                            for="forContactEmail">Email <span
                                                class="text-sm text-primary">*</span></label>
                                        <input id="forContactEmail"
                                            class="block w-full rounded-md py-1.5 px-3 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                            type="email" placeholder="Your Email">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-base/normal font-semibold text-gray-600 mb-2"
                                            for="forContactSubject">Subject <span
                                                class="text-sm text-primary">*</span></label>
                                        <input id="forContactSubject"
                                            class="block w-full rounded-md py-1.5 px-3 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                            type="text" placeholder="Your Subject">
                                    </div>
                                    <div class="lg:col-span-2 mb-4">
                                        <label class="block text-base/normal font-semibold text-gray-600 mb-2"
                                            for="forContactComments">Comments <span
                                                class="text-sm text-primary">*</span></label>
                                        <textarea id="forContactComments"
                                            class="block w-full rounded-md py-1.5 px-3 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                            rows="4" type="text" placeholder="Enter Your Message"></textarea>
                                    </div>
                                </div>
                                <div class="">
                                    <a href="#"
                                        class="inline-flex items-center justify-center border font-semibold border-primary text-primary px-4 py-2 transition-all duration-200 rounded-md hover:bg-primary hover:text-white">Send
                                        Message <i class="uil uil-message ms-2"></i></a>
                                </div>
                            </form><!-- end form -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- contact modal end -->
    </section>
</body>

</html>