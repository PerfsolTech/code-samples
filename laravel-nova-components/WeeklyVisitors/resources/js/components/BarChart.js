import {Bar} from 'vue-chartjs'

export default {
    extends: Bar,
    props: ['options'],
    data() {
        return {
            weekly_visitors: null,
        }
    },
    mounted() {
        this.getWeeklyVisitorsByMenuCount()

    },
    methods: {
        getWeeklyVisitorsByMenuCount() {
            Nova.request().get('/nova-vendor/weekly-visitors/weekly-visitors').then(response => {
                this.weekly_visitors = response.data['visits'];

                this.renderChart({
                    labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
                    datasets: [
                        {
                            label: 'Weekly Visitors',
                            backgroundColor: '#8d8d8d',
                            data: this.weekly_visitors
                        }
                    ]
                })
            });
        },
    },

}