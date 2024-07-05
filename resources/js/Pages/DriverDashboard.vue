<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Driver Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h1>Driver Dashboard</h1>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer Name
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Pickup Date
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="customer in customers" :key="customer.id">
                  <td class="px-6 py-4 whitespace-nowrap">{{ customer.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ customer.pickup_date }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button class="bg-green-500 text-white px-4 py-2 rounded" @click="claimPickup(customer.id)">Claim Pickup</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default {
  setup() {
    const customers = ref([]);
    const { props } = usePage();

    onMounted(() => {
      customers.value = props.value.customers;
    });

    const claimPickup = async (customerId) => {
      await axios.post(`/api/driver/customers/${customerId}/claim`);
      alert('Pickup claimed!');
      customers.value = customers.value.filter(customer => customer.id !== customerId);
    };

    return { customers, claimPickup };
  }
};
</script>

<style scoped>
</style>
