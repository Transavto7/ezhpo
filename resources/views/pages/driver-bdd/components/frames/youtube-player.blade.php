<style>
    html, body, a, img {
        display: block;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
    }
    a:before, a:after {
        position: absolute;
        content: '';
        left: 50%;
        top: 50%;
    }
    a:before {
        margin: -4.9% 0 0 -7%;
        background: rgba(31,31,30,.8);
        padding-top: 9.8%;
        width: 14%;
        border-radius: 16% / 27%;
    }
    a:after {
        margin: -1.9vw 0 0 -1vw;
        border: 2vw solid transparent;
        border-left: 3.8vw solid #fff;
    }
    a:hover:before {
        background: #c0171c;
    }
</style>

<a href='http://www.youtube-nocookie.com/embed/{{ $youtube }}?autoplay=1'>
    <img src='//img.youtube.com/vi/{{ $youtube }}/maxresdefault.jpg'
         srcset='//img.youtube.com/vi/{{ $youtube }}/mqdefault.jpg 320w,
            //img.youtube.com/vi/{{ $youtube }}/hqdefault.jpg 480w,
            //img.youtube.com/vi/{{ $youtube }}/maxresdefault.jpg 1307w'>
</a>
