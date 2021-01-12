<template>

    <card class="p-6">

        <heading class="">{{ __('Existing QR Codes') }}</heading>
        <div class="overflow-hidden overflow-x-auto relative m-6">
            <div v-for="code in codes" class="m-6">

                <form id="edit_form">
                    <input type="text" name="hidden_id" hidden="hidden" v-model="code.id">
                    <div>
                        <h2>
                            <!--                            {{ code.menu.name }}-->
                            {{ code.name }}
                        </h2>

                        <a class="link_on_menu" target="_blank"
                           :href="link+code.menu.id+'?show_navigation='+code.show_navigation">
                            {{ link+code.menu.id }}
                        </a>
                    </div>
                    <div class="inline-block relative w-64 mt-2">
                        <img class="qr_code" :src="/qr-codes/+code.link_name+'.png'"
                             width="150px" alt="">
                    </div>

                    <div class="inline-block relative w-64 mt-6 ml-6">

                        <div style="vertical-align: text-top;">
                            <label>
                                <input
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-11"
                                        type="checkbox"
                                        v-model="code.show_navigation"
                                        :value="code.show_navigation"
                                        true-value="1"
                                        false-value="0"
                                        name="show_navigation">
                                {{ __('Show Navigation') }}</label>
                        </div>
                        <div class="buttons_class">
                            <button type="button"
                                    v-on:click="saveCodeChanges(code.id, $event)"
                                    style="padding: 10px; background-color: gray; color: white;"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 mr-4 px-4 rounded">
                                {{ __('Save') }}
                            </button>
                            <button type="button"
                                    style="padding: 10px; background-color: lightgray; color: white;"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 mr-4 px-4 rounded"
                                    v-on:click="downloadImg('/qr-codes/'+code.link_name+'.png', code.link_name)">
                                {{ __('Download Image') }}
                            </button>
                            <!--                                    v-on:click="deleteCode(code.id)"-->
                            <button type="button"
                                    @click="openDeleteModal(code.id)"
                                    style="padding: 10px; background-color: lightgray; color: white;"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>


                </form>
            </div>

        </div>
    </card>

</template>

<script>
    export default {
        name: "Codes",
        props: {
            codes: [],
            user: [],
            link: '',
            hidden_titles: [],
        },
        methods: {
            saveCodeChanges(code_id, event) {
                let form_elements = (event.target.parentElement).parentElement.parentElement.elements

                let data = {
                    id: form_elements[0].value,
                    show_navigation: form_elements[1].checked ? 1 : 0,
                }

                Nova.request().post('/nova-vendor/qr-codes/save-code-changes', data).then((response) => {
                    this.$parent.getCodes()
                    this.$toasted.show('Code updated!', {type: 'success'})
                })
            },

            openDeleteModal(code_id) {
                this.$parent.temp_id = code_id
                this.$parent.openModal()
            },

            editCodeName(code_id) {
                this.hidden_titles.push(code_id)
            },

            checkInArr(code_id) {
                return this.hidden_titles.includes(code_id);
            },

            downloadImg(url, restaurant) {
                var link = document.createElement('a');
                link.href = url;
                link.download = restaurant + '.jpg';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },

        },


    }
</script>

<style scoped>

    .link_on_menu:link, .link_on_menu:visited {
        color: gray;
        text-decoration: none;
    }

    .qr_code {
        border: 13px solid black;
        border-radius: 30px;
    }

    .buttons_class {
        vertical-align: bottom;
        display: flex;
    }

    .buttons_class button {
        width: 150px;
        flex: 1;
    }


</style>