const styles = {
    wrapper: {
        display: 'flex',
        justifyContent: 'space-between',
        gap: '5px'
    },
}

const initInput = (label, onInput) => {
    return $(`<input type="number" class="form-control" placeholder="${label}">`).on('input', onInput);
}

const initDualInputNumber = (targetElement, conf = {}) => {
    const DOMElements = {
        targetElement: null,
        wrapper: null,
        inputDown: null,
        inputUp: null,
    }

    const config = {
        separator: '/',
        ...conf
    }

    const inputHandler = () => {
        const value1 = DOMElements.inputDown.val() || '';
        const value2 = DOMElements.inputUp.val() || '';

        let value = `${value1}${config.separator}${value2}`

        if (value === config.separator) {
            value = ''
        }

        DOMElements.targetElement.attr('value', value);
    }

    const initDOMElements = () => {
        targetElement.attr('type', 'text')
        targetElement.hide()

        const wrapper = $('<div>');

        wrapper.css(styles.wrapper);
        targetElement.before(wrapper);
        wrapper.append(targetElement);

        const inputDown = initInput('Нижнее значение', inputHandler)
        const inputUp = initInput('Верхнее значение', inputHandler)

        wrapper.prepend(inputDown, inputUp);

        DOMElements.targetElement = targetElement
        DOMElements.wrapper = wrapper
        DOMElements.inputDown = inputDown
        DOMElements.inputUp = inputUp
    }

    const initValues = () => {
        const targetValue = DOMElements.targetElement.val()

        if (targetValue === config.separator) {
            DOMElements.targetElement.attr('value', '')
            return
        }

        if (!targetValue) {
            return
        }

        const split = targetValue.split(config.separator)

        DOMElements.inputDown.attr('value', split[0] ?? '')
        DOMElements.inputUp.attr('value', split[1] ?? '')
    }

    const specialAttr = targetElement.attr('data-dual-number')

    if (!specialAttr) {
        console.error('initDualInputNumber error: not found attribute data-dual-number')
        return
    }

    initDOMElements()
    initValues()
}

window.initDualInputNumber = initDualInputNumber
