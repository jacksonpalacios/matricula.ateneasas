<template>
    <td>
        <label>
            <input type="checkbox" class="checkOther"
                   v-on:click="checkMe()"
                   v-model="isChecked"
                   :value="false">
            {{ enrollment.student_last_name +" "+ enrollment.student_name}}
        </label>
    </td>
</template>

<script>
    export default {
        name: "item-enrollments",
        props: {
            enrollment: {type: Object},
            index: {type: Number}
        },
        data() {
            return {
                isChecked: false,
                position: this.index
            }
        },
        methods: {
            checkMe: function () {
                this.$emit('click', this)
            },
        }
    }
</script>

<style scoped>
    label {
        font-size: 12px;
        margin-bottom: 0px;
    }

    td,th{
        padding: 4px !important;
    }
</style>