export function tidyEmptyObjectFields(obj) {
    return Object.keys(obj).reduce((acc, key) => {
        const value = obj[key]

        if (value && typeof value === 'object' && !Array.isArray(value)) {
            const cleanedValue = tidyEmptyObjectFields(value)
            if (Object.keys(cleanedValue).length > 0) {
                acc[key] = cleanedValue
            }
        } else if (value !== null && value !== undefined && value !== '') {
            acc[key] = value
        }

        return acc
    }, {})
}
