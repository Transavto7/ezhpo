require('jquery-mask-plugin')
require('select2')
require('html5-qrcode')

/**
 * Init masks
 */
const INIT_MASKS = () => {
    const MASKS = {
        '.MASK_ID_ELEM': '000000000',
        '.MASK_DATETIME': '00.00.0000 00:00',
        '.MASK_UTC': 'UTC-#',
        '.MASK_TONOM': '900/00',
        '.MASK_PHONE': '+70000000000'
    };

    for(let mask in MASKS) {
        $(mask).mask(MASKS[mask])
    }
}

const INIT_SELECTS = () => {
    $('select').select2({

    })
}

/**
 * Initialization plugins
 * @constructor
 */
const INIT_PLUGINS = () => {
    INIT_MASKS()
    //INIT_SELECTS()
}

$(window).on('load', INIT_PLUGINS)
