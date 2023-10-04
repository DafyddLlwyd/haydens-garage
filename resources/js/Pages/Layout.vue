<script setup>
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <main>
        <Head title="Hayden's Garage" />
        <h1>
            <span class="fa-solid fa-car"></span> Hayden's Garage
        </h1>

        <article>
            <slot />
        </article>
    </main>
</template>
