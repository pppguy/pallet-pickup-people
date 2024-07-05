<template>
    <div>
        <h1>Admin Dashboard</h1>
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
                        Status
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
                        {{ customer.status }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button
                            class="bg-blue-500 text-white px-4 py-2 rounded"
                            @click="sendReminder(customer.id)"
                        >
                            Send Reminder
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
                .get("/api/admin/customers")
                .then((response) => {
                    this.customers = response.data;
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        sendReminder(customerId) {
            axios
                .post(`/api/admin/customers/${customerId}/reminder`)
                .then((response) => {
                    alert("Reminder sent!");
                })
                .catch((error) => {
                    console.error(error);
                });
        },
    },
};
</script>

<style scoped></style>
