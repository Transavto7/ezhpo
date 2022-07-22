export function getTotalAll(reports, ...names) {
    let total = 0;
    for (let key in reports) {
        const totalDriver = getTotal(reports[key], ...names);

        if (typeof totalDriver === 'number') {
            total += totalDriver;
        }
    }

    if (total > 0) {
        return total;
    }

    return 'отсутствует';
}

export function getSumAll(reports, ...names) {
    let sum = 0;
    for (let key in reports) {
        const totalDriver = getSum(reports[key], ...names);

        if (typeof totalDriver === 'number') {
            sum += totalDriver;
        }
    }

    if (sum > 0) {
        return sum;
    }

    return null;
}

export function getTotal(item, ...names) {
    let total = 0;

    if (item.types) {
        for (let key in item.types) {
            names.forEach(name => {
                if (key.split('/')[0].trim().toLowerCase() === name.toLowerCase()) {
                    total += parseInt(item.types[key]?.total);
                }
            });
        }
    }

    if (total > 0) {
        return total;
    }

    return 'отсутствует';
}

export function getSum(item, ...names) {
    let sum = 0;

    if (item.types) {
        for (let key in item.types) {
            names.forEach(name => {
                if (key.split('/')[0].trim().toLowerCase() === name.toLowerCase()) {
                    sum += parseInt(item.types[key]?.sum);
                }
            });
        }
    }

    if (sum > 0) {
        return sum;
    }

    return null
}
