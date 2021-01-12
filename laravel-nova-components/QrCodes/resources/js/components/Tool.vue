<template>
    <div>
        <heading class="mb-6">QR Codes</heading>

        <Codes :codes="codes" :user="user" :link="link"></Codes>
        <hr>
        <NewCode :menus="menus"></NewCode>

        <portal to="modals">
            <transition name="fade">
                <ConfirmModal
                        v-if="modalOpen"
                        @confirm="confirmModal"
                        @close="closeModal"
                />
            </transition>
        </portal>
    </div>
</template>

<script>


    import Codes from './Codes'
    import NewCode from './NewCode'
    import ConfirmModal from './ConfirmModal'

    export default {
        data() {
            return {
                codes: [],
                menus: [],
                user: [],
                link: '',
                hidden_titles: [],
                modalOpen: false,
                temp_id: '',
            }
        },
        components: {
            Codes,
            NewCode,
            ConfirmModal,
        },

        mounted() {
            this.getCodes();
        },
        methods: {
            getCodes() {
                Nova.request().get('/nova-vendor/qr-codes/codes').then(response => {
                    this.codes = response.data['codes'];
                    this.menus = response.data['menus'];
                    this.user = response.data['user'];
                    this.link = response.data['link'];
                });
            },


            deleteCode(code_id) {
                Nova.request().post('/nova-vendor/qr-codes/delete-code', {
                    code_id: code_id
                }).then((response) => {
                    if (response.data == 'success') {
                        this.$toasted.show('Code deleted!', {type: 'success'})
                        this.getCodes()
                    } else {
                        this.$toasted.show('Error!', {type: 'error'})
                    }

                })
            },

            openModal() {
                this.modalOpen = true;
            },

            confirmModal() {
                if (this.temp_id) {
                    this.deleteCode(this.temp_id)
                    this.temp_id = ''
                    this.modalOpen = false;
                }
            },
            closeModal() {
                if (this.temp_id) {
                    this.temp_id = ''
                    this.modalOpen = false;
                }
            }
        },
    }
</script>
