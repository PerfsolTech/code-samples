<template>
    <card class="flex flex-col items-center justify-center" style="width: 500px; height:500px;">
        <div class="px-3 py-3">
            <h1 class="text-center text-3xl text-80 font-light">{{__('Weekly Visitors')}}</h1>
        </div>
        <div>
            <bar-chart></bar-chart>
        </div>
    </card>
</template>

<script>

    import BarChart from './BarChart.js'

    export default {

        components: {
            BarChart
        },
        props: [
            'card',
        ],

    }
</script>


<style>
</style>