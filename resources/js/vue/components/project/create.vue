<template>
    <div class="position-relative">
        <ValidationObserver ref="projectForm">
            <form class="" @submit.prevent="createProject">
                <loading-spinner v-if="showSpinner"></loading-spinner>
                <section class="setup-project-block" :class="[isEditMode ? '': 'mt50']">
                <div class="container">
                    <div class="setup-project-inner-block">
                        <section class="mb-5 mt-3 project-edit-header" v-if="isEditMode">
                            <div class="container">
                                <div class="page-header-inner-third">
                                    <a class="arrow-btn left-arrow" href="#" @click="diiscardForm()"><i class="fa-solid fa-arrow-left"></i></a>
                                    <div class="mint-details">
                                        <div class="mint-logo">
                                            <img alt="logo" :src="input.profile_image">
                                        </div>
                                    <div class="mint-name">
                                        <h1 class="mt-3">{{input.name}}</h1>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="card-light-grey">
                            
                            <div class="setup-project-from">
                                <div v-if="isEditMode" class="text-start d-flex">
                                    <h1 class="text-start mr-3">Edit Project</h1>
                                    <div class="alert alert-warning mx-4" role="alert" v-if="haveUnsavedChanges">You have unsaved changes </div>
                                </div>
                                <div v-else>
                                    <h1>Set up your Project</h1>
                                    <p>Set up the basic information for your project to add your whitelist campaigns.</p>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12" >
                                        <ValidationProvider name="Name" rules="required" v-slot="{ errors }">
                                            <label for="ProjectName" class="form-label">Project Name</label>
                                            <input name="name" ref="projectName" v-model="project.name"  type="text" class="form-control" id="ProjectName" placeholder="Enter Project Name">
                                            <span class="text-danger">{{ errors[0] }}</span>
                                        </ValidationProvider>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <ValidationProvider name="Bio" rules="required" v-slot="{ errors }">
                                            <label for="ProjectBio" class="form-label">Project Bio</label>
                                            <textarea class="form-control" name="Bio" v-model="project.bio" id="ProjectBio" rows="3"></textarea>
                                            <span class="text-danger">{{ errors[0] }}</span>
                                        </ValidationProvider>
                                        <span class="textarea-text">(Add max characters and this description will appear in the campaign pages)</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ValidationProvider name="link" rules="required" v-slot="{ errors }">
                                            <label for="OfficialLink" class="form-label"><i class="fa-solid fa-globe"></i> Official Link</label>
                                            <input name="link" v-model="project.link" type="url" class="form-control" placeholder="Enter Official Link Site" id="OfficialLink">
                                            <span class="text-danger">{{ errors[0] }}</span>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-md-6 twitterUsername">
                                        <ValidationProvider name="Twitter Username" rules="alpha_num" v-slot="{ errors }">
                                            <label for="TwitterUsername" class="form-label"><i class="fa-brands fa-twitter"></i> Twitter Username</label>
                                            <div class="input-group">
                                                <div class="input-group-text">@</div>
                                                <input type="text" class="form-control" v-model="project.twitter_user" id="TwitterUsername" placeholder="Enter Twitter Username">
                                            </div>
                                            <span class="text-danger" v-if="errors[0]">Only letters and numbers</span>
                                        </ValidationProvider>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="Discord" class="form-label"><i class="fa-brands fa-discord"></i> Discord Server</label>
                                        <input type="url" placeholder="Enter Discord Server URL" v-model="project.discord_server_link" class="form-control" id="Discord">
                                    </div>
                                    <div class="col-md-6 mint-date">
                                        <label for="dateP" class="form-label"><i class="fa-regular fa-calendar-days"></i> Mint Date (optional)</label>
                                        <div class="input-group date" id="datepicker">
                                            <input type="datetime-local" :min="minDate" v-model="project.mintDate" class="form-control" id="dateP" name="mint_date">
                                            <!-- <input type="text" :min="minDate" v-model="project.mintDate" class="form-control" id="date" name="mint_date" /> -->
                                            <!-- <span class="input-group-append">
                                                <span class="input-group-text d-block"> <i class="fa-regular fa-calendar-days"></i></span>
                                            </span> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 fileUpload">
                                        <label for="Discord" class="form-label"><i class="fa-solid fa-image-portrait"></i>
                                            Upload Profile Image
                                        </label>
                                        <ValidationProvider name="Profile Image" ref="profileImg" :rules="imageRules" v-slot="{ errors }">
                                            <div class="input-group">
                                                <input type="file" name="profile_image" @change="handleProfileImg" title="your text" class="form-control file-upload" id="inputGroupFile01">
                                                <label class="input-group-text" for="inputGroupFile01">Upload</label>
                                            </div>
                                            <span class="text-danger">{{ errors[0] }}</span>
                                        </ValidationProvider>
                                        <span class="textarea-text">This will go in your Campaign pages. Recommended size: 300 x 300.</span>
                                    </div>
                                    <div class="col-md-6 fileUpload">
                                        <label for="TwitterUsername" class="form-label"><i class="fa-regular fa-image"></i> Upload Banner Image</label>
                                        <ValidationProvider name="Banner Image" ref="bannerImg" :rules="imageRules" v-slot="{ errors }">
                                            <div class="input-group">
                                                <input type="file" name="banner_image" @change="handleBannerImg" title="your text" class="form-control file-upload" id="inputGroupFile02">
                                                <label class="input-group-text" for="inputGroupFile02">Upload</label>
                                            </div>
                                            <span class="text-danger">{{ errors[0] }}</span>
                                        </ValidationProvider>
                                        <span class="textarea-text">This will go at the top of the Campaign pages. Recommended size: 300 x 1400.</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="project-whitelist">
                                    <h3><i class="fa-solid fa-user-group"></i> Project Whitelist</h3>
                                    <div class="project-whitelist-inner-block">
                                        <div class="row">
                                            <label for="totalwhitelistSpots" class="col-sm-7 col-form-label">Total whitelist Spots</label>
                                            <div class="col-sm-5">
                                                <ValidationProvider name="Whitelist Spots" rules="required|numeric|min:1" v-slot="{ errors }">
                                                    <input v-model="project.total_spots" type="number" class="form-control total-whitelist" id="totalwhitelistSpots">
                                                    <span class="text-danger text-end" v-if="errors[0]">Only positive numbers</span>
                                                </ValidationProvider>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="" class="col-sm-7 col-form-label">Allowed Mints per wallet</label>
                                            <div class="col-sm-5">
                                                <div class="input-group project-whitelist-cal">
                                                    <div class="mint-per-wallet">
                                                        <ValidationProvider name="whitelist Spots" rules="numeric|min_value:1" v-slot="{ errors }">
                                                            <vue-number-input v-model="project.mint_per_wallet" :min="1" :max="10" class="mints-per-wallet" :inputtable="false" inline  controls></vue-number-input>
                                                            <span class="text-danger text-end" v-if="errors[0]">Minimum value 1</span>
                                                        </ValidationProvider>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="project-whitelist-info">Total whitelist spots are distributed throughout the entire project's campaigns.</span>
                                </div>
                                <div class="form-footer" v-if="!isEditMode">
                                    <button class="button-primery"> <i class="fa-regular fa-floppy-disk"></i> Save and continue to Campaigns</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                </section>
                <section class="edit-action" v-if="isEditMode">
                    <div class="edit-action-btn-block">
                    <div class="btn btn-border" @click="diiscardForm()">Discard Changes</div>
                    <button class="btn btn-white"> <i class="fa-regular fa-floppy-disk"></i> Save Changes</button>
                    </div>
                </section>
            </form>
         </ValidationObserver>
     </div>
