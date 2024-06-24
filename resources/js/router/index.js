import { createRouter,createWebHashHistory } from "vue-router";

import invoiceIndex from '../components/invoices/index.vue'
import notFound from '../components/invoices/NotFound.vue'

const routes = [
    {
        path:'/',
        component: invoiceIndex
    },
    {
        path: '/:pathMatch(.*)*',
        component: notFound
    }
]
const router = createRouter({
    history: createWebHashHistory(),
    routes
})

export default router;
