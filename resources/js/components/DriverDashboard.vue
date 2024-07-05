<template>
    <div>
        <h1>Driver Dashboard</h1>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Customer Name
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Pickup Date
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="customer in customers" :key="customer.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ customer.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ customer.pickup_date }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button
                            class="bg-green-500 text-white px-4 py-2 rounded"
                            @click="claimPickup(customer.id)"
                        >
                            Claim Pickup
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            customers: [],
        };
    },
    created() {
        this.fetchCustomers();
    },
    methods: {
        fetchCustomers() {
            axios
                .get("/api/driver/customers")
                .then((response) => {
                    this.customers = response.data;
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        claimPickup(customerId) {
            axios
                .post(`/api/driver/customers/${customerId}/claim`)
                .then((response) => {
                    alert("Pickup claimed!");
                    this.fetchCustomers();
                })
                .catch((error) => {
                    console.error(error);
                });
        },
    },
};
</script>

<style scoped></style>