</template>


<script>
import Vue from 'vue';
import VueNumberInput from '@chenfengyuan/vue-number-input';
import axios from "../../axios";
Vue.component('vue-number-input', VueNumberInput);

export default {
    name: 'ProjectCreate',
    props:{
        input: {
            type: Object,
            require: true
        },
        userid: {
            type: String,
            require: false
        },
        actionType: {
            type: String,
            require: true
        },
    },
    data() {
        return {
            showSpinner: false,
            minDate: null,
            profileImg: null,
            bannerImg: null,
            haveUnsavedChanges: false,
            project: {
                id: 0,
                name: '',
                userid: this.userid,
                total_spots: 0,
                mint_per_wallet: 1,
            }
        };
    },
    computed: {
        isEditMode() {
            return this.actionType === 'edit';
        },
        imageRules() {
            return this.isEditMode ? 'image' : 'required|image';
        }
    },
    methods: {
        diiscardForm() {
            window.location = '/project/detail/'+this.input.slug;
        },
        createProject(){

            //console.log('createProject');
            this.$refs.projectForm.validate().then(success => {
                if (!success) {

                    this.$nextTick(() => {
                        console.log('nextTick');
                        if(!this.project.name || !this.project.bio) {
                            this.$refs.projectName.focus();
                        }
                    });

                    return;
                }

                this.showSpinner = true;
                let formdata = new FormData();

                if(this.profileImg !== null ){
                    formdata.append("profile_image", this.profileImg);
                }
                
                if(this.bannerImg !== null ){
                    formdata.append("banner_image", this.bannerImg);
                }

                formdata.append("data", JSON.stringify(this.project));
                var API_URL = this.isEditMode ? '/api/project-update' : '/api/project-create';

                axios.post(API_URL, formdata, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                }).then((response)=> {
                    //console.log("response ", response.data)
                    //this.users = response.data.users
                    this.showSpinner = false;

                    //type: 'success' | 'error' | 'warning' | 'info' | 'question'
                    this.$fire({
                        title: "Success",
                        text: "Project Create",
                        type: "success",
                        timer: 3000
                    }).then(r => {
                        window.location = '/project/detail/'+response.data.slug;
                    });

                }).catch((error) => {
                    //console.log("error ", error)
                    this.showSpinner = false;

                    this.$fire({
                        title: "Error",
                        text: error.response.data.message,
                        type: 'error',
                        timer: 3000
                    }).then(r => {
                    
                    });
                })
            });
        },

        async handleProfileImg(e){
            const { valid } = await this.$refs.profileImg.validate(e);

            if (valid) {
                //console.log('Uploaded the file...', e);
                this.profileImg =  e.target.files[0];
            }
        },

        async handleBannerImg(e){
            const { valid } = await this.$refs.bannerImg.validate(e);

            if (valid) {
                //console.log('Uploaded the file...', e);
                this.bannerImg = e.target.files[0];
            }
        },
        formUpdated: function(newV, oldV){
            //console.log('the form object updated')
            this.haveUnsavedChanges = true;
        }

    },
    created(){
        
    },
    mounted() {
        const today = new Date().toISOString().substring(0,16);
        this.minDate = today;

        //console.log("actionType=>", this.actionType);
        //console.log("input=> ", this.input);

        if(this.actionType === 'edit'){
            this.project = this.input;
            this.project.id = this.input.id;
            
            let DateAndTime = this.input.mint_date +' '+ this.input.mint_time;
            this.project.mintDate = new Date(DateAndTime).toISOString().substring(0,16);
        }

        this.$watch('project', this.formUpdated, {
            deep: true
        })
    }
};
</script>
<style lang="scss" scoped>

