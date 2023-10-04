<script setup>
import Layout from './Layout.vue'
import {ref, defineProps, computed, reactive, watch} from 'vue'
import { CalendarView, CalendarViewHeader } from "vue-simple-calendar"
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    bookings: Object,
    blockedDates: Object,
    errors: Object
})

const showDate = ref(null);

const form = useForm({
    date: ref(),
})

const setShowDate = (d) => {
    showDate.value = d;
};

// Convert bookings to calendar items format
const items = computed(() => {
    if (!props.bookings) return []

    // Convert bookings to calendar items format
    const items =  props.bookings.map(booking => {
        const date = booking.booking_datetime.split('T')
        const time = date[0].split(' ')[1].substring(0, 5)

            return {
                id: booking.id,
                startDate: date[0],
                title: `${time} <b>${booking.name}`,
                // Not indented as it breaks the formatting
                tooltip:`${booking.vehicle_make} ${booking.vehicle_model}
${booking.phone}
${booking.email}`
            }
        })

    // Convert blocked dates to calendar items format
    if (props.blockedDates) {
        const blockedDates = props.blockedDates.map(date => {
            return {
                id: date.id,
                startDate: date.locked_date,
                title: '<span class="fa-solid fa-lock"></span> Date locked',
                tooltip: 'This date is locked, and can no longer be booked.'
            }
        })
        items.push(...blockedDates)
    }

    return items
})

const messages = reactive({
    success: null,
    error: null,
})

// watch messages for changes, and reset them after 3 seconds
watch(messages, (newValue, oldValue) => {
    setTimeout(() => {
        messages.success = null
        messages.error = null
    }, 3000)
})

function lockDate(d, events, a) {
    if (form.processing) return

    if (events.length > 1 || (events.length === 1 && !events[0].title.includes('Date locked'))) {
        messages.error = 'This date is already booked, and can no longer be booked.'
        return
    }

    // if clicked on a weekend, return
    if (a.target.classList.contains('dow0') || a.target.classList.contains('dow6')) {
        messages.error = 'You cannot lock a weekend date.'
        return
    }

    const isLocked = events[0]?.title.includes('Date locked')
    const successMessage = isLocked ? 'Date unlocked successfully.' : 'Date locked successfully.'
    const url = isLocked ? 'api/unlock' : 'api/lock'

    // format date to match database
    const date = new Date(d)
    const year = date.toLocaleDateString('en-GB', {year: 'numeric'})
    const month = date.toLocaleDateString('en-GB', {month: '2-digit'})
    const day = date.toLocaleDateString('en-GB', {day: '2-digit'})

    // set v-model to date clicked
    form.date = `${year}-${month}-${day}`

    form.post(url, {
        preserveScroll: true,
        onSuccess: () => {
            messages.success = successMessage
            form.reset()
        },
    })


}
</script>

<template>
    <Layout>
        <div id="booking-container" >
            <div class="calendar">
            <h2>View Bookings</h2>
            <h3>Hover calendar item for more details, click empty cells to lock date</h3>

                <div v-if="messages.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-2" role="alert">
                    <span class="block sm:inline">{{ messages.success }}</span>
                </div>
                <div v-if="messages.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-2" role="alert">
                    <span class="block sm:inline">{{ messages.error }}</span>
                </div>

            <div class="spinner" v-if="form.processing">
                <span class="fa-solid fa-spinner fa-spin fa-5x"></span>
            </div>

            <calendar-view
                :class="form.processing ? 'opacity-50' : ''"
                :show-date="showDate"
                :items="items"
                @click-date="lockDate"
                disablePast
                class="theme-default">
                <template #header="{ headerProps }">
                    <calendar-view-header
                        :header-props="headerProps"
                        @input="setShowDate"/>
                </template>
            </calendar-view>
        </div>
        </div>
    </Layout>
</template>

<style scoped lang="scss">
#booking-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 2rem;

    .calendar {
        align-content: center;
        align-items: center;
        background-color: #fff;
        padding: 1rem;
    }
    :deep(.cv-wrapper) {
        width: 80vw;
        height: 70vh;
        .cv-day {
            height: 200px;
        }
        .dow0, .dow6 {
            background-color: #f5f5f5;
        }
    }

    .spinner {
        height: 100px;
        width: 100px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
    }
}
</style>
