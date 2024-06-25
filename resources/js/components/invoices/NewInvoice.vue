<script setup>
import {computed, onMounted, ref} from "vue"
import axios from 'axios'

let form = ref({})
let allCustomers = ref([])
let allProducts = ref([])
let customer_id = ref(null)
let listProduct = ref([])
let invoice = ref({
    items: [],
    terms: '',
    discount: 0
})
const showModal = ref(false)

onMounted(async () => {
    await indexForm()
    await getAllCustomers()
    await getAllProducts()
})

const indexForm = async () => {
    let response = await axios.get('/api/create_invoice')
    form.value = response.data
}

const getAllCustomers = async () => {
    let response = await axios.get('/api/get_customers')
    allCustomers.value = response.data
}

const getAllProducts = async () => {
    let response = await axios.get('/api/get_products')
    allProducts.value = response.data
    listProduct.value = response.data
}

const openModal = () => {
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
}

const addCart = (item) => {
    const itemCart = {
        id: item.id,
        item_code: item.item_code,
        description: item.description,
        unit_price: item.unit_price,
        quantity: 1
    }
    invoice.value.items.push(itemCart)
}

const removeItem = (index) => {
    invoice.value.items.splice(index, 1)
}

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)
}

const subTotal = computed(() => {
    return invoice.value.items.reduce((acc, item) => acc + item.unit_price * item.quantity, 0)
})

const grandTotal = computed(() => {
    return subTotal.value - invoice.value.discount
})
</script>

<template>
    <div class="invoice-container">
        <div class="invoice-header">
            <h2 class="invoice-title">New Invoice</h2>
        </div>

        <div class="invoice-content">
            <div class="invoice-details">
                <div class="invoice-detail">
                    <label for="customer">Customer</label>
                    <select v-model="customer_id" id="customer" class="input">
                        <option value="">Select Customer</option>
                        <option v-for="customer in allCustomers" :key="customer.id" :value="customer.id">
                            {{ customer.firstname }}
                        </option>
                    </select>
                </div>
                <div class="invoice-detail">
                    <label for="date">Date</label>
                    <input v-model="form.date" id="date" type="date" class="input" />
                    <label for="due_date">Due Date</label>
                    <input v-model="form.due_date" id="due_date" type="date" class="input" />
                </div>
                <div class="invoice-detail">
                    <label for="numero">Number</label>
                    <input v-model="form.number" id="numero" type="text" class="input" />
                    <label for="reference">Reference (Optional)</label>
                    <input v-model="form.reference" id="reference" type="text" class="input" />
                </div>
            </div>

            <div class="invoice-items">
                <div class="invoice-items-header">
                    <p>Description</p>
                    <p>Unit Price</p>
                    <p>Quantity</p>
                    <p>Total</p>
                    <p></p>
                </div>

                <div v-for="(item, index) in invoice.items" :key="index" class="invoice-item">
                    <input v-model="item.description" type="text" class="input description-input" placeholder="Item Description" />
                    <input v-model="item.unit_price" type="number" class="input price-input" placeholder="Unit Price" />
                    <input v-model="item.quantity" type="number" class="input qty-input" placeholder="quantity" />
                    <p class="item-total">{{ formatCurrency(item.unit_price * item.quantity) }}</p>
                    <button @click="removeItem(index)" class="remove-item-button">&times;</button>
                </div>

                <button @click="openModal" class="btn add-line-button">Add New Line</button>
            </div>

            <div class="invoice-footer">
                <div class="invoice-terms">
                    <label for="terms">Terms and Conditions</label>
                    <textarea v-model="invoice.terms" id="terms" class="textarea" rows="5"></textarea>
                </div>
                <div class="invoice-summary">
                    <div class="summary-item">
                        <p>Sub Total</p>
                        <span>{{ formatCurrency(subTotal) }}</span>
                    </div>
                    <div class="summary-item">
                        <label for="discount">Discount</label>
                        <input v-model="invoice.discount" id="discount" type="number" class="input discount-input" />
                    </div>
                    <div class="summary-item">
                        <p>Grand Total</p>
                        <span>{{ formatCurrency(grandTotal) }}</span>
                    </div>
                </div>
            </div>

            <div class="invoice-actions">
                <button class="btn save-button">Save</button>
            </div>
        </div>
    </div>

    <div class="modal main__modal" :class="{ show: showModal }">
        <div class="modal__content">
            <span @click="closeModal" class="modal__close btn__close--modal">×</span>
            <h3 class="modal__title">Add Item</h3>
            <hr><br>
            <div class="modal__items">
                <ul>
                    <li v-for="(item, i) in listProduct" :key="item.id" class="modal__item">
                        <p>{{ i + 1 }}</p>
                        <a href="#">{{ item.item_code }} {{ item.description }}</a>
                        <button @click="addCart(item)" class="modal__add-button">+</button>
                    </li>
                </ul>
            </div>
            <br><hr>
            <div class="modal__footer">
                <button @click="closeModal" class="btn btn-light mr-2 btn__close--modal">Cancel</button>
                <button class="btn btn-light btn__close--modal">Save</button>
            </div>
        </div>
    </div>

</template>
