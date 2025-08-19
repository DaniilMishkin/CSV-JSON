import { usePage } from '@inertiajs/vue3'

const getQuery = () => {
    return usePage().props?.ziggy?.query
}

const convertValueType = value => {
    return isNaN(value) ? value : Number(value)
}

export const fillFormFromRouteQuery = (form, query = getQuery()) => {
    Object.keys(query).forEach(key => {
        const value = query[key]

        if (value && typeof value === 'object') {
            form[key] = form[key] || {}
            return fillFormFromRouteQuery(form[key], value)
        }

        form[key] = convertValueType(value)
    })
}
