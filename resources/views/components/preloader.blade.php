<style>

    #id1 {
        animation: ani1 -0.5s linear 1s infinite alternate;
    }

    #id2 {
        animation: ani2 -0.5s linear 2s infinite alternate;
    }

    #id3 {
        animation: ani3 -0.5s linear 2s infinite alternate;
    }

    @keyframes ani1 {
        0% {
            opacity: 0;
            fill: #fff;
        }

        100% {
            opacity: 1;
        }
    }

    @keyframes ani2 {
        0% {
            opacity: 1;
        }

        100% {
            opacity: 0;
            fill: #fff;
        }
    }

    @keyframes ani3 {
        0% {
            opacity: 1;
        }

        100% {
            opacity: 0;
            fill: #fff;
        }
    }

    .preloader {
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 200;
    }

    .preloader {
        width: 400px !important;
        height: 400px !important;
    }

    @media only screen and (max-width: 980px) {
        .preloader {
            height: 200px !important;
            width: 200px !important;
        }
    }
</style>
<div class="preloader-wrapper">
    <div class="preloader">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" style="enable-background:new 0 0 1920 1080;" xml:space="preserve" viewBox="613.63 452 692.73 176">
            <style type="text/css">
                .st0{fill:url(#SVGID_1_);}
                .st1{fill:url(#SVGID_00000094585197760877431550000003770138108059616904_);}
                .st2{fill:#9B9B9B;}
            </style>
            <g>
                <g id="id2">
                    <linearGradient id="SVGID_1_" gradientUnits="userSpaceOnUse" x1="639.9316" y1="478.3102" x2="760.7034" y2="599.082">
                        <stop offset="0" style="stop-color:#FF9800"/>
                        <stop offset="1" style="stop-color:#FF6700"/>
                    </linearGradient>
                    <path class="st0" d="M789.63,540c0,20.77-7.24,39.86-19.29,54.91l-15.64-15.77c8.07-10.89,12.87-24.33,12.94-38.87    c0.13-36.39-29.35-66.11-65.74-66.26c-36.39-0.15-66.13,29.35-66.26,65.74c-0.15,36.39,29.35,66.11,65.74,66.26    c13.93,0.07,26.84-4.22,37.53-11.57l15.66,15.8C739.82,621.4,721.47,628,701.63,628c-48.53,0-88-39.47-88-88    c0-48.51,39.47-88,88-88C750.14,452,789.63,491.49,789.63,540z"/>

                    <linearGradient id="SVGID_00000173158714271730211820000014876640185369202090_" gradientUnits="userSpaceOnUse" x1="670.7742" y1="509.1541" x2="731.3524" y2="569.7322">
                        <stop offset="0" style="stop-color:#FF9800"/>
                        <stop offset="1" style="stop-color:#FF6700"/>
                    </linearGradient>
                    <path style="fill:url(#SVGID_00000173158714271730211820000014876640185369202090_);" d="M657.62,540c0,24.27,19.73,44,44,44    s44-19.73,44-44c0-24.27-19.73-44-44-44S657.62,515.74,657.62,540z M701.62,562c-12.12,0-22-9.88-22-22h44    C723.62,552.12,713.74,562,701.62,562z"/>
                </g>
                <g id="id2">
                    <path id="id1" d="M906.03,460.16h16.49v63.78c0,7.04-1.18,13.19-3.54,18.44c-2.36,5.26-5.59,9.35-9.69,12.29c-4.1,2.94-8.63,5.11-13.6,6.51    c-4.97,1.4-10.39,2.1-16.27,2.1c-5.88,0-11.33-0.7-16.34-2.1c-5.01-1.4-9.6-3.57-13.74-6.51c-4.15-2.94-7.4-7.04-9.76-12.29    c-2.36-5.25-3.54-11.4-3.54-18.44v-63.78h16.49v62.91c0,16.58,8.97,24.88,26.9,24.88c17.74,0,26.61-8.29,26.61-24.88V460.16z"/>
                    <path id="id1" d="M938,510.78c0-7.04,2.68-12.24,8.03-15.62c5.35-3.37,11.88-5.06,19.6-5.06c8.97,0,17.31,1.98,25.02,5.93v13.59    c-2.89-1.93-6.61-3.54-11.14-4.84c-4.53-1.3-8.82-1.95-12.87-1.95c-3.57,0-6.61,0.6-9.11,1.81c-2.51,1.21-3.76,3.11-3.76,5.71    c0,2.41,1.45,4.41,4.34,6c2.89,1.59,6.39,2.94,10.49,4.05c4.1,1.11,8.17,2.41,12.22,3.91c4.05,1.5,7.52,3.79,10.41,6.87    c2.89,3.09,4.34,6.99,4.34,11.72c0,7.14-2.8,12.32-8.39,15.55c-5.59,3.23-12.78,4.84-21.55,4.84c-10.9,0-19.96-2.22-27.19-6.65    v-13.74c7.42,4.92,16.25,7.38,26.47,7.38c10.03,0,15.04-2.36,15.04-7.09c0-2.31-1.45-4.27-4.34-5.86    c-2.89-1.59-6.39-2.94-10.49-4.05c-4.1-1.11-8.2-2.41-12.29-3.91c-4.1-1.49-7.59-3.81-10.49-6.94    C939.44,519.29,938,515.41,938,510.78z"/>
                    <path id="id1" d="M1005.25,526.69c0-5.98,1.06-11.4,3.18-16.27c2.12-4.87,4.92-8.72,8.39-11.57c3.47-2.84,7.23-5.01,11.28-6.51    c4.05-1.49,8.15-2.24,12.29-2.24c5.3,0,10.1,0.82,14.39,2.46c4.29,1.64,7.74,3.86,10.34,6.65c2.6,2.8,4.72,6.03,6.36,9.69    c1.64,3.67,2.51,7.5,2.6,11.5c0.09,4-0.24,8.03-1.01,12.08h-52.21c0.77,5.88,3.66,10.29,8.68,13.23    c5.01,2.94,10.99,4.41,17.93,4.41c7.91,0,15.23-1.64,21.98-4.92v13.31c-6.65,3.18-14.95,4.77-24.88,4.77    c-11.28,0-20.66-3.18-28.13-9.55C1008.99,547.37,1005.25,538.36,1005.25,526.69z M1059.2,520.91c0.58-3.95-0.6-7.93-3.54-11.93    c-2.94-4-7.79-6-14.54-6c-5.5,0-10.2,1.59-14.1,4.77c-3.91,3.18-6,7.57-6.29,13.16H1059.2z"/>
                    <path id="id1" d="M1102.59,503.41c2.7-4.34,6.7-7.71,12-10.12c5.3-2.41,11.04-3.33,17.21-2.75v15.19c-6.17-0.87-11.77-0.05-16.78,2.46    c-5.02,2.51-8.58,6.22-10.7,11.14v42.09h-15.48v-69.42h13.74V503.41z"/>
                    <path id="id1" d="M1152.05,553.45c0,3.09-0.92,5.45-2.75,7.09c-1.83,1.64-4.15,2.46-6.94,2.46c-2.8,0-5.14-0.82-7.02-2.46    c-1.88-1.64-2.82-4-2.82-7.09c0-2.99,0.94-5.28,2.82-6.87c1.88-1.59,4.22-2.39,7.02-2.39c2.8,0,5.11,0.8,6.94,2.39    C1151.13,548.17,1152.05,550.46,1152.05,553.45z"/>
                    <path id="id1" d="M1217.57,491.98h15.48v69.42h-13.74v-9.55c-6.36,7.62-14.42,11.43-24.15,11.43c-8.97,0-15.98-2.6-21.04-7.81    c-5.06-5.21-7.59-12.05-7.59-20.54v-42.96h15.48v40.93c0,11.09,5.4,16.63,16.2,16.63c7.62,0,14.08-3.86,19.38-11.57V491.98z"/>
                    <path id="id1" d="M1249.53,491.98h54.38v11.57l-37.03,44.69h39.48v13.16h-58.72v-11.57l37.03-44.69h-35.15V491.98z"/>
                </g>
                <g id="id2">
                    <path id="id1" class="st2" d="M860.76,588.47h5.02v19.4c0,2.14-0.36,4.01-1.08,5.61c-0.72,1.6-1.7,2.85-2.95,3.74    c-1.25,0.9-2.63,1.56-4.14,1.98c-1.51,0.42-3.16,0.64-4.95,0.64c-1.79,0-3.45-0.21-4.97-0.64c-1.53-0.42-2.92-1.08-4.18-1.98    c-1.26-0.89-2.25-2.14-2.97-3.74c-0.72-1.6-1.08-3.47-1.08-5.61v-19.4h5.02v19.14c0,5.05,2.73,7.57,8.18,7.57    c5.4,0,8.1-2.52,8.1-7.57V588.47z"/>
                    <path id="id1" class="st2" d="M875.94,601.05c1.94-2.32,4.38-3.48,7.35-3.48c2.64,0,4.75,0.8,6.34,2.4c1.58,1.6,2.38,3.67,2.38,6.23v13.07    h-4.71v-12.45c0-1.64-0.46-2.9-1.36-3.76c-0.91-0.87-2.1-1.3-3.56-1.3c-2.32,0-4.28,1.17-5.9,3.52v13.99h-4.71v-21.12h4.18V601.05    z"/>
                    <path id="id1" class="st2" d="M902.95,591.28c0,0.91-0.27,1.61-0.81,2.09c-0.54,0.48-1.23,0.73-2.05,0.73c-0.82,0-1.51-0.24-2.07-0.73    c-0.56-0.48-0.84-1.18-0.84-2.09c0-0.88,0.28-1.56,0.84-2.05c0.56-0.48,1.25-0.73,2.07-0.73c0.82,0,1.5,0.24,2.05,0.73    C902.68,589.72,902.95,590.4,902.95,591.28z M902.43,619.27h-4.71v-21.12h4.71V619.27z"/>
                    <path id="id1" class="st2" d="M923.9,598.15h5.15l-9.06,21.12h-4.97l-9.02-21.12h5.1l6.42,16.02L923.9,598.15z"/>
                    <path id="id1" class="st2" d="M930.28,608.71c0-1.82,0.32-3.47,0.97-4.95c0.65-1.48,1.5-2.65,2.55-3.52c1.06-0.87,2.2-1.52,3.43-1.98    c1.23-0.45,2.48-0.68,3.74-0.68c1.61,0,3.07,0.25,4.38,0.75c1.3,0.5,2.35,1.17,3.15,2.02c0.79,0.85,1.44,1.83,1.94,2.95    c0.5,1.12,0.76,2.28,0.79,3.5c0.03,1.22-0.07,2.44-0.31,3.67h-15.88c0.23,1.79,1.11,3.13,2.64,4.03c1.53,0.9,3.34,1.34,5.46,1.34    c2.41,0,4.63-0.5,6.69-1.5v4.05c-2.02,0.97-4.55,1.45-7.57,1.45c-3.43,0-6.29-0.97-8.56-2.9    C931.42,615,930.28,612.26,930.28,608.71z M946.69,606.95c0.18-1.2-0.18-2.41-1.08-3.63c-0.9-1.22-2.37-1.83-4.42-1.83    c-1.67,0-3.1,0.48-4.29,1.45c-1.19,0.97-1.83,2.3-1.91,4H946.69z"/>
                    <path id="id1" class="st2" d="M959.89,601.62c0.82-1.32,2.04-2.35,3.65-3.08c1.61-0.73,3.36-1.01,5.24-0.84v4.62    c-1.88-0.26-3.58-0.01-5.1,0.75c-1.53,0.76-2.61,1.89-3.26,3.39v12.8h-4.71v-21.12h4.18V601.62z"/>
                    <path id="id1" class="st2" d="M971.24,603.87c0-2.14,0.81-3.73,2.44-4.75c1.63-1.03,3.62-1.54,5.96-1.54c2.73,0,5.26,0.6,7.61,1.8v4.14    c-0.88-0.59-2.01-1.08-3.39-1.47s-2.68-0.59-3.92-0.59c-1.08,0-2.01,0.18-2.77,0.55c-0.76,0.37-1.14,0.95-1.14,1.74    c0,0.73,0.44,1.34,1.32,1.83s1.94,0.9,3.19,1.23c1.25,0.34,2.49,0.73,3.72,1.19c1.23,0.46,2.29,1.15,3.17,2.09    c0.88,0.94,1.32,2.13,1.32,3.56c0,2.17-0.85,3.75-2.55,4.73s-3.89,1.47-6.56,1.47c-3.32,0-6.07-0.67-8.27-2.02v-4.18    c2.26,1.5,4.94,2.24,8.05,2.24c3.05,0,4.58-0.72,4.58-2.16c0-0.7-0.44-1.3-1.32-1.78c-0.88-0.48-1.94-0.89-3.19-1.23    c-1.25-0.34-2.49-0.73-3.74-1.19c-1.25-0.45-2.31-1.16-3.19-2.11C971.68,606.46,971.24,605.28,971.24,603.87z"/>
                    <path id="id1" class="st2" d="M1011.02,614.38c0,1.03,0.51,1.54,1.54,1.54c0.35,0,0.82-0.09,1.41-0.26v3.39c-0.88,0.35-1.88,0.53-2.99,0.53    c-2.11,0-3.52-0.82-4.22-2.46c-2.26,1.82-4.91,2.73-7.96,2.73c-2.05,0-3.8-0.56-5.24-1.69s-2.16-2.72-2.16-4.77    c0-2.46,0.92-4.26,2.77-5.39c1.85-1.13,4.06-1.69,6.64-1.69c1.7,0,3.53,0.21,5.5,0.62v-0.75c0-1.35-0.48-2.43-1.45-3.26    c-0.97-0.82-2.33-1.23-4.09-1.23c-2.61,0-5.15,0.54-7.61,1.63v-4.31c2.76-0.94,5.46-1.41,8.1-1.41c3.08,0,5.48,0.76,7.19,2.29    c1.72,1.53,2.57,3.84,2.57,6.95V614.38z M1001.73,609.59c-1.56,0-2.86,0.28-3.92,0.84c-1.06,0.56-1.58,1.44-1.58,2.64    c0,1,0.38,1.77,1.14,2.31c0.76,0.54,1.7,0.81,2.82,0.81c2.4,0,4.44-0.84,6.12-2.51v-3.56    C1004.61,609.76,1003.08,609.59,1001.73,609.59z"/>
                    <path id="id1" class="st2" d="M1017.31,587.15h4.71v32.12h-4.71V587.15z"/>
                    <path id="id1" class="st2" d="M1037.28,603.87c0-2.14,0.81-3.73,2.44-4.75c1.63-1.03,3.62-1.54,5.96-1.54c2.73,0,5.26,0.6,7.61,1.8v4.14    c-0.88-0.59-2.01-1.08-3.39-1.47s-2.68-0.59-3.92-0.59c-1.08,0-2.01,0.18-2.77,0.55c-0.76,0.37-1.14,0.95-1.14,1.74    c0,0.73,0.44,1.34,1.32,1.83s1.94,0.9,3.19,1.23c1.25,0.34,2.49,0.73,3.72,1.19c1.23,0.46,2.29,1.15,3.17,2.09    c0.88,0.94,1.32,2.13,1.32,3.56c0,2.17-0.85,3.75-2.55,4.73s-3.89,1.47-6.56,1.47c-3.32,0-6.07-0.67-8.27-2.02v-4.18    c2.26,1.5,4.94,2.24,8.05,2.24c3.05,0,4.58-0.72,4.58-2.16c0-0.7-0.44-1.3-1.32-1.78c-0.88-0.48-1.94-0.89-3.19-1.23    c-1.25-0.34-2.49-0.73-3.74-1.19c-1.25-0.45-2.31-1.16-3.19-2.11C1037.72,606.46,1037.28,605.28,1037.28,603.87z"/>
                    <path id="id1" class="st2" d="M1057.74,608.71c0-1.82,0.32-3.47,0.97-4.95c0.65-1.48,1.5-2.65,2.55-3.52c1.06-0.87,2.2-1.52,3.43-1.98    c1.23-0.45,2.48-0.68,3.74-0.68c1.61,0,3.07,0.25,4.38,0.75c1.3,0.5,2.35,1.17,3.15,2.02c0.79,0.85,1.44,1.83,1.94,2.95    c0.5,1.12,0.76,2.28,0.79,3.5c0.03,1.22-0.07,2.44-0.31,3.67h-15.88c0.23,1.79,1.11,3.13,2.64,4.03c1.53,0.9,3.34,1.34,5.46,1.34    c2.41,0,4.63-0.5,6.69-1.5v4.05c-2.02,0.97-4.55,1.45-7.57,1.45c-3.43,0-6.29-0.97-8.56-2.9    C1058.88,615,1057.74,612.26,1057.74,608.71z M1074.16,606.95c0.18-1.2-0.18-2.41-1.08-3.63c-0.9-1.22-2.37-1.83-4.42-1.83    c-1.67,0-3.1,0.48-4.29,1.45c-1.19,0.97-1.83,2.3-1.91,4H1074.16z"/>
                    <path id="id1" class="st2" d="M1087.36,601.62c0.82-1.32,2.04-2.35,3.65-3.08c1.61-0.73,3.36-1.01,5.24-0.84v4.62    c-1.88-0.26-3.58-0.01-5.1,0.75c-1.53,0.76-2.61,1.89-3.26,3.39v12.8h-4.71v-21.12h4.18V601.62z"/>
                    <path id="id1" class="st2" d="M1115.29,598.15h5.15l-9.06,21.12h-4.97l-9.02-21.12h5.1l6.42,16.02L1115.29,598.15z"/>
                    <path id="id1" class="st2" d="M1129.2,591.28c0,0.91-0.27,1.61-0.81,2.09c-0.54,0.48-1.23,0.73-2.05,0.73c-0.82,0-1.51-0.24-2.07-0.73    c-0.56-0.48-0.84-1.18-0.84-2.09c0-0.88,0.28-1.56,0.84-2.05c0.56-0.48,1.25-0.73,2.07-0.73c0.82,0,1.5,0.24,2.05,0.73    C1128.93,589.72,1129.2,590.4,1129.2,591.28z M1128.67,619.27h-4.71v-21.12h4.71V619.27z"/>
                    <path id="id1" class="st2" d="M1133.33,608.88c0-3.67,1.16-6.47,3.48-8.4s5.04-2.9,8.18-2.9c2.52,0,4.69,0.54,6.51,1.63v4.31    c-1.64-1.17-3.59-1.76-5.85-1.76c-2.08,0-3.86,0.58-5.32,1.74c-1.47,1.16-2.2,2.9-2.2,5.21c0,2.26,0.73,3.98,2.18,5.17    c1.45,1.19,3.21,1.78,5.26,1.78c2.23,0,4.28-0.54,6.16-1.63v4.36c-2.02,0.97-4.36,1.45-7,1.45c-3.08,0-5.75-0.95-8.01-2.84    S1133.33,612.4,1133.33,608.88z"/>
                    <path id="id1" class="st2" d="M1154.76,608.71c0-1.82,0.32-3.47,0.97-4.95c0.65-1.48,1.5-2.65,2.55-3.52c1.06-0.87,2.2-1.52,3.43-1.98    c1.23-0.45,2.48-0.68,3.74-0.68c1.61,0,3.07,0.25,4.38,0.75c1.3,0.5,2.35,1.17,3.15,2.02c0.79,0.85,1.44,1.83,1.94,2.95    c0.5,1.12,0.76,2.28,0.79,3.5c0.03,1.22-0.07,2.44-0.31,3.67h-15.88c0.23,1.79,1.11,3.13,2.64,4.03c1.53,0.9,3.34,1.34,5.46,1.34    c2.41,0,4.63-0.5,6.69-1.5v4.05c-2.02,0.97-4.55,1.45-7.57,1.45c-3.43,0-6.29-0.97-8.56-2.9    C1155.9,615,1154.76,612.26,1154.76,608.71z M1171.17,606.95c0.18-1.2-0.18-2.41-1.08-3.63c-0.9-1.22-2.37-1.83-4.42-1.83    c-1.67,0-3.1,0.48-4.29,1.45c-1.19,0.97-1.83,2.3-1.91,4H1171.17z"/>
                    <path id="id1" class="st2" d="M1179.09,603.87c0-2.14,0.81-3.73,2.44-4.75c1.63-1.03,3.62-1.54,5.96-1.54c2.73,0,5.26,0.6,7.61,1.8v4.14    c-0.88-0.59-2.01-1.08-3.39-1.47s-2.68-0.59-3.92-0.59c-1.08,0-2.01,0.18-2.77,0.55c-0.76,0.37-1.14,0.95-1.14,1.74    c0,0.73,0.44,1.34,1.32,1.83s1.94,0.9,3.19,1.23c1.25,0.34,2.49,0.73,3.72,1.19c1.23,0.46,2.29,1.15,3.17,2.09    c0.88,0.94,1.32,2.13,1.32,3.56c0,2.17-0.85,3.75-2.55,4.73s-3.89,1.47-6.56,1.47c-3.32,0-6.07-0.67-8.27-2.02v-4.18    c2.26,1.5,4.94,2.24,8.05,2.24c3.05,0,4.58-0.72,4.58-2.16c0-0.7-0.44-1.3-1.32-1.78c-0.88-0.48-1.94-0.89-3.19-1.23    c-1.25-0.34-2.49-0.73-3.74-1.19c-1.25-0.45-2.31-1.16-3.19-2.11C1179.53,606.46,1179.09,605.28,1179.09,603.87z"/>
                </g>
            </g>
        </svg>
    </div>
</div>
