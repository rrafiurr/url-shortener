<template>   
    <div class="input-group mb-3">
        <div class="valid-feedback standard" v-if="previoulyCreated">Your URL previously Shorted and here is your url</div>
        <input type="text" class="form-control" readonly v-model="shortenUrl" >
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" ref="clone"  @click="copy">Copy</button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        shortenUrl: {
            type: String,
            required: true,
            default: () => "",
        },
        previoulyCreated: {
            type: Boolean,
            required: true,
            default: () => false,
        },
    },
    methods: {
        async copy() {
            await navigator.clipboard.writeText(this.shortenUrl);
            alert('Copied!');
        }
    }
}
</script>

<style>
.standard{
    display:block !important;font-size: 25px;
}
</style>