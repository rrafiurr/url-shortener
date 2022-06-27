<template>
  <div class="py-5 text-center parent">
        <div class="input-group has-validation mb-3">
            <input type="text" class="form-control" placeholder="Enter the link here" v-model="url">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" @click="submitUrl">Shorten URL</button>
            </div>
            <div class="invalid-feedback error" v-if="errorMessege">
                {{ errorMessege }}
            </div>
        </div>
        <ResultComponent v-if="result" :shortenUrl='shortenUrl' :previoulyCreated='previoulyCreated'></ResultComponent>
    </div>
</template>

<script>
import axios from 'axios';
import ResultComponent from './Result.vue';

export default {
    name: 'Short',
    components: { 
        ResultComponent },
    props: [],
    data() {
        return{
            url: '',
            result: false,
            shortenUrl: '',
            errorMessege: false,
            previoulyCreated: false,
        }
    },
    asyncData( { } ) { },
    created() {},
    mounted() {
        // this.fetchAPI()
    },
    methods: {
        submitUrl() {
            if(this.url == '')
            {
                this.errorMessege = 'Please input a valid URL'
                return false
            }
            axios
            .post('/api/short',{
                'url' : this.url
            })
            .then(response => {
                    if(response.data.error === undefined ) 
                    {
                        this.error = false
                        this.shortenUrl = response.data.url
                        this.previoulyCreated = response.data.exist
                        this.result = true
                    }
                    else{
                        this.result = false
                        this.errorMessege = response.data.error
                    }
            })
        }
    },
    computed: { },

}
</script>

<style>
.parent{
    background:#fff;box-shadow: 0 1px 4px #ccc;border-radius: 10px;padding: 10px 30px 5px;
}
.error{
    display:block !important
}
</style>