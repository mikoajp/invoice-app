<script>
import { ref, computed } from 'vue';

export function useInvoiceFilters() {
const invoices = ref([]);
const searchCustomer = ref('');
const statusFilter = ref('all');
const customerFilter = ref('');
const startDate = ref('');
const endDate = ref('');
const sortField = ref('');
const sortDirection = ref('desc');

const setInvoices = (data) => {
invoices.value = data;
};

const filterInvoices = (invoice) => {
const filters = [
(invoice) => statusFilter.value === 'all' || invoice.status === statusFilter.value,
(invoice) => !customerFilter.value || invoice.customer_id === customerFilter.value,
(invoice) => !searchCustomer.value || (invoice.customer && invoice.customer.firstname.toLowerCase().includes(searchCustomer.value.toLowerCase())),
(invoice) => !startDate.value || new Date(invoice.date) >= new Date(startDate.value),
(invoice) => !endDate.value || new Date(invoice.date) <= new Date(endDate.value)
];

return filters.every(filter => filter(invoice));
};
const sortInvoices = (a, b) => {
    switch (sortField.value) {
        case 'id':
            return sortDirection.value === 'asc' ? a.id - b.id : b.id - a.id;
        case 'date':
            return sortDirection.value === 'asc' ? new Date(a.date) - new Date(b.date) : new Date(b.date) - new Date(a.date);
        default:
            return 0;
    }
};

const filteredInvoices = computed(() => {
return invoices.value.filter(filterInvoices).sort(sortInvoices);
});

const filterByStatus = (status) => {
statusFilter.value = status;
};

const filterByCustomer = (customer) => {
customerFilter.value = customer;
};

const filterByDate = () => {};

const sortBy = (field) => {
if (sortField.value === field) {
sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
} else {
sortField.value = field;
sortDirection.value = 'desc';
}
};

const clearFilters = () => {
statusFilter.value = 'all';
customerFilter.value = '';
searchCustomer.value = '';
startDate.value = '';
endDate.value = '';
sortField.value = '';
sortDirection.value = 'desc';
};

return {
invoices,
searchCustomer,
statusFilter,
customerFilter,
startDate,
endDate,
sortField,
sortDirection,
setInvoices,
filteredInvoices,
filterByStatus,
filterByCustomer,
filterByDate,
sortBy,
clearFilters
};
}
</script>
