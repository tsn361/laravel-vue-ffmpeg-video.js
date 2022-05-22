<template>
    <div class="">
        <div class="text-center create-project">
            <h1 class="title mt-5">Welcome to video library</h1>
            <div class="mt-4 sub-title"></div>
        </div>

       <button @click="testAPI()">test API</button>

    </div>
</template>

<script>
export default {
    props:{
        projects: {
            type: Array,
            require: true
        }
    },
    data() {
        return {};
    },
    methods: {
        createProject(){
            window.location.href = '/project/create';
        },
        projectDetail(slug){
            window.location.href = '/project/detail/'+slug;
        },
        testAPI(){
            axios.get('/api/get-key')
                .then(response => {
                    console.log(response.data);
                })
                .catch(error => {
                    console.log(error);
                });
        }

    }
};
</script>

<style lang="scss" scoped>
.create-project{
    cursor: pointer;
   .title{
       font: normal normal bold 32px/48px Poppins;
       letter-spacing: 0px;
       color: #1F1F1F;
   }
    .sub-title{
        font-size: 22px;
        font-style: normal;
    }
    .action-text{
        font: var(--unnamed-font-style-normal) normal var(--unnamed-font-weight-600) 25px/38px var(--unnamed-font-family-poppins);
        letter-spacing: var(--unnamed-character-spacing-0);
        color: var(--unnamed-color-1f1f1f);
        text-align: center;
        font: normal normal 600 25px/38px Poppins;
        letter-spacing: 0px;
        color: #1F1F1F;
        opacity: 1;
    }
    .profile-img{
        width: 110px !important;
        height: 110px !important;;
    }
    .icon-color{
        color: #BBC8E2;
    }
    .info-text{
        font: var(--unnamed-font-style-normal) normal var(--unnamed-font-weight-normal) var(--unnamed-font-size-16)/var(--unnamed-line-spacing-20) var(--unnamed-font-family-inter);
        letter-spacing: var(--unnamed-character-spacing-0);
        color: var(--unnamed-color-1f1f1f);
        text-align: left;
        font: normal normal normal 12px/16px Inter;
        letter-spacing: 0px;
        color: #1F1F1F;
        opacity: 1;
    }
}
</style>