.mints-per-wallet{
    width: 140px;
}

::v-deep .form-control {
    background: #fff !important;
}
::v-deep .number-input__input{
  min-height: 42px !important;
}

::v-deep .number-input__button--plus{
  border-bottom-left-radius: 0;
  border-left: 1px solid #ddd;
  border-top-left-radius: 0;
  right: 1px;
}

::v-deep .number-input__button--minus{
  border-bottom-left-radius: 0;
  border-left: 1px solid #ddd;
  border-top-left-radius: 0;
  right: 1px;
}

::v-deep .number-input__button::before {
  height: 1px;
  width: 40% !important;
}
::v-deep .number-input__button::after {
  height: 40% !important;
  width: 1px;
}

//background: #E9ECEF;
//border: 1px solid #CED4DA;
::v-deep .number-input__button {
  background-color: #E9ECEF !important;
  border: 0;
  border-radius: 0.25rem;
  bottom: 1px;
  position: absolute;
  top: 1px;
  width: 2.5rem;
  z-index: 1;
}

::v-deep .number-input__button:disabled::after, ::v-deep .number-input__button:disabled::before {
   background-color: #000 !important;
 }

input[type="datetime-local"]::-webkit-inner-spin-button,
input[type="datetime-local"]::-webkit-calendar-picker-indicator {
    // display: none;
    // -webkit-appearance: none;
    font-size: 18px;
    color: #2081E2 !important;
}
.setup-project-inner-block{
    //max-width: 802px;
}
.mt50{
    margin-top: 50px;
}
.project-edit-header{
    margin-left: 78px;
}
</style>
