<template>
    <modal @modal-close="handleClose">
        <form
                @submit.prevent="handleConfirm"
                slot-scope="props"
                class="bg-white rounded-lg shadow-lg overflow-hidden"
                style="width: 460px"
        >
            <slot :uppercaseMode="uppercaseMode" :mode="mode">
                <div class="p-8">
                    <heading :level="2" class="mb-6">{{ __('Delete QR Code') }}</heading>
                    <p class="text-80 leading-normal">{{__('Are you sure you want to delete this QR Code?')}}</p>
                </div>
            </slot>

            <div class="bg-30 px-6 py-3 flex">
                <div class="ml-auto">
                    <button type="button" data-testid="cancel-button" dusk="cancel-general-button" @click.prevent="handleClose" class="btn text-80 font-normal h-9 px-3 mr-3 btn-link">{{__('Cancel')}}</button>
                    <button id="confirm-delete-button" ref="confirmButton" data-testid="confirm-button" type="submit" class="btn btn-default btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </form>
    </modal>
</template>

<script>
    export default {
        name: "ConfirmModal",
        /**
         * Mount the component.
         */
        mounted() {
            this.$refs.confirmButton.focus()
        },
        methods: {
            /**
             * Execute the selected action.
             */
            handleConfirm() {
                this.$emit('confirm')
            },
            /**
             * Close the modal.
             */
            handleClose() {
                this.$emit('close')
            },
        },
    }

</script>

<style scoped>

</style>