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
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Claimed By
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="pickup in pickups" :key="pickup.id">
                  <td class="px-6 py-4 whitespace-nowrap">{{ pickup.customer_prompt.customer.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ pickup.pickup_date }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button class="bg-green-500 text-white px-4 py-2 rounded" @click="claimPickup(pickup.id)">Claim Pickup</button>
                    <button class="bg-green-500 text-white px-4 py-2 rounded" @click="completePickup(pickup.id)">Complete Pickup</button>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ pickup.driver ? pickup.driver.name : 'Unclaimed' }}</td>
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
import axios from 'axios';

export default {
  setup() {
    const pickups = ref([]);
    const { props } = usePage();

    onMounted(() => {
      pickups.value = props.pickups;
    });

    console.log(props.pickups);

    const claimPickup = async (pickupId) => {
      try {
        await axios.post(`/api/driver/pickups/${pickupId}/claim`);
        alert('Pickup claimed!');
        pickups.value = pickups.value.filter(pickup => pickup.id !== pickupId);
      } catch (error) {
        alert(error.response.data.message);
      }
    };

    const completePickup = async (pickupId) => {
      try {
        await axios.post(`/api/driver/pickups/${pickupId}/complete`);
        alert('Pickup completed!');
        pickups.value = pickups.value.filter(pickup => pickup.id !== pickupId);
      } catch (error) {
        alert(error.response.data.message);
      }
    };

    return { pickups, claimPickup, completePickup };
  }
};
</script>

<style scoped>
</style>
