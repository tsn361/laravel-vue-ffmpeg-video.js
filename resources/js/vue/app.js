
import Vue from 'vue';
import projects from './components/project/index.vue';
import ProjectCreate from './components/project/create.vue';

import {ValidationObserver, ValidationProvider, extend } from 'vee-validate';
import LoadingSpinner from './components/LoadingSpinner.vue';
import VueSimpleAlert from "vue-simple-alert";

Vue.use(VueSimpleAlert, { reverseButtons: true });

import * as rules from 'vee-validate/dist/rules';

export const bus = new Vue();

Object.keys(rules).forEach(rule => {
    extend(rule, rules[rule]);
});
Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);
Vue.component('loading-spinner', LoadingSpinner);

new Vue({
    el: '#app',
    components: { projects, ProjectCreate}
});
