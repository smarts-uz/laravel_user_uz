<style>

    .st0 {
        fill: #DB2091;
    }

    .st1 {
        fill: #009FDC;
    }

    .st2 {
        fill: #FF932B;
    }

    .st3 {
        display: none;
    }

    .st4 {
        display: inline;
    }


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
        <svg id="_Слой_2" data-name="Слой 2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 386.13 495.55">
            <defs>
                <style>
                    .cls-1 {
                        fill: #171717;
                    }

                    .cls-2 {
                        fill: url(#_Безымянный_градиент_29);
                        fill-rule: evenodd;
                    }
                </style>
                <linearGradient id="_Безымянный_градиент_29" data-name="Безымянный градиент 29" x1="193.07" y1="392.67" x2="193.07" y2="0" gradientUnits="userSpaceOnUse">
                    <stop offset=".26" stop-color="#f1552b"/>
                    <stop offset=".29" stop-color="#f26929"/>
                    <stop offset=".33" stop-color="#f37127"/>
                    <stop offset=".44" stop-color="#f58522"/>
                    <stop offset=".57" stop-color="#f7931f"/>
                    <stop offset=".73" stop-color="#f89b1d"/>
                    <stop offset="1" stop-color="#f99e1d"/>
                </linearGradient>
            </defs>
            {{--USER.UZ--}}
            <g id="id2" data-name="Слой 4">
                <path class="cls-1 st2" d="M65.18,450.08c-4.92,0-9.32-1.09-13.22-3.26-3.83-2.24-6.87-5.27-9.1-9.1-2.24-3.89-3.35-8.3-3.35-13.22v-41.86h9v41.67c0,3.51,.73,6.55,2.2,9.1,1.47,2.55,3.45,4.54,5.94,5.94,2.55,1.4,5.4,2.11,8.53,2.11s6.1-.7,8.53-2.11c2.49-1.4,4.44-3.39,5.84-5.94,1.47-2.55,2.2-5.56,2.2-9v-41.77h9.1v41.96c0,4.92-1.12,9.29-3.35,13.12-2.24,3.83-5.27,6.86-9.1,9.1-3.83,2.17-8.24,3.26-13.22,3.26Zm58.22,0c-5.24,0-9.71-.96-13.41-2.87-3.7-1.92-6.99-4.69-9.87-8.33l6.13-6.13c2.11,2.94,4.53,5.17,7.28,6.71,2.75,1.47,6.13,2.2,10.15,2.2s7.12-.86,9.48-2.59c2.43-1.72,3.64-4.09,3.64-7.09,0-2.49-.57-4.5-1.72-6.03-1.15-1.53-2.71-2.78-4.69-3.74-1.92-1.02-4.02-1.92-6.32-2.68-2.3-.83-4.6-1.69-6.9-2.59-2.3-.96-4.41-2.11-6.32-3.45-1.92-1.4-3.48-3.22-4.69-5.46-1.15-2.24-1.72-5.04-1.72-8.43,0-3.77,.89-6.96,2.68-9.58,1.85-2.68,4.34-4.73,7.47-6.13,3.19-1.47,6.77-2.2,10.73-2.2,4.34,0,8.24,.86,11.69,2.59,3.45,1.66,6.26,3.86,8.43,6.61l-6.13,6.13c-1.98-2.3-4.12-4.02-6.42-5.17-2.23-1.15-4.82-1.72-7.76-1.72-3.58,0-6.42,.8-8.53,2.39-2.11,1.53-3.16,3.7-3.16,6.51,0,2.24,.57,4.06,1.72,5.46,1.21,1.34,2.78,2.49,4.69,3.45,1.92,.96,4.02,1.85,6.32,2.68,2.36,.77,4.69,1.63,6.99,2.59,2.3,.96,4.41,2.17,6.32,3.64,1.92,1.47,3.45,3.39,4.6,5.75,1.21,2.3,1.82,5.21,1.82,8.72,0,5.88-2.04,10.47-6.13,13.79-4.02,3.32-9.48,4.98-16.38,4.98Zm53.53,0c-4.53,0-8.62-1.02-12.26-3.07-3.64-2.11-6.51-4.95-8.62-8.53-2.11-3.58-3.16-7.63-3.16-12.17s1.02-8.49,3.07-12.07c2.11-3.58,4.92-6.38,8.43-8.43,3.58-2.11,7.57-3.16,11.97-3.16s7.92,.96,11.11,2.87c3.26,1.92,5.78,4.56,7.57,7.95,1.85,3.39,2.78,7.22,2.78,11.5,0,.64-.03,1.34-.1,2.11-.06,.7-.19,1.53-.38,2.49h-38.41v-7.18h33.81l-3.16,2.78c0-3.07-.54-5.65-1.63-7.76-1.09-2.17-2.62-3.83-4.6-4.98-1.98-1.21-4.37-1.82-7.18-1.82s-5.52,.64-7.76,1.92c-2.24,1.28-3.96,3.07-5.17,5.36-1.21,2.3-1.82,5.01-1.82,8.14s.64,6,1.92,8.43c1.28,2.36,3.1,4.21,5.46,5.56,2.36,1.28,5.08,1.92,8.14,1.92,2.55,0,4.89-.45,6.99-1.34,2.17-.9,4.02-2.24,5.56-4.02l5.56,5.65c-2.17,2.55-4.85,4.5-8.05,5.84-3.13,1.34-6.48,2.01-10.06,2.01Zm30.28-.96v-45.5h8.62v45.5h-8.62Zm8.62-25.96l-3.26-1.44c0-5.81,1.34-10.44,4.02-13.89,2.68-3.45,6.55-5.17,11.59-5.17,2.3,0,4.37,.42,6.23,1.25,1.85,.77,3.58,2.07,5.17,3.93l-5.65,5.84c-.96-1.02-2.01-1.76-3.16-2.2-1.15-.45-2.49-.67-4.02-.67-3.19,0-5.81,1.02-7.86,3.07-2.04,2.04-3.07,5.14-3.07,9.29Zm31.69,26.92c-1.79,0-3.29-.61-4.5-1.82-1.15-1.28-1.72-2.78-1.72-4.5s.57-3.26,1.72-4.41c1.21-1.21,2.71-1.82,4.5-1.82s3.26,.61,4.41,1.82c1.21,1.15,1.82,2.62,1.82,4.41s-.61,3.22-1.82,4.5c-1.15,1.21-2.62,1.82-4.41,1.82Zm38.51,0c-3.7,0-7.06-.83-10.06-2.49-2.94-1.72-5.24-4.09-6.9-7.09-1.66-3-2.49-6.48-2.49-10.44v-26.44h8.62v26.06c0,2.49,.41,4.66,1.25,6.51,.89,1.79,2.17,3.16,3.83,4.12,1.66,.96,3.61,1.44,5.84,1.44,3.38,0,6.03-1.05,7.95-3.16,1.92-2.17,2.87-5.14,2.87-8.91v-26.06h8.62v26.44c0,3.96-.83,7.44-2.49,10.44-1.66,3-3.96,5.36-6.9,7.09-2.87,1.66-6.26,2.49-10.15,2.49Zm27.46-5.65l27.01-36.11h10.15l-27.01,36.11h-10.15Zm0,4.69v-4.69l7.66-3.16h28.93v7.86h-36.59Zm1.63-37.65v-7.86h35.54v4.69l-7.95,3.16h-27.59Z"/>
            </g>
            {{--Universal services--}}
            <g id="id2" data-name="Слой 3">
                <path class="cls-1 st2" d="M47.71,495.55c-1.77,0-3.35-.39-4.75-1.18-1.38-.78-2.47-1.87-3.28-3.24-.79-1.4-1.18-2.97-1.18-4.72v-16.36h1.92v16.21c0,1.5,.32,2.81,.96,3.94,.64,1.11,1.51,1.96,2.62,2.58,1.11,.59,2.35,.88,3.72,.88s2.64-.29,3.72-.88c1.08-.62,1.94-1.47,2.58-2.58,.64-1.13,.96-2.43,.96-3.91v-16.25h1.95v16.4c0,1.74-.41,3.3-1.22,4.68s-1.9,2.46-3.28,3.24-2.95,1.18-4.72,1.18Zm27.96-.37v-10.17c0-1.67-.49-3.02-1.47-4.05-.98-1.03-2.28-1.55-3.91-1.55-1.11,0-2.09,.25-2.95,.74-.84,.47-1.5,1.12-1.99,1.95-.47,.84-.7,1.81-.7,2.91l-.96-.55c0-1.3,.31-2.46,.92-3.46,.61-1.03,1.45-1.84,2.51-2.43,1.06-.59,2.26-.88,3.61-.88s2.53,.32,3.54,.96c1.03,.62,1.83,1.46,2.39,2.54,.56,1.08,.85,2.28,.85,3.61v10.39h-1.84Zm-12.9,0v-17.13h1.88v17.13h-1.88Zm19.97,0v-17.13h1.84v17.13h-1.84Zm.92-21.11c-.42,0-.77-.15-1.07-.44-.27-.32-.41-.69-.41-1.11s.14-.76,.41-1.03c.29-.29,.65-.44,1.07-.44s.77,.15,1.07,.44c.29,.27,.44,.62,.44,1.03s-.15,.78-.44,1.11c-.29,.29-.65,.44-1.07,.44Zm12.64,21.11l-8-17.13h2.06l7.07,15.55h-1.14l7.11-15.55h2.03l-8.03,17.13h-1.11Zm19.83,.37c-1.69,0-3.23-.39-4.61-1.18-1.35-.78-2.42-1.85-3.21-3.21-.79-1.35-1.18-2.87-1.18-4.57s.38-3.21,1.14-4.53c.76-1.35,1.81-2.42,3.13-3.21,1.35-.78,2.85-1.18,4.49-1.18s2.95,.36,4.13,1.07c1.2,.69,2.14,1.65,2.8,2.87,.69,1.23,1.03,2.64,1.03,4.24,0,.15-.01,.34-.04,.59-.02,.22-.06,.5-.11,.85h-15.25v-1.62h14.18l-.63,.52c.05-1.35-.17-2.53-.66-3.54-.49-1.03-1.2-1.83-2.14-2.39-.93-.56-2.05-.85-3.35-.85s-2.54,.31-3.57,.92c-1.03,.59-1.84,1.41-2.43,2.47-.56,1.06-.85,2.28-.85,3.68,0,1.47,.29,2.76,.88,3.87,.61,1.08,1.46,1.93,2.54,2.54,1.08,.59,2.35,.88,3.79,.88,1.11,0,2.12-.2,3.06-.59,.96-.42,1.76-1.03,2.39-1.84l1.25,1.22c-.79,.93-1.78,1.67-2.98,2.21-1.18,.52-2.46,.77-3.83,.77Zm12.01-.37v-17.13h1.88v17.13h-1.88Zm1.88-10.24l-.92-.33c0-2.18,.55-3.88,1.66-5.08,1.11-1.23,2.55-1.84,4.35-1.84,.84,0,1.61,.15,2.32,.44,.74,.29,1.41,.77,2.03,1.44l-1.25,1.33c-.47-.52-.97-.88-1.51-1.11-.54-.22-1.18-.33-1.92-.33-1.4,0-2.54,.48-3.43,1.44-.88,.93-1.33,2.28-1.33,4.05Zm17.24,10.61c-.93,0-1.82-.12-2.65-.37-.81-.27-1.56-.64-2.25-1.11-.66-.47-1.25-1.01-1.77-1.62l1.25-1.25c.69,.91,1.49,1.58,2.39,2.03,.93,.42,1.95,.63,3.06,.63,1.28,0,2.28-.26,3.02-.77,.74-.52,1.11-1.24,1.11-2.17,0-.84-.23-1.48-.7-1.95-.47-.49-1.08-.87-1.84-1.14-.74-.27-1.52-.53-2.36-.77-.81-.25-1.6-.54-2.36-.88-.74-.34-1.34-.82-1.81-1.44-.47-.62-.7-1.44-.7-2.47,0-.91,.23-1.71,.7-2.39,.47-.69,1.12-1.22,1.95-1.58,.86-.39,1.87-.59,3.02-.59,1.28,0,2.42,.22,3.43,.66,1.03,.44,1.85,1.09,2.47,1.95l-1.22,1.25c-.49-.69-1.15-1.22-1.99-1.58-.83-.39-1.77-.59-2.8-.59-1.23,0-2.16,.26-2.8,.77-.64,.52-.96,1.18-.96,1.99,0,.76,.23,1.36,.7,1.81,.47,.44,1.07,.78,1.81,1.03,.76,.25,1.55,.49,2.36,.74,.84,.22,1.62,.52,2.36,.88,.76,.37,1.38,.87,1.84,1.51,.47,.64,.7,1.51,.7,2.62,0,1.47-.54,2.65-1.62,3.54-1.08,.86-2.53,1.29-4.35,1.29Zm17.94,0c-1.65,0-3.13-.39-4.46-1.18-1.3-.78-2.33-1.84-3.09-3.17-.76-1.35-1.14-2.87-1.14-4.57s.38-3.18,1.14-4.53c.76-1.35,1.79-2.42,3.09-3.21,1.3-.81,2.76-1.22,4.38-1.22,1.33,0,2.51,.26,3.54,.77,1.06,.49,1.92,1.19,2.58,2.1,.69,.91,1.13,1.99,1.33,3.24v5.6c-.22,1.25-.68,2.35-1.36,3.28-.66,.91-1.51,1.62-2.54,2.14-1.01,.49-2.16,.74-3.46,.74Zm.26-1.77c1.97,0,3.52-.65,4.68-1.95,1.18-1.33,1.77-3.06,1.77-5.19,0-1.45-.27-2.7-.81-3.76-.52-1.08-1.26-1.92-2.25-2.51-.98-.62-2.12-.92-3.43-.92s-2.55,.31-3.61,.92c-1.03,.62-1.85,1.46-2.47,2.54-.59,1.08-.88,2.31-.88,3.68s.29,2.64,.88,3.72c.61,1.08,1.45,1.93,2.51,2.54,1.06,.62,2.26,.92,3.61,.92Zm6.37,1.4v-4.61l.41-4.2-.41-4.16v-4.16h1.88v17.13h-1.88Zm7.4,0v-25.86h1.88v25.86h-1.88Zm23.24,.37c-1.97,0-3.66-.37-5.08-1.11-1.42-.76-2.65-1.81-3.68-3.13l1.36-1.33c.96,1.3,2.04,2.26,3.24,2.87,1.23,.59,2.64,.88,4.24,.88,1.72,0,3.12-.41,4.2-1.22,1.08-.81,1.62-1.94,1.62-3.39,0-1.11-.23-2-.7-2.69-.47-.69-1.09-1.25-1.88-1.69-.76-.44-1.61-.84-2.54-1.18-.91-.34-1.83-.69-2.76-1.03-.91-.37-1.74-.81-2.51-1.33-.76-.54-1.39-1.23-1.88-2.06-.47-.84-.7-1.89-.7-3.17s.31-2.38,.92-3.32c.64-.96,1.5-1.69,2.58-2.21,1.11-.52,2.37-.77,3.79-.77,1.62,0,3.07,.32,4.35,.96,1.28,.62,2.35,1.48,3.21,2.62l-1.36,1.33c-.81-1.01-1.72-1.77-2.73-2.28-.98-.54-2.15-.81-3.5-.81-1.57,0-2.85,.39-3.83,1.18-.96,.76-1.44,1.83-1.44,3.21,0,1.03,.23,1.87,.7,2.51,.47,.64,1.08,1.18,1.84,1.62,.79,.42,1.63,.78,2.54,1.11,.93,.32,1.85,.66,2.76,1.03,.91,.37,1.74,.84,2.51,1.4,.79,.54,1.41,1.24,1.88,2.1,.47,.86,.7,1.95,.7,3.28,0,2.14-.71,3.78-2.14,4.94-1.4,1.13-3.3,1.69-5.71,1.69Zm20.26,0c-1.69,0-3.23-.39-4.61-1.18-1.35-.78-2.42-1.85-3.21-3.21-.79-1.35-1.18-2.87-1.18-4.57s.38-3.21,1.14-4.53c.76-1.35,1.81-2.42,3.13-3.21,1.35-.78,2.85-1.18,4.49-1.18s2.95,.36,4.13,1.07c1.2,.69,2.14,1.65,2.8,2.87,.69,1.23,1.03,2.64,1.03,4.24,0,.15-.01,.34-.04,.59-.02,.22-.06,.5-.11,.85h-15.25v-1.62h14.18l-.63,.52c.05-1.35-.17-2.53-.66-3.54-.49-1.03-1.2-1.83-2.14-2.39-.93-.56-2.05-.85-3.35-.85s-2.54,.31-3.57,.92c-1.03,.59-1.84,1.41-2.43,2.47-.57,1.06-.85,2.28-.85,3.68,0,1.47,.29,2.76,.88,3.87,.61,1.08,1.46,1.93,2.54,2.54,1.08,.59,2.35,.88,3.79,.88,1.11,0,2.12-.2,3.06-.59,.96-.42,1.76-1.03,2.39-1.84l1.25,1.22c-.79,.93-1.78,1.67-2.98,2.21-1.18,.52-2.46,.77-3.83,.77Zm12.01-.37v-17.13h1.88v17.13h-1.88Zm1.88-10.24l-.92-.33c0-2.18,.55-3.88,1.66-5.08,1.11-1.23,2.55-1.84,4.35-1.84,.84,0,1.61,.15,2.32,.44,.74,.29,1.41,.77,2.03,1.44l-1.25,1.33c-.47-.52-.97-.88-1.51-1.11-.54-.22-1.18-.33-1.92-.33-1.4,0-2.54,.48-3.43,1.44-.88,.93-1.33,2.28-1.33,4.05Zm18.16,10.24l-8-17.13h2.06l7.07,15.55h-1.14l7.11-15.55h2.03l-8.03,17.13h-1.11Zm12.81,0v-17.13h1.84v17.13h-1.84Zm.92-21.11c-.42,0-.77-.15-1.07-.44-.27-.32-.41-.69-.41-1.11s.14-.76,.41-1.03c.29-.29,.65-.44,1.07-.44s.77,.15,1.07,.44c.29,.27,.44,.62,.44,1.03s-.15,.78-.44,1.11c-.29,.29-.65,.44-1.07,.44Zm14.3,21.48c-1.67,0-3.18-.39-4.53-1.18-1.33-.81-2.38-1.89-3.17-3.24-.79-1.37-1.18-2.9-1.18-4.57s.39-3.17,1.18-4.49c.79-1.35,1.84-2.42,3.17-3.21,1.35-.78,2.86-1.18,4.53-1.18,1.25,0,2.43,.23,3.54,.7s2.05,1.13,2.84,1.99l-1.25,1.25c-.64-.69-1.4-1.22-2.28-1.58-.86-.39-1.81-.59-2.84-.59-1.33,0-2.52,.32-3.57,.96-1.03,.62-1.85,1.45-2.47,2.51-.61,1.06-.92,2.27-.92,3.65s.31,2.6,.92,3.68c.61,1.08,1.44,1.94,2.47,2.58,1.06,.64,2.25,.96,3.57,.96,1.03,0,1.99-.18,2.87-.55,.91-.39,1.68-.95,2.32-1.66l1.25,1.25c-.81,.86-1.78,1.54-2.91,2.03-1.11,.47-2.28,.7-3.54,.7Zm17.96,0c-1.69,0-3.23-.39-4.61-1.18-1.35-.78-2.42-1.85-3.21-3.21-.79-1.35-1.18-2.87-1.18-4.57s.38-3.21,1.14-4.53c.76-1.35,1.81-2.42,3.13-3.21,1.35-.78,2.85-1.18,4.49-1.18s2.95,.36,4.13,1.07c1.2,.69,2.14,1.65,2.8,2.87,.69,1.23,1.03,2.64,1.03,4.24,0,.15-.01,.34-.04,.59-.02,.22-.06,.5-.11,.85h-15.25v-1.62h14.18l-.63,.52c.05-1.35-.17-2.53-.66-3.54-.49-1.03-1.2-1.83-2.14-2.39-.93-.56-2.05-.85-3.35-.85s-2.54,.31-3.57,.92c-1.03,.59-1.84,1.41-2.43,2.47-.56,1.06-.85,2.28-.85,3.68,0,1.47,.29,2.76,.88,3.87,.61,1.08,1.46,1.93,2.54,2.54,1.08,.59,2.35,.88,3.79,.88,1.11,0,2.12-.2,3.06-.59,.96-.42,1.76-1.03,2.39-1.84l1.25,1.22c-.79,.93-1.78,1.67-2.98,2.21-1.18,.52-2.46,.77-3.83,.77Zm17.13,0c-.93,0-1.82-.12-2.65-.37-.81-.27-1.56-.64-2.25-1.11-.66-.47-1.25-1.01-1.77-1.62l1.25-1.25c.69,.91,1.49,1.58,2.39,2.03,.93,.42,1.95,.63,3.06,.63,1.28,0,2.28-.26,3.02-.77,.74-.52,1.11-1.24,1.11-2.17,0-.84-.23-1.48-.7-1.95-.47-.49-1.08-.87-1.84-1.14-.74-.27-1.52-.53-2.36-.77-.81-.25-1.6-.54-2.36-.88-.74-.34-1.34-.82-1.81-1.44-.47-.62-.7-1.44-.7-2.47,0-.91,.23-1.71,.7-2.39,.47-.69,1.12-1.22,1.95-1.58,.86-.39,1.87-.59,3.02-.59,1.28,0,2.42,.22,3.43,.66,1.03,.44,1.85,1.09,2.47,1.95l-1.22,1.25c-.49-.69-1.15-1.22-1.99-1.58-.84-.39-1.77-.59-2.8-.59-1.23,0-2.16,.26-2.8,.77-.64,.52-.96,1.18-.96,1.99,0,.76,.23,1.36,.7,1.81,.47,.44,1.07,.78,1.81,1.03,.76,.25,1.55,.49,2.36,.74,.83,.22,1.62,.52,2.36,.88,.76,.37,1.38,.87,1.84,1.51,.47,.64,.7,1.51,.7,2.62,0,1.47-.54,2.65-1.62,3.54-1.08,.86-2.53,1.29-4.35,1.29Z"/>
            </g>
            <path id="id1" class="cls-2 st1" d="M307.9,161.99h45.31c2.56,6.23,8.7,10.63,15.83,10.63,9.43,0,17.09-7.65,17.09-17.09s-7.65-17.09-17.09-17.09c-7.13,0-13.27,4.39-15.83,10.63h-40.53v-40.2c6.23-2.56,10.63-8.7,10.63-15.83,0-9.43-7.65-17.09-17.09-17.09s-17.09,7.65-17.09,17.09c0,7.13,4.39,13.27,10.63,15.83v43.06l-86.5,66.13v122.32c-1.87-.14-3.74-.27-5.63-.39-2.67-.17-5.32-.31-7.97-.44V200.93l52.36-40.03c2.64,1.66,5.76,2.62,9.13,2.62,9.43,0,17.09-7.65,17.09-17.09s-7.65-17.09-17.09-17.09-17.09,7.65-17.09,17.09c0,1.35,.17,2.66,.45,3.93l-44.85,34.3v-27.92l66.9-51.75V55.98c6.79-2.28,11.67-8.68,11.67-16.22,0-9.43-7.65-17.09-17.09-17.09s-17.09,7.65-17.09,17.09c0,6.75,3.89,12.57,9.58,15.36v43.5l-20.08,15.54v-16.78c6.23-2.55,10.62-8.67,10.62-15.81,0-9.44-7.65-17.09-17.09-17.09s-17.09,7.65-17.09,17.09c0,7.15,4.39,13.27,10.63,15.82v26.79l-20.95,16.21V32.95c6.29-2.52,10.73-8.67,10.73-15.86,0-9.44-7.65-17.09-17.09-17.09s-17.09,7.65-17.09,17.09c0,7.11,4.34,13.2,10.51,15.78v108.06l-20.89-16.15v-27.23c6.47-2.43,11.08-8.66,11.08-15.98,0-9.44-7.65-17.09-17.09-17.09s-17.09,7.65-17.09,17.09c0,6.97,4.18,12.96,10.17,15.62v17.59l-20.42-15.79V55.46c5.66-2.78,9.57-8.59,9.57-15.33,0-9.44-7.65-17.09-17.09-17.09s-17.09,7.65-17.09,17.09c0,7.54,4.89,13.94,11.68,16.2v48.99h0s0,.02,0,.02h0s.01,.01,.01,.01l67.16,51.93v27.94l-45.14-34.5c.29-1.26,.47-2.56,.47-3.9,0-9.44-7.65-17.09-17.09-17.09s-17.09,7.65-17.09,17.09,7.65,17.09,17.09,17.09c3.35,0,6.47-.98,9.11-2.65l52.65,40.25v137.57c-4.67-.11-9.3-.17-13.86-.16v-120.5h0s0-.01,0-.01h0s0,0,0,0l-86.49-66.12v-1.62h0v-41.45c6.23-2.55,10.62-8.67,10.62-15.81,0-9.44-7.65-17.09-17.09-17.09s-17.09,7.65-17.09,17.09c0,7.15,4.39,13.27,10.62,15.82v40.21H32.91c-2.55-6.23-8.67-10.63-15.82-10.63-9.44,0-17.09,7.65-17.09,17.09s7.65,17.09,17.09,17.09c7.15,0,13.26-4.39,15.81-10.62h45.34l35.94,27.47-27.55,20.97c-2.39-1.27-5.12-2-8.01-2-9.44,0-17.09,7.65-17.09,17.09s7.65,17.09,17.09,17.09,17.09-7.65,17.09-17.09c0-1.57-.23-3.08-.63-4.53l30.28-22.99,34.57,26.42v114.3c-65.29,1.79-116.39,16.23-130.09,37.34,41.19-17.94,107.18-27.17,180.78-22.55,66.84,4.19,126.02,18.93,165.25,38.79-18.24-23.59-76.98-44.17-149.7-51.21v-117.01l33.76-25.8,30.82,23.39c-.23,1.13-.36,2.3-.36,3.5,0,9.44,7.65,17.09,17.09,17.09s17.09-7.65,17.09-17.09-7.65-17.09-17.09-17.09c-3.28,0-6.33,.94-8.93,2.54l-27.42-20.89,36.77-28.1Z"/>
        </svg>
    </div>
</div>
