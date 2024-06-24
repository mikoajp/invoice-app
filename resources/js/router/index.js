import { createRouter,createWebHashHistory } from "vue-router";

import invoiceIndex from '../components/invoices/index.vue'
import notFound from '../components/invoices/NotFound.vue'
import newInvoice from '../components/invoices/NewInvoice.vue'

const routes = [
    {
        path:'/',
        component: invoiceIndex
    },
    {
        path: '/:pathMatch(.*)*',
        component: notFound
    },
    {
        path: '/invoice/new',
        component: newInvoice
    }
]
const router = createRouter({
    history: createWebHashHistory(),
    routes
})

export default router;
