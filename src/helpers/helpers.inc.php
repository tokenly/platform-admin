<?php

function successInterjection() {
    $words = [
        'Absolutely!',
        'Ahh!',
        'Aha!',
        'Ahoy!',
        'Alrighty!',
        'Amen!',
        'Anytime!',
        'Bam!',
        'Behold!',
        'Bingo!',
        'Bless!',
        'Bravo!',
        'Cheers!',
        'Blah!',
        'Gee Whiz!',
        'Golly!',
        'Goodness Gracious!',
        'Hallelujah!',
        'Indeed!',
        'There!',
        'Woot!',
        'Wow!',
        'Yay!',
        'Yes!',
    ];

    return $words[rand(0,count($words)-1)];
}

function goodbyeInterjection() {
    $words = [
        'Alack!',
        'Argh!',
        'Aww!',
        'Bah!',
        'Humbug!',
        'Boo!',
        'Crud!',
        'Darn!',
        'Dang!',
        'Doh!',
        'Drat!',
        'Eek!',
        'Geepers!',
        'Gosh!',
        'Jeez!',
        'No!',
        'Ouch!',
        'Phew!',
        'Rats!',
        'Shoot!',
        'Shucks!',
        'Tut!',
        'Uggh!',
        'Waa!',
        'What!',
        'Woah!',
        'Yikes!',
    ];

    return $words[rand(0,count($words)-1)];
}

