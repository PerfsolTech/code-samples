<template>
    <card class="p-6">

        <heading class="">{{ __('Add New QR Code') }}</heading>
        <div class="overflow-hidden overflow-x-auto relative m-6">

            <form id="create_form" @submit.prevent="createNewCode">
                <div class="">
                    <input class="leading-tight bg-white border border-gray-400 hover:border-gray-500 mb-6 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline"
                           name="name"
                           placeholder="Code Name"
                           required>
                </div>


                <div class="inline-block relative w-64">
                    <div>
                        <select class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline"
                                type="text" name="menu_id"
                                placeholder="menu_id"
                                required>
                            <option value="" disabled>{{ __('Linked Menu') }}</option>
                            <option v-for="menu in menus" :value="menu.id">{{ menu.name }}</option>
                        </select>
                        <div class="pointer-events-none absolute flex items-center px-2 text-gray-700"
                             style="position: absolute; right: 0; top:0; bottom: 0;">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="inline-block relative w-64 ml-4" style="vertical-align: text-top;">
                    <label>
                        <input
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-11"
                                type="checkbox"
                                name="show_navigation">
                        {{ __('Show Navigation') }}
                    </label>
                </div>


                <div>
                    <button type="submit"
                            style="padding: 10px; background-color: gray; color: white;"
                            class="bg-blue-500 hover:bg-blue-700 text-white mr-4 font-bold py-2 px-4 rounded">
                        {{ __('Create QR Code') }}
                    </button>
                    <button type="button"
                            v-on:click="cancelButton()"
                            style="padding: 10px; background-color: lightgray; color: white;"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Cancel') }}
                    </button>
                </div>


            </form>

        </div>
    </card>

</template>

<script>
    export default {
        name: "NewCode",
        props: {
            menus: [],
        },
        methods: {
            createNewCode() {
                let create_form = document.getElementById('create_form')

                let form_elements = create_form.elements

                let data = {
                    name: form_elements[0].value,
                    menu_id: form_elements[1].value,
                    show_navigation: form_elements[2].checked ? 1 : 0,
                }
                // console.log(data.show_navigation)
                Nova.request().post('/nova-vendor/qr-codes/create-new-code', data).then((response) => {
                    if (response.data == 'success') {
                        this.$toasted.show('Code saved!', {type: 'success'})
                        this.$parent.getCodes()
                        this.cancelButton()
                    }else{
                        this.$toasted.show('Code with that name already exist!', {type: 'error'})
                    }
                })
            },

            cancelButton() {
                let form_elements = document.getElementById('create_form').elements
                for (var i = 0; i <= 3; i++) {
                    form_elements[i].value = ''
                }
                this.$toasted.show('Form cleared!', {type: 'info'})
            },
        },
    }
</script>

<style scoped>

</style>