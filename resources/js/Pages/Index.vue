<script setup>
import Layout from './Layout.vue'
import { useForm } from '@inertiajs/vue3'
import {ref, defineProps, reactive, watch, onMounted, computed} from 'vue'
import VueDatePicker from '@vuepic/vue-datepicker';

const props = defineProps({
    bookings: Object,
    blockedDates: Object,
    errors: Object
})

const form = useForm({
    name: '',
    email: '',
    phone: '',
    vehicleMake: '',
    vehicleModel: '',
    bookingDateTime: ref(),
})

const datepickerDateTime = ref()

watch(datepickerDateTime, (newValue, oldValue) => {
    const date = new Date(newValue)

    // Add 1 hour to the date to correct iso format
    date.setHours(date.getHours() + 1);

    form.bookingDateTime = date.toISOString()
})

const messages = reactive({
    success: null,
    error: null,
})
function submit() {
    messages.success = null
    messages.error = null

    form.post('api/book', {
        preserveScroll: true,
        onSuccess: () => {
            messages.success = 'Your booking has been successfully submitted.'
            form.reset()
        },
        onError: () => messages.error = 'Please check the form below for errors.',
    })
}

const disabledDates = computed(() => {
    if (!props.blockedDates) return []

    return props.blockedDates.map(date => {
        return new Date(date.locked_date)
    })
})

onMounted(() => {
    const  days = 1;
    // set the datepickerDateTime to tomorrow at 9am
    datepickerDateTime.value = new Date(Date.now() + days*24*60*60*1000).setHours(9, 0, 0)
})
</script>

<template>
    <Layout>
        <div id="booking-form-container">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-6">
                <h2>Book a Service</h2>

                <div v-if="messages.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-2" role="alert">
                    <span class="block sm:inline">{{ messages.success }}</span>
                </div>
                <div v-if="messages.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-2" role="alert">
                    <span class="block sm:inline">{{ messages.error }}</span>
                </div>

                <form @submit.prevent="submit">
                    <label for="name">Name:</label>
                    <input v-model="form.name" type="text" id="name"><br>
                    <div class="text-red-600 text-xs mb-3 mt--2" v-if="errors.name">
                        <span class="fa-solid fa-exclamation-triangle mr-1"></span> {{ errors.name }}
                    </div>

                    <label for="email">Email Address:</label>
                    <input v-model="form.email" type="email" id="email"><br>
                    <div class="text-red-600 text-xs mb-3 mt--2" v-if="errors.email">
                        <span class="fa-solid fa-exclamation-triangle mr-1"></span> {{ errors.email }}
                    </div>

                    <label for="phone">Phone Number:</label>
                    <input v-model="form.phone" type="tel" id="phone"><br>
                    <div class="text-red-600 text-xs mb-3 mt--2" v-if="errors.phone">
                        <span class="fa-solid fa-exclamation-triangle mr-1"></span> {{ errors.phone }}
                    </div>

                    <label for="vehicle">Vehicle Make:</label>
                    <input v-model="form.vehicleMake" type="text" id="make"><br>
                    <div class="text-red-600 text-xs mb-3 mt--2" v-if="errors.vehicleMake">
                        <span class="fa-solid fa-exclamation-triangle mr-1"></span> {{ errors.vehicleMake }}
                    </div>

                    <label for="vehicle">Vehicle Model:</label>
                    <input v-model="form.vehicleModel" type="text" id="model"><br>
                    <div class="text-red-600 text-xs mb-3 mt--2" v-if="errors.vehicleModel">
                        <span class="fa-solid fa-exclamation-triangle mr-1"></span> {{ errors.vehicleModel }}
                    </div>

                    <label for="bookingDateTime">Date & Time of Booking:</label>
                    <VueDatePicker
                        v-model="datepickerDateTime"
                        time-picker-inline
                        minutes-increment="30"
                        :disabled-dates="disabledDates"
                        :disabled-week-days="[0, 6]"
                        :min-time="{ hours: 9, minutes: 0 }"
                        :max-time="{ hours: 17, minutes: 0 }"
                     />
                    <div class="text-red-600 text-xs mb-3 mt--2" v-if="errors.bookingDateTime">
                        <span class="fa-solid fa-exclamation-triangle mr-1"></span> {{ errors.bookingDateTime }}
                    </div>

                    <button id="submit" :disabled="form.processing" type="submit">
                        Submit
                        <span v-show="form.processing" class="fa-solid fa-spinner fa-spin"></span>
                    </button>
                </form>
            </div>
        </div>
    </Layout>
</template>

<style scoped lang="scss">
    #booking-form-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 2rem;
    }

    // override the datepicker styles
    :deep(.dp__input_wrap) {
        .dp__icon {
            margin-top: -8px;
        }
    }
    :deep([type='button'].dp__action_select) {
        background-color: #3b82f6;
    }
</style>
