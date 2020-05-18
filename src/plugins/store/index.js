import Vue from 'vue';
import Vuex from 'vuex';

import axios from "axios";

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        ready: false,
        contexts: [],
        templates: []
    },
    mutations: {
        setContexts(state, payload) {
            state.contexts = payload.contexts;
        },
        setTemplates(state, payload) {
            state.templates = payload.templates;
        },
        setAppReady(state) {
            state.ready = true;
        }
    },
    actions: {
        getContexts({commit}) {
            return new Promise(resolve => {
                axios.get('/assets/components/rebatcher/connectors/contexts.php')
                    .then(r => {
                        commit('setContexts', {
                            contexts: r.data
                        });
                        resolve();
                    });
            });
        },
        getTemplates({commit}) {
            return new Promise(resolve => {
                axios.get('/assets/components/rebatcher/connectors/templates.php')
                    .then(r => {
                        commit('setTemplates', {
                            templates: [{id: 0, templatename: 'Не задан'}].concat(r.data)
                        });
                        resolve();
                    });
            });
        },
        async init({commit, dispatch}) {
            await dispatch('getContexts');
            await dispatch('getTemplates');
            commit('setAppReady');
        }
    },
    getters: {
        ready(state) {
            return state.ready;
        },
        contexts(state) {
            return state.contexts;
        },
        templates(state) {
            return state.templates;
        }
    }
});