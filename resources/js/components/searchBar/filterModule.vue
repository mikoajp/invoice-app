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
};

const filterByCustomer = (customer) => {
customerFilter.value = customer;
};

const filterByDate = () => {
};

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
