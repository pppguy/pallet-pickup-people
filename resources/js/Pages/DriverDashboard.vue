<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import dayjs from 'dayjs';
import advancedFormat from 'dayjs/plugin/advancedFormat';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Adjust the import path as needed

dayjs.extend(advancedFormat);

const pickups = ref([]);
const { props } = usePage();
const user = ref(props.user);

onMounted(() => {
  pickups.value = props.pickups;
});

const formatPickupDate = (date) => {
  const today = dayjs().startOf('day');
  const pickupDay = dayjs(date).startOf('day');

  if (pickupDay.isSame(today)) {
    return 'Today';
  }
  return dayjs(date).format('MMMM D');
};

const claimPickup = async (pickupId) => {
  try {
    await axios.post(`/driver/pickups/${pickupId}/claim`);
    alert('Pickup claimed!');

    // Update the pickup object
    pickups.value = pickups.value.map(pickup => {
      if (pickup.id === pickupId) {
        pickup.driver = user.value; // Assign the current user as the driver
        pickup.driver_id = user.value.id;
      }
      return pickup;
    });
  } catch (error) {
    alert(error.response.data.message);
  }
};

const completePickup = async (pickupId) => {
  try {
    await axios.post(`/driver/pickups/${pickupId}/complete`);
    alert('Pickup completed!');
    pickups.value = pickups.value.filter(pickup => pickup.id !== pickupId);
  } catch (error) {
    alert(error.response.data.message);
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Pending Pickups
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="py-6 bg-white border-b border-gray-200">
            <div class="p-4">
              <h1>Currently active pickups</h1>
            </div>          
            <div v-if="pickups.length === 0" class="p-4 text-center text-gray-500">
              <div>
              No pickups available. Have a nice day!
              </div>
              <div class="text-6xl mt-4">
                😎
              </div>

            </div>


            <table v-if="pickups.length > 0" class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer Name
                  </th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Pickup Date
                  </th>
                  <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Address
                  </th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Pickup Actions
                  </th>
                  <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Claimed By
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="pickup in pickups" :key="pickup.id">
                  <td class="px-4 py-4 whitespace-normal break-words">{{ pickup.customer_prompt.customer.name }}</td>
                  <td class="px-4 py-4 whitespace-nowrap">{{ formatPickupDate(pickup.customer_prompt.pickup_date) }}</td>
                  <td class="px-4 py-4 whitespace-nowrap text-center">
                    <a :href="'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(pickup.customer_prompt.customer.address)" target="_blank" class="text-blue-500">Map</a>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <button 
                      v-if="!pickup.driver"
                      class="bg-amber-300 text-white px-4 py-2 rounded w-28" 
                      @click="claimPickup(pickup.id)"
                    >                    
                      Claim
                    </button>
                    <button 
                      v-if="pickup.driver && pickup.driver.id === user.id"
                      class="bg-green-500 text-white px-4 py-2 rounded w-28" 
                      @click="completePickup(pickup.id)"
                    >
                      Complete
                    </button>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">{{ pickup.driver ? pickup.driver.name : 'Unclaimed' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
</style>
