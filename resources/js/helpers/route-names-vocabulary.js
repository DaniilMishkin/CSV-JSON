const AuthRoutes = {
    auth: {
        login: {
            page: 'auth.login.page',
            handler: 'auth.login.handler',
        },
        logout: 'auth.logout',
    },
}

const UploadsRoutes = {
    uploads: {
        page: {
            list: 'uploads.page.list',
            create: 'uploads.page.create',
        },
        ajax: {
            store: 'uploads.ajax.store',
            download: 'uploads.ajax.download',
        },
    },
}

export const RouteNamesVocabulary = {
    ...AuthRoutes,
    ...UploadsRoutes,
}
