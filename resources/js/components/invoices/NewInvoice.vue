<template>
    <div class="form-container">
        <form @submit.prevent="submitForm">
            <div class="form-group">
                <label>Number:</label>
                <input type="text" v-model="invoice.number" required @input="validateInvoiceNumber" />
                <p v-if="!isInvoiceNumberValid" class="error-message">Number must be in the format INV-</p>
            </div>
            <div class="form-group">
                <label>Customer:</label>
                <select v-model="invoice.customer_id" id="customer" class="input">
                    <option value="">Select Customer</option>
                    <option v-for="customer in allCustomers" :key="customer.id" :value="customer.id">
                        {{ customer.firstname }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label>Date:</label>
                <input type="date" v-model="invoice.date" required />
            </div>
            <div class="form-group">
                <label>Due Date:</label>
                <input type="date" v-model="invoice.due_date" required />
            </div>
            <div class="form-group">
                <label>Reference:</label>
                <input type="text" v-model="invoice.reference" @input="validateReference" />
                <p v-if="!isReferenceValid" class="error-message">Reference must be in the format REF-</p>
            </div>
            <div class="invoice-items">
                <div class="invoice-items-header">
                    <p>Description</p>
                    <p>Unit Price</p>
                    <p>Quantity</p>
                    <p>Total</p>
                    <p></p>
                </div>

                <div v-for="(item, index) in invoiceAr.items" :key="index" class="invoice-item">
                    <input v-model="item.description" type="text" class="input description-input" placeholder="Item Description" />
                    <input v-model="item.unit_price" type="number" class="input price-input" placeholder="Unit Price" />
                    <input v-model="item.quantity" type="number" class="input qty-input" placeholder="Quantity" />
                    <p class="item-total">{{ formatCurrency(item.unit_price * item.quantity) }}</p>
                    <button @click="removeItem(index)" class="remove-item-button">&times;</button>
                </div>

                <button @click="openModal" type="button" class="btn add-line-button">Add New Line</button>
            </div>

            <div class="form-group">
                <label>Terms and Conditions:</label>
                <textarea v-model="invoice.terms_and_conditions" required></textarea>
            </div>
            <div class="form-group">
                <label>Sub Total:</label>
                <input type="number" :value="calculateSubTotal" readonly />
            </div>
            <div class="form-group">
                <label>Discount:</label>
                <input type="number" v-model="invoice.discount" />
            </div>
            <div class="form-group">
                <label>Total:</label>
                <input type="number" :value="calculateTotal" readonly />
            </div>
            <button type="submit" :disabled="!isInvoiceNumberValid || !isReferenceValid">Save</button>
        </form>

        <div class="modal main__modal" :class="{ show: showModal }">
            <div class="modal__content">
                <span @click="closeModal" class="modal__close btn__close--modal">×</span>
                <h3 class="modal__title">Add Item</h3>
                <hr><br>
                <div class="modal__items">
                    <ul>
                        <li v-for="(item, i) in listProduct" :key="item.id" class="modal__item">
                            <p>{{ i + 1 }}</p>
                            <a>{{ item.item_code }} {{ item.description }}</a>
                            <button @click="addCart(item)" class="modal__add-button">+</button>
                        </li>
                    </ul>
                </div>
                <br><hr>
                <div class="modal__footer">
                    <button @click="closeModal" class="btn btn-light btn__close--modal">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import axios from 'axios';

const listProduct = ref([]);
const allCustomers = ref([]);
const allProducts = ref([]);
const showModal = ref(false);
let invoiceAr = ref({
    items: []
});
const openModal = () => {
    showModal.value = true;
};
const getAllProducts = async () => {
    const response = await axios.get('/api/get_products');
    allProducts.value = response.data;
    listProduct.value = response.data;
};

const closeModal = () => {
    showModal.value = false;
};

const removeItem = (index) => {
    invoiceAr.value.items.splice(index, 1);
}

const addCart = (item) => {
    const itemCart = {
        product_id: item.id,
        item_code: item.item_code,
        description: item.description,
        unit_price: item.unit_price,
        quantity: 1
    }
    invoiceAr.value.items.push(itemCart)
    closeModal();
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
}

const invoice = reactive({
    number: '',
    customer_id: '',
    date: '',
    due_date: '',
    reference: '',
    terms_and_conditions: '',
    sub_total: 0,
    discount: 0,
    total: 0,
});

const isInvoiceNumberValid = ref(true);
const isReferenceValid = ref(true);

const validateInvoiceNumber = () => {
    const regex = /^INV-/;
    isInvoiceNumberValid.value = regex.test(invoice.number);
};

const validateReference = () => {
    const regex = /^REF-/;
    isReferenceValid.value = regex.test(invoice.reference);
};

const calculateSubTotal = computed(() => {
    return invoiceAr.value.items.reduce((acc, item) => acc + (item.unit_price * item.quantity), 0);
});

const calculateTotal = computed(() => {
    return calculateSubTotal.value - invoice.discount;
});

watch(calculateSubTotal, (newVal) => {
    invoice.sub_total = newVal;
});

watch(calculateTotal, (newVal) => {
    invoice.total = newVal;
});

function submitForm() {
    console.log("Submitting invoice:", invoice);
    axios.post('/api/invoices', invoice)
        .then(response => {
            console.log('Invoice saved:', response.data);
        })
        .catch(error => {
            console.error('There was an error!', error);
        });
}

const getAllCustomers = async () => {
    const response = await axios.get('/api/get_customers');
    allCustomers.value = response.data;
};

const init = async () => {
    try {
        await getAllCustomers();
        await getAllProducts();
    } catch (error) {
        console.error('Error loading data:', error);
        alert('Failed to load initial data.');
    }
};

init();
</script>
