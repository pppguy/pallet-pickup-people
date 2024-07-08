<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import dayjs from 'dayjs';
import advancedFormat from 'dayjs/plugin/advancedFormat';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Adjust the import path as needed

dayjs.extend(advancedFormat);


const customers = ref([]);
const showModal = ref(false);
const selectedCustomer = ref(null);

const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

onMounted(async () => {
  const response = await axios.get('/admin/customers/status');
  customers.value = response.data;

  console.log('mounted customers');
  console.log(customers.value);
});

const formatPickupDate = (date) => {
  if (!date) {
    return 'Invalid Date';
  }

  const today = dayjs().startOf('day');
  const pickupDay = dayjs(date).startOf('day');

  // if (pickupDay.isSame(today)) {
  //   return 'Today';
  // }
  return dayjs(date).format('MMMM D, YYYY h:mm A');
  //return dayjs(date).format('MMMM D');


};

const openModal = (customer) => {
  selectedCustomer.value = { ...customer };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedCustomer.value = null;
};

const updateCustomer = async () => {
  try {
    const response = await axios.post(`/admin/customers/${selectedCustomer.value.id}/update`, {
      name: selectedCustomer.value.name,
      email: selectedCustomer.value.email,
      address: selectedCustomer.value.address,
      pickup_day: selectedCustomer.value.pickup_day,
      pickup_frequency: selectedCustomer.value.pickup_frequency,
      contact_method: selectedCustomer.value.contact_method,
      contact_email: selectedCustomer.value.contact_email,
      contact_phone: selectedCustomer.value.contact_phone,
    });

    //refresh customers
    const statusResponse = await axios.get('/admin/customers/status');
    customers.value = statusResponse.data;

    alert('Customer updated successfully');
    closeModal();
  } catch (error) {
    alert('Error updating customer');
  }
};

const sendReminder = async (customerId) => {
  try {
    console.log('sending remind');
    console.log(customerId);
    //await axios.post(`/admin/customers/${customerId}/reminder`);
    await axios.get(`/admin/customers/${customerId}/reminder`);


    //refresh customers
    const statusResponse = await axios.get('/admin/customers/status');
    customers.value = statusResponse.data;

    alert('Reminder sent successfully');

    
  } catch (error) {
    alert('Error sending reminder');
  }
};

const getPickupDay = (dayIndex) => {
  return days[dayIndex];
};

const getLastPickup = (latestPickup) => {
  console.log('latest pickup');
  console.log(latestPickup);
  if (latestPickup) {
    return formatPickupDate(latestPickup.updated_at);
  }
  return 'N/A';
};

const getCurrentStatus = (latestPrompt) => {
  if (latestPrompt) {
    if (latestPrompt.response === null) {
      return 'Prompt Created';
    }
    return latestPrompt.response === 'YES' ? 'Accepted' : 'Declined';
  }
  return 'No Prompt';
};
</script>


<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Admin Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h1>Pallet Status Report</h1>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Pickup Day
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Freq
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Last Successful Pickup
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Current Status
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="customer in customers" :key="customer.customer.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button @click="openModal(customer.customer)" class="text-blue-500">{{ customer.customer.name }}</button>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ getPickupDay(customer.customer.pickup_day) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ customer.customer.pickup_frequency }}</td>
                  <td class="px-6 py-4 whitespace-normal">{{ getLastPickup(customer.lastSuccessfulPickup) }}</td>
                  <!-- <td class="px-6 py-4 whitespace-nowrap">{{ getCurrentStatus(customer.latestPrompt) }}</td> -->
                  <td class="px-6 py-4 whitespace-nowrap">{{ customer.current_status }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button v-if="['Pickup Complete', 'Nothing yet'].includes(customer.current_status)" @click="sendReminder(customer.customer.id)" class="bg-blue-500 text-white px-4 py-2 rounded">Send Reminder</button>
                    <!-- <button v-if="customer.latestPickup" @click="sendReminder(customer.customer.id)" class="bg-blue-500 text-white px-4 py-2 rounded">Send Reminder</button> -->
                    <!-- <button v-if="customer.latestPickup && customer.latestPickup.pickup_status === 1" @click="sendReminder(customer.customer.id)" class="bg-blue-500 text-white px-4 py-2 rounded">Send Reminder</button> -->
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Customer</h3>
                <div class="mt-2">
                  <div class="grid grid-cols-1 gap-y-4">
                    <label class="block">
                      <span class="text-gray-700">Name</span>
                      <input v-model="selectedCustomer.name" class="form-input mt-1 block w-full">
                    </label>
                    <label class="block">
                      <span class="text-gray-700">Address</span>
                      <input v-model="selectedCustomer.address" class="form-input mt-1 block w-full">
                    </label>
                    <label class="block">
                      <span class="text-gray-700">Pickup Day</span>
                      <select v-model="selectedCustomer.pickup_day" class="form-select mt-1 block w-full">
                        <option v-for="(day, index) in days" :value="index" :key="index">{{ day }}</option>
                      </select>
                    </label>
                    <label class="block">
                      <span class="text-gray-700">Pickup Frequency</span>
                      <input type="number" v-model="selectedCustomer.pickup_frequency" class="form-input mt-1 block w-full">
                    </label>
                    <label class="block">
                      <span class="text-gray-700">Contact Method</span>
                      <input v-model="selectedCustomer.contact_method" class="form-input mt-1 block w-full">
                    </label>
                    <label class="block">
                      <span class="text-gray-700">Contact Email</span>
                      <input v-model="selectedCustomer.contact_email" class="form-input mt-1 block w-full">
                    </label>
                    <label class="block">
                      <span class="text-gray-700">Contact Phone</span>
                      <input v-model="selectedCustomer.contact_phone" class="form-input mt-1 block w-full">
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="updateCustomer" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
            <button @click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>



<style scoped>
/* Add any styles needed for your component here */
</style>
