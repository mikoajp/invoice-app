<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRouter } from "vue-router"
import axios from 'axios';

const router = useRouter()

let invoices = ref([])
let customers = ref([])
let searchCustomer = ref('')
let statusFilter = ref('all')
let customerFilter = ref('')
let startDate = ref('')
let endDate = ref('')
let sortField = ref('') // To track the current sort field
let sortDirection = ref('desc') // To track the current sort direction

onMounted(async () => {
    await getInvoices()
    await getCustomers()
})

const getInvoices = async () => {
    try {
        let response = await axios.get('/api/get_all_invoices', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        invoices.value = response.data;
    } catch (error) {
        console.error('Error fetching invoices:', error)
    }
}

const getCustomers = async () => {
    try {
        let response = await axios.get('/api/get_all_customers', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        customers.value = response.data;
    } catch (error) {
        console.error('Error fetching customers:', error)
    }
}

const search = async () => {
    // Trigger computed property re-evaluation by changing the value of searchCustomer
    filterInvoices();
}

const newInvoice = async () => {
    try {
        await axios.get('/api/create_invoice');
        await router.push('/invoice/new');
    } catch (error) {
        console.error('Error creating new invoice:', error);
    }
};

const filteredInvoices = computed(() => {
    let filtered = invoices.value;

    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(invoice => invoice.status === statusFilter.value);
    }

    if (customerFilter.value) {
        filtered = filtered.filter(invoice => invoice.customer_id === customerFilter.value);
    }

    if (searchCustomer.value) {
        filtered = filtered.filter(invoice =>
            invoice.customer && invoice.customer.firstname.toLowerCase().includes(searchCustomer.value.toLowerCase())
        );
    }

    if (startDate.value) {
        filtered = filtered.filter(invoice => new Date(invoice.date) >= new Date(startDate.value));
    }

    if (endDate.value) {
        filtered = filtered.filter(invoice => new Date(invoice.date) <= new Date(endDate.value));
    }

    if (sortField.value) {
        filtered = filtered.sort((a, b) => {
            if (sortField.value === 'id') {
                return sortDirection.value === 'asc' ? a.id - b.id : b.id - a.id;
            } else if (sortField.value === 'date') {
                return sortDirection.value === 'asc' ? new Date(a.date) - new Date(b.date) : new Date(b.date) - new Date(a.date);
            }
            return 0;
        });
    }

    return filtered;
});

const filterByStatus = (status) => {
    statusFilter.value = status;
}

const filterByCustomer = () => {
    // Just triggering the computed property to re-evaluate
}

const filterByDate = () => {
    // Just triggering the computed property to re-evaluate
}

const sortBy = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'desc';
    }
}

const clearFilters = () => {
    statusFilter.value = 'all';
    customerFilter.value = '';
    searchCustomer.value = '';
    startDate.value = '';
    endDate.value = '';
    sortField.value = '';
    sortDirection.value = 'desc';
}
</script>


<template>
    <div class="container">
        <div class="invoices">
            <div class="card__header">
                <div>
                    <h2 class="invoice__title">Invoices</h2>
                </div>
                <div>
                    <a class="btn btn-secondary" @click="newInvoice">
                        New Invoice
                    </a>
                </div>
            </div>
            <div class="table card__content">
                <div class="table--filter">
                    <span class="table--filter--collapseBtn">
                        <i class="fas fa-ellipsis-h"></i>
                    </span>
                    <div class="table--filter--listWrapper">
                        <ul class="table--filter--list">
                            <li>
                                <p @click="filterByStatus('all')"
                                   :class="{'table--filter--link--active': statusFilter === 'all'}"
                                   class="table--filter--link">
                                    All
                                </p>
                            </li>
                            <li>
                                <p @click="filterByStatus('paid')"
                                   :class="{'table--filter--link--active': statusFilter === 'paid'}"
                                   class="table--filter--link">
                                    Paid
                                </p>
                            </li>
                            <li>
                                <p @click="filterByStatus('unpaid')"
                                   :class="{'table--filter--link--active': statusFilter === 'unpaid'}"
                                   class="table--filter--link">
                                    Unpaid
                                </p>
                            </li>
                            <li>
                                <p @click="sortBy('id')"
                                   :class="{'table--filter--link--active': sortField === 'id'}"
                                   class="table--filter--link">
                                    Sort by ID
                                </p>
                            </li>
                            <li>
                                <p @click="sortBy('date')"
                                   :class="{'table--filter--link--active': sortField === 'date'}"
                                   class="table--filter--link">
                                    Sort by Date
                                </p>
                            </li>
                            <li class="table--filter--clear">
                                <p @click="clearFilters"
                                   class="table--filter--link">
                                    Clear
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="table--search">
                    <div class="relative">
                        <i class="table--search--input--icon fas fa-search"></i>
                        <input class="table--search--input" type="text" placeholder="Search by customer first name"
                               v-model="searchCustomer" @keyup="search()">
                        <input class="table--search--input" style="margin-top: 5px" type="date" v-model="startDate" @change="filterByDate" placeholder="Start Date">
                        <input class="table--search--input" style="margin-top: 5px; " type="date" v-model="endDate" @change="filterByDate" placeholder="End Date">
                    </div>
                </div>
                <div class="table--heading">
                    <p>ID</p>
                    <p>Date</p>
                    <p>Number</p>
                    <p>Customer</p>
                    <p>Due Date</p>
                    <p>Total</p>
                </div>
                <div class="table--items" v-for="item in filteredInvoices" :key="item.id">
                    <a href="#" class="table--items--transactionId">#{{ item.id }}</a>
                    <p>{{ item.date }}</p>
                    <p>{{ item.number }}</p>
                    <p v-if="item.customer">{{ item.customer.firstname }} {{ item.customer.lastname }}</p>
                    <p v-else>{{ item.customer_id }}</p>
                    <p>{{ item.due_date }}</p>
                    <p>${{ item.total }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
