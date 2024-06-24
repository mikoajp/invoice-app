<template>
    <div class="invoices">
        <div class="card__header">
            <div>
                <h2 class="invoice__title">New Invoice</h2>
            </div>
            <div></div>
        </div>

        <div class="card__content">
            <div class="card__content--header">
                <div>
                    <p class="my-1">Customer</p>
                    <select v-model="invoice.customer" class="input">
                        <option value="">Select Customer</option>
                        <option value="cust1">Customer 1</option>
                        <!-- Dodaj więcej opcji tutaj -->
                    </select>
                </div>
                <div>
                    <p class="my-1">Date</p>
                    <input v-model="invoice.date" id="date" placeholder="dd-mm-yyyy" type="date" class="input" />
                    <p class="my-1">Due Date</p>
                    <input v-model="invoice.due_date" id="due_date" type="date" class="input" />
                </div>
                <div>
                    <p class="my-1">Numero</p>
                    <input v-model="invoice.numero" type="text" class="input" />
                    <p class="my-1">Reference (Optional)</p>
                    <input v-model="invoice.reference" type="text" class="input" />
                </div>
            </div>
            <br /><br />
            <div class="table">
                <div class="table--heading2">
                    <p>Item Description</p>
                    <p>Unit Price</p>
                    <p>Qty</p>
                    <p>Total</p>
                    <p></p>
                </div>

                <!-- dynamiczne pozycje faktury -->
                <div v-for="(item, index) in invoice.items" :key="index" class="table--items2">
                    <p>{{ item.description }}</p>
                    <p>
                        <input v-model="item.unit_price" type="number" class="input" />
                    </p>
                    <p>
                        <input v-model="item.qty" type="number" class="input" />
                    </p>
                    <p>
                        {{ formatCurrency(item.unit_price * item.qty) }}
                    </p>
                    <p @click="removeItem(index)" class="remove-item">
                        &times;
                    </p>
                </div>
                <div class="add-line">
                    <button @click="addItem" class="btn btn-sm btn__open--modal">Add New Line</button>
                </div>
            </div>

            <div class="table__footer">
                <div class="document-footer">
                    <p>Terms and Conditions</p>
                    <textarea v-model="invoice.terms" cols="50" rows="7" class="textarea"></textarea>
                </div>
                <div>
                    <div class="table__footer--subtotal">
                        <p>Sub Total</p>
                        <span>{{ formatCurrency(subTotal) }}</span>
                    </div>
                    <div class="table__footer--discount">
                        <p>Discount</p>
                        <input v-model="invoice.discount" type="number" class="input" />
                    </div>
                    <div class="table__footer--total">
                        <p>Grand Total</p>
                        <span>{{ formatCurrency(grandTotal) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card__footer">
            <div></div>
            <div>
                <button @click="saveInvoice" class="btn btn-secondary">Save</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            invoice: {
                customer: '',
                date: '',
                due_date: '',
                numero: '',
                reference: '',
                items: [
                    {
                        description: '',
                        unit_price: 0,
                        qty: 0
                    }
                ],
                terms: '',
                discount: 0
            }
        };
    },
    computed: {
        subTotal() {
            return this.invoice.items.reduce((sum, item) => sum + item.unit_price * item.qty, 0);
        },
        grandTotal() {
            return this.subTotal - this.invoice.discount;
        }
    },
    methods: {
        addItem() {
            this.invoice.items.push({
                description: '',
                unit_price: 0,
                qty: 0
            });
        },
        removeItem(index) {
            this.invoice.items.splice(index, 1);
        },
        formatCurrency(amount) {
            return `$ ${amount.toFixed(2)}`;
        },
        saveInvoice() {
            // Implementuj logikę zapisu faktury, np. wysyłanie danych na serwer
            console.log('Saving invoice:', this.invoice);
        }
    }
};
</script>

<style scoped>

</style>
